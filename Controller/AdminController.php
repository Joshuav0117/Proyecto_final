<?php

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



  private function render($view, $data = [])
  {
    extract($data);
    require __DIR__ . '/../View/' . $view . '.php';
  }
}
