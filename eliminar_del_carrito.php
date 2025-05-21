<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['producto_id'])) {
        $producto_id = $_POST['producto_id'];

        // Eliminar el producto del carrito en la sesión
        unset($_SESSION['carrito'][$producto_id]);

        // Recalcular el total del carrito
        $total_carrito = 0;
        $carrito_vacio = empty($_SESSION['carrito']);

        if (!$carrito_vacio) {
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

        // Devolver una respuesta JSON indicando éxito y el nuevo total
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'total_carrito' => $total_carrito, 'carrito_vacio' => $carrito_vacio]);
        exit();

    } else {
        // Si falta el producto_id
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Falta producto_id']);
        exit();
    }
} else {
    // Si la petición no es POST
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}
?>