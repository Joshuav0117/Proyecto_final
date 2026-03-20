<?php
require_once __DIR__ . '/../Model/dbConnect.php'; // $pdo disponible

class AdminController
{
  public function index()
  {
    $action = $_GET['action'] ?? 'dashboard';

    switch ($action) {
      case 'edit_classrooms':
        $this->editRooms();
        break;

      case 'toogle_room_status':
        $this->toggleRoomStatus();
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

      case 'dashboard':
      default:
        $this->render('admin/dashboard');
        break;
    }
  }

  private function editRooms()
  {
    $classrooms = [
      [
        's_id' => 'AC-232',
        's_localizacion' => 'Desconocido',
        's_capacidad' => 0,
        's_estado' => 1
      ],
      [
        's_id' => 'AC-233B',
        's_localizacion' => 'Desconocido',
        's_capacidad' => 0,
        's_estado' => 0
      ]
    ];

    $this->render('admin/edit_classrooms', [
      'classrooms' => $classrooms
    ]);
  }

  private function toggleRoomStatus()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: index_admin.php?action=edit_rooms');
      exit;
    }

    $s_id = trim($_POST['s_id'] ?? '');
    $current_status = isset($_POST['current_status']) ? (int)$_POST['current_status'] : 0;

    $new_status = ($current_status === 1) ? 0 : 1;

     /*Aquí iría los archivos del Modelo cuando tengamos conectada la base de datos*/

    header('Location: index_admin.php?action=edit_rooms');
    exit;
  }

  private function añadirArchivo()
  {
    $this->render('admin/uploads/añadirArchivo');
  }

  public function uploadCSV() {

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

  private function saveCSVToDB() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
        $_SESSION['csv_rows'] = $_POST['data'];
    }

    require_once __DIR__ . '/../Model/uploadModel.php';
    $model = new UploadModel();

    global $pdo; // Usamos el $pdo de dbConnect.php
    if (!$pdo) {
        die("No hay conexión a la base de datos");
    }

    $result = $model->saveCSVToDB($_SESSION['csv_rows'], $pdo);

    if (isset($result['error'])) {
        die("Error al guardar en DB: " . $result['error']);
    }

    // Limpiamos los datos de sesión
    unset($_SESSION['csv_rows']);

    header('Location: index_admin.php?action=dashboard');
    exit;
}

  private function render($view, $data = [])
  {
    extract($data);
    require __DIR__ . '/../View/' . $view . '.php';
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
