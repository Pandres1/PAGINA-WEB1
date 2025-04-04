<?php
session_start();

include '../tutorial/conexion.php';

// Función para registrar mensajes de depuración
function log_message($message) {
    $log_file = 'debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

if ($conexion->connect_errno) {
    log_message("Error de conexión: " . $conexion->connect_errno);
    die("Error de conexión: " . $conexion->connect_errno);
}

$usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
$contrasena = $_POST['contrasena'];

log_message("Usuario: " . $usuario);
log_message("Contraseña: " . $contrasena);

$query = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) > 0) {
    $usuario_datos = mysqli_fetch_assoc($resultado);
    log_message("Datos del usuario: " . print_r($usuario_datos, true)); // Registra los datos del usuario
    if (password_verify($contrasena, $usuario_datos['contrasena'])) {
        $_SESSION['usuario_id'] = $usuario_datos['id'];
        $_SESSION['usuario_nombre'] = $usuario_datos['nombre'];
        $_SESSION['tipo_nivel'] = $usuario_datos['tipo_nivel'];
        header("Location: index.php");
        exit;
    } else {
        log_message("Contraseña incorrecta.");
        echo "<script>alert('Contraseña incorrecta.'); window.location = 'login.html';</script>";
    }
} else {
    log_message("Usuario incorrecto.");
    echo "<script>alert('Usuario incorrecto.'); window.location = 'login.html';</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>