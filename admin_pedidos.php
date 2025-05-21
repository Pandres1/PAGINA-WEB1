<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

// Incluir la conexión a la base de datos
include '../tutorial/conexion.php';

// Verificar la conexión
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_errno);
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar la base de datos para obtener el tipo_nivel del usuario
$query_rol = "SELECT tipo_nivel FROM usuarios WHERE id = '$usuario_id'";
$resultado_rol = mysqli_query($conexion, $query_rol);
$fila_rol = mysqli_fetch_assoc($resultado_rol);

// Verificar si se encontró el usuario y si su tipo_nivel es 'admin'
if (!$fila_rol || $fila_rol['tipo_nivel'] !== 'admin') {
    header("Location: login.html"); // Redirigir si no es administrador
    exit;
}

// Consulta para obtener todos los pedidos, ordenados por fecha descendente
$query_todos_pedidos = "SELECT p.id, p.numero_factura, p.fecha_pedido, p.total, p.estado, u.nombre AS nombre_cliente, u.apellido AS apellido_cliente
                            FROM pedidos p
                            INNER JOIN usuarios u ON p.usuario_id = u.id
                            ORDER BY p.fecha_pedido DESC";
$resultado_todos_pedidos = mysqli_query($conexion, $query_todos_pedidos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Pedidos - Cuteland</title>
    <link rel="stylesheet" href="admin_pedidos.css">
    <style>
        .admin-orders-table td a {
            color: #007bff;
            text-decoration: none;
        }

        .admin-orders-table td a:hover {
            text-decoration: underline;
        }

        .mensaje-exito {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Administración de Pedidos</h2>
        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'estado_actualizado') : ?>
            <p class="mensaje-exito">El estado del pedido ha sido actualizado.</p>
        <?php endif; ?>
        <?php if (mysqli_num_rows($resultado_todos_pedidos) > 0) : ?>
            <table class="admin-orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número de Factura</th>
                        <th>Fecha del Pedido</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = mysqli_fetch_assoc($resultado_todos_pedidos)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['numero_factura']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($pedido['fecha_pedido']))); ?></td>
                            <td><?php echo htmlspecialchars($pedido['nombre_cliente']) . ' ' . htmlspecialchars($pedido['apellido_cliente']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($pedido['total'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                            <td>
                                <form method="post" action="procesar_pedido_admin.php">
                                    <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($pedido['id']); ?>">
                                    <?php if ($pedido['estado'] != 'Completado' && $pedido['estado'] != 'Cancelado') : ?>
                                        <button type="submit" name="accion" value="completar">Completar</button>
                                        <button type="submit" name="accion" value="cancelar">Cancelar</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td><a href="detalle_pedido_admin.php?id=<?php echo htmlspecialchars($pedido['id']); ?>">Ver Detalles</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No hay pedidos registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
mysqli_close($conexion);
?>