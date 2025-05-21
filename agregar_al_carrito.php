<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
        $producto_id = $_POST['producto_id'];
        $cantidad = intval($_POST['cantidad']);

        if ($cantidad > 0) {
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }
            $_SESSION['carrito'][$producto_id] = ($SESSION['carrito'][$producto_id] ?? 0) + $cantidad;

            echo "Contenido del carrito antes de la redirección:<pre>";
            var_dump($_SESSION['carrito']);
            echo "</pre>";

            header("Location: catalogo.php?mensaje=producto_añadido");
            exit();
        } else {
            header("Location: catalogo.php?error=cantidad_invalida");
            exit();
        }
    } else {
        header("Location: catalogo.php?error=datos_faltantes");
        exit();
    }
} else {
    header("Location: catalogo.php");
    exit();
}
?>