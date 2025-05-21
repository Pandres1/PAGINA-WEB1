<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_nivel'] !== 'admin') {
    header("Location: login.html"); // O la página de inicio de sesión del administrador
    exit;
}

include '../tutorial/conexion.php';

// Obtener todas las categorías para el select
$query_categorias = "SELECT id, nombre FROM categorias";
$resultado_categorias = mysqli_query($conexion, $query_categorias);

$categorias = [];
if (mysqli_num_rows($resultado_categorias) > 0) {
    while ($fila = mysqli_fetch_assoc($resultado_categorias)) {
        $categorias[] = $fila;
    }
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Producto</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5spx;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="file"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        .form-group button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Crear Nuevo Producto</h1>

        <form action="procesar_crear_producto.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="imagen">Imagen del Producto:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" required>
            </div>

            <div class="form-group">
                <label for="categoria_id">Categoría:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Seleccionar Categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo htmlspecialchars($categoria['id']); ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="0" required>
            </div>

            <div class="form-group">
                <label for="activo">Activo:</label>
                <input type="checkbox" id="activo" name="activo" value="1" checked> (Marcar para mostrar en el catálogo)
            </div>

            <div class="form-group">
                <button type="submit">Guardar Producto</button>
            </div>
        </form>

        <p><a href="admin_productos.php">Volver a la Lista de Productos</a></p>
    </div>
</body>
</html>