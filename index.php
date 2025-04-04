<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    include '../tutorial/conexion.php'; // Incluye el archivo de conexión

    // Verificar la conexión
    if ($conexion->connect_errno) {
        die("Error de conexión: " . $conexion->connect_errno);
    }

    // Obtener los datos del usuario
    $usuario_id = $_SESSION['usuario_id'];
    $query = "SELECT * FROM usuarios WHERE id = '$usuario_id'";
    $resultado = mysqli_query($conexion, $query);
    $usuario_datos = mysqli_fetch_assoc($resultado);

    // Cerrar la conexión
        mysqli_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuteland - Tu tienda de artículos de...</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="index-page">

    <video autoplay loop muted playsinline class="video-bg">
        <source src="pagina-web.mp4" type="video/mp4">
        Tu navegador no soporta videos en HTML5.
    </video>

    <header>
        <div class="logo">
            <img src="logo.jpg" alt="Logotipo de Cuteland">
        </div>
        <nav class="menu">
            <ul class="main-nav">
                <li><a href="#">INICIO</a></li>
                <li><a href="#">CATÁLOGO</a></li> 
                <li><a href="#sobre-nosotros">SOBRE NOSOTROS</a></li> 
                <li><a href="#contacto">CONTACTO</a></li> 
            </ul>
            <div class="profile-menu">
                <img src="perfil-icono.png" alt="Perfil" class="profile-icon">
                <ul class="dropdown">
                    <?php if (isset($_SESSION["usuario_id"])) { ?>
                        <li><a href="perfil_usuario.php">Mi perfil</a></li>
                        <li><a href="configurar_usuario.php">Configuración</a></li>
                        <li><a href="cerrar_sesion.php">Cerrar sesión</a></li>
                    <?php } else { ?>
                        <li><a href="login.html">Iniciar sesión</a></li>
                        <li><a href="register.html">Registrarse</a></li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="text-container">
                <h1>Bienvenido a Cuteland</h1>
            </div>
            <a href="#" class="btn">Explorar Catálogo</a>
        </section>
    </main>

    <section class="about-us" id="sobre-nosotros">
        <div class="about-content">
            <div class="about-logo">
                <img src="Cuteland store titulo.png" alt="Imagen sobre Nosotros">
            </div>
            <div class="about-text">
                <h2>Sobre Nosotros</h2>
                <p>
                    "Un proyecto dedicado en traer a sus clientes los más lindos productos importados y de excelente calidad con temática anime, gaming y geek ♡(> ਊ <)♡.
                    Nos apasiona la cultura otaku y gamer, por eso seleccionamos con mucho cariño cada artículo para que encuentres lo que más te gusta. Desde ropa y accesorios hasta figuras coleccionables y artículos de uso diario, todo con el toque especial que hace brillar tu fandom.
                    ¡Queremos que cada compra sea una experiencia mágica! Nos esforzamos por ofrecer atención personalizada, envíos seguros y novedades constantes para que siempre tengas algo increíble por descubrir. ✨"
                </p>
            </div>
        </div>
    </section>

    <section class="product-preview">
        <h2>Nuestros productos</h2>
        <p>Aquí podrás visualizar algunos de los muchos productos con los cuales contamos</p>
        <div class="product-grid">
            <div class="product-item">
                <h3>BOLSOS</h3>
                <img src="bolso1.png" alt="Bolso 1">
            </div>
            
            <div class="product-item">
                <h3>BISUTERIA</h3>
                <img src="bisuteria1.png" alt="Bisutería 1">
            </div>
            <div class="product-item">
                <img src="bolso2.png" alt="Bolso 2">
            </div>
            <div class="product-item">
                <img src="bistueria2.png" alt="Anillos 1">
            </div>
        </div>
        <button class="view-products-button">VER PRODUCTOS</button>
    </section>

    <section class="contact-section" id="contacto">
        <h2>Contacto</h2>
        <p>PARA CUALQUIER INFORMACIÓN QUE NECESITES AQUÍ NOS PUEDES CONTACTAR.</p>
        <div class="contact-info">
            <div class="social-links">
                <h3>Síguenos en redes</h3>
                <a href="https://wa.me/584127734417" class="social-icon whatsapp" target="_blank"></a>
                <a href="https://www.instagram.com/cuteland.store/?hl=es" class="social-icon instagram" target="_blank"></a>
                <a href="https://www.facebook.com/CutelandStore1" class="social-icon facebook" target="_blank"></a>
            </div>
            <div class="contact-details">
                <h3>Sección de Contacto</h3>
                <div class="contact-item">
                    <span class="icon phone"></span>
                    <a href="tel:+584127734417">+58 412-7734417</a>
                </div>
                <div class="contact-item">
                    <span class="icon email"></span>
                    <a href="mailto:correoayf@gmail.com">correoayf@gmail.com</a>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const profileMenu = document.querySelector(".profile-menu");
            const dropdown = document.querySelector(".dropdown");
            
            // Alternar visibilidad del menú al hacer clic en el icono de perfil
            profileMenu.addEventListener("click", function (event) {
                dropdown.classList.toggle("show");
                event.stopPropagation(); // Evita que el clic cierre inmediatamente el menú
            });
            
            // Cerrar el menú si se hace clic fuera de él
            document.addEventListener("click", function (event) {
                if (!profileMenu.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.remove("show");
                }
            });
        });
    </script>
</body>
</html>