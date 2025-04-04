<?php
session_start(); // Iniciar sesión

session_destroy(); // Destruir la sesión

header("Location: iniciar_sesion.php"); // Redirigir a iniciar_sesion.php
exit;
?>