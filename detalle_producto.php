<?php
session_start();
include '../tutorial/conexion.php';

// Verificar si se recibió el ID del producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: catalogo.php"); // Redirigir si no hay ID válido
    exit;
}

$producto_id = mysqli_real_escape_string($conexion, $_GET['id']);

// Consulta para obtener los detalles del producto
$query_detalle = "SELECT p.*, c.nombre AS nombre_categoria
                  FROM productos p
                  INNER JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.id = '$producto_id' AND p.activo = 1";
$resultado_detalle = mysqli_query($conexion, $query_detalle);

if (mysqli_num_rows($resultado_detalle) !== 1) {
    header("Location: catalogo.php"); // Redirigir si el producto no existe o no está activo
    exit;
}

$producto = mysqli_fetch_assoc($resultado_detalle);

// Calcular la cantidad total de items en el carrito
$total_items_carrito = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $cantidad) {
        $total_items_carrito += $cantidad;
    }
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - Cuteland</title>
    <link rel="stylesheet" href="detalle_producto.css">
</head>
<body>
    <div class="carrito-indicador">
        <a href="carrito.php">
            <img src="carrito.png" alt="Carrito">
            <span class="carrito-cantidad">(<?php echo htmlspecialchars($total_items_carrito); ?>)</span>
        </a>
    </div>
    <div class="detalle-container">
        <div class="detalle-imagen">
            <?php if (!empty($producto['imagen'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <?php else: ?>
                <img src="img/sin_imagen.png" alt="Sin imagen">
            <?php endif; ?>
        </div>
        <div class="detalle-info">
            <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
            <p class="categoria">Categoría: <?php echo htmlspecialchars(ucfirst($producto['nombre_categoria'])); ?></p>
            <p class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
            <p class="precio">Precio: $<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></p>
            <form action="agregar_al_carrito.php" method="post">
                <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto['id']); ?>">
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" value="1" min="1">
                <button type="submit">Añadir al Carrito</button>
            </form>
            <a href="catalogo.php" class="volver-catalogo">Volver al Catálogo</a>
        </div>
    </div>
</body>
</html>