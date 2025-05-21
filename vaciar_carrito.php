<?php
session_start();

// Eliminar la variable de sesión del carrito
unset($_SESSION['carrito']);

// Redirigir al usuario de vuelta a la página del carrito
header("Location: carrito.php?mensaje=carrito_vaciado");
exit();
?>