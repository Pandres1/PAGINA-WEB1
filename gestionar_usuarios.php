<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
include '../tutorial/conexion.php'; // Asegúrate de que esta ruta sea correcta

// Verificar si el usuario ha iniciado sesión y tiene permisos de administrador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    $_SESSION['mensaje_error'] = "Acceso denegado. Por favor, inicia sesión como administrador.";
    header("Location: login.html");
    exit;
}

// Opcional: Obtener todos los usuarios para mostrar una lista
$usuarios = [];
$query_usuarios = "SELECT id, usuario, tipo_nivel FROM usuarios ORDER BY usuario ASC";
$resultado_usuarios = mysqli_query($conexion, $query_usuarios);
if ($resultado_usuarios) {
    while ($fila = mysqli_fetch_assoc($resultado_usuarios)) {
        $usuarios[] = $fila;
    }
} else {
    // Si hay un error en la consulta, asegúrate de que se capture y se muestre si la depuración está activa
    $_SESSION['mensaje_error'] = "Error al cargar la lista de usuarios: " . mysqli_error($conexion);
}
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Roles de Usuario</title>
    <link rel="stylesheet" href="styles.css"> </head>
<body>
    <div class="admin-container">
        <h1>Gestionar Roles de Usuario</h1>

        <?php
        // Mostrar mensajes de error o éxito
        if (isset($_SESSION['mensaje_error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['mensaje_error']) . '</p>';
            unset($_SESSION['mensaje_error']);
        }
        if (isset($_SESSION['mensaje_exito'])) {
            echo '<p class="success-message">' . htmlspecialchars($_SESSION['mensaje_exito']) . '</p>';
            unset($_SESSION['mensaje_exito']);
        }
        ?>

        <h2>Cambiar Rol de Usuario</h2>
        <form action="procesar_cambio_rol.php" method="post">
            <div>
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required
                       placeholder="Escribe el nombre de usuario">
            </div>
            <div>
                <label for="nuevo_rol">Nuevo Rol:</label>
                <select id="nuevo_rol" name="nuevo_rol" required>
                    <option value="">Seleccionar Rol</option>
                    <option value="admin">Administrador</option>
                    <option value="usuario">Usuario Normal</option>
                    </select>
            </div>
            <button type="submit">Guardar Rol</button>
        </form>

        <?php if (!empty($usuarios)): ?>
            <h2 style="margin-top: 40px;">Lista de Usuarios</h2>
            <table class="product-list"> <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                            <td><?php echo htmlspecialchars($user['tipo_nivel']); ?></td>
                            <td class="actions">
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-products">No hay usuarios registrados o un error impidió cargarlos.</p>
        <?php endif; ?>

        <p><a href="index.php">Volver al inicio</a></p>
    </div>
</body>
</html>