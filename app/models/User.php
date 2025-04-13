<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * User model
 * Handles user-related operations such as finding users and verifying passwords.
 */
class User {
    public int $id;
    public string $username;
    public string $email;
    public string $role;
    private string $password_hash;

    /**
     * Constructor (optional but enforces OOP usage)
     */
    public function __construct() {}

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return User|null
     */
    public static function findById($id): ?User {
        global $pdo;

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return self::mapDataToUser($data);
        }

        return null;
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return User|null
     */
    public static function findByEmail($email): ?User {
        global $pdo;

        // Normalize email input
        $email = strtolower(trim($email));

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            return self::mapDataToUser($data);
        }

        return null;
    }

    /**
     * Verify the given password against the stored hash.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Populate a User object with fetched database row.
     *
     * @param array $data
     * @return User
     */
    private static function mapDataToUser(array $data): User {
        $user = new User();
        $user->id = (int)$data['id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->password_hash = $data['password_hash'];
        return $user;
    }
}
