<?php
require_once __DIR__ . '/../models/User.php';

/**
 * Auth class
 * Manages user login, logout, and session-based authentication.
 */
class Auth
{
    /**
     * Attempt to log in a user with their email and password.
     *
     * @param string $email
     * @param string $password
     * @return bool True if login successful, false otherwise
     */
    public static function login(string $email, string $password): bool
    {
        // Find the user by email
        $user = User::findByEmail($email);

        // Verify user exists and password is valid
        if ($user && $user->verifyPassword($password)) {
            // Set session variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_role'] = $user->role;
            return true;
        }

        return false;
    }

    /**
     * Log the current user out by clearing the session.
     */
    public static function logout(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Check if a user is currently logged in.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return User|null
     */
    public static function user(): ?User
    {
        if (self::check()) {
            return User::findById($_SESSION['user_id']);
        }

        return null;
    }

    /**
     * Check if the current user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
}
