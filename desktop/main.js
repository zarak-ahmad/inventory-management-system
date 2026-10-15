const statusEl = document.getElementById('status');
const errorEl = document.getElementById('error');
const spinnerEl = document.getElementById('spinner');

function setStatus(message) {
  statusEl.textContent = message;
}

async function waitForServer(url, attempts = 60) {
  for (let i = 0; i < attempts; i++) {
    try {
      const response = await fetch(url, { method: 'GET', cache: 'no-store' });
      if (response.ok || response.status === 302 || response.status === 200) {
        return true;
      }
    } catch (_) {
      // server not ready yet
    }
    setStatus(`Waiting for local server… (${i + 1}/${attempts})`);
    await new Promise((resolve) => setTimeout(resolve, 500));
  }
  return false;
}

async function boot() {
  try {
    const { invoke } = await import('@tauri-apps/api/core');
    setStatus('Starting PHP runtime…');
    const url = await invoke('start_laravel_server');
    setStatus('Connecting…');
    const ready = await waitForServer(url);
    if (!ready) {
      throw new Error('Local server did not become ready in time.');
    }
    window.location.replace(url);
  } catch (error) {
    spinnerEl.style.display = 'none';
    setStatus('Unable to start the application.');
    errorEl.textContent = String(error?.message || error);
  }
}

boot();
