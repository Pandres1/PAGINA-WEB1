<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

include '../tutorial/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT usuario, correo FROM usuarios WHERE id = '$usuario_id'";
$resultado = mysqli_query($conexion, $query);
$usuario_datos = mysqli_fetch_assoc($resultado);

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="profile-logo">
        <img src="logo texto.png" alt="Logo">
    </div>
    <div class="container perfil-container">
        <h1>Perfil de Usuario</h1>
        <p>Usuario: <?php echo $usuario_datos['usuario']; ?></p>
        <p>Correo: <?php echo $usuario_datos['correo']; ?></p>
        <a href="index.php">Volver a la página principal</a>
        <br><br>
        <a href="cerrar_sesion.php">Cerrar sesión</a>
    </div>
</body>
</html>