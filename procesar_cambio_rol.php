<?php
session_start();
include '../tutorial/conexion.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el usuario ha iniciado sesión y tiene permisos de administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['mensaje_error'] = "Acceso denegado. Por favor, inicia sesión como administrador.";
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = filter_input(INPUT_POST, 'nombre_usuario', FILTER_SANITIZE_STRING);
    $nuevo_rol = filter_input(INPUT_POST, 'nuevo_rol', FILTER_SANITIZE_STRING);

    // Validar los datos recibidos
    if (empty($nombre_usuario) || empty($nuevo_rol) || !in_array($nuevo_rol, ['admin', 'usuario'])) {
        $_SESSION['mensaje_error'] = "Datos de usuario o rol inválidos.";
        header("Location: gestionar_usuarios.php");
        exit;
    }

    // Preparar la consulta para actualizar el rol del usuario
    $query = "UPDATE usuarios SET tipo_nivel = ? WHERE usuario = ?";
    $stmt = mysqli_prepare($conexion, $query);

    if ($stmt === false) {
        $_SESSION['mensaje_error'] = "Error al preparar la consulta: " . mysqli_error($conexion);
        header("Location: gestionar_usuarios.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ss", $nuevo_rol, $nombre_usuario);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['mensaje_exito'] = "Rol del usuario '" . htmlspecialchars($nombre_usuario) . "' actualizado a '" . htmlspecialchars($nuevo_rol) . "' correctamente.";

           
            if ($nombre_usuario === $_SESSION['nombre_usuario'] && $nuevo_rol !== $_SESSION['rol']) {
                $_SESSION['mensaje_exito'] .= " (Tu propio rol ha sido cambiado. Por favor, cierra sesión y vuelve a iniciar para que los cambios surtan efecto en tu sesión actual).";
            }

        } else {
            $_SESSION['mensaje_error'] = "No se encontró el usuario '" . htmlspecialchars($nombre_usuario) . "' o el rol ya era el mismo.";
        }
    } else {
        $_SESSION['mensaje_error'] = "Error al actualizar el rol: " . mysqli_error($conexion);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    header("Location: gestionar_usuarios.php");
    exit;

} else {
    // Si no se accedió por POST, redirigir
    $_SESSION['mensaje_error'] = "Acceso no permitido.";
    header("Location: gestionar_usuarios.php");
    exit;
}
?>