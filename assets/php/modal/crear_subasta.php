<?php
session_start();

// Verifica si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: index.html');
    exit();
}

// Incluye el archivo de configuración de la base de datos
include('../database/config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Función para convertir valores numéricos a un formato adecuado para MySQL
function convertirDecimal($valor)
{
    // Eliminar símbolo de moneda y separadores de miles
    $valor = str_replace(['€', '.', ','], ['', '', '.'], $valor);
    // Convertir a float
    return floatval($valor);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Convertir todos los valores monetarios antes de la inserción
        $valor_subasta = convertirDecimal($_POST['valor_subasta']);
        $tasacion = convertirDecimal($_POST['tasacion']);
        $importe_deposito = convertirDecimal($_POST['importe_deposito']);
        $puja_minima = convertirDecimal($_POST['puja_minima']);
        $cantidad_reclamada = convertirDecimal($_POST['cantidad_reclamada']);
        $tramos_pujas = convertirDecimal($_POST['tramos_pujas']);
        $precio_medio = convertirDecimal($_POST['precio_medio']);
        $puja_mas_alta = convertirDecimal($_POST['puja_mas_alta']);
        $carga_subastas = convertirDecimal($_POST['carga_subastas']); // Añadir la conversión de carga_subastas si es monetario

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
        $stmt->bindParam(':valor_subasta', $valor_subasta);
        $stmt->bindParam(':tasacion', $tasacion);
        $stmt->bindParam(':importe_deposito', $importe_deposito);
        $stmt->bindParam(':puja_minima', $puja_minima);
        $stmt->bindParam(':tramos_pujas', $tramos_pujas);
        $stmt->bindParam(':cantidad_reclamada', $cantidad_reclamada);
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
            $pdfPrecios = '../../pdf_compra/' . basename($_FILES['pdf_precios']['name']);
            if (!file_exists('../../pdf_compra')) {
                mkdir('../../pdf_compra', 0777, true);
            }
            move_uploaded_file($_FILES['pdf_precios']['tmp_name'], $pdfPrecios);
        }

        $stmt = $conn->prepare("
            INSERT INTO SubastaDetalles (id_subasta, precio_medio, puja_mas_alta, url_pdf_precios, carga_subastas)
            VALUES (:id_subasta, :precio_medio, :puja_mas_alta, :url_pdf_precios, :carga_subastas)
        ");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':precio_medio', $precio_medio);
        $stmt->bindParam(':puja_mas_alta', $puja_mas_alta);
        $stmt->bindParam(':url_pdf_precios', $pdfPrecios);
        $stmt->bindParam(':carga_subastas', $carga_subastas);
        $stmt->execute();

        // Insertar en la tabla Catastro
        $stmt = $conn->prepare("
            INSERT INTO Catastro (id_subasta, ref_catastral, clase, uso_principal, sup_construida, vivienda, garaje, almacen, terraza, ano_construccion, enlace_catastro, zonas_comunes)
            VALUES (:id_subasta, :ref_catastral, :clase, :uso_principal, :sup_construida, :vivienda, :garaje, :almacen, :terraza, :ano_construccion, :enlace_catastro, :zonas_comunes)
        ");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':ref_catastral', $_POST['ref_catastral']);
        $stmt->bindParam(':clase', $_POST['clase']);
        $stmt->bindParam(':uso_principal', $_POST['uso_principal']);
        $stmt->bindParam(':sup_construida', $_POST['sup_construida']);
        $stmt->bindParam(':vivienda', $_POST['vivienda']);
        $stmt->bindParam(':garaje', $_POST['garaje']);
        $stmt->bindParam(':almacen', $_POST['almacen']);
        $stmt->bindParam(':terraza', $_POST['terraza']); // Añadido el campo terraza
        $stmt->bindParam(':ano_construccion', $_POST['ano_construccion']);
        $stmt->bindParam(':enlace_catastro', $_POST['enlace_catastro']);
        $stmt->bindParam(':zonas_comunes', $_POST['zonas_comunes']);
        $stmt->execute();

        // Insertar en la tabla Valoraciones - sin conversión
        $stmt = $conn->prepare("
            INSERT INTO Valoraciones (id_subasta, fachada_y_exteriores, techo_y_canaletas, ventanas_y_puerta, jardin_y_terrenos, estado_estructuras, instalaciones_visibles, vecindario, seguridad, ruido_y_olores, acceso_y_estacionamiento, localizacion, estado_inquilino)
            VALUES (:id_subasta, :fachada_y_exteriores, :techo_y_canaletas, :ventanas_y_puerta, :jardin_y_terrenos, :estado_estructuras, :instalaciones_visibles, :vecindario, :seguridad, :ruido_y_olores, :acceso_y_estacionamiento, :localizacion, :estado_inquilino)
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
        $stmt->execute();

        // Insertar en la tabla SubastaIdealista
        $stmt = $conn->prepare("
            INSERT INTO SubastaIdealista (id_subasta, habitaciones, banos, piscina, jardin, ascensor, garaje_idealista, trastero, enlace_idealista)
            VALUES (:id_subasta, :habitaciones, :banos, :piscina, :jardin, :ascensor, :garaje_idealista, :trastero, :enlace_idealista)
        ");

        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':habitaciones', $_POST['habitaciones']);
        $stmt->bindParam(':banos', $_POST['banos']);
        $stmt->bindParam(':piscina', $_POST['piscina']);
        $stmt->bindParam(':jardin', $_POST['jardin']);
        $stmt->bindParam(':ascensor', $_POST['ascensor']);
        $stmt->bindParam(':garaje_idealista', $_POST['garaje_idealista']);
        $stmt->bindParam(':trastero', $_POST['trastero']);
        $stmt->bindParam(':enlace_idealista', $_POST['enlace_idealista']);
        $stmt->execute();


        // Insertar en la tabla Comentarios
        if (!empty($_POST['comentarios'])) {
            $stmt = $conn->prepare("
                INSERT INTO Comentarios (id_subasta, comentario)
                VALUES (:id_subasta, :comentario)
            ");
            $stmt->bindParam(':id_subasta', $id_subasta);
            $stmt->bindParam(':comentario', $_POST['comentarios']);
            $stmt->execute();
        }

        // Subir imágenes y documentos
        foreach ($_FILES['imagenes_subasta']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes_subasta']['error'][$key] === UPLOAD_ERR_OK) {
                $uploadDir = '/Applications/MAMP/htdocs/subastas_fdm/assets/img/VIVIENDAS/';
                $fileName = basename($_FILES['imagenes_subasta']['name'][$key]);
                $targetFilePath = $uploadDir . $fileName;

                // Crear directorio si no existe
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777,
                        true
                    );
                }

                move_uploaded_file($tmp_name, $targetFilePath);

                // Guardar la ruta relativa en la base de datos
                $relativeFilePath = 'assets/img/VIVIENDAS/' . $fileName;

                $stmt = $conn->prepare("
            INSERT INTO ImagenesSubasta (id_subasta, url_imagen)
            VALUES (:id_subasta, :url_imagen)
        ");
                $stmt->bindParam(':id_subasta', $id_subasta);
                $stmt->bindParam(':url_imagen', $relativeFilePath);
                $stmt->execute();
            }
        }


        // Subir videos de la subasta
        foreach ($_FILES['videos_subasta']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['videos_subasta']['error'][$key] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../assets/videos/VIVIENDAS/';
                $fileName = basename($_FILES['videos_subasta']['name'][$key]);
                $targetFilePath = $uploadDir . $fileName;

                // Crear directorio si no existe
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Mover el archivo subido al directorio de destino
                move_uploaded_file($tmp_name, $targetFilePath);

                // Guardar la ruta relativa en la base de datos
                $relativeFilePath = 'assets/videos/VIVIENDAS/' . $fileName;

                $stmt = $conn->prepare("
                    INSERT INTO VideosSubasta (id_subasta, url_video)
                    VALUES (:id_subasta, :url_video)
                ");
                $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
                $stmt->bindParam(':url_video', $relativeFilePath, PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        foreach ($_FILES['documentos_subasta']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_subasta']['error'][$key] === UPLOAD_ERR_OK) {
                $documentoSubasta = '../../documentos/' . basename($_FILES['documentos_subasta']['name'][$key]);

                if (!file_exists('../../documentos')) {
                    mkdir('../../documentos', 0777, true);
                }

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

        header('Location: /subastas_fdm/subastas.php');
        exit();
    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $conn->rollBack();
        echo "Error al crear la subasta: " . $e->getMessage();
    }
}
