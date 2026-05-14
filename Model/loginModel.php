<?php

class LoginModel
{
    private PDO $pdo;

    // Constructor
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Usuarios temporeros
    private $users = [

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

    // Validar credenciales
    public function validateUser($email, $password)
    {
        return isset($this->users[$email]) &&
               $this->users[$email]['password'] === $password;
    }

    // Buscar administrador/director
    public function getAdminData($email)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a_email,
                a_rol,
                a_departamento
            FROM Administrador
            WHERE a_email = :email
            AND a_estado = 1
        ");

        $stmt->bindParam(':email', $email);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>