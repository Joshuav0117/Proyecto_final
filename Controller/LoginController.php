<?php

class LoginController
{
    public function handle()
    {
        $error = '';
        $email = '';

        // Diccionario de usuarios
        $users = [
            'aixa.ramirez@upr.edu' => [
                'password' => 'pass1234',
                'role' => 'admin'
            ],
            'joshua.valentin2@upr.edu' => [
                'password' => 'pass1234',
                'role' => 'admin'
            ],
            'michael.velez12@upr.edu' => [
                'password' => 'pass1234',
                'role' => 'director'
            ],
            'dereck.declet@upr.edu' => [
                'password' => 'pass1234',
                'role' => 'director'
            ],
            'usuario@upr.edu' => [
                'password' => 'pass1234',
                'role' => 'usuario'
            ]
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $error = 'Debes completar todos los campos.';
            } else {
                // Verificar usuario
                if (isset($users[$email]) && $users[$email]['password'] === $password) {

                    $_SESSION['user'] = [
                        'email' => $email,
                        'role' => $users[$email]['role']
                    ];

                    // Redirección según rol
                    switch ($users[$email]['role']) {
                        case 'admin':
                            header('Location: index_admin.php');
                            break;

                        case 'director':
                            header('Location: index_director.php');
                            break;

                        case 'usuario':
                            header('Location: index_usuario.php');
                            break;
                    }
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