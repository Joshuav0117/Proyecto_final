<?php

class AuthController
{
    public static function logout()
    {
        $_SESSION = [];
        session_destroy();

        header("Location: index_login.php");
        exit();
    }

    public static function requireLogin()
    {
        if (
            !isset($_SESSION['logged_in']) ||
            $_SESSION['logged_in'] !== true ||
            !isset($_SESSION['user_role'])
        ) {
            header("Location: index_login.php");
            exit();
        }
    }

    public static function requireRole(array $allowedRoles)
    {
        self::requireLogin();

        $role = $_SESSION['user_role'] ?? '';

        if (!in_array($role, $allowedRoles, true)) {
            self::redirectByRole($role);
        }
    }

    public static function redirectByRole(string $role)
    {
        switch ($role) {
            case 'Administrador':
                header("Location: index_admin.php");
                exit();

            case 'Director':
                header("Location: index_director.php");
                exit();

            case 'Usuario':
            default:
                header("Location: index_usuario.php");
                exit();
        }
    }
}