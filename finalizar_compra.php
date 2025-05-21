<?php
session_start();

// Verificar si el carrito tiene productos
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: catalogo.php");
    exit();
}

// Incluir el archivo de conexión a la base de datos
include '../tutorial/conexion.php';

// Verificar si el usuario está logueado (opcional)
$usuario_id = $_SESSION['usuario_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_entrega = $_POST['tipo_entrega'];
    $telefono = $_POST['telefono'];
    $nombre_apellido = $_POST['nombre_apellido'];
    $forma_pago = $_POST['forma_pago'];
    $direccion_envio = '';
    $agencia_envio = ''; // Nuevo campo para la agencia

    // Validar que se haya seleccionado un tipo de entrega
    if (empty($tipo_entrega)) {
        echo "<p style='color:red;'>Por favor, selecciona el tipo de entrega.</p>";
        // Mostrar el formulario de nuevo
    } else {
        if ($tipo_entrega === 'agencia') {
            $agencia_envio = $_POST['agencia_envio']; // Obtener la agencia seleccionada
            if (empty($agencia_envio)) {
                echo "<p style='color:red;'>Por favor, selecciona la agencia de envío.</p>";
                // Mostrar el formulario de nuevo
            } else {
                $direccion_envio = $_POST['direccion_envio']; // Requerir dirección si es por agencia
                if (empty($direccion_envio)) {
                    echo "<p style='color:red;'>Por favor, ingresa la dirección de envío.</p>";
                    // Mostrar el formulario de nuevo
                }
            }
        }

        if (!empty($tipo_entrega) && ($tipo_entrega !== 'agencia' || (!empty($agencia_envio) && !empty($direccion_envio)) || $tipo_entrega === 'barquisimeto')) {
            // Calcular el total del pedido
            $total_pedido = 0;
            $detalles_pedido = [];

            foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
                $query_producto = "SELECT id, precio FROM productos WHERE id = '$producto_id'";
                $resultado_producto = mysqli_query($conexion, $query_producto);
                $producto = mysqli_fetch_assoc($resultado_producto);

                if ($producto) {
                    $precio_unitario = $producto['precio'];
                    $subtotal = $precio_unitario * $cantidad;
                    $total_pedido += $subtotal;

                    $detalles_pedido[] = [
                        'producto_id' => $producto['id'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario,
                        'subtotal' => $subtotal
                    ];
                }
            }

            // Generar un número de factura único
            $numero_factura = uniqid('FAC-');

            // Iniciar transacción
            mysqli_begin_transaction($conexion);

            // Insertar el pedido en la tabla 'pedidos'
            $metodo_envio_final = ($tipo_entrega === 'agencia') ? $agencia_envio : $tipo_entrega;
            $query_pedido = "INSERT INTO pedidos (usuario_id, fecha_pedido, total, estado, direccion_envio, metodo_pago, numero_factura, telefono, nombre_apellido, tipo_entrega, metodo_envio)
                                        VALUES ('$usuario_id', NOW(), '$total_pedido', 'pendiente', '$direccion_envio', '$forma_pago', '$numero_factura', '$telefono', '$nombre_apellido', '$tipo_entrega', '$metodo_envio_final')";
            $resultado_pedido = mysqli_query($conexion, $query_pedido);

            if ($resultado_pedido) {
                $pedido_id = mysqli_insert_id($conexion);

                $inserciones_detalle = true;
                foreach ($detalles_pedido as $detalle) {
                    $query_detalle = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                                                    VALUES ('$pedido_id', '{$detalle['producto_id']}', '{$detalle['cantidad']}', '{$detalle['precio_unitario']}', '{$detalle['subtotal']}')";
                    $resultado_detalle = mysqli_query($conexion, $query_detalle);
                    if (!$resultado_detalle) {
                        $inserciones_detalle = false;
                        break;
                    }
                }

                if ($inserciones_detalle) {
                    mysqli_commit($conexion);
                    unset($_SESSION['carrito']);

                    echo "<h1>Pedido realizado con éxito</h1>";
                    echo "<p>Número de factura: " . htmlspecialchars($numero_factura) . "</p>";
                    echo "<p>Total del pedido: $" . htmlspecialchars(number_format($total_pedido, 2)) . "</p>";
                    echo "<p>Tipo de Entrega: " . htmlspecialchars($tipo_entrega) . "</p>";
                    echo "<p>Teléfono: " . htmlspecialchars($telefono) . "</p>";
                    echo "<p>Nombre y Apellido: " . htmlspecialchars($nombre_apellido) . "</p>";
                    echo "<p>Forma de Pago: " . htmlspecialchars($forma_pago) . "</p>";
                    if ($tipo_entrega === 'agencia') {
                        echo "<p>Agencia de Envío: " . htmlspecialchars($agencia_envio) . "</p>";
                        echo "<p>Dirección de Envío: " . htmlspecialchars($direccion_envio) . "</p>";
                    }
                    echo "<p><a href='index.php'>Volver a la página principal</a></p>";
                } else {
                    mysqli_rollback($conexion);
                    echo "Error al guardar los detalles del pedido.";
                }
            } else {
                mysqli_rollback($conexion);
                echo "Error al guardar el pedido.";
            }

            mysqli_close($conexion);
            exit(); // Importante: detener la ejecución después de procesar el formulario
        }
    }
}

// Si la petición no es POST o hubo un error de validación, mostrar el formulario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="finalizar_compra.css">
    <style>
        #seleccion_agencia {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="logo-finalizar-compra">
        <a href="index.php">
            <img src="logo.jpg" alt="Logotipo de Cutenland">
        </a>
    </div>
    <div class="container">
        <h1>Finalizar Compra</h1>
        <form method="post" action="">
            <div>
                <label for="tipo_entrega">Tipo de Entrega:</label><br>
                <select id="tipo_entrega" name="tipo_entrega" required onchange="mostrarOpcionesEnvio()">
                    <option value="">Seleccionar</option>
                    <option value="barquisimeto">Entrega en Barquisimeto</option>
                    <option value="agencia">Envío por Agencia</option>
                </select>
            </div>
            <div id="seleccion_agencia">
                <label for="agencia_envio">Seleccione la Agencia:</label><br>
                <select id="agencia_envio" name="agencia_envio">
                    <option value="">Seleccionar</option>
                    <option value="mrw">MRW</option>
                    <option value="zoom">Zoom</option>
                </select>
            </div>
            <div>
                <label for="telefono">Teléfono:</label><br>
                <input type="tel" id="telefono" name="telefono" required>
            </div>
            <div>
                <label for="nombre_apellido">Nombre y Apellido:</label><br>
                <input type="text" id="nombre_apellido" name="nombre_apellido" required>
            </div>
            <div>
                <label for="forma_pago">Forma de Pago:</label><br>
                <input type="text" id="forma_pago" name="forma_pago" required>
            </div>
            <div id="direccion_envio_div" style="display: none;">
                <label for="direccion_envio">Dirección de Envío:</label><br>
                <textarea id="direccion_envio" name="direccion_envio" rows="4" cols="50"></textarea>
            </div>
            <button type="submit">Confirmar Pedido</button>
        </form>
        <div class="volver-carrito">
            <a href="carrito.php"><button>Volver al Carrito</button></a>
        </div>
    </div>

    <script>
        const tipoEntregaSelect = document.getElementById("tipo_entrega");
        const seleccionAgenciaDiv = document.getElementById("seleccion_agencia");
        const agenciaEnvioSelect = document.getElementById("agencia_envio");
        const direccionEnvioDiv = document.getElementById("direccion_envio_div");
        const direccionEnvioTextarea = document.getElementById("direccion_envio");

        function mostrarOpcionesEnvio() {
            if (tipoEntregaSelect.value === "agencia") {
                seleccionAgenciaDiv.style.display = "block";
                direccionEnvioDiv.style.display = "block";
                agenciaEnvioSelect.setAttribute("required", "");
                direccionEnvioTextarea.setAttribute("required", "");
            } else {
                seleccionAgenciaDiv.style.display = "none";
                direccionEnvioDiv.style.display = "none";
                agenciaEnvioSelect.removeAttribute("required");
                direccionEnvioTextarea.removeAttribute("required");
                agenciaEnvioSelect.value = ""; // Limpiar la selección de agencia
                direccionEnvioTextarea.value = ""; // Limpiar la dirección
            }
        }

        // Asegurarse de que la sección de agencia esté oculta al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            mostrarOpcionesEnvio();
        });
    </script>
</body>
</html>
<?php
?>