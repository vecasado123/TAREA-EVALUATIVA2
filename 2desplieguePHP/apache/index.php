<!DOCTYPE html>

<html>
	<head>
		<meta charset="UTF-8">
		<title>Consultas preparadas con MySQLi</title>
		<link href="dwes.css" rel="stylesheet">
	</head>

	<body>
        <?php
            if (isset($_POST['producto'])) {
                $producto = $_POST['producto'];
                // echo $producto;
            }

            // Crear la conexión
            $servername = "192.168.0.30:3306";
            $database = "dwess";
            $username = "root";
            $password = "root";
            $conn = new mysqli($servername, $username, $password, $database);

            // comprobar conexión
            if (!$conn) {
                die("Error en la conexión: " . $conn->connect_errno);
            }

            // Comprobamos si tenemos que actualizar los valores
            if (isset($_POST['actualiz'])) {
                // Preparamos la consulta
                $tienda = $_POST['tienda'];
                $unidades = $_POST['unidades'];
                // echo print_r($tienda);
                // echo print_r($unidades);

                $consulta = $conn->stmt_init();
                $sql = "UPDATE stock SET unidades=? WHERE tienda=? AND producto='$producto'";
                $consulta->prepare($sql);

                // La ejecutamos dentro de un bucle, tantas veces como tiendas haya
                $conn->autocommit(true);
                for($i=0;$i<count($tienda);$i++) {
                    // echo "unidades[" . $i . "] = " . $unidades[$i];
                    // echo $tienda[$i];
                    $consulta->bind_param('ii', $unidades[$i], $tienda[$i]);
                    $consulta->execute();
                }

                // $conn->commit();
                $mensaje = "Se han actualizado los datos.";
                $consulta->close();

            }
        ?>

		<header id="encabezado">
			<h1>Consultas preparadas con MySQLi</h1>
            <form id="form_seleccion" action="index.php" method="post">
            <span>Producto: </span>
            <select name="producto">
                <?php
                // Rellenamos el desplegable con los datos de todos los productos
                if ($conn) {
                    $sql = "SELECT cod, nombre_corto FROM producto";
                    $resultado = $conn->query($sql);
                    if($resultado) {
                        $row = $resultado->fetch_assoc();
                        while ($row != null) {
                            echo "<option value='${row['cod']}'";
                            // Si se recibió un código de producto lo seleccionamos
                            //  en el desplegable usando selected='true'
                            if (isset($producto) && $producto == $row['cod'])
                            echo " selected='true'";
                            echo ">${row['nombre_corto']}</option>";
                            $row = $resultado->fetch_assoc();
                        }
                        $resultado->close();
                    }
                }
                ?>
            </select>
            <input type="submit" value="Mostrar stock" name="enviar"/>
            </form>
		</header>

		<section id="contenido">
            <h2>Stock del producto en las tiendas:</h2>
        
            <?php
                // Si se recibió un código de producto y no se produjo ningún error
                //  mostramos el stock de ese producto en las distintas tiendas
                if ($conn && isset($producto)) {
                    // Ahora necesitamos también el código de tienda
                    $sql = "SELECT tienda.cod, tienda.nombre, stock.unidades
                    FROM tienda INNER JOIN stock ON tienda.cod=stock.tienda
                    WHERE stock.producto='$producto'";
                    $resultado = $conn->query($sql);
                    if($resultado) {
                        // Creamos un formulario con los valores obtenidos
                        echo '<form id="form_actualiz" action="index.php" method="post">';
                        $row = $resultado->fetch_assoc();
                        while ($row != null) {
                            // Metemos ocultos el código de producto y los de las tiendas
                            echo "<input type='hidden' name='producto' value='$producto'/>";
                            echo "<input type='hidden' name='tienda[]' value='".$row['cod']."'/>";
                            echo "<p>Tienda ${row['nombre']}: ";
                            // El número de unidades ahora va en un cuadro de texto
                            echo "<input type='text' name='unidades[]' size='4' ";
                            echo "value='".$row['unidades']."'/> unidades.</p>";
                            $row = $resultado->fetch_assoc();
                        }
                        $resultado->close();
                        echo "<input type='submit' value='Actualizar' name='actualiz'/>";
                        echo "</form>";
                    }
                }
                ?>
		</section>

		<footer id="pie">
			<?php
			// Si se produjo algún error se muestra en el pie
			if (!$conn)
				echo "<p>Se ha producido un error!</p>";
			else {
				echo "<p>Consultas preparadas con MySQLi</p>";
				$conn->close();
				unset($conn);
			}
			?>
		</footer>
	</body>
</html>
