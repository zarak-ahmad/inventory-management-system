# Professional Offline POS & Inventory Management System

![Project Status](https://img.shields.io/badge/Status-Development-blue)
![Laravel](https://img.shields.io/badge/Laravel-13-red)
![PHP](https://img.shields.io/badge/PHP-8.3-blue)
![Database](https://img.shields.io/badge/Database-SQLite-green)
![Platform](https://img.shields.io/badge/Platform-Windows-lightgrey)

---

## Overview

Offline Windows POS & Inventory Management System for retail shops.

- Laravel + Blade + Bootstrap 5
- SQLite (local)
- Packaged later with Tauri as a Windows `.exe`

Full documentation lives in [`docs/`](docs/).

---

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+
- npm

---

## Local Setup

```bash
composer install
copy .env.example .env
php artisan key:generate
type nul > database\database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open: http://127.0.0.1:8000

### Default login

- Email: `admin@example.com`
- Password: `password`

For asset hot reload during UI work:

```bash
npm run dev
```

---

## Documentation

| File | Purpose |
|------|---------|
| [docs/01_AI_RULES.md](docs/01_AI_RULES.md) | AI development rules |
| [docs/02_PROJECT_REQUIREMENTS.md](docs/02_PROJECT_REQUIREMENTS.md) | Functional requirements |
| [docs/03_DATABASE_DESIGN.md](docs/03_DATABASE_DESIGN.md) | Database schema |
| [docs/04_UI_GUIDELINES.md](docs/04_UI_GUIDELINES.md) | UI/UX standards |
| [docs/05_CODING_STANDARDS.md](docs/05_CODING_STANDARDS.md) | Coding conventions |
| [docs/06_DEVELOPMENT_ROADMAP.md](docs/06_DEVELOPMENT_ROADMAP.md) | Development phases |
| [docs/07_MODULE_SPECIFICATIONS.md](docs/07_MODULE_SPECIFICATIONS.md) | Module details |
| [docs/08_TESTING_GUIDE.md](docs/08_TESTING_GUIDE.md) | Testing procedures |
| [docs/09_CHANGELOG.md](docs/09_CHANGELOG.md) | Version history |
| [docs/10_TODO.md](docs/10_TODO.md) | Development tasks |

---

## Current Phase

Phase 1 — Project Setup (complete, awaiting approval)

Next: Phase 2 — Authentication & User Roles

---

## Version

**1.0.0** (Development)

Note: Project docs originally targeted Laravel 12. The app scaffold uses **Laravel 13** (current stable at project start). Behavior and conventions remain aligned with the documented architecture.
