<?php
session_start();
include '../tutorial/conexion.php'; 



// Verificar si el usuario ha iniciado sesión y tiene permisos de administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['mensaje_error'] = "Acceso denegado. Por favor, inicia sesión como administrador.";
    header("Location: login.html"); // O la página de acceso denegado
    exit;
}

// Verificar que los datos se enviaron por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y sanitizar los datos del formulario
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Si la descripción está vacía, asegúrate de que sea una cadena vacía
    if ($descripcion === false || $descripcion === null) {
        $descripcion = '';
    }


    // Validaciones básicas
    if (!$id || $nombre === false || $precio === false || $categoria_id === false || $stock === false) {
        $_SESSION['mensaje_error'] = "Error: Faltan datos esenciales del producto o son inválidos.";
        header("Location: admin_productos.php"); // Redirigir de vuelta a la lista de productos
        exit;
    }

    // Ruta donde se guardarán las imágenes
    $target_dir = "uploads/";
    $imagen_actual = filter_input(INPUT_POST, 'imagen_actual', FILTER_SANITIZE_STRING) ?? '';
    $nombre_imagen = $imagen_actual; // Por defecto, mantiene la imagen actual


    // 2. Manejo de la subida de la nueva imagen
    if (isset($_FILES["nueva_imagen"]) && $_FILES["nueva_imagen"]["error"] == UPLOAD_ERR_OK) {
        $target_file = $target_dir . basename($_FILES["nueva_imagen"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $_SESSION['mensaje_error'] = "Tipo de archivo de imagen no permitido. Solo JPG, JPEG, PNG y GIF.";
            header("Location: admin_productos.php");
            exit;
        }

        $check = getimagesize($_FILES["nueva_imagen"]["tmp_name"]);
        if($check === false) {
            $_SESSION['mensaje_error'] = "El archivo subido no es una imagen válida o está corrupto.";
            header("Location: admin_productos.php");
            exit;
        }

        $filename = pathinfo($_FILES["nueva_imagen"]["name"], PATHINFO_FILENAME);
        $unique_filename = $filename . '_' . time() . '.' . $imageFileType;
        $target_file = $target_dir . $unique_filename;

        if (move_uploaded_file($_FILES["nueva_imagen"]["tmp_name"], $target_file)) {
            if (!empty($imagen_actual) && file_exists($target_dir . $imagen_actual) && $imagen_actual !== $unique_filename && $imagen_actual !== 'sin_imagen.png') {
                unlink($target_dir . $imagen_actual);
                // echo "<p style='color: green;'>IMAGEN ANTERIOR ELIMINADA: " . htmlspecialchars($imagen_actual) . "</p>"; // Depuración
            }
            $nombre_imagen = $unique_filename;
            // echo "<p style='color: green;'>NUEVA IMAGEN SUBIDA EXITOSAMENTE: " . htmlspecialchars($nombre_imagen) . "</p>"; // Depuración
        } else {
            $_SESSION['mensaje_error'] = "Error al subir la nueva imagen. Verifica permisos de la carpeta 'uploads/'.";
            header("Location: admin_productos.php");
            exit;
        }
    }

    // 3. Preparar la consulta SQL para actualizar el producto
    $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria_id = ?, imagen = ?, activo = ?, stock = ? WHERE id = ?");

    if ($stmt === false) {
        $_SESSION['mensaje_error'] = "Error al preparar la consulta: " . $conexion->error;
        header("Location: admin_productos.php");
        exit;
    }

    $stmt->bind_param("ssdisiii", $nombre, $descripcion, $precio, $categoria_id, $nombre_imagen, $activo, $stock, $id);

    // 4. Ejecutar la consulta
    // echo "<h2>Ejecutando Actualización de Base de Datos:</h2>"; // Depuración
    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Producto actualizado exitosamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al actualizar el producto: " . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conexion->close();

    // 5. Redirigir de vuelta a la página de administración de productos
    header("Location: admin_productos.php");
    exit;

} else {
    // Si no se accedió por POST, redirigir
    $_SESSION['mensaje_error'] = "Acceso no permitido.";
    header("Location: admin_productos.php");
    exit;
}
?>