<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="carrito.css">
</head>
<body>
    <div class="logo-carrito-pagina">
        <a href="index.php">
            <img src="logo.jpg" alt="Logotipo de tu empresa">
        </a>
    </div>

    <div class="carrito-contenedor">
        <h1>Tu Carrito de Compras</h1>
        <?php
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            include '../tutorial/conexion.php';

            $total_carrito = 0;

            echo '<div class="carrito-items">';
            foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
                $query = "SELECT id, nombre, precio, imagen FROM productos WHERE id = '$producto_id'";
                $resultado = mysqli_query($conexion, $query);
                $producto = mysqli_fetch_assoc($resultado);

                if ($producto) {
                    $subtotal_producto = $producto['precio'] * $cantidad;
                    $total_carrito += $subtotal_producto;

                    echo '<div class="carrito-item">';
                    echo '<div class="item-imagen">';
                    if (!empty($producto['imagen'])) {
                        echo '<img src="uploads/' . htmlspecialchars($producto['imagen']) . '" alt="' . htmlspecialchars($producto['nombre']) . '">';
                    } else {
                        echo '<img src="img/sin_imagen.png" alt="Sin imagen">';
                    }
                    echo '</div>';
                    echo '<div class="item-detalles">';
                        echo '<div class="item-info">';
                            echo '<h3>' . htmlspecialchars($producto['nombre']) . '</h3>';
                            echo '<p class="precio">Precio: $' . htmlspecialchars(number_format($producto['precio'], 2)) . '</p>';
                        echo '</div>';
                        echo '<div class="item-cantidad">';
                            echo '<label for="cantidad_' . $producto_id . '">Cantidad:</label>';
                            echo '<input type="number" id="cantidad_' . $producto_id . '" class="cantidad-input" name="cantidad_' . $producto_id . '" value="' . $cantidad . '" min="1" data-producto-id="' . htmlspecialchars($producto['id']) . '" data-precio="' . htmlspecialchars($producto['precio']) . '">';
                        echo '</div>';
                    echo '</div>';
                    echo '<p class="item-subtotal">Subtotal: $' . htmlspecialchars(number_format($subtotal_producto, 2)) . '</p>';
                    echo '<button class="eliminar-item-btn" data-producto-id="' . htmlspecialchars($producto['id']) . '">Eliminar</button>';
                    echo '</div>'; // Cierra carrito-item
                }
            }
            echo '</div>'; // Cierra carrito-items

            echo '<div class="carrito-total">';
            echo '<strong>Total: $<span id="total-carrito">' . htmlspecialchars(number_format($total_carrito, 2)) . '</span></strong>';
            echo '</div>';

            echo '<div class="carrito-acciones">';
                echo '<form action="vaciar_carrito.php" method="post">';
                    echo '<button type="submit">Vaciar Carrito</button>';
                echo '</form>';
                echo '<a href="finalizar_compra.php" class="finalizar-compra-btn"><button>Finalizar Compra</button></a>';
            echo '</div>';

            mysqli_close($conexion);
        } else {
            echo '<p class="carrito-vacio">Tu carrito está vacío.</p>';
            echo '<div class="carrito-acciones">';
            echo '<a href="catalogo.php">Volver al Catálogo</a>';
            echo '</div>';
        }
        ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cantidadInputs = document.querySelectorAll('.cantidad-input');
            const totalCarritoSpan = document.getElementById('total-carrito');
            const eliminarItemButtons = document.querySelectorAll('.eliminar-item-btn');
            const carritoItemsContainer = document.querySelector('.carrito-items');

            cantidadInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const productoId = this.dataset.productoId;
                    const nuevaCantidad = this.value;
                    const precioUnitario = parseFloat(this.dataset.precio);
                    const subtotalElement = this.closest('.carrito-item').querySelector('.item-subtotal');

                    const nuevoSubtotal = precioUnitario * nuevaCantidad;
                    subtotalElement.textContent = 'Subtotal: $' + nuevoSubtotal.toFixed(2);

                    fetch('actualizar_carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'producto_id=' + encodeURIComponent(productoId) + '&cantidad=' + encodeURIComponent(nuevaCantidad)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.total_carrito !== undefined) {
                            totalCarritoSpan.textContent = data.total_carrito.toFixed(2);
                        } else {
                            console.error('Error: Total del carrito no recibido del servidor.');
                        }
                    })
                    .catch(error => {
                        console.error('Error al actualizar el carrito:', error);
                    });
                });
            });

            eliminarItemButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productoId = this.dataset.productoId;
                    const carritoItemToRemove = this.closest('.carrito-item');

                    fetch('eliminar_del_carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'producto_id=' + encodeURIComponent(productoId)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (carritoItemToRemove) {
                                carritoItemToRemove.remove();
                            }
                            if (data.total_carrito !== undefined) {
                                totalCarritoSpan.textContent = data.total_carrito.toFixed(2);
                            } else if (data.carrito_vacio) {
                                carritoItemsContainer.innerHTML = '<p class="carrito-vacio">Tu carrito está vacío.</p>';
                                totalCarritoSpan.textContent = '0.00';
                            } else {
                                console.error('Error: Total del carrito no recibido del servidor.');
                            }
                        } else {
                            console.error('Error al eliminar el item del carrito:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error al eliminar el item del carrito:', error);
                    });
                });
            });
        });
    </script>
</body>
</html>