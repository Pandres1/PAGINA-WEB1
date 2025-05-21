<?php
session_start();

// Verificar si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

include '../tutorial/conexion.php';

if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_errno);
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar el rol del administrador
$query_rol = "SELECT tipo_nivel FROM usuarios WHERE id = '$usuario_id'";
$resultado_rol = mysqli_query($conexion, $query_rol);
$fila_rol = mysqli_fetch_assoc($resultado_rol);

if (!$fila_rol || $fila_rol['tipo_nivel'] !== 'admin') {
    header("Location: login.html");
    exit;
}

// Obtener el ID del pedido de la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $pedido_id = $_GET['id'];

    // Obtener la información del pedido
    $query_pedido = "SELECT p.id AS pedido_id, p.numero_factura, p.fecha_pedido, p.total, p.estado,
                            u.nombre AS nombre_cliente, u.apellido AS apellido_cliente,
                            u.correo AS correo_cliente, p.direccion_envio, p.metodo_pago,
                            p.metodo_envio,
                            p.telefono AS telefono_cliente,
                            p.tipo_entrega
                     FROM pedidos p
                     INNER JOIN usuarios u ON p.usuario_id = u.id
                     WHERE p.id = '$pedido_id'";
    $resultado_pedido = mysqli_query($conexion, $query_pedido);
    $pedido = mysqli_fetch_assoc($resultado_pedido);

    // Obtener los detalles del pedido (productos)
    $query_detalle = "SELECT dp.cantidad, dp.precio_unitario, dp.subtotal,
                             pr.nombre AS nombre_producto
                      FROM detalle_pedido dp
                      INNER JOIN productos pr ON dp.producto_id = pr.id
                      WHERE dp.pedido_id = '$pedido_id'";
    $resultado_detalle = mysqli_query($conexion, $query_detalle);
    $detalles_pedido = mysqli_fetch_all($resultado_detalle, MYSQLI_ASSOC);

    if (!$pedido) {
        echo "<p>Pedido no encontrado.</p>";
        exit;
    }

} else {
    echo "<p>ID de pedido inválido.</p>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido <?php echo htmlspecialchars($pedido['numero_factura']); ?></title>
    <link rel="stylesheet" href="detalle_pedido_admin.css">
</head>
<body>
    <div class="details-container">
        <h2>Detalles del Pedido <?php echo htmlspecialchars($pedido['numero_factura']); ?></h2>
        <p><strong>ID del Pedido:</strong> <?php echo htmlspecialchars($pedido['pedido_id']); ?></p>
        <p><strong>Fecha del Pedido:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($pedido['fecha_pedido']))); ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nombre_cliente']) . ' ' . htmlspecialchars($pedido['apellido_cliente']); ?></p>
        <p><strong>Correo del Cliente:</strong> <?php echo htmlspecialchars($pedido['correo_cliente']); ?></p>
        <?php if (!empty($pedido['telefono_cliente'])) : ?>
            <p><strong>Teléfono del Cliente:</strong> <?php echo htmlspecialchars($pedido['telefono_cliente']); ?></p>
        <?php endif; ?>
        <p><strong>Dirección de Envío:</strong> <?php echo htmlspecialchars($pedido['direccion_envio']); ?></p>
        <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($pedido['metodo_pago']); ?></p>
        <?php if (isset($pedido['tipo_entrega'])) : ?>
            <p><strong>Tipo de Entrega:</strong> <?php echo htmlspecialchars($pedido['tipo_entrega']); ?></p>
        <?php endif; ?>
        <p><strong>Método de Envío:</strong> <?php echo htmlspecialchars($pedido['metodo_envio']); ?></p>
        <p><strong>Estado del Pedido:</strong> <?php echo htmlspecialchars($pedido['estado']); ?></p>
        <h3>Productos del Pedido</h3>
        <?php if (!empty($detalles_pedido)) : ?>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles_pedido as $detalle) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                            <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($detalle['precio_unitario'], 2)); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($detalle['subtotal'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Total del Pedido:</strong> $<?php echo htmlspecialchars(number_format($pedido['total'], 2)); ?></p>
        <?php else : ?>
            <p>No hay productos en este pedido.</p>
        <?php endif; ?>
        <p><a href="admin_pedidos.php">Volver a la lista de pedidos</a></p>
    </div>
</body>
</html>

<?php
mysqli_close($conexion);
?>