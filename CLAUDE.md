# OptiPlan - AI-Powered Productivity Dashboard

## Project Overview

OptiPlan is a full-stack web application designed for students and young professionals. It unifies schedule management, study tracking, and budget management in a single platform, addressing the problem that 75%+ of students use 3+ apps daily.

**Repository:** https://github.com/ChenZiYau/FYP-Web-Code

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 7.4+ with PDO for MySQL |
| Database | MySQL/MariaDB (via XAMPP) |
| Frontend | Vanilla HTML5, CSS3, JavaScript |
| Server | Apache (XAMPP local development) |
| Fonts | Google Fonts: Outfit (display), Inter (body) |

## Project Structure

```
FYP/
├── FYP-Web-Code/
│   ├── pages/
│   │   ├── landing/      # index.php + styles.css + script.js
│   │   ├── auth/          # login.php, signup.php, logout.php + auth.css
│   │   ├── dashboard/     # dashboard.php + dashboard.css + dashboard.js
│   │   ├── finance/       # finance.php, finance_api.php + finance.css + finance.js
│   │   ├── admin/         # admin.php, adminuserdb.php, adminfeedback.php, api_content.php + admin.css + admin.js
│   │   └── settings/      # settings.php, api_settings.php + settings.css
│   ├── includes/          # db.php, security.php, env_loader.php
│   ├── api/               # save_task.php, update_task.php, get_events.php, submit_feedback.php, calendar_proxy.php
│   ├── assets/img/        # Brand assets (logos)
│   └── uploads/pfps/      # User profile picture uploads
└── CLAUDE.md
```

## Key Directories

### `/FYP-Web-Code/pages/`
Page-grouped folders — each page's PHP, CSS, and JS live together.

| Folder | Files | Purpose |
|--------|-------|---------|
| `landing/` | `index.php`, `styles.css`, `script.js` | Landing page with hero, features, FAQ, feedback form |
| `auth/` | `login.php`, `signup.php`, `logout.php`, `auth.css` | Authentication and registration |
| `dashboard/` | `dashboard.php`, `dashboard.css`, `dashboard.js` | Main user dashboard (tasks, calendar, chatbot) |
| `finance/` | `finance.php`, `finance_api.php`, `finance.css`, `finance.js` | Budget management |
| `admin/` | `admin.php`, `adminuserdb.php`, `adminfeedback.php`, `api_content.php`, `admin.css`, `admin.js` | Admin panel |
| `settings/` | `settings.php`, `api_settings.php`, `settings.css` | User settings and profile |

### `/FYP-Web-Code/includes/`
Shared server-side utilities.

| File | Purpose |
|------|---------|
| `db.php` | Database connection, auto-creates schema and seeds admin |
| `security.php` | CSRF protection, session configuration, auth helpers |
| `env_loader.php` | Loads `.env` file for configuration |

### `/FYP-Web-Code/api/`
Stateless API endpoints returning JSON.

| File | Purpose |
|------|---------|
| `save_task.php` | Create new tasks |
| `update_task.php` | Update existing tasks |
| `get_events.php` | Fetch calendar events |
| `submit_feedback.php` | Submit feedback from landing page |
| `calendar_proxy.php` | Server-side proxy for Google Calendar API |

## Database Schema

Auto-created by `db.php` on first run:

```sql
users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50),
  last_name VARCHAR(50),
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

Default admin seeded: `admin@optiplan.com` / `12345`

## Development Commands

**No build system** - This is a traditional PHP application.

### Running Locally
1. Start XAMPP (Apache + MySQL)
2. Access: `http://localhost/FYP/FYP-Web-Code/pages/landing/index.php`
3. Database auto-creates `optiplan_db` on first page load

### Database Access
- phpMyAdmin: `http://localhost/phpmyadmin`
- Database name: `optiplan_db`

## Entry Points

| URL Path | Purpose | Auth Required |
|----------|---------|---------------|
| `/pages/landing/index.php` | Public landing page | No |
| `/pages/auth/login.php` | User login | No |
| `/pages/auth/signup.php` | Registration | No |
| `/pages/dashboard/dashboard.php` | User dashboard | Yes (user role) |
| `/pages/admin/admin.php` | Admin panel | Yes (admin role) |
| `/pages/finance/finance.php` | Finance tracker | Yes (user role) |
| `/pages/settings/settings.php` | User settings | Yes (user role) |

## User Roles

- **user** (default): Access to dashboard features
- **admin**: Access to admin panel + user management

Role-based redirect implemented in `pages/auth/login.php`

## Authentication Flow

1. User submits credentials via AJAX (`pages/auth/login.php`)
2. Server validates with bcrypt
3. Session created with user_id, role, name
4. JSON response triggers redirect
5. Protected pages check `$_SESSION['user_id']`

## Design System

CSS custom properties defined in `pages/landing/styles.css`:

| Token | Value | Usage |
|-------|-------|-------|
| `--primary-purple` | #a78bfa | Primary actions |
| `--dark-purple` | #8b5cf6 | Hover states |
| `--bg-dark` | #0f0b1a | Page background |
| `--surface` | #1a1625 | Card backgrounds |

## Key Implementation Details

- **Password hashing**: BCRYPT via `password_hash()`
- **SQL injection prevention**: PDO prepared statements throughout
- **State persistence**: localStorage for sidebar state (`dashboard.js`)
- **AJAX pattern**: FormData + fetch with JSON responses
- **PHP includes**: All pages use `require_once __DIR__ . '/../../includes/...'` for absolute paths
- **API endpoints**: API files use `require_once __DIR__ . '/../includes/...'`

## Path Conventions

- Page PHP files include shared code via: `require_once __DIR__ . '/../../includes/db.php'`
- API PHP files include shared code via: `require_once __DIR__ . '/../includes/db.php'`
- CSS/JS are co-located with their page PHP files
- Cross-page CSS references use `../folder/file.css` (e.g., `../dashboard/dashboard.css`)
- JS fetch URLs to API: `../../api/endpoint.php` (from pages/*/)
- Navigation links between pages: `../folder/page.php`

## Additional Documentation

When working on specific features, check these files:

| Topic | File |
|-------|------|
| Architectural patterns & conventions | [.claude/docs/architectural_patterns.md](.claude/docs/architectural_patterns.md) |
