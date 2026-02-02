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
│   ├── php/           # Server-side pages and logic
│   ├── css/           # Stylesheets
│   └── img/           # Brand assets
├── JavaScript/        # Client-side scripts
└── CLAUDE.md
```

## Key Directories

### `/FYP-Web-Code/php/`
Server-side PHP files handling routing, authentication, and page rendering.

| File | Purpose |
|------|---------|
| `index.php` | Landing page with hero, features, FAQ, feedback form |
| `dashboard.php` | Main user dashboard (tasks, calendar, finance, chatbot) |
| `admin.php` | Admin panel (user management, feedback monitoring) |
| `login.php` | Authentication with AJAX form handling |
| `signup.php` | User registration with validation |
| `logout.php` | Session destruction |
| `db.php` | Database connection, auto-creates schema and seeds admin |

### `/FYP-Web-Code/css/`
Stylesheets using CSS custom properties for theming.

| File | Purpose |
|------|---------|
| `styles.css` | Landing page design system (1522 lines) |
| `dashboard.css` | Dashboard layout and components |
| `admin.css` | Admin panel tables and statistics |
| `auth.css` | Login/signup form styling |

### `/JavaScript/`
Client-side interactivity without frameworks.

| File | Purpose |
|------|---------|
| `script.js` | Landing page: scroll effects, nav dropdowns, mobile menu |
| `dashboard.js` | Sidebar toggle, calendar, modals, keyboard shortcuts |
| `admin.js` | Section switching, search, user management functions |

## Database Schema

Auto-created by `db.php:10-27` on first run:

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
2. Access: `http://localhost/FYP/FYP-Web-Code/php/index.php`
3. Database auto-creates `optiplan_db` on first page load

### Database Access
- phpMyAdmin: `http://localhost/phpmyadmin`
- Database name: `optiplan_db`

## Entry Points

| URL Path | Purpose | Auth Required |
|----------|---------|---------------|
| `/php/index.php` | Public landing page | No |
| `/php/login.php` | User login | No |
| `/php/signup.php` | Registration | No |
| `/php/dashboard.php` | User dashboard | Yes (user role) |
| `/php/admin.php` | Admin panel | Yes (admin role) |

## User Roles

- **user** (default): Access to dashboard features
- **admin**: Access to admin panel + user management

Role-based redirect implemented in `login.php:24`

## Authentication Flow

1. User submits credentials via AJAX (`login.php:177-195`)
2. Server validates with bcrypt (`login.php:17`)
3. Session created with user_id, role, name (`login.php:18-21`)
4. JSON response triggers redirect (`login.php:26-28`)
5. Protected pages check `$_SESSION['user_id']` (`dashboard.php:3-10`, `admin.php:5-8`)

## Design System

CSS custom properties defined in `styles.css:1-50`:

| Token | Value | Usage |
|-------|-------|-------|
| `--primary-purple` | #a78bfa | Primary actions |
| `--dark-purple` | #8b5cf6 | Hover states |
| `--bg-dark` | #0f0b1a | Page background |
| `--surface` | #1a1625 | Card backgrounds |

## Key Implementation Details

- **Password hashing**: BCRYPT via `password_hash()` (`signup.php:278`, `db.php:34`)
- **SQL injection prevention**: PDO prepared statements (`login.php:13-14`)
- **State persistence**: localStorage for sidebar state (`dashboard.js:18`)
- **AJAX pattern**: FormData + fetch with JSON responses (`login.php:181-194`)

## Additional Documentation

When working on specific features, check these files:

| Topic | File |
|-------|------|
| Architectural patterns & conventions | [.claude/docs/architectural_patterns.md](.claude/docs/architectural_patterns.md) |
