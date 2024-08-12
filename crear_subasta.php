<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: index.html');
    exit();
}

include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Insertar en la tabla Subastas
        $stmt = $conn->prepare("
            INSERT INTO Subastas (direccion, valor_subasta, fecha_conclusion, id_estado)
            VALUES (:direccion, :valor_subasta, :fecha_conclusion, (SELECT id_estado FROM EstadosSubasta WHERE estado = :estado_subasta))
        ");
        $stmt->bindParam(':direccion', $_POST['direccion']);
        $stmt->bindParam(':valor_subasta', $_POST['valor_subasta']);
        $stmt->bindParam(':fecha_conclusion', $_POST['fecha_conclusion']);
        $stmt->bindParam(':estado_subasta', $_POST['estado_subasta']);
        $stmt->execute();

        // Obtener el ID de la subasta recién creada
        $id_subasta = $conn->lastInsertId();

        // Insertar en la tabla Localizaciones
        $stmt = $conn->prepare("
            INSERT INTO Localizaciones (id_subasta, latitud, altitud)
            VALUES (:id_subasta, :latitud, :altitud)
        ");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':latitud', $_POST['latitud']);
        $stmt->bindParam(':altitud', $_POST['altitud']);
        $stmt->execute();

        // Insertar en la tabla SubastaDetalles
        $stmt = $conn->prepare("
            INSERT INTO SubastaDetalles (id_subasta, precio_medio, precio_venta_min, precio_venta_medio, precio_venta_max)
            VALUES (:id_subasta, :precio_medio, :precio_venta_min, :precio_venta_medio, :precio_venta_max)
        ");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':precio_medio', $_POST['precio_medio']);
        $stmt->bindParam(':precio_venta_min', $_POST['precio_venta_min']);
        $stmt->bindParam(':precio_venta_medio', $_POST['precio_venta_medio']);
        $stmt->bindParam(':precio_venta_max', $_POST['precio_venta_max']);
        $stmt->execute();

        // Insertar en la tabla Catastro
        $stmt = $conn->prepare("
            INSERT INTO Catastro (id_subasta, ref_catastral, clase, uso_principal, sup_construida, vivienda, garaje, almacen, ano_construccion, enlace_catastro)
            VALUES (:id_subasta, :ref_catastral, :clase, :uso_principal, :sup_construida, :vivienda, :garaje, :almacen, :ano_construccion, :enlace_catastro)
        ");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':ref_catastral', $_POST['ref_catastral']);
        $stmt->bindParam(':clase', $_POST['clase']);
        $stmt->bindParam(':uso_principal', $_POST['uso_principal']);
        $stmt->bindParam(':sup_construida', $_POST['sup_construida']);
        $stmt->bindParam(':vivienda', $_POST['vivienda']);
        $stmt->bindParam(':garaje', $_POST['garaje']);
        $stmt->bindParam(':almacen', $_POST['almacen']);
        $stmt->bindParam(':ano_construccion', $_POST['ano_construccion']);
        $stmt->bindParam(':enlace_catastro', $_POST['enlace_catastro']);
        $stmt->execute();

        // Insertar en la tabla Valoraciones
        $stmt = $conn->prepare("
            INSERT INTO Valoraciones (id_subasta, fachada_y_exteriores, techo_y_canaletas, ventanas_y_puerta, jardin_y_terrenos, estado_estructuras, instalaciones_visibles, vecindario, seguridad, ruido_y_olores, acceso_y_estacionamiento, localizacion, estado_inquilino, tipo_de_vivienda, puntuacion_final)
            VALUES (:id_subasta, :fachada_y_exteriores, :techo_y_canaletas, :ventanas_y_puerta, :jardin_y_terrenos, :estado_estructuras, :instalaciones_visibles, :vecindario, :seguridad, :ruido_y_olores, :acceso_y_estacionamiento, :localizacion, :estado_inquilino, :tipo_de_vivienda, :puntuacion_final)
        ");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':fachada_y_exteriores', $_POST['fachada_y_exteriores']);
        $stmt->bindParam(':techo_y_canaletas', $_POST['techo_y_canaletas']);
        $stmt->bindParam(':ventanas_y_puerta', $_POST['ventanas_y_puerta']);
        $stmt->bindParam(':jardin_y_terrenos', $_POST['jardin_y_terrenos']);
        $stmt->bindParam(':estado_estructuras', $_POST['estado_estructuras']);
        $stmt->bindParam(':instalaciones_visibles', $_POST['instalaciones_visibles']);
        $stmt->bindParam(':vecindario', $_POST['vecindario']);
        $stmt->bindParam(':seguridad', $_POST['seguridad']);
        $stmt->bindParam(':ruido_y_olores', $_POST['ruido_y_olores']);
        $stmt->bindParam(':acceso_y_estacionamiento', $_POST['acceso_y_estacionamiento']);
        $stmt->bindParam(':localizacion', $_POST['localizacion']);
        $stmt->bindParam(':estado_inquilino', $_POST['estado_inquilino']);
        $stmt->bindParam(':tipo_de_vivienda', $_POST['tipo_de_vivienda']);
        $stmt->bindParam(':puntuacion_final', $_POST['puntuacion_final']);
        $stmt->execute();

        // Confirmar la transacción
        $conn->commit();

        header('Location: subastas.php');
        exit();
    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $conn->rollBack();
        echo "Error al crear la subasta: " . $e->getMessage();
    }
}
