<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

include '../tutorial/conexion.php';

// Verificar la conexión
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_errno);
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar la base de datos para obtener el tipo_nivel del usuario
$query_rol = "SELECT tipo_nivel FROM usuarios WHERE id = '$usuario_id'";
$resultado_rol = mysqli_query($conexion, $query_rol);
$fila_rol = mysqli_fetch_assoc($resultado_rol);

// Verificar si se encontró el usuario y si su tipo_nivel es 'admin'
if (!$fila_rol || $fila_rol['tipo_nivel'] !== 'admin') {
    header("Location: login.html"); // Redirigir si no es administrador
    exit;
}

if (isset($_POST['accion']) && isset($_POST['pedido_id']) && is_numeric($_POST['pedido_id'])) {
    $accion = $_POST['accion'];
    $pedido_id = $_POST['pedido_id'];

    $nuevo_estado = '';
    if ($accion == 'completar') {
        $nuevo_estado = 'Completado';
    } elseif ($accion == 'cancelar') {
        $nuevo_estado = 'Cancelado';
    }

    if ($nuevo_estado != '') {
        $query_actualizar_pedido = "UPDATE pedidos SET estado = '$nuevo_estado' WHERE id = '$pedido_id'";
        if (mysqli_query($conexion, $query_actualizar_pedido)) {
            header("Location: admin_pedidos.php?mensaje=estado_actualizado"); // Añadir mensaje de éxito
            exit;
        } else {
            echo "Error al actualizar el estado del pedido: " . mysqli_error($conexion);
        }
    } else {
        echo "Acción no válida.";
    }
} else {
    echo "Solicitud no válida.";
}

mysqli_close($conexion);
?>