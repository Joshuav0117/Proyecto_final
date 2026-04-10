<?php
require_once __DIR__ . '/../Model/dbConnect.php';
require_once __DIR__ . '/../Model/ClassroomModel.php';
require_once __DIR__ . '/../Model/reunionModel.php';
require_once __DIR__ . '/../Model/AdminModel.php';

class AdminController
{
    public function index()
    {
        $action = $_GET['action'] ?? 'dashboard';

        switch ($action) {
            case 'edit_classrooms':
                $this->editClassrooms();
                break;

            case 'toggle_classroom_status':
                $this->toggleClassroomStatus();
                break;

            case 'add_classroom':
                $this->addClassroom();
                break;

            case 'save_classroom':
                $this->saveClassroom();
                break;

            case 'añadirArchivo':
                $this->añadirArchivo();
                break;

            case 'uploadCSV':
                $this->uploadCSV();
                break;

            case 'saveCSV':
                $this->saveCSVToDB();
                break;

            case 'add_admin':
                $this->addAdmin();
                break;

            case 'save_admin':
                $this->saveAdmin();
                break;
            
            case 'update_admin_role':
                $this->updateAdminRole();
                break;

            case 'toggle_admin_status':
                $this->toggleAdminStatus();
                break;

            case 'actualizarReserva':
                $this->actualizarReserva();
                break;
            
            case 'dashboard':
            default:
            $model = new ReunionModel();
            $pendientes = $model->getPendientes();

            $this->render('admin/admin_dashboard', [
                'pendientes' => $pendientes
            ]);
        }
    }

    private function render($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../View/' . $view . '.php';
    }

    // Mostrar la vista con todos los salones
    private function editClassrooms()
    {
        $model = new ClassroomModel();
        $classrooms = $model->getAllClassrooms();

        $this->render('admin/edit_classrooms', [
            'classrooms' => $classrooms,
            'panel_base' => 'index_admin.php'
        ]);
    }

    // Cambiar estado del salón
    private function toggleClassroomStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index_admin.php?action=edit_classrooms');
            exit;
        }

        $s_id = trim($_POST['s_id'] ?? '');
        $current_status = isset($_POST['current_status']) ? (int)$_POST['current_status'] : 0;

        if ($s_id === '') {
            header('Location: index_admin.php?action=edit_classrooms');
            exit;
        }

        $new_status = ($current_status === 1) ? 0 : 1;

        $model = new ClassroomModel();
        $model->updateClassroomStatus($s_id, $new_status);

        header('Location: index_admin.php?action=edit_classrooms');
        exit;
    }

    private function addClassroom()
    {
        // Lista de departamentos para el dropdown
        $departments = [
            'CCOM' => 'Ciencias de Computadoras',
            'MATE' => 'Matemáticas',
            'BIOL' => 'Biología',
            'FISI' => 'Física',
            'QUIM' => 'Química',
            'ADEM' => 'Administración de Empresas',
            'COMU' => 'Comunicaciones',
            'CISO' => 'Ciencias Sociales',
            'EDUC' => 'Educación',
            'ESPA' => 'Español',
            'INGL' => 'Inglés',
            'HUMA' => 'Humanidades',
            'ENFE' => 'Enfermería',
            'GTEC' => 'Gerencia de Tecnologías de Información y Procesos Administrativos',
            'ADMIN' => 'Administración'
        ];

        $this->render('admin/add_classroom', [
            'departments' => $departments,
            'error' => '',
            'success' => ''
        ]);
    }

    private function saveClassroom()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index_admin.php?action=add_classroom');
            exit;
        }

        $s_id = trim($_POST['s_id'] ?? '');
        $s_departamento = trim($_POST['s_departamento'] ?? '');
        $s_capacidad = (int)($_POST['s_capacidad'] ?? 0);

        $departments = [
            'CCOM' => 'Ciencias de Computadoras',
            'MATE' => 'Matemáticas',
            'BIOL' => 'Biología',
            'FISI' => 'Física',
            'QUIM' => 'Química',
            'ADEM' => 'Administración de Empresas',
            'COMU' => 'Comunicaciones',
            'CISO' => 'Ciencias Sociales',
            'EDUC' => 'Educación',
            'ESPA' => 'Español',
            'INGL' => 'Inglés',
            'HUMA' => 'Humanidades',
            'ENFE' => 'Enfermería',
            'GTEC' => 'Gerencia de Tecnologías de Información y Procesos Administrativos',
            'ADMINISTRACION' => 'Administración'
        ];

        if ($s_id === '' || $s_departamento === '' || $s_capacidad < 0) {
            $this->render('admin/add_classroom', [
                'departments' => $departments,
                'error' => 'Completa todos los campos correctamente.',
                'success' => ''
            ]);
            return;
        }

        $model = new ClassroomModel();

        if ($model->classroomExists($s_id)) {
            $this->render('admin/add_classroom', [
                'departments' => $departments,
                'error' => 'Ya existe un salón con ese nombre.',
                'success' => ''
            ]);
            return;
        }

        $saved = $model->insertClassroom($s_id, $s_capacidad, $s_departamento);

        if ($saved) {
            $this->render('admin/add_classroom', [
                'departments' => $departments,
                'error' => '',
                'success' => 'Salón añadido correctamente.'
            ]);
            return;
        }

        $this->render('admin/add_classroom', [
            'departments' => $departments,
            'error' => 'No se pudo guardar el salón.',
            'success' => ''
        ]);
    }

    private function addAdmin()
    {
        $departments = [
            'CCOM' => 'Ciencias de Computadoras',
            'MATE' => 'Matemáticas',
            'BIOL' => 'Biología',
            'FISI' => 'Física',
            'QUIM' => 'Química',
            'ADEM' => 'Administración de Empresas',
            'COMU' => 'Comunicaciones',
            'CISO' => 'Ciencias Sociales',
            'EDUC' => 'Educación',
            'ESPA' => 'Español',
            'INGL' => 'Inglés',
            'HUMA' => 'Humanidades',
            'ENFE' => 'Enfermería',
            'GTEC' => 'Gerencia de Tecnologías de Información y Procesos Administrativos',
            'ADMIN' => 'Administración'
        ];

        $lists = $this->getAdminLists();

        $this->render('admin/add_admin', [
            'departments' => $departments,
            'administradores' => $lists['administradores'],
            'directores' => $lists['directores'],
            'error' => '',
            'success' => ''
        ]);
    }

    private function saveAdmin()
    {
        global $pdo;

        $departments = [
            'CCOM' => 'Ciencias de Computadoras',
            'MATE' => 'Matemáticas',
            'BIOL' => 'Biología',
            'FISI' => 'Física',
            'QUIM' => 'Química',
            'ADEM' => 'Administración de Empresas',
            'COMU' => 'Comunicaciones',
            'CISO' => 'Ciencias Sociales',
            'EDUC' => 'Educación',
            'ESPA' => 'Español',
            'INGL' => 'Inglés',
            'HUMA' => 'Humanidades',
            'ENFE' => 'Enfermería',
            'GTEC' => 'Gerencia de Tecnologías de Información y Procesos Administrativos',
            'ADMIN' => 'Administración'
        ];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index_admin.php?action=add_admin');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $inicial = trim($_POST['inicial'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $segundo_apellido = trim($_POST['segundo_apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rol = trim($_POST['rol'] ?? '');
        $departamento = trim($_POST['departamento'] ?? '');

        if ($nombre === '' || $apellido === '' || $email === '' || $rol === '' || $departamento === '') {
            $lists = $this->getAdminLists();

            $this->render('admin/add_admin', [
                'departments' => $departments,
                'administradores' => $lists['administradores'],
                'directores' => $lists['directores'],
                'error' => 'Completa todos los campos obligatorios.',
                'success' => ''
            ]);
            return;
        }

        try {
            $sqlCheck = "SELECT a_id FROM Administrador WHERE a_email = ?";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute([$email]);

            if ($stmtCheck->fetch()) {
                $lists = $this->getAdminLists();

                $this->render('admin/add_admin', [
                    'departments' => $departments,
                    'administradores' => $lists['administradores'],
                    'directores' => $lists['directores'],
                    'error' => 'Este email ya está registrado.',
                    'success' => ''
                ]);
                return;
            }

            $sql = "INSERT INTO Administrador 
                (a_nombre, a_inicial, a_primer_apellido, a_segundo_apellido, a_email, a_rol, a_departamento, a_estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $nombre,
                $inicial,
                $apellido,
                $segundo_apellido,
                $email,
                $rol,
                $departamento
            ]);

            $lists = $this->getAdminLists();

            $this->render('admin/add_admin', [
                'departments' => $departments,
                'administradores' => $lists['administradores'],
                'directores' => $lists['directores'],
                'error' => '',
                'success' => 'Administrador añadido correctamente.'
            ]);
        } catch (PDOException $e) {
            $lists = $this->getAdminLists();

            $this->render('admin/add_admin', [
                'departments' => $departments,
                'administradores' => $lists['administradores'],
                'directores' => $lists['directores'],
                'error' => 'Error al guardar: ' . $e->getMessage(),
                'success' => ''
            ]);
        }
    }

      private function añadirArchivo()
    {
        $this->render('admin/uploads/añadirArchivo');
    }
    
    public function uploadCSV()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_FILES['csv_file'])) {
            die("No se recibió archivo");
        }

        require_once __DIR__ . '/../Model/uploadModel.php';

        $model = new UploadModel();
        $result = $model->processCSV($_FILES['csv_file']);

        if (isset($result['error'])) {
            die($result['error']);
        }

        $_SESSION['csv_rows'] = $result['rows'];

        $rows = $result['rows'];
        $errors = $result['errors'];
        $fieldMap = $result['fieldMap'];

        require __DIR__ . '/../View/admin/uploads/previewCSV.php';
    }

    private function saveCSVToDB()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
            $_SESSION['csv_rows'] = $_POST['data'];
        }

        require_once __DIR__ . '/../Model/uploadModel.php';
        $model = new UploadModel();

        global $pdo;
        if (!$pdo) {
            die("No hay conexión a la base de datos");
        }

        $result = $model->saveCSVToDB($_SESSION['csv_rows'], $pdo);

        if (isset($result['error'])) {
            die("Error al guardar en DB: " . $result['error']);
        }

        unset($_SESSION['csv_rows']);

        header('Location: index_admin.php?action=dashboard');
        exit;
    }

   private function actualizarReserva() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = $_POST['id'];
        $estado = $_POST['estado'];
        $nota = $_POST['nota'];

        $model = new ReunionModel();
        $model->actualizarEstado($id, $estado, $nota);

        $reserva = $model->getById($id);

        $mensaje = "Su reservación ha sido ";
        $mensaje .= ($estado == 1) ? "CONFIRMADA" : "DENEGADA";
        $mensaje .= "\n\nNota: " . $nota;

        mail($reserva['r_email'], "Estado de reservación", $mensaje);

        echo "ok";
    }

    private function updateAdminRole()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index_admin.php?action=add_admin');
            exit;
        }

        $a_id = (int)($_POST['a_id'] ?? 0);
        $new_role = trim($_POST['new_role'] ?? '');

        if ($a_id <= 0 || ($new_role !== 'Administrador' && $new_role !== 'Director')) {
            header('Location: index_admin.php?action=add_admin');
            exit;
        }

        $model = new AdminModel();
        $model->updateRole($a_id, $new_role);

        header('Location: index_admin.php?action=add_admin');
        exit;
    }

    private function toggleAdminStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index_admin.php?action=add_admin');
            exit;
        }

        $a_id = (int)($_POST['a_id'] ?? 0);
        $current_status = isset($_POST['current_status']) ? (int)$_POST['current_status'] : 0;

        if ($a_id <= 0) {
            header('Location: index_admin.php?action=add_admin');
            exit;
        }

        // Si está activo lo pone inactivo y viceversa
        $new_status = ($current_status === 1) ? 0 : 1;

        $model = new AdminModel();
        $model->updateStatus($a_id, $new_status);

        header('Location: index_admin.php?action=add_admin');
        exit;
    }

    private function getAdminLists()
    {
        $adminModel = new AdminModel();

        return [
            'administradores' => $adminModel->getAllByRole('Administrador'),
            'directores' => $adminModel->getAllByRole('Director')
        ];
    }
}

// if (isset($_GET['action'])) {
//     $controller = new AdminController();

//     switch ($_GET['action']) {
//         case 'uploadCSV':
//             $controller->uploadCSV();
//             break;
//     }
// }