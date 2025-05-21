<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_nivel'] !== 'admin') {
    header("Location: login.html"); // O la página de inicio de sesión del administrador
    exit;
}

include '../tutorial/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir datos del formulario
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $categoria_id = intval($_POST['categoria_id']);
    $stock = intval($_POST['stock']);
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Manejo de la imagen subida
    $imagen_nombre = $_FILES['imagen']['name'];
    $imagen_temporal = $_FILES['imagen']['tmp_name'];
    $imagen_error = $_FILES['imagen']['error'];
    $imagen_tipo = $_FILES['imagen']['type'];
    $imagen_size = $_FILES['imagen']['size'];

    $ruta_destino_base = 'uploads/'; // Ruta base a tu carpeta de imágenes (CORRECCIÓN)
    $ruta_destino = '';

    // Validar la imagen
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));
    $tamaño_maximo = 2 * 1024 * 1024; // 2MB

    if ($imagen_error === UPLOAD_ERR_OK) {
        if (in_array($extension, $extensiones_permitidas) && $imagen_size <= $tamaño_maximo) {
            // Crear un nombre de archivo único para evitar conflictos
            $nombre_unico_imagen = uniqid('producto_', true) . '.' . $extension;

            // Determinar la subcarpeta basada en la categoría (opcional)
            $query_categoria_nombre = "SELECT nombre FROM categorias WHERE id = '$categoria_id'";
            $resultado_categoria_nombre = mysqli_query($conexion, $query_categoria_nombre);
            if ($fila_categoria = mysqli_fetch_assoc($resultado_categoria_nombre)) {
                $subcarpeta = strtolower(str_replace(' ', '_', $fila_categoria['nombre']));
                if (!is_dir($ruta_destino_base . $subcarpeta)) {
                    mkdir($ruta_destino_base . $subcarpeta, 0755, true);
                }
                $ruta_destino = $subcarpeta . '/' . $nombre_unico_imagen;
                $ruta_completa = $ruta_destino_base . $ruta_destino;
            } else {
                $ruta_destino = $nombre_unico_imagen; // Si no se encuentra la categoría, guardar en la raíz de uploads
                $ruta_completa = $ruta_destino_base . $ruta_destino;
            }

            if (move_uploaded_file($imagen_temporal, $ruta_completa)) {
                // Insertar datos en la base de datos
                $query_insertar = "INSERT INTO productos (nombre, descripcion, precio, imagen, categoria_id, stock, activo) VALUES ('$nombre', '$descripcion', '$precio', '$ruta_destino', '$categoria_id', '$stock', '$activo')";

                if (mysqli_query($conexion, $query_insertar)) {
                    $_SESSION['mensaje'] = 'Producto creado exitosamente.';
                    header("Location: admin_productos.php");
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al guardar el producto en la base de datos: ' . mysqli_error($conexion);
                    header("Location: crear_producto.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Error al subir la imagen.';
                header("Location: crear_producto.php");
                exit;
            }
        } else {
            $_SESSION['error'] = 'Formato de imagen no válido o tamaño demasiado grande (máximo 2MB).';
            header("Location: crear_producto.php");
            exit;
        }
    } else if ($imagen_error !== UPLOAD_ERR_NO_FILE) {
        $_SESSION['error'] = 'Error al subir la imagen: ' . $imagen_error;
        header("Location: crear_producto.php");
        exit;
    } else {
        $ruta_destino = ''; // Si no se subió imagen (aunque es requerido en el formulario)
        // Insertar datos en la base de datos (sin imagen) - Considera si esto es permitido
        $query_insertar = "INSERT INTO productos (nombre, descripcion, precio, imagen, categoria_id, stock, activo) VALUES ('$nombre', '$descripcion', '$precio', '$ruta_destino', '$categoria_id', '$stock', '$activo')";

        if (mysqli_query($conexion, $query_insertar)) {
            $_SESSION['mensaje'] = 'Producto creado exitosamente (sin imagen).';
            header("Location: admin_productos.php");
            exit;
        } else {
            $_SESSION['error'] = 'Error al guardar el producto en la base de datos: ' . mysqli_error($conexion);
            header("Location: crear_producto.php");
            exit;
        }
    }

    mysqli_close($conexion);
} else {
    // Si se intenta acceder al script por GET
    header("Location: crear_producto.php");
    exit;
}
?>