// Backend: crear_subasta.php
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
            INSERT INTO Subastas (direccion, cp, localidad, provincia, fecha_inicio, fecha_conclusion, enlace_subasta, valor_subasta, tasacion, importe_deposito, puja_minima, tramos_pujas, cantidad_reclamada, id_tipo_subasta, id_estado)
            VALUES (:direccion, :cp, :localidad, :provincia, :fecha_inicio, :fecha_conclusion, :enlace_subasta, :valor_subasta, :tasacion, :importe_deposito, :puja_minima, :tramos_pujas, :cantidad_reclamada, :id_tipo_subasta, :id_estado)
        ");
        $stmt->bindParam(':direccion', $_POST['direccion']);
        $stmt->bindParam(':cp', $_POST['cp']);
        $stmt->bindParam(':localidad', $_POST['localidad']);
        $stmt->bindParam(':provincia', $_POST['provincia']);
        $stmt->bindParam(':fecha_inicio', $_POST['fecha_inicio']);
        $stmt->bindParam(':fecha_conclusion', $_POST['fecha_conclusion']);
        $stmt->bindParam(':enlace_subasta', $_POST['enlace_subasta']);
        $stmt->bindParam(':valor_subasta', $_POST['valor_subasta']);
        $stmt->bindParam(':tasacion', $_POST['tasacion']);
        $stmt->bindParam(':importe_deposito', $_POST['importe_deposito']);
        $stmt->bindParam(':puja_minima', $_POST['puja_minima']);
        $stmt->bindParam(':tramos_pujas', $_POST['tramos_pujas']);
        $stmt->bindParam(':cantidad_reclamada', $_POST['cantidad_reclamada']);
        $stmt->bindParam(':id_tipo_subasta', $_POST['id_tipo_subasta']);
        $stmt->bindParam(':id_estado', $_POST['id_estado']);
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
        $pdfPrecios = '';
        if (isset($_FILES['pdf_precios']) && $_FILES['pdf_precios']['error'] === UPLOAD_ERR_OK) {
            $pdfPrecios = 'assets/pdf_compra/' . basename($_FILES['pdf_precios']['name']);
            move_uploaded_file($_FILES['pdf_precios']['tmp_name'], $pdfPrecios);
        }

        $stmt = $conn->prepare("
            INSERT INTO SubastaDetalles (id_subasta, precio_medio, precio_venta_medio, puja_mas_alta, url_pdf_precios)
            VALUES (:id_subasta, :precio_medio, :precio_venta_medio, :puja_mas_alta, :url_pdf_precios)
        ");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':precio_medio', $_POST['precio_medio']);
        $stmt->bindParam(':precio_venta_medio', $_POST['precio_venta_medio']);
        $stmt->bindParam(':puja_mas_alta', $_POST['puja_mas_alta']);
        $stmt->bindParam(':url_pdf_precios', $pdfPrecios);
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
            INSERT INTO Valoraciones (id_subasta, fachada_y_exteriores, techo_y_canaletas, ventanas_y_puerta, jardin_y_terrenos, estado_estructuras, instalaciones_visibles, vecindario, seguridad, ruido_y_olores, acceso_y_estacionamiento, localizacion, estado_inquilino, tipo_de_vivienda)
            VALUES (:id_subasta, :fachada_y_exteriores, :techo_y_canaletas, :ventanas_y_puerta, :jardin_y_terrenos, :estado_estructuras, :instalaciones_visibles, :vecindario, :seguridad, :ruido_y_olores, :acceso_y_estacionamiento, :localizacion, :estado_inquilino, :tipo_de_vivienda)
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
        $stmt->execute();

        // Subir imágenes y documentos
        foreach ($_FILES['imagenes_subasta']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes_subasta']['error'][$key] === UPLOAD_ERR_OK) {
                $imagenSubasta = 'assets/img/VIVIENDAS/' . basename($_FILES['imagenes_subasta']['name'][$key]);
                move_uploaded_file($tmp_name, $imagenSubasta);

                $stmt = $conn->prepare("
                    INSERT INTO ImagenesSubasta (id_subasta, url_imagen)
                    VALUES (:id_subasta, :url_imagen)
                ");
                $stmt->bindParam(':id_subasta', $id_subasta);
                $stmt->bindParam(':url_imagen', $imagenSubasta);
                $stmt->execute();
            }
        }

        foreach ($_FILES['documentos_subasta']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_subasta']['error'][$key] === UPLOAD_ERR_OK) {
                $documentoSubasta = 'assets/documentos/' . basename($_FILES['documentos_subasta']['name'][$key]);
                move_uploaded_file($tmp_name, $documentoSubasta);

                $stmt = $conn->prepare("
                    INSERT INTO Documentos (id_subasta, nombre_documento, url_documento)
                    VALUES (:id_subasta, :nombre_documento, :url_documento)
                ");
                $nombreDocumento = $_FILES['documentos_subasta']['name'][$key];
                $stmt->bindParam(':id_subasta', $id_subasta);
                $stmt->bindParam(':nombre_documento', $nombreDocumento);
                $stmt->bindParam(':url_documento', $documentoSubasta);
                $stmt->execute();
            }
        }

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
