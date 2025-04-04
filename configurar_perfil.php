<?php
session_start(); // Iniciar sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar_sesion.php"); // Redirigir a iniciar_sesion.php si no ha iniciado sesión
    exit;
}

include '../tutorial/conexion.php'; // Incluye el archivo de conexión

// Verificar la conexión
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_errno);
}

// Obtener los datos del usuario
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT * FROM usuarios WHERE id = '$usuario_id'";
$resultado = mysqli_query($conexion, $query);
$usuario_datos = mysqli_fetch_assoc($resultado);

// Cerrar la conexión
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Perfil - Cuteland</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="profile-page">
    <a href="index.php" class="home-logo">
        <img src="Cuteland store titulo.png" alt="Logotipo de Cuteland">
    </a>
    <div class="profile-container">
        <h2>Configurar Perfil</h2>
        <form action="actualizar_contrasena.php" method="POST">
            <label for="contrasena_actual">Contraseña Actual:</label>
            <input type="password" id="contrasena_actual" name="contrasena_actual" required>

            <label for="contrasena_nueva">Contraseña Nueva:</label>
            <input type="password" id="contrasena_nueva" name="contrasena_nueva" required>

            <label for="confirmar_contrasena_nueva">Confirmar Contraseña Nueva:</label>
            <input type="password" id="confirmar_contrasena_nueva" name="confirmar_contrasena_nueva" required>

            <button type="submit">Cambiar Contraseña</button>
        </form>
        <a href="perfil.php">Volver al Perfil</a>
    </div>
</body>
</html>