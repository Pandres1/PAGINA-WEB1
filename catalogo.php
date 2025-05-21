<?php
session_start();
include '../tutorial/conexion.php';

// Obtener todas las categorías de productos
$query_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$resultado_categorias = mysqli_query($conexion, $query_categorias);
$categorias = mysqli_fetch_all($resultado_categorias, MYSQLI_ASSOC);

// Determinar la categoría a filtrar (si existe)
$categoria_filtrar_id = $_GET['categoria'] ?? null;

// Construir la consulta SQL para obtener los productos activos (con filtro de categoría si es necesario)
$query_productos = "SELECT p.id, p.nombre, p.precio, p.imagen, c.nombre AS nombre_categoria
                    FROM productos p
                    INNER JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.activo = 1";
if ($categoria_filtrar_id) {
    $categoria_filtrar_id_segura = mysqli_real_escape_string($conexion, $categoria_filtrar_id);
    $query_productos .= " AND p.categoria_id = '$categoria_filtrar_id_segura'";
}
$resultado_productos = mysqli_query($conexion, $query_productos);

// Verificar si hay productos activos
if (mysqli_num_rows($resultado_productos) > 0) {
    $productos_activos = mysqli_fetch_all($resultado_productos, MYSQLI_ASSOC);
} else {
    $productos_activos = [];
}

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
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="catalogo.css">
</head>
<body>
    <div class="logo-catalogo">
        <a href="index.php">
            <img src="logo.jpg" alt="Logotipo de tu empresa">
        </a>
    </div>
    <div class="carrito-indicador">
        <a href="carrito.php">
            <img src="carrito.png" alt="Carrito">
            <span class="carrito-cantidad">(<?php echo htmlspecialchars($total_items_carrito); ?>)</span>
        </a>
    </div>
    <div class="catalogo-container">
        <div class="filtros-container">
            <h3>Categorías</h3>
            <ul>
                <li><a href="catalogo.php">Todas</a></li>
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <li><a href="catalogo.php?categoria=<?php echo htmlspecialchars($categoria['id']); ?>"><?php echo htmlspecialchars(ucfirst($categoria['nombre'])); ?></a></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No hay categorías disponibles.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="productos-grid">
            <?php if (!empty($productos_activos)): ?>
                <?php foreach ($productos_activos as $producto): ?>
                    <div class="producto-item">
                        <div class="imagen-contenedor">
                            <?php if (!empty($producto['imagen'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <?php else: ?>
                                <img src="img/sin_imagen.png" alt="Sin imagen">
                            <?php endif; ?>
                            <a href="detalle_producto.php?id=<?php echo htmlspecialchars($producto['id']); ?>"></a>
                        </div>
                        <h3><a href="detalle_producto.php?id=<?php echo htmlspecialchars($producto['id']); ?>"><?php echo htmlspecialchars($producto['nombre']); ?></a></h3>
                        <p class="precio">$<?php echo htmlspecialchars(number_format($producto['precio'], 2)); ?></p>

                        <form action="agregar_al_carrito.php" method="post">
                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto['id']); ?>">
                            <input type="hidden" name="cantidad" value="1">
                            <button type="submit" class="boton-añadir-carrito">
                                <img src="carrito.png" alt="Añadir al Carrito" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;">
                                Añadir
                            </button>
                        </form>

                        <a href="detalle_producto.php?id=<?php echo htmlspecialchars($producto['id']); ?>" class="ver-detalle">Ver Detalle</a>
                        <p class="categoria-producto">Categoría: <?php echo htmlspecialchars(ucfirst($producto['nombre_categoria'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-productos">No hay productos disponibles en esta categoría.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>