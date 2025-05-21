<?php
session_start();

// Verificar si el usuario es administrador
// Ahora esperamos $_SESSION['rol']
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.html"); // O la página de inicio de sesión del administrador
    exit;
}

include '../tutorial/conexion.php';

// Obtener todos los productos de la base de datos
$query_productos = "SELECT id, nombre, precio FROM productos";
$resultado_productos = mysqli_query($conexion, $query_productos);

$productos = [];
if (mysqli_num_rows($resultado_productos) > 0) {
    while ($fila = mysqli_fetch_assoc($resultado_productos)) {
        $productos[] = $fila;
    }
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Productos</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function confirmarEliminar(event, nombreProducto) {
            if (confirm("¿Estás seguro de que quieres eliminar el producto '" + nombreProducto + "'?")) {
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        }
    </script>
</head>
<body>
    <div class="admin-container">
        <h1>Administración de Productos</h1>

        <?php
        if (isset($_SESSION['mensaje_exito'])) {
            echo '<p class="success-message">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</p>';
            unset($_SESSION['mensaje_exito']);
        }
        if (isset($_SESSION['mensaje_error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['mensaje_error']) . '</p>';
            unset($_SESSION['mensaje_error']);
        }
        ?>

        <p><a href="crear_producto.php">Crear Nuevo Producto</a></p>

        <?php if (!empty($productos)): ?>
            <table class="product-list">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['id']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></td>
                            <td class="actions">
                                <a href="editar_producto.php?id=<?php echo htmlspecialchars($producto['id']); ?>" class="edit">Editar</a>
                                <a href="eliminar_producto.php?id=<?php echo htmlspecialchars($producto['id']); ?>" class="delete"
                                   onclick="return confirmarEliminar(event, '<?php echo htmlspecialchars($producto['nombre']); ?>')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-products">No hay productos registrados.</p>
        <?php endif; ?>

        <p><a href="index.php">Volver al inicio</a></p>
    </div>
</body>
</html>