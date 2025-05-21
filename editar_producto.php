<?php
session_start();
include '../tutorial/conexion.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el usuario ha iniciado sesión y tiene permisos de administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.html"); // Redirigir a la página de login si no es admin
    exit;
}

// Obtener el ID del producto a editar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $producto_id = mysqli_real_escape_string($conexion, $_GET['id']); // Sanitizar ID

    // Consultar la base de datos para obtener la información del producto
    $query = "SELECT * FROM productos WHERE id = ?";
    $stmt = mysqli_prepare($conexion, $query);

    if ($stmt === false) {
        $_SESSION['mensaje_error'] = "Error al preparar la consulta del producto: " . mysqli_error($conexion);
        header("Location: admin_productos.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $producto_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $producto = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    if (!$producto) {
        $_SESSION['mensaje_error'] = "Producto no encontrado.";
        header("Location: admin_productos.php");
        exit;
    }
} else {
    $_SESSION['mensaje_error'] = "ID de producto inválido o no proporcionado.";
    header("Location: admin_productos.php");
    exit;
}

// Obtener todas las categorías para el select (siempre consulta la DB)
$query_categorias = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
$resultado_categorias = mysqli_query($conexion, $query_categorias);
$categorias = mysqli_fetch_all($resultado_categorias, MYSQLI_ASSOC);

mysqli_close($conexion); // Cerrar la conexión después de todas las consultas
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="styles.css"> </head>
<body>
    <div class="admin-container">
        <h1>Editar Producto</h1>

        <?php
        // Mostrar mensajes de error o éxito
        if (isset($_SESSION['mensaje_error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['mensaje_error']) . '</p>';
            unset($_SESSION['mensaje_error']); // Limpiar el mensaje
        }
        if (isset($_SESSION['mensaje_exito'])) {
            echo '<p class="success-message">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</p>';
            unset($_SESSION['mensaje_exito']); // Limpiar el mensaje
        }
        ?>

        <form action="procesar_editar_producto.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto['id']); ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto['imagen']); ?>"> <div>
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            <div>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div>
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
            </div>
            <div>
                <label for="nueva_imagen">Imagen del Producto:</label>
                <input type="file" id="nueva_imagen" name="nueva_imagen"> <?php if ($producto['imagen']): ?>
                    <p>Imagen actual: <a href="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" target="_blank"><?php echo htmlspecialchars($producto['imagen']); ?></a> (Si subes una nueva imagen, la anterior será reemplazada)</p>
                <?php endif; ?>
            </div>
            <div>
                <label for="categoria_id">Categoría:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Seleccionar Categoría</option>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo htmlspecialchars($categoria['id']); ?>"
                                <?php echo ($producto['categoria_id'] == $categoria['id'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay categorías disponibles</option>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
            </div>
            <div>
                <label for="activo">Activo:</label>
                <input type="checkbox" id="activo" name="activo" value="1" <?php echo $producto['activo'] == 1 ? 'checked' : ''; ?>> (Marcar para mostrar en el catálogo)
            </div>
            <button type="submit">Guardar Cambios</button>
            <p><a href="admin_productos.php">Volver a la Lista de Productos</a></p>
        </form>
    </div>
</body>
</html>