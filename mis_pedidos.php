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

$usuario_id = $_SESSION['usuario_id'];

// Consulta para obtener los pedidos del usuario actual con el número de factura
$query_pedidos = "SELECT id, numero_factura, fecha_pedido, total, estado
                    FROM pedidos
                    WHERE usuario_id = '$usuario_id'
                    ORDER BY fecha_pedido DESC";
$resultado_pedidos = mysqli_query($conexion, $query_pedidos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Cuteland</title>
    <link rel="stylesheet" href="mispedidos.css">
</head>
<body class="orders-page">
    <div class="orders-container">
        <h2>Mis Pedidos</h2>
        <?php
        if (mysqli_num_rows($resultado_pedidos) > 0) {
            echo "<table class='orders-table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Número de Factura</th>";
            echo "<th>Fecha del Pedido</th>";
            echo "<th>Total</th>";
            echo "<th>Estado</th>";
            echo "<th>Acciones</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($fila_pedido = mysqli_fetch_assoc($resultado_pedidos)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($fila_pedido['numero_factura']) . "</td>";
                echo "<td>" . htmlspecialchars(date('d/m/Y H:i:s', strtotime($fila_pedido['fecha_pedido']))) . "</td>";
                echo "<td>$" . htmlspecialchars(number_format($fila_pedido['total'], 2)) . "</td>";
                echo "<td>" . htmlspecialchars($fila_pedido['estado']) . "</td>";
                echo "<td>";
                // Eliminar o comentar la siguiente línea para quitar "Ver Detalles"
                // echo "<a href='detalle_pedido.php?id=" . htmlspecialchars($fila_pedido['id']) . "'>Ver Detalles</a>";
                echo "<a href='ver_factura.php?id=" . htmlspecialchars($fila_pedido['id']) . "' class='invoice-link'>Ver Factura</a>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p class='no-orders'>No has realizado ningún pedido aún.</p>";
        }
        ?>
    </div>
    <div class="back-link">
        <a href="perfil_usuario.php">Volver a mi perfil</a>
    </div>
</body>
</html>

<?php
mysqli_close($conexion);
?>