<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

include '../tutorial/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$nuevo_usuario = $_POST['nuevo_usuario'];
$contrasena_actual = $_POST['contrasena_actual'];

// Verificar la contraseña actual (comparación directa)
$query_verificar = "SELECT contrasena FROM usuarios WHERE id = '$usuario_id'";
$resultado_verificar = mysqli_query($conexion, $query_verificar);
$usuario_datos = mysqli_fetch_assoc($resultado_verificar);

if ($usuario_datos && $contrasena_actual === $usuario_datos['contrasena']) {
    // Verificar si el nuevo nombre de usuario ya existe
    $query_usuario_existe = "SELECT id FROM usuarios WHERE usuario = '$nuevo_usuario' AND id != '$usuario_id'";
    $resultado_usuario_existe = mysqli_query($conexion, $query_usuario_existe);

    if (mysqli_num_rows($resultado_usuario_existe) > 0) {
        echo "<script>alert('El nuevo nombre de usuario ya está en uso.'); window.location = 'configurar_usuario.php';</script>";
    } else {
        $query_actualizar = "UPDATE usuarios SET usuario = '$nuevo_usuario' WHERE id = '$usuario_id'";

        if (mysqli_query($conexion, $query_actualizar)) {
            $_SESSION['usuario_nombre'] = $nuevo_usuario; // Actualizar la variable de sesión
            echo "<script>alert('Nombre de usuario actualizado exitosamente.'); window.location = 'perfil_usuario.php';</script>";
        } else {
            echo "Error al actualizar el usuario: " . mysqli_error($conexion);
            echo "<script>alert('Error al actualizar el usuario.'); window.location = 'configurar_usuario.php';</script>";
        }
    }
} else {
    echo "<script>alert('Contraseña actual incorrecta.'); window.location = 'configurar_usuario.php';</script>";
}

mysqli_close($conexion);
?>