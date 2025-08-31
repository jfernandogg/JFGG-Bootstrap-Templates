<?php
session_start();

// --- CONFIGURACIÓN ---
$email_destinatario = "tu-correo@tudominio.com"; // Reemplaza con tu correo
$asunto_email = "Nuevo Pedido desde Marikaditas Literarias";
$limite_tiempo_envio = 30; // Segundos mínimos entre pedidos para un mismo usuario

// --- CABECERAS DE RESPUESTA ---
header('Content-Type: application/json; charset=utf-8');

// --- FUNCIÓN PARA ENVIAR RESPUESTAS DE ERROR ---
function enviar_error($mensaje, $codigo_http = 400) {
    http_response_code($codigo_http);
    echo json_encode(['status' => 'error', 'message' => $mensaje]);
    exit;
}

// --- CAPA 1: LIMITACIÓN DE TASA (Rate Limiting) ---
if (isset($_SESSION['last_submission_time']) && (time() - $_SESSION['last_submission_time']) < $limite_tiempo_envio) {
    enviar_error("Por favor, espera un momento antes de enviar otro pedido.", 429); // 429 Too Many Requests
}

// --- CAPA 2: VALIDACIÓN DE MÉTODO Y DATOS DE ENTRADA ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enviar_error("Método no permitido.", 405); // 405 Method Not Allowed
}

$json_str = file_get_contents('php://input');
$datos_cliente = json_decode($json_str, true);

if ($datos_cliente === null || !isset($datos_cliente['cart']) || !is_array($datos_cliente['cart']) || empty($datos_cliente['cart'])) {
    enviar_error("Datos del carrito inválidos o vacíos.");
}

$carrito_cliente = $datos_cliente['cart'];

// --- CAPA 3: VALIDACIÓN DEL LADO DEL SERVIDOR (Precios y Productos) ---
$productos_json_str = file_get_contents('js/productos.json');
if ($productos_json_str === false) {
    enviar_error("Error interno del servidor: no se pudieron cargar los productos.", 500);
}

$productos_servidor_lista = json_decode($productos_json_str, true);
if ($productos_servidor_lista === null) {
    enviar_error("Error interno del servidor: formato de productos inválido.", 500);
}

// Convertir la lista de productos a un mapa para búsqueda rápida por ID
$productos_servidor_mapa = [];
foreach ($productos_servidor_lista as $producto) {
    $productos_servidor_mapa[$producto['id']] = $producto;
}

$carrito_validado = [];
$total_general = 0;

foreach ($carrito_cliente as $item_cliente) {
    // Validar que el item tenga los campos necesarios y que el ID exista en nuestro catálogo
    if (!isset($item_cliente['id'], $item_cliente['cantidad']) || !isset($productos_servidor_mapa[$item_cliente['id']])) {
        continue; // Ignorar productos inválidos o que no existen
    }

    $id_producto = $item_cliente['id'];
    $producto_servidor = $productos_servidor_mapa[$id_producto];
    
    // Usar siempre el precio del servidor, nunca el del cliente
    $precio_servidor = (float)$producto_servidor['precio'];
    $cantidad = (int)$item_cliente['cantidad'];

    if ($cantidad <= 0) {
        continue; // Ignorar cantidades inválidas
    }

    $subtotal = $precio_servidor * $cantidad;
    $total_general += $subtotal;

    $carrito_validado[] = [
        'nombre' => $producto_servidor['titulo'], // Usar el nombre del servidor
        'cantidad' => $cantidad,
        'precio_unitario' => $precio_servidor,
        'subtotal' => $subtotal
    ];
}

if (empty($carrito_validado)) {
    enviar_error("El carrito está vacío o contiene productos no válidos.");
}

// --- CAPA 4: CONSTRUCCIÓN Y SANEAMIENTO DEL EMAIL ---
$cuerpo_email = "<h1>Nuevo Pedido Recibido</h1>";
$cuerpo_email .= "<p>Se ha realizado un nuevo pedido a través del sitio web:</p>";
$cuerpo_email .= "<table border='1' cellpadding='10' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
$cuerpo_email .= "<thead><tr style='background-color:#f2f2f2;'><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr></thead>";
$cuerpo_email .= "<tbody>";

foreach ($carrito_validado as $item) {
    $cuerpo_email .= "<tr>";
    // Saneamiento con htmlspecialchars para prevenir XSS
    $cuerpo_email .= "<td>" . htmlspecialchars($item['nombre']) . "</td>";
    $cuerpo_email .= "<td>" . $item['cantidad'] . "</td>";
    $cuerpo_email .= "<td>$" . number_format($item['precio_unitario'], 2) . "</td>";
    $cuerpo_email .= "<td>$" . number_format($item['subtotal'], 2) . "</td>";
    $cuerpo_email .= "</tr>";
}

$cuerpo_email .= "</tbody><tfoot><tr><td colspan='3' style='text-align:right; font-weight:bold;'>Total General:</td><td style='font-weight:bold;'>$" . number_format($total_general, 2) . "</td></tr></tfoot>";
$cuerpo_email .= "</table>";

// Cabeceras del correo (Prevención de Inyección de Cabeceras)
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: <pedidos@marikaditasliterarias.com>' . "\r\n"; // Usar un remitente fijo

// --- ENVÍO Y RESPUESTA FINAL ---
if (mail($email_destinatario, $asunto_email, $cuerpo_email, $headers)) {
    $_SESSION['last_submission_time'] = time(); // Actualizar el tiempo del último envío
    echo json_encode(['status' => 'success', 'message' => 'Pedido enviado con éxito.']);
} else {
    enviar_error("El servidor no pudo enviar el correo. Por favor, contacta al administrador.", 500);
}

?>