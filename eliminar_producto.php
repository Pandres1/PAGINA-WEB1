<?php
session_start();
include '../tutorial/conexion.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['mensaje_error'] = "Acceso denegado. Por favor, inicia sesión como administrador.";
    header("Location: login.html");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $producto_id = mysqli_real_escape_string($conexion, $_GET['id']); // Sanitizar ID

    // Primero, obtener el nombre de la imagen para eliminarla del servidor
    $query_select_imagen = "SELECT imagen FROM productos WHERE id = ?";
    $stmt_select = mysqli_prepare($conexion, $query_select_imagen);
    if ($stmt_select === false) {
        $_SESSION['mensaje_error'] = "Error al preparar la consulta de imagen: " . mysqli_error($conexion);
        header("Location: admin_productos.php");
        exit;
    }
    mysqli_stmt_bind_param($stmt_select, "i", $producto_id);
    mysqli_stmt_execute($stmt_select);
    $resultado_imagen = mysqli_stmt_get_result($stmt_select);
    $producto_imagen = mysqli_fetch_assoc($resultado_imagen);
    mysqli_stmt_close($stmt_select);

    $nombre_imagen = $producto_imagen['imagen'] ?? null; // Obtener el nombre de la imagen

    // Iniciar una transacción para asegurar la integridad
    mysqli_begin_transaction($conexion);

    try {
        // Eliminar el producto de la tabla 'productos'
        // Esto automáticamente eliminará los detalles de pedido si 'ON DELETE CASCADE' está configurado en la DB
        $query_delete_producto = "DELETE FROM productos WHERE id = ?";
        $stmt_delete = mysqli_prepare($conexion, $query_delete_producto);
        if ($stmt_delete === false) {
            throw new Exception("Error al preparar la consulta de eliminación de producto: " . mysqli_error($conexion));
        }
        mysqli_stmt_bind_param($stmt_delete, "i", $producto_id);

        if (!mysqli_stmt_execute($stmt_delete)) {
            throw new Exception("Error al eliminar el producto: " . mysqli_error($conexion));
        }
        mysqli_stmt_close($stmt_delete);

        // Si la eliminación en la DB fue exitosa, intentar eliminar la imagen del servidor
        if ($nombre_imagen && $nombre_imagen !== 'sin_imagen.png') { // No eliminar una imagen por defecto
            $ruta_imagen = "uploads/" . $nombre_imagen;
            if (file_exists($ruta_imagen)) {
                if (!unlink($ruta_imagen)) {
                    // Si falla la eliminación del archivo, registrarlo pero no abortar la transacción de la DB
                    error_log("No se pudo eliminar el archivo de imagen: " . $ruta_imagen);
                }
            }
        }

        mysqli_commit($conexion); // Confirmar la transacción
        $_SESSION['mensaje_exito'] = "Producto y su imagen eliminados correctamente.";

    } catch (Exception $e) {
        mysqli_rollback($conexion); // Revertir la transacción si algo sale mal
        $_SESSION['mensaje_error'] = "Error al eliminar el producto: " . $e->getMessage();
        error_log("Error en eliminar_producto.php: " . $e->getMessage()); // Registrar el error para depuración
    }

} else {
    $_SESSION['mensaje_error'] = "ID de producto inválido.";
}

mysqli_close($conexion);
header("Location: admin_productos.php");
exit;
?>