<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
        $producto_id = $_POST['producto_id'];
        $cantidad = intval($_POST['cantidad']);

        if ($cantidad > 0) {
            $_SESSION['carrito'][$producto_id] = $cantidad;
        } else {
            unset($_SESSION['carrito'][$producto_id]);
        }

        $total_carrito = 0;
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            include '../tutorial/conexion.php';
            foreach ($_SESSION['carrito'] as $id => $cant) {
                $query = "SELECT precio FROM productos WHERE id = '$id'";
                $resultado = mysqli_query($conexion, $query);
                if ($producto = mysqli_fetch_assoc($resultado)) {
                    $total_carrito += $producto['precio'] * $cant;
                }
            }
            mysqli_close($conexion);
        }

        header('Content-Type: application/json');
        echo json_encode(['total_carrito' => $total_carrito]);
        exit();

    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan producto_id o cantidad']);
        exit();
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit();
}
?>