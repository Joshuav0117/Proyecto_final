<?php

class LoginController
{
    public function handle()
    {
        $error = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $error = 'Debes completar todos los campos.';
            } else {
                // Usuario de prueba
                if ($email === 'admin@upr.edu' && $password === '123456') {
                    $_SESSION['user'] = [
                        'email' => $email,
                        'role' => 'admin'
                    ];

                    header('Location: index_admin.php');
                    exit;
                } else {
                    $error = 'Correo o contraseña incorrectos.';
                }
            }
        }

        require_once __DIR__ . '/../View/auth/login.php';
    }
}
?>