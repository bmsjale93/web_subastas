<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: index.html');
    exit();
}

include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_subasta = $_POST['id_subasta'];

    // Datos para la tabla Subastas
    $direccion = $_POST['direccion'];
    $valor_subasta = $_POST['valor_subasta'];
    $fecha_conclusion = $_POST['fecha_conclusion'];
    $estado_subasta = $_POST['estado_subasta'];

    // Datos para la tabla Localizaciones
    $latitud = $_POST['latitud'];
    $altitud = $_POST['altitud'];

    // Datos para la tabla SubastaDetalles
    $precio_medio = $_POST['precio_medio'];
    $precio_venta_min = $_POST['precio_venta_min'];
    $precio_venta_medio = $_POST['precio_venta_medio'];
    $precio_venta_max = $_POST['precio_venta_max'];

    // Datos para la tabla Catastro
    $ref_catastral = $_POST['ref_catastral'];
    $clase = $_POST['clase'];
    $uso_principal = $_POST['uso_principal'];
    $sup_construida = $_POST['sup_construida'];
    $vivienda = $_POST['vivienda'];
    $garaje = $_POST['garaje'];
    $almacen = $_POST['almacen'];
    $ano_construccion = $_POST['ano_construccion'];
    $enlace_catastro = $_POST['enlace_catastro'];

    // Datos para la tabla Valoraciones
    $fachada_y_exteriores = $_POST['fachada_y_exteriores'];
    $techo_y_canaletas = $_POST['techo_y_canaletas'];
    $ventanas_y_puerta = $_POST['ventanas_y_puerta'];
    $jardin_y_terrenos = $_POST['jardin_y_terrenos'];
    $estado_estructuras = $_POST['estado_estructuras'];
    $instalaciones_visibles = $_POST['instalaciones_visibles'];
    $vecindario = $_POST['vecindario'];
    $seguridad = $_POST['seguridad'];
    $ruido_y_olores = $_POST['ruido_y_olores'];
    $acceso_y_estacionamiento = $_POST['acceso_y_estacionamiento'];
    $localizacion = $_POST['localizacion'];
    $estado_inquilino = $_POST['estado_inquilino'];
    $tipo_de_vivienda = $_POST['tipo_de_vivienda'];
    $puntuacion_final = $_POST['puntuacion_final'];

    try {
        // Actualizar la tabla Subastas
        $stmt = $conn->prepare("
            UPDATE Subastas 
            SET direccion = :direccion, valor_subasta = :valor_subasta, fecha_conclusion = :fecha_conclusion, id_estado = (SELECT id_estado FROM EstadosSubasta WHERE estado = :estado_subasta)
            WHERE id_subasta = :id_subasta
        ");
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':valor_subasta', $valor_subasta);
        $stmt->bindParam(':fecha_conclusion', $fecha_conclusion);
        $stmt->bindParam(':estado_subasta', $estado_subasta);
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();

        // Actualizar la tabla Localizaciones
        $stmt = $conn->prepare("
            UPDATE Localizaciones 
            SET latitud = :latitud, altitud = :altitud 
            WHERE id_subasta = :id_subasta
        ");
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':altitud', $altitud);
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();

        // Actualizar la tabla SubastaDetalles
        $stmt = $conn->prepare("
            UPDATE SubastaDetalles 
            SET precio_medio = :precio_medio, precio_venta_min = :precio_venta_min, precio_venta_medio = :precio_venta_medio, precio_venta_max = :precio_venta_max 
            WHERE id_subasta = :id_subasta
        ");
        $stmt->bindParam(':precio_medio', $precio_medio);
        $stmt->bindParam(':precio_venta_min', $precio_venta_min);
        $stmt->bindParam(':precio_venta_medio', $precio_venta_medio);
        $stmt->bindParam(':precio_venta_max', $precio_venta_max);
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();

        // Actualizar la tabla Catastro
        $stmt = $conn->prepare("
            UPDATE Catastro 
            SET ref_catastral = :ref_catastral, clase = :clase, uso_principal = :uso_principal, sup_construida = :sup_construida, vivienda = :vivienda, garaje = :garaje, almacen = :almacen, ano_construccion = :ano_construccion, enlace_catastro = :enlace_catastro 
            WHERE id_subasta = :id_subasta
        ");
        $stmt->bindParam(':ref_catastral', $ref_catastral);
        $stmt->bindParam(':clase', $clase);
        $stmt->bindParam(':uso_principal', $uso_principal);
        $stmt->bindParam(':sup_construida', $sup_construida);
        $stmt->bindParam(':vivienda', $vivienda);
        $stmt->bindParam(':garaje', $garaje);
        $stmt->bindParam(':almacen', $almacen);
        $stmt->bindParam(':ano_construccion', $ano_construccion);
        $stmt->bindParam(':enlace_catastro', $enlace_catastro);
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();

        // Actualizar la tabla Valoraciones
        $stmt = $conn->prepare("
            UPDATE Valoraciones 
            SET fachada_y_exteriores = :fachada_y_exteriores, techo_y_canaletas = :techo_y_canaletas, ventanas_y_puerta = :ventanas_y_puerta, jardin_y_terrenos = :jardin_y_terrenos, estado_estructuras = :estado_estructuras, instalaciones_visibles = :instalaciones_visibles, vecindario = :vecindario, seguridad = :seguridad, ruido_y_olores = :ruido_y_olores, acceso_y_estacionamiento = :acceso_y_estacionamiento, localizacion = :localizacion, estado_inquilino = :estado_inquilino, tipo_de_vivienda = :tipo_de_vivienda, puntuacion_final = :puntuacion_final 
            WHERE id_subasta = :id_subasta
        ");
        $stmt->bindParam(':fachada_y_exteriores', $fachada_y_exteriores);
        $stmt->bindParam(':techo_y_canaletas', $techo_y_canaletas);
        $stmt->bindParam(':ventanas_y_puerta', $ventanas_y_puerta);
        $stmt->bindParam(':jardin_y_terrenos', $jardin_y_terrenos);
        $stmt->bindParam(':estado_estructuras', $estado_estructuras);
        $stmt->bindParam(':instalaciones_visibles', $instalaciones_visibles);
        $stmt->bindParam(':vecindario', $vecindario);
        $stmt->bindParam(':seguridad', $seguridad);
        $stmt->bindParam(':ruido_y_olores', $ruido_y_olores);
        $stmt->bindParam(':acceso_y_estacionamiento', $acceso_y_estacionamiento);
        $stmt->bindParam(':localizacion', $localizacion);
        $stmt->bindParam(':estado_inquilino', $estado_inquilino);
        $stmt->bindParam(':tipo_de_vivienda', $tipo_de_vivienda);
        $stmt->bindParam(':puntuacion_final', $puntuacion_final);
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();

        header('Location: subastas.php');
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar la subasta: " . $e->getMessage();
    }
}
