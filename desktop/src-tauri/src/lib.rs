use std::fs;
use std::net::TcpStream;
use std::path::{Path, PathBuf};
use std::process::{Child, Command, Stdio};
use std::sync::Mutex;
use std::thread;
use std::time::Duration;

use tauri::{AppHandle, Manager, State};

const SERVER_HOST: &str = "127.0.0.1";
const SERVER_PORT: u16 = 8765;

struct AppState {
    child: Mutex<Option<Child>>,
}

fn resource_path(app: &AppHandle, relative: &str) -> Result<PathBuf, String> {
    let resource_dir = app
        .path()
        .resource_dir()
        .map_err(|e| format!("Unable to resolve resource directory: {e}"))?;

    Ok(resource_dir.join(relative))
}

fn ensure_user_data(app: &AppHandle, bundled_app: &Path) -> Result<PathBuf, String> {
    let data_dir = app
        .path()
        .app_data_dir()
        .map_err(|e| format!("Unable to resolve app data directory: {e}"))?;

    let runtime_app = data_dir.join("app");
    let db_dir = data_dir.join("database");
    let storage_dir = data_dir.join("storage");

    fs::create_dir_all(&runtime_app).map_err(|e| e.to_string())?;
    fs::create_dir_all(&db_dir).map_err(|e| e.to_string())?;
    fs::create_dir_all(&storage_dir).map_err(|e| e.to_string())?;

    // Copy packaged Laravel app into a writable user data location on first run / update.
    let stamp = runtime_app.join(".runtime_ready");
    if !stamp.exists() {
        copy_dir_recursive(bundled_app, &runtime_app)?;
        fs::write(&stamp, b"1").map_err(|e| e.to_string())?;
    }

    let bundled_db = bundled_app.join("database").join("database.sqlite");
    let user_db = db_dir.join("database.sqlite");
    if !user_db.exists() && bundled_db.exists() {
        fs::copy(&bundled_db, &user_db).map_err(|e| e.to_string())?;
    } else if !user_db.exists() {
        fs::File::create(&user_db).map_err(|e| e.to_string())?;
    }

    // Ensure storage directories exist for Laravel.
    for sub in [
        "app/public",
        "app/backups",
        "framework/cache",
        "framework/sessions",
        "framework/views",
        "logs",
    ] {
        fs::create_dir_all(storage_dir.join(sub)).map_err(|e| e.to_string())?;
    }

    // Prefer user storage over packaged storage.
    let runtime_storage = runtime_app.join("storage");
    if runtime_storage.exists() {
        // Keep runtime storage; logs/uploads should go to user data storage via env when possible.
    }

    Ok(runtime_app)
}

fn copy_dir_recursive(from: &Path, to: &Path) -> Result<(), String> {
    fs::create_dir_all(to).map_err(|e| e.to_string())?;
    for entry in fs::read_dir(from).map_err(|e| e.to_string())? {
        let entry = entry.map_err(|e| e.to_string())?;
        let path = entry.path();
        let dest = to.join(entry.file_name());
        if path.is_dir() {
            // Skip bulky/dev folders if present.
            let name = entry.file_name().to_string_lossy().to_lowercase();
            if matches!(
                name.as_str(),
                "node_modules" | ".git" | "tests" | "desktop" | "vendor/bin"
            ) {
                // Still need vendor for PHP — only skip node_modules/tests/desktop/.git
                if name == "node_modules" || name == ".git" || name == "tests" || name == "desktop" {
                    continue;
                }
            }
            copy_dir_recursive(&path, &dest)?;
        } else {
            if let Some(parent) = dest.parent() {
                fs::create_dir_all(parent).map_err(|e| e.to_string())?;
            }
            fs::copy(&path, &dest).map_err(|e| e.to_string())?;
        }
    }
    Ok(())
}

fn find_php(app: &AppHandle) -> Result<PathBuf, String> {
    let candidates = [
        resource_path(app, "resources/php/php.exe")?,
        resource_path(app, "php/php.exe")?,
    ];

    for candidate in candidates {
        if candidate.exists() {
            return Ok(candidate);
        }
    }

    Err(
        "Bundled PHP runtime was not found. Run desktop/scripts/download-php.ps1 before packaging."
            .into(),
    )
}

fn wait_for_port(host: &str, port: u16, attempts: u32) -> bool {
    for _ in 0..attempts {
        if TcpStream::connect((host, port)).is_ok() {
            return true;
        }
        thread::sleep(Duration::from_millis(250));
    }
    false
}

#[tauri::command]
fn start_laravel_server(app: AppHandle, state: State<AppState>) -> Result<String, String> {
    {
        let guard = state.child.lock().map_err(|e| e.to_string())?;
        if guard.is_some() {
            return Ok(format!("http://{SERVER_HOST}:{SERVER_PORT}"));
        }
    }

    let bundled_app = resource_path(&app, "resources/app")
        .or_else(|_| resource_path(&app, "app"))?;
    if !bundled_app.exists() {
        return Err(
            "Packaged Laravel application was not found. Run desktop/scripts/prepare-runtime.ps1 first."
                .into(),
        );
    }

    let runtime_app = ensure_user_data(&app, &bundled_app)?;
    let php = find_php(&app)?;
    let data_dir = app
        .path()
        .app_data_dir()
        .map_err(|e| format!("Unable to resolve app data directory: {e}"))?;
    let user_db = data_dir.join("database").join("database.sqlite");
    let artisan = runtime_app.join("artisan");
    let public_index = runtime_app.join("public").join("index.php");

    if !artisan.exists() || !public_index.exists() {
        return Err("Laravel runtime is incomplete (missing artisan/public/index.php).".into());
    }

    let user_storage = data_dir.join("storage");
    let mut command = Command::new(&php);
    command
        .current_dir(&runtime_app)
        .arg(artisan.as_os_str())
        .arg("serve")
        .arg(format!("--host={SERVER_HOST}"))
        .arg(format!("--port={SERVER_PORT}"))
        .env("APP_ENV", "production")
        .env("APP_DEBUG", "false")
        .env("APP_URL", format!("http://{SERVER_HOST}:{SERVER_PORT}"))
        .env("DB_CONNECTION", "sqlite")
        .env("DB_DATABASE", user_db.as_os_str())
        .env("SESSION_DRIVER", "file")
        .env("CACHE_STORE", "file")
        .env("LOG_CHANNEL", "stack")
        // Point writable paths at AppData when Laravel respects these conventions.
        .env("VIEW_COMPILED_PATH", user_storage.join("framework").join("views").as_os_str())
        .stdout(Stdio::null())
        .stderr(Stdio::null());

    let child = command
        .spawn()
        .map_err(|e| format!("Failed to start PHP/Laravel server: {e}"))?;

    {
        let mut guard = state.child.lock().map_err(|e| e.to_string())?;
        *guard = Some(child);
    }

    if !wait_for_port(SERVER_HOST, SERVER_PORT, 80) {
        return Err("Laravel server started but did not open the expected port.".into());
    }

    Ok(format!("http://{SERVER_HOST}:{SERVER_PORT}"))
}

fn kill_child(state: &AppState) {
    if let Ok(mut guard) = state.child.lock() {
        if let Some(child) = guard.as_mut() {
            let _ = child.kill();
            let _ = child.wait();
        }
        *guard = None;
    }
}

#[cfg_attr(mobile, tauri::mobile_entry_point)]
pub fn run() {
    tauri::Builder::default()
        .plugin(tauri_plugin_shell::init())
        .manage(AppState {
            child: Mutex::new(None),
        })
        .invoke_handler(tauri::generate_handler![start_laravel_server])
        .on_window_event(|window, event| {
            if let tauri::WindowEvent::CloseRequested { .. } = event {
                let state = window.state::<AppState>();
                kill_child(&state);
            }
        })
        .setup(|app| {
            // Warm-start path resolution early so missing resources fail clearly in logs.
            let _ = app.path().resource_dir();
            Ok(())
        })
        .run(tauri::generate_context!())
        .expect("error while running POS Inventory desktop app");
}
