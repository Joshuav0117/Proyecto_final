<?php
require_once __DIR__ . '/../Model/dbConnect.php';
class LoginController
{
    public function handle()
    {
        global $pdo;

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
            ],
            'juan.delpueblo@upr.edu' => [
                'password' => 'pass1234',
                'role' => 'usuario'
            ]
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $error = 'Debes completar todos los campos.';
            } 
            else {
                // Verificar usuario
                if (isset($users[$email]) && $users[$email]['password'] === $password) {

                    $_SESSION['user'] = [
                        'email' => $email,
                        'role' => $users[$email]['role'],
                    ];
                    
                // Este es el pedazo de codigo que quiero verificar
                    // Buscar el usuario en la base de datos
                    $stmt = $pdo->prepare("SELECT a_email, a_rol, a_departamento FROM Administrador WHERE a_email = :email");
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();

                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                         // Guardar sesión para Admin o Director
                            $_SESSION['logged_in'] = true;
                            $_SESSION['user_role'] = $result['a_rol']; // Administrador o Director
                            $_SESSION['user_email'] = $result['a_email'];
                            $_SESSION['user_department'] = $result['a_departamento'];

                        $_SESSION['user'] = [
                            'email' => $result['a_email'],
                            'rol' => $result['a_rol'],
                            'departamento' => $result['a_departamento']
                        ];

                        // Redirección según rol
                        switch ($result['a_rol']) {
                            case "Administrador":
                                header('Location: index_admin.php');
                                break;

                            case "Director":
                                header('Location: index_director.php');
                                break;

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