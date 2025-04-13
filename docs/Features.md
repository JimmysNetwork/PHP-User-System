# PHP OOP User System Skeleton

A clean, modern PHP user authentication system built using Object-Oriented Programming (OOP). Designed as a reusable base for any PHP-based website requiring user management, roles, and admin tools.

---

## 🚀 Features

### ✅ Authentication

- User Registration
- User Login
- Secure Logout
- Password Hashing with `password_hash()`
- Session-based Authentication

### ✅ CSRF Protection

- CSRF token generation and validation on all forms
- Blocks unauthorized external form submissions

### ✅ Flash Messaging

- One-time success/error messages displayed after actions like login or registration
- Built-in helper class for setting and retrieving flash messages

### ✅ Role-Based Access Control

- User roles (`user`, `admin`)
- Admin-only access enforcement for sensitive pages
- Role detection via session and Auth class

### ✅ Admin Dashboard

- View all registered users (ID, Username, Email, Role)
- Protected from non-admin access
- Extendable with edit/delete functionality

### ✅ Reusable Layout Includes

- Shared `header.php` and `footer.php` files
- Dynamic navigation bar (based on login status and role)
- Bootstrap 5 CDN for styling

---

## 🗂️ Folder Structure

/app
├── /controllers # Auth class, Role manager
├── /models # User model
├── /helpers # Flash messaging, CSRF helpers

/config # Database config (PDO)

/includes # Header and footer includes

/public # Web-accessible files (login, register, dashboard, admin)

/README.md

---

## 🧠 Requirements

- PHP 8.2 or higher
- MySQL 5.7+/MariaDB 10+
- Web server (Apache/Nginx) with rewrite enabled

---

## 🛠 Setup Instructions

1. Clone or download this repository.
2. Set up a database and import the database.sql file.
