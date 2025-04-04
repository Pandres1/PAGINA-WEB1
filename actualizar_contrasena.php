<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

include '../tutorial/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$nueva_contrasena = $_POST['nueva_contrasena'];

// Verificar la contraseña actual
$contrasena_actual = $_POST['contrasena_actual'];

$query_verificar = "SELECT contrasena FROM usuarios WHERE id = '$usuario_id'";
$resultado_verificar = mysqli_query($conexion, $query_verificar);
$usuario_datos = mysqli_fetch_assoc($resultado_verificar);

if (password_verify($contrasena_actual, $usuario_datos['contrasena'])) {
    $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
    $query = "UPDATE usuarios SET contrasena = '$nueva_contrasena_hash' WHERE id = '$usuario_id'";

    if (mysqli_query($conexion, $query)) {
        header("Location: perfil_usuario.php");
    } else {
        echo "Error al actualizar la contraseña: " . mysqli_error($conexion);
    }
} else {
    echo "Contraseña actual incorrecta.";
}

mysqli_close($conexion);
?>