<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

include '../tutorial/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT usuario, contrasena FROM usuarios WHERE id = '$usuario_id'";
$resultado = mysqli_query($conexion, $query);
$usuario_datos = mysqli_fetch_assoc($resultado);

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="profile-logo">
        <a href="index.php">
            <img src="logo.jpg" alt="Logo">
        </a>
    </div>
    <div class="container configuracion-container">
        <h1>Configuraciones</h1>

        <h2>Actualizar Usuario</h2>
        <form action="actualizar_usuario.php" method="POST">
            <label for="contrasena_actual">Contraseña Actual:</label>
            <input type="password" id="contrasena_actual" name="contrasena_actual">
            <label for="nuevo_usuario">Nuevo Usuario:</label>
            <input type="text" id="nuevo_usuario" name="nuevo_usuario">
            <button type="submit">Actualizar Usuario</button>
        </form>

        <h2>Actualizar Contraseña</h2>
        <form action="actualizar_contrasena.php" method="POST">
            <label for="contrasena_actual_password">Contraseña Actual:</label>
            <input type="password" id="contrasena_actual_password" name="contrasena_actual" required>
            <label for="nueva_contrasena">Nueva Contraseña:</label>
            <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
            <button type="submit">Actualizar Contraseña</button>
        </form>

        <a href="index.php">Volver a la página principal</a>
    </div>
</body>
</html>