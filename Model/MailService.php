<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class MailService
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/mailConfig.php';
    }

    public function sendReservationStatusEmail(array $reserva, int $estado, string $nota = ''): bool
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        $fueConfirmada = ($estado === 2);
        $estadoTexto = $fueConfirmada ? 'aceptada' : 'denegada';
        $estadoTitulo = $fueConfirmada ? 'ACEPTADA ✅' : 'DENEGADA ❌';

        $nota = trim($nota);

        $subject = "Estado de su reservación: {$estadoTitulo}";

        $html = "
            <p>Hola {$this->escape($reserva['r_nombre'] ?? 'solicitante')},</p>
            <p>Su reservación ha sido <strong>{$estadoTitulo}</strong>.</p>
            <p><strong>Salón:</strong> " . $this->escape($reserva['s_id'] ?? '') . "</p>
            <p><strong>Día:</strong> " . $this->escape($reserva['r_dia'] ?? '') . "</p>
            <p><strong>Horario:</strong> " . $this->escape($reserva['r_hora_inicio'] ?? '') . " - " . $this->escape($reserva['r_hora_final'] ?? '') . "</p>
        ";

        if ($nota !== '') {
            $html .= "<p><strong>Nota:</strong> " . nl2br($this->escape($nota)) . "</p>";
        }

        $html .= "<p>Gracias por utilizar el sistema de reservaciones.</p>";

        $plain = "Hola " . ($reserva['r_nombre'] ?? 'solicitante') . ",\n\n";
        $plain .= "Su reservación ha sido {$estadoTexto}.\n";
        $plain .= "Salón: " . ($reserva['s_id'] ?? '') . "\n";
        $plain .= "Día: " . ($reserva['r_dia'] ?? '') . "\n";
        $plain .= "Horario: " . ($reserva['r_hora_inicio'] ?? '') . " - " . ($reserva['r_hora_final'] ?? '') . "\n";

        if ($nota !== '') {
            $plain .= "Nota: {$nota}\n";
        }

        $plain .= "\nGracias por utilizar el sistema de reservaciones.";

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->Port = $this->config['port'];

            if (($this->config['encryption'] ?? '') === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif (($this->config['encryption'] ?? '') === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            // Remitente y destinatario
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($reserva['r_email'], $reserva['r_nombre'] ?? '');

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = $plain;

            // $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Error enviando correo de reservación: ' . $e->getMessage());
            return false;
        }
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}