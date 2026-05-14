<?php

require_once __DIR__ . '/../Model/dbConnect.php';
require_once __DIR__ . '/../Model/loginModel.php';

class LoginController
{
    public function handle()
    {
        global $pdo;

        $loginModel = new LoginModel($pdo);

        $error = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {

                $error = 'Debes completar todos los campos.';

            } else {

                // Validar usuario
                if ($loginModel->validateUser($email, $password)) {

                    // Buscar en BD
                    $result = $loginModel->getAdminData($email);

                    if ($result) {

                        $_SESSION['logged_in'] = true;
                        $_SESSION['user_role'] = $result['a_rol'];
                        $_SESSION['user_email'] = $result['a_email'];
                        $_SESSION['user_department'] = $result['a_departamento'];

                        $_SESSION['user'] = [
                            'email' => $result['a_email'],
                            'rol' => $result['a_rol'],
                            'departamento' => $result['a_departamento']
                        ];

                        switch ($result['a_rol']) {

                            case "Administrador":
                                header('Location: index_admin.php');
                                exit();

                            case "Director":
                                header('Location: index_director.php');
                                exit();

                            default:
                                header('Location: index_usuario.php');
                                exit();
                        }

                    } else {

                        // Usuario normal
                        $_SESSION['logged_in'] = true;
                        $_SESSION['user_role'] = 'Usuario';
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_department'] = null;

                        $_SESSION['user'] = [
                            'email' => $email,
                            'rol' => 'Usuario',
                            'departamento' => null
                        ];

                        header('Location: index_usuario.php');
                        exit();
                    }

                } else {

                    $error = 'Correo o contraseña incorrectos.';
                }
            }
        }

        require_once __DIR__ . '/../View/auth/login.php';
    }
}
?>