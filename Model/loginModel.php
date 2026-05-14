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
        ],

        'idalyz.centeno1@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'admin'
        ],
        
        'yuitza.humaran@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],
        
        'luis.gonzalez38@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'jose.arbelo@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'carlos.valle@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'nancy.jimenez@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'maria.rodriguez52@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'luis.colon19@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'inocencio.rodriguez@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'vanessa.montalvo1@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'karen.morales@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'geissa.torres@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'jose.ortega@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'merylin.martinez@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

        'rebeca.franqui@upr.edu' => [
            'password' => 'pass1234',
            'role' => 'director'
        ],

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