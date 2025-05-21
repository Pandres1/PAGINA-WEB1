<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar_sesion.php");
    exit;
}

include '../tutorial/conexion.php';

// Verificar la conexión
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_errno);
}

// Obtener el ID del pedido de la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $pedido_id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Verificar si el pedido pertenece al usuario logueado
    $query_verificar_pedido = "SELECT id FROM pedidos WHERE id = '$pedido_id' AND usuario_id = '$usuario_id'";
    $resultado_verificar_pedido = mysqli_query($conexion, $query_verificar_pedido);

    if (mysqli_num_rows($resultado_verificar_pedido) == 0) {
        echo "<p>Este pedido no existe o no pertenece a tu cuenta.</p>";
        exit;
    }

    // Obtener la información del pedido
    $query_pedido = "SELECT p.id AS pedido_id, p.numero_factura, p.fecha_pedido, p.total,
                            u.nombre AS nombre_usuario, u.apellido AS apellido_usuario,
                            u.correo AS correo_usuario, p.direccion_envio,
                            p.metodo_pago, p.telefono
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
    <title>Factura del Pedido <?php echo htmlspecialchars($pedido['numero_factura']); ?> - Cuteland</title>
    <link rel="stylesheet" href="factura/factura.css">
</head>
<body>
    <div class="invoice-container">
        <h1>Factura</h1>
        <div class="invoice-header">
            <div class="invoice-details">
                <strong>Número de Factura:</strong> <?php echo htmlspecialchars($pedido['numero_factura']); ?><br>
                <strong>Fecha del Pedido:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($pedido['fecha_pedido']))); ?><br>
            </div>
        </div>

        <div class="customer-info">
            <h3>Información del Cliente</h3>
            <strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['nombre_usuario']) . ' ' . htmlspecialchars($pedido['apellido_usuario']); ?><br>
            <strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($pedido['correo_usuario']); ?><br>
            <strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['telefono']); ?><br>
            <strong>Dirección de Envío:</strong> <?php echo htmlspecialchars($pedido['direccion_envio']); ?><br>
        </div>

        <div class="payment-info">
            <h3>Información de Pago</h3>
            <strong>Método de Pago:</strong> <?php echo htmlspecialchars($pedido['metodo_pago']); ?><br>
        </div>

        <div class="order-items">
            <h3>Detalles del Pedido</h3>
            <table>
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
                <tfoot>
                    <tr>
                        <td colspan="3" class="total-label"><strong>Total:</strong></td>
                        <td class="total-amount">$<?php echo htmlspecialchars(number_format($pedido['total'], 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="invoice-footer">
            <p>Gracias por tu compra en Cuteland.</p>
        </div>
    </div>
</body>
</html>

<?php
mysqli_close($conexion);
?>