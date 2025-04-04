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
    <title>Perfil - Cuteland</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="profile-page">
    <a href="index.php" class="home-logo">
        <img src="Cuteland store titulo.png" alt="Logotipo de Cuteland">
    </a>
    <div class="profile-container">
        <h2>Perfil de <?php echo $usuario_datos['nombre']; ?></h2>
        <div class="profile-info">
            <p><strong>Nombre:</strong> <?php echo $usuario_datos['nombre']; ?></p>
            <p><strong>Apellido:</strong> <?php echo $usuario_datos['apellido']; ?></p>
            <p><strong>Usuario:</strong> <?php echo $usuario_datos['usuario']; ?></p>
            <p><strong>Correo electrónico:</strong> <?php echo $usuario_datos['correo']; ?></p>
        </div>
        <div class="profile-links">
            <a href="configurar_perfil.php">Configuración</a>
            <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>