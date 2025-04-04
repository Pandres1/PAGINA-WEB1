<?php
session_start(); // Iniciar sesión

include '../tutorial/conexion.php'; // Incluye el archivo de conexión

// Verificar la conexión
if ($conexion->connect_errno) {
    die("Error de conexión: " . $conexion->connect_errno);
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$usuario = $_POST['usuario'];
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

// Verificar si el correo electrónico ya existe
$query_correo = "SELECT * FROM usuarios WHERE correo = '$correo'";
$resultado_correo = mysqli_query($conexion, $query_correo);

if (mysqli_num_rows($resultado_correo) > 0) {
    echo '<script>alert("Este correo electrónico ya está registrado."); window.location = "register.html";</script>';
    exit; // Detener la ejecución del script
}

// Verificar si el nombre de usuario ya existe
$query_usuario = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
$resultado_usuario = mysqli_query($conexion, $query_usuario);

if (mysqli_num_rows($resultado_usuario) > 0) {
    echo '<script>alert("Este nombre de usuario ya está registrado."); window.location = "register.html";</script>';
    exit; // Detener la ejecución del script
}

// Consulta SQL para insertar datos en la tabla 'usuarios'
$query_insert = "INSERT INTO usuarios (nombre, apellido, usuario, correo, contrasena, tipo_nivel) 
          VALUES ('$nombre', '$apellido', '$usuario', '$correo', '$contrasena', 'usuario')";

// Ejecutar la consulta
$ejecutar = mysqli_query($conexion, $query_insert);

// Manejo de resultados
if ($ejecutar) {
    echo '<script>alert("Usuario registrado exitosamente"); window.location = "iniciar_sesion.php";</script>'; // Redirige a iniciar_sesion.php
} else {
    // Imprimir mensaje de error de la base de datos
    echo "Error al registrar usuario: " . mysqli_error($conexion);
    echo '<script>alert("Error al registrar usuario: ' . mysqli_error($conexion) . '"); window.location = "register.html";</script>'; // Redirige a register.html en caso de error
}

// Cerrar la conexión
mysqli_close($conexion);
?>