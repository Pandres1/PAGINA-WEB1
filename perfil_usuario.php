<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

// Incluir la conexión a la base de datos
include '../tutorial/conexion.php';

// Obtener la información del usuario
$usuario_id = $_SESSION['usuario_id'];
// Usa prepared statement por seguridad, incluso para SELECT simples
$query = "SELECT usuario, tipo_nivel FROM usuarios WHERE id = ?";
$stmt = mysqli_prepare($conexion, $query);
if ($stmt === false) {
    // Manejo de error de preparación de consulta
    error_log("Error al preparar la consulta en perfil_usuario.php: " . mysqli_error($conexion));
    // Podrías redirigir a una página de error o mostrar un mensaje
    header("Location: error.html");
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario_datos = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

mysqli_close($conexion);

if (!$usuario_datos) {
    // Si por alguna razón no se encuentran los datos del usuario, destruir sesión y redirigir
    session_destroy();
    header("Location: login.html");
    exit;
}

// ==================================================================================================
// --- ¡IMPORTANTE! ---
// Asigna el 'tipo_nivel' de la base de datos a la variable de sesión 'rol'
// para que todos los demás scripts lo puedan usar de forma consistente.
// Este fue el cambio crucial para que tu admin_productos.php y otros funcionaran.
$_SESSION['rol'] = $usuario_datos['tipo_nivel'];
// ==================================================================================================
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
    <div class="pagina-perfil">
        <h1>Perfil de Usuario</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($usuario_datos['usuario']); ?></p>

        <?php if ($usuario_datos['tipo_nivel'] === 'admin'): // Sigue usando $usuario_datos para mostrar, pero la sesión ya tiene 'rol' ?>
            <div class="user-admin-panel">
                <h2>Panel de Administración</h2>
                <p><a href="admin_productos.php">Gestionar Productos</a></p>
                <p><a href="admin_pedidos.php" class="admin-pedidos-link">Gestionar Pedidos</a></p>
                <p><a href="gestionar_usuarios.php">Gestionar Roles de Usuario</a></p> </div>
        <?php endif; ?>
        
        <p><a href="configurar_usuario.php">Configurar Usuario</a></p>
        <p><a href="cerrar_sesion.php">Cerrar Sesión</a></p>
    </div>
</body>
</html>