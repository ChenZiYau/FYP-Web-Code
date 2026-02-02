# Architectural Patterns & Conventions

This document describes the architectural patterns and design conventions used throughout the OptiPlan codebase.

## State Management

### Session-Based Authentication (Backend)
PHP sessions store user state across requests.

**Implementation:**
- Session start and variable assignment: `login.php:18-21`
- Session validation on protected pages: `dashboard.php:3-10`, `admin.php:5-8`
- Complete session destruction: `logout.php:1-20`

**Variables:**
- `$_SESSION['user_id']` - User identifier
- `$_SESSION['role']` - 'user' or 'admin'
- `$_SESSION['name']` - Display name

### LocalStorage State (Frontend)
Persists UI preferences across page reloads.

**Implementation:**
- Save state: `dashboard.js:39` - `localStorage.setItem('sidebarCollapsed', ...)`
- Restore state: `dashboard.js:18` - `if (localStorage.getItem('sidebarCollapsed') === 'true')`

## Module Organization

### Section-Based Comment Headers
JavaScript files use comment blocks to delineate functional sections.

**Pattern:**
```javascript
// ==========================================
// SECTION NAME
// ==========================================
```

**Files using this pattern:**
- `admin.js:1-2, 58-59, 85-86, 123-124`
- `dashboard.js` sections: SIDEBAR (8-53), CALENDAR (56-183), MODAL (186-266)

### Layered PHP Architecture
Each PHP page follows: database include → business logic → HTML presentation.

**Pattern:**
1. `require_once 'db.php'` - Database dependency
2. Session checks and form processing - Logic layer
3. HTML output with embedded PHP - Presentation layer

**Examples:**
- `login.php:1-3` (include), `login.php:6-36` (logic), `login.php:39+` (HTML)
- `admin.php:1-10` (include + auth), `admin.php:11-40` (queries), `admin.php:41+` (HTML)

## API Communication

### JSON AJAX Pattern
Forms submit via fetch() and receive JSON responses.

**Request flow:**
1. Form submit intercepted: `e.preventDefault()`
2. FormData created: `new FormData(this)`
3. Fetch POST: `fetch('endpoint.php', { method: 'POST', body: formData })`
4. Response parsed: `.then(r => r.json())`

**Response structure:**
```json
{
  "success": true|false,
  "message": "Error or success text",
  "redirect": "target.php"
}
```

**Implementations:**
- Login: `login.php:177-195` (client), `login.php:26-31` (server)
- Signup: `signup.php:218-235` (client), `signup.php:265-290` (server)
- Dashboard modals: `dashboard.js:246-266`

### Confirmation for Destructive Actions
User confirmation required before DELETE operations.

**Pattern:** `if (confirm('Are you sure...')) { /* proceed */ }`

**Implementations:**
- Delete user: `admin.js:143`
- Delete feedback: `admin.js:260`
- Logout: `admin.js:318`

## Data Flow

### Server-Rendered Data Binding
PHP queries database and loops results into HTML.

**Pattern:**
```php
<?php foreach ($results as $item): ?>
  <tr>
    <td><?php echo htmlspecialchars($item['field']); ?></td>
  </tr>
<?php endforeach; ?>
```

**Implementations:**
- User table: `admin.php:229-268`
- Dashboard data: `dashboard.php:105-277`

### Unidirectional Form Flow
DOM → FormData → Server → JSON → DOM update

**Complete example in `login.php`:**
1. Form capture: line 177
2. FormData extraction: line 181
3. Fetch POST: lines 183-185
4. Server processing: lines 13-34
5. JSON response: lines 26-31
6. DOM navigation: lines 188-194

## Routing Patterns

### Role-Based Redirect
Login determines redirect URL based on user role.

**Implementation:** `login.php:24`
```php
$redirect = ($user['role'] === 'admin') ? 'admin.php' : 'dashboard.php';
```

### Section-Based SPA Navigation
Single page with multiple sections shown/hidden via JavaScript.

**Pattern:**
- Navigation items have `data-section` attributes
- Click handler calls `switchSection(sectionName)`
- Function toggles `.hidden` class on sections

**Implementation:** `admin.js:6-56`

### Anchor Smooth Scroll
Landing page navigation uses hash links with smooth scroll.

**Pattern:**
- HTML: `<a href="#features">Features</a>`
- Target: `<section id="features">`
- JS scroll: `script.js:31-64`

## Form Handling

### Dual-Layer Validation
Client-side validation for UX, server-side for security.

**Client-side (signup.php:192-216):**
- Password match check
- Minimum length validation
- Terms agreement check

**Server-side (signup.php:265-290):**
- Duplicate email check via PDOException
- Password length revalidation
- Parameterized query execution

### Error Message Display
Errors shown in dedicated divs with CSS transitions.

**Pattern:**
1. Hidden div: `<div class="error-message" id="errorMessage"></div>`
2. Show: `errorElement.classList.add('show')`
3. Auto-hide: `setTimeout(() => { classList.remove('show') }, 5000)`

**Implementations:**
- Login: `login.php:192`
- Signup: `signup.php:172-190`

### Modal Tab Forms
Multi-type creation forms using tab navigation.

**Implementation:** `dashboard.js:234-266`
- Tabs with `data-tab` attribute
- Active state toggled on click
- Form submission includes active tab type

## Security Patterns

### BCRYPT Password Hashing
Secure password storage using PHP's built-in functions.

**Hash creation:** `signup.php:278`, `db.php:34`
```php
$hash = password_hash($password, PASSWORD_BCRYPT);
```

**Verification:** `login.php:17`
```php
password_verify($password, $user['password_hash'])
```

### Prepared Statements (SQL Injection Prevention)
All database queries use PDO prepared statements.

**Pattern:**
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

**All queries in:** `login.php:13-14`, `signup.php:279-280`, `admin.php:20-32`

### Exit Early Pattern
Prevents HTML output after AJAX JSON responses.

**Pattern:** `exit;` after `echo json_encode(...)`

**Implementations:** `login.php:36`, `signup.php:268`, `signup.php:290`

## Error Handling

### Try-Catch with JSON Response
Database errors caught and returned as user-friendly JSON.

**Pattern:**
```php
try {
    $stmt = $pdo->prepare("...");
    $stmt->execute([...]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
```

**Implementations:** `login.php:12-35`, `signup.php:277-289`, `admin.php:11-40`

## Naming Conventions

### PHP Files
- Lowercase, single-word or hyphenated: `login.php`, `db.php`, `dashboard.php`
- Function names are action-based: descriptive of what they do

### JavaScript
- Files: lowercase single-word: `admin.js`, `dashboard.js`
- Functions: camelCase: `switchSection()`, `handleLogout()`, `toggleSidebar()`
- Variables: camelCase: `sidebarCollapsed`, `navItems`, `searchInput`

### CSS Classes
- kebab-case: `.nav-item`, `.btn-primary`, `.form-group`, `.stat-card`
- State classes: `.active`, `.hidden`, `.mobile-open`, `.collapsed`

### HTML IDs
- Semantic, camelCase: `#sidebar`, `#loginForm`, `#createModal`, `#calendarGrid`

## Global Utilities

Utility functions defined outside DOMContentLoaded in `dashboard.js:483-535`:

| Function | Purpose |
|----------|---------|
| `debounce()` | Rate-limit function calls |
| `formatDate()` | Date string formatting |
| `daysUntil()` | Calculate days between dates |
| `getRandomColor()` | Generate random hex color |
| `getInitials()` | Extract initials from name |
