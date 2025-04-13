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
- Password Reset via Email (Forgot Password)
- CSRF Token Protection on all forms

### ✅ Flash Messaging

- One-time success/error messages
- Displays messages after login, logout, password reset, etc.
- Flash helper class included

### ✅ User Profile

- Logged-in users can update their:
  - Username
  - Email
  - Password (optional)
- Input validation and secure update logic

### ✅ Password Reset System

- Forgot Password request form
- Secure reset token generation and expiry handling
- Password reset link via email
- Reset form with confirmation
- Flash success message on reset

### ✅ CSRF Protection

- Token-based CSRF defense on all user input forms
- Session-bound random token generation and validation

### ✅ Role-Based Access Control

- Role support: `user`, `admin`
- Admin-only dashboard access
- Role checked through session and Auth class

### ✅ Admin Dashboard

- View all users (ID, Username, Email, Role)
- Pagination for user listing
- Filter/search users by role, email, or username
- Edit user info and role
- Delete users (except self)
- Role-specific navigation

### ✅ Reusable Layout Includes

- `header.php` and `footer.php` shared across all pages
- Flash message and navbar logic in header
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
2. Set up a database and import the `database.sql` file (or use provided schema).
3. Update `/config/database.php` with your database credentials.
4. Start your local server or deploy to a PHP-supported host.

---

### Notes

I have tested all of the code - everything is working as intended. If you come across any bugs please open an issue. I will continue to update this system with more features, etc. I hope everyone enjoys using it for projects.

---

## 📄 License

MIT License — free to use and modify for personal or commercial projects.
