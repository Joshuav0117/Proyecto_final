<?php

require_once __DIR__ . '/../Model/ClassroomModel.php';
require_once __DIR__ . '/../Model/reunionModel.php';
require_once __DIR__ . '/../Model/MailService.php';


class DirectorController
{
    public function index()
    {
        $action = $_GET['action'] ?? 'dashboard';

        switch ($action) {
            case 'edit_classrooms':
                $departamento = $_SESSION['user']['departamento'];
                $this->editClassrooms($departamento);
                break;

            case 'toggle_classroom_status':
                $this->toggleClassroomStatus();
                break;

            case 'actualizarReserva':
                $this->actualizarReserva();
                break;

            case 'dashboard':
            default:
            $model = new ReunionModel();
            $departamento = $_SESSION['user']['departamento'];
            $pendientes = $model->getPendientesDirector($departamento);
            $this->render('director/director_dashboard', [
                'pendientes' => $pendientes
            ]);
        }
    }

    private function editClassrooms($departamento)
    {
        $model = new ClassroomModel();
        $classrooms = $model->getDeptClassrooms($departamento);

        $this->render('admin/edit_classrooms', [
            'classrooms' => $classrooms,
            'panel_base' => 'index_director.php'
        ]);
    }

    private function toggleClassroomStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index_director.php?action=edit_classrooms');
            exit;
        }

        $s_id = trim($_POST['s_id'] ?? '');
        $current_status = isset($_POST['current_status']) ? (int)$_POST['current_status'] : 0;

        if ($s_id === '') {
            header('Location: index_director.php?action=edit_classrooms');
            exit;
        }

        $new_status = ($current_status === 1) ? 0 : 1;

        $model = new ClassroomModel();
        $model->updateClassroomStatus($s_id, $new_status);

        header('Location: index_director.php?action=edit_classrooms');
        exit;
    }

    private function render($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../View/' . $view . '.php';
    }

    private function actualizarReserva()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método no permitido";
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $estado = (int)($_POST['estado'] ?? 0);
        $nota = trim($_POST['nota'] ?? '');

        if ($id <= 0 || !in_array($estado, [0, 2], true)) {
            http_response_code(400);
            echo "Datos inválidos";
            return;
        }

        $model = new ReunionModel();
        $model->actualizarEstado($id, $estado);

        $reserva = $model->getById($id);

        if (!$reserva) {
            http_response_code(404);
            echo "Reservación no encontrada";
            return;
        }

        $mailService = new MailService();
        $mailSent = $mailService->sendReservationStatusEmail($reserva, $estado, $nota);

        if (!$mailSent) {
            http_response_code(500);
            echo "La reservación se actualizó, pero no se pudo enviar el correo";
            return;
        }

        echo "ok";
    }
}