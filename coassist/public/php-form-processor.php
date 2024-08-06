<?php
// Configurar CORS de manera más flexible
$allowed_origins = [
    'https://coassist.com.co',
    'https://www.coassist.com.co',
    'http://coassist.com.co',
    'http://www.coassist.com.co',
    'http://localhost:8080',
    'http://172.16.111.6:8080',
    'http://desarrollo.coassist.com.co:8080',
    'http://desarrollo.coassist.com.co'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: https://coassist.com.co");
}

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Verificar si la solicitud es OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

// Configuración de SendGrid
$sendgrid_api_key = 'TU_API_KEY_DE_SENDGRID';
$to_email = 'correo_destino@ejemplo.com';
$from_email = 'remitente@tudominio.com';

// Procesar los datos del formulario
$nombre = $_POST['Nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['Telefono'] ?? '';
$celular = $_POST['celular'] ?? '';
$mensaje = $_POST['Mensaje'] ?? '';

// Preparar el contenido del correo
$subject = 'Nueva solicitud de hoja de vida';
$content = "Nombre: $nombre\n";
$content .= "Email: $email\n";
$content .= "Teléfono: $telefono\n";
$content .= "Celular: $celular\n";
$content .= "Mensaje: $mensaje\n";

// Procesar el archivo adjunto
$attachment = null;
if (isset($_FILES['HV']) && $_FILES['HV']['error'] === UPLOAD_ERR_OK) {
    $file_tmp_name = $_FILES['HV']['tmp_name'];
    $file_name = $_FILES['HV']['name'];
    $file_content = base64_encode(file_get_contents($file_tmp_name));
    $attachment = [
        'content' => $file_content,
        'type' => mime_content_type($file_tmp_name),
        'filename' => $file_name,
        'disposition' => 'attachment'
    ];
}

// Preparar la solicitud para SendGrid
$data = [
    'personalizations' => [
        [
            'to' => [['email' => $to_email]]
        ]
    ],
    'from' => ['email' => $from_email],
    'subject' => $subject,
    'content' => [
        [
            'type' => 'text/plain',
            'value' => $content
        ]
    ]
];

if ($attachment) {
    $data['attachments'] = [$attachment];
}

// Enviar el correo utilizando cURL y la API de SendGrid
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $sendgrid_api_key,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// Manejar la respuesta
if ($http_status == 202) {
    echo json_encode(['success' => true, 'message' => 'Formulario enviado correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al enviar el formulario']);
}
