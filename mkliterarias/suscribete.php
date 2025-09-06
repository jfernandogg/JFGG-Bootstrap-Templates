<?php
// suscribete.php
header('Content-Type: application/json; charset=utf-8');

$email_destinatario = "ventas@marikaditasliterarias.com";
$asunto_email = "Nueva Suscripción a Newsletter";

function enviar_error($mensaje, $codigo_http = 400) {
    http_response_code($codigo_http);
    echo json_encode(['status' => 'error', 'message' => $mensaje]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enviar_error("Método no permitido.", 405);
}

// Permitir tanto application/json como x-www-form-urlencoded
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $json_str = file_get_contents('php://input');
    $data = json_decode($json_str, true);
    $email = $data['email'] ?? '';
} else {
    $email = $_POST['email'] ?? '';
}

$email = trim($email);
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    enviar_error("Email inválido o vacío.");
}

$cuerpo_email = "<h1>Nueva Suscripción</h1><p>Se ha suscrito el siguiente email: <strong>" . htmlspecialchars($email) . "</strong></p>";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <ventas@marikaditasliterarias.com>' . "\r\n";

if (mail($email_destinatario, $asunto_email, $cuerpo_email, $headers)) {
    echo json_encode(['status' => 'success', 'message' => '¡Gracias por suscribirte!']);
} else {
    enviar_error("No se pudo enviar la suscripción. Intenta más tarde.", 500);
}
