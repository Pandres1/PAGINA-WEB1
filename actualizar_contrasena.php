<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

include '../tutorial/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$nueva_contrasena = $_POST['nueva_contrasena'];
$contrasena_actual = $_POST['contrasena_actual'];

// Obtener la contraseña actual de la base de datos
$query_verificar = "SELECT contrasena FROM usuarios WHERE id = '$usuario_id'";
$resultado_verificar = mysqli_query($conexion, $query_verificar);
$usuario_datos = mysqli_fetch_assoc($resultado_verificar);

// Verificar la contraseña actual (comparación directa)
if ($usuario_datos && $contrasena_actual === $usuario_datos['contrasena']) {
    // Actualizar la contraseña en la base de datos (sin encriptar la nueva contraseña)
    $query_actualizar = "UPDATE usuarios SET contrasena = '$nueva_contrasena' WHERE id = '$usuario_id'";

    if (mysqli_query($conexion, $query_actualizar)) {
        echo "<script>alert('Contraseña actualizada exitosamente.'); window.location = 'perfil_usuario.php';</script>";
    } else {
        echo "Error al actualizar la contraseña: " . mysqli_error($conexion);
        echo "<script>alert('Error al actualizar la contraseña.'); window.location = 'configurar_usuario.php';</script>";
    }
} else {
    echo "<script>alert('La contraseña actual es incorrecta.'); window.location = 'configurar_usuario.php';</script>";
}

mysqli_close($conexion);
?>