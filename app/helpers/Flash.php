<?php
/**
 * Flash message helper class
 */
class Flash
{
    public static function set(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    public static function get(string $key): ?string {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); // remove after showing
            return $msg;
        }
        return null;
    }
}
