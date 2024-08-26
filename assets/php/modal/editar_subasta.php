<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: index.html');
    exit();
}

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
    $id_subasta = $_POST['id_subasta'];

    // Actualizar la tabla Subastas
    $fieldsToUpdate = [];

    // Información Básica
    if (!empty($_POST['direccion'])) {
        $fieldsToUpdate['direccion'] = $_POST['direccion'];
    }
    if (!empty($_POST['cp'])) {
        $fieldsToUpdate['cp'] = $_POST['cp'];
    }
    if (!empty($_POST['localidad'])) {
        $fieldsToUpdate['localidad'] = $_POST['localidad'];
    }
    if (!empty($_POST['provincia'])) {
        $fieldsToUpdate['provincia'] = $_POST['provincia'];
    }
    if (!empty($_POST['fecha_inicio'])) {
        $fieldsToUpdate['fecha_inicio'] = $_POST['fecha_inicio'];
    }
    if (!empty($_POST['fecha_conclusion'])) {
        $fieldsToUpdate['fecha_conclusion'] = $_POST['fecha_conclusion'];
    }
    if (!empty($_POST['enlace_subasta'])) {
        $fieldsToUpdate['enlace_subasta'] = $_POST['enlace_subasta'];
    }
    if (!empty($_POST['valor_subasta'])) {
        $fieldsToUpdate['valor_subasta'] = convertirDecimal($_POST['valor_subasta']);
    }
    if (!empty($_POST['tasacion'])) {
        $fieldsToUpdate['tasacion'] = convertirDecimal($_POST['tasacion']);
    }
    if (!empty($_POST['importe_deposito'])) {
        $fieldsToUpdate['importe_deposito'] = convertirDecimal($_POST['importe_deposito']);
    }
    if (!empty($_POST['puja_minima'])) {
        $fieldsToUpdate['puja_minima'] = convertirDecimal($_POST['puja_minima']);
    }
    if (!empty($_POST['tramos_pujas'])) {
        $fieldsToUpdate['tramos_pujas'] = convertirDecimal($_POST['tramos_pujas']);
    }
    if (!empty($_POST['cantidad_reclamada'])) {
        $fieldsToUpdate['cantidad_reclamada'] = convertirDecimal($_POST['cantidad_reclamada']);
    }
    if (!empty($_POST['id_tipo_subasta'])) {
        $fieldsToUpdate['id_tipo_subasta'] = $_POST['id_tipo_subasta'];
    }
    if (!empty($_POST['id_estado'])) {
        $fieldsToUpdate['id_estado'] = $_POST['id_estado'];
    }

    if (!empty($fieldsToUpdate)) {
        $setClause = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setClause[] = "$field = :$field";
        }

        $sql = "UPDATE Subastas SET " . implode(', ', $setClause) . " WHERE id_subasta = :id_subasta";
        $stmt = $conn->prepare($sql);

        foreach ($fieldsToUpdate as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Actualizar la tabla Localizaciones
    $fieldsToUpdate = [];

    if (!empty($_POST['latitud'])) {
        $fieldsToUpdate['latitud'] = $_POST['latitud'];
    }
    if (!empty($_POST['altitud'])) {
        $fieldsToUpdate['altitud'] = $_POST['altitud'];
    }

    if (!empty($fieldsToUpdate)) {
        $setClause = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setClause[] = "$field = :$field";
        }

        $sql = "UPDATE Localizaciones SET " . implode(', ', $setClause) . " WHERE id_subasta = :id_subasta";
        $stmt = $conn->prepare($sql);

        foreach ($fieldsToUpdate as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Actualizar la tabla SubastaDetalles
    $fieldsToUpdate = [];

    if (!empty($_POST['precio_medio'])) {
        $fieldsToUpdate['precio_medio'] = convertirDecimal($_POST['precio_medio']);
    }
    if (!empty($_POST['precio_venta_medio'])) {
        $fieldsToUpdate['precio_venta_medio'] = convertirDecimal($_POST['precio_venta_medio']);
    }
    if (!empty($_POST['puja_mas_alta'])) {
        $fieldsToUpdate['puja_mas_alta'] = convertirDecimal($_POST['puja_mas_alta']);
    }
    if (!empty($_POST['precio_trastero'])) {
        $fieldsToUpdate['precio_trastero'] = convertirDecimal($_POST['precio_trastero']);
    }
    if (!empty($_POST['precio_garaje'])) {
        $fieldsToUpdate['precio_garaje'] = convertirDecimal($_POST['precio_garaje']);
    }
    if (!empty($_FILES['pdf_precios']['name'])) {
        $uploadDir = '../../pdf_compra/';
        $fileName = basename($_FILES['pdf_precios']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['pdf_precios']['tmp_name'], $targetFilePath)) {
            $relativeFilePath = 'assets/pdf_compra/' . $fileName;
            $fieldsToUpdate['url_pdf_precios'] = $relativeFilePath;
        }
    }

    if (!empty($fieldsToUpdate)) {
        $setClause = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setClause[] = "$field = :$field";
        }

        $sql = "UPDATE SubastaDetalles SET " . implode(', ', $setClause) . " WHERE id_subasta = :id_subasta";
        $stmt = $conn->prepare($sql);

        foreach ($fieldsToUpdate as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Actualizar comentarios
    if (isset($_POST['comentarios'])) {
        $comentario = trim($_POST['comentarios']);

        if ($comentario === '') {
            // Si el comentario está vacío, eliminar cualquier comentario existente
            $stmt = $conn->prepare("DELETE FROM Comentarios WHERE id_subasta = :id_subasta");
            $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Comprobar si ya existe un comentario para esta subasta
            $stmt = $conn->prepare("SELECT id_comentario FROM Comentarios WHERE id_subasta = :id_subasta");
            $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
            $stmt->execute();
            $id_comentario_existente = $stmt->fetchColumn();

            if ($id_comentario_existente) {
                // Si ya existe, actualizarlo
                $stmt = $conn->prepare("UPDATE Comentarios SET comentario = :comentario WHERE id_comentario = :id_comentario");
                $stmt->bindValue(':comentario', $comentario, PDO::PARAM_STR);
                $stmt->bindValue(':id_comentario', $id_comentario_existente, PDO::PARAM_INT);
            } else {
                // Si no existe, insertarlo
                $stmt = $conn->prepare("INSERT INTO Comentarios (id_subasta, comentario) VALUES (:id_subasta, :comentario)");
                $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
                $stmt->bindValue(':comentario', $comentario, PDO::PARAM_STR);
            }

            $stmt->execute();
        }
    }

    // Gestionar las imágenes
    if (!empty($_POST['imagen_portada'])) {
        // Usar la tabla PortadaSubasta para establecer la portada
        $stmt = $conn->prepare("DELETE FROM PortadaSubasta WHERE id_subasta = :id_subasta");
        $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO PortadaSubasta (id_subasta, id_imagen) VALUES (:id_subasta, :id_imagen)");
        $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->bindParam(':id_imagen', $_POST['imagen_portada'], PDO::PARAM_INT);
        $stmt->execute();
    }

    if (isset($_FILES['nuevas_imagenes']) && !empty($_FILES['nuevas_imagenes']['name'][0])) {
        // Rutas relativas a partir del directorio actual del script
        $uploadDir = __DIR__ . '/../../../assets/img/VIVIENDAS/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['nuevas_imagenes']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['nuevas_imagenes']['name'][$key]);
            $targetFilePath = $uploadDir . $fileName;

            // Manejo de nombres duplicados
            $fileExtension = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $baseName = pathinfo($targetFilePath, PATHINFO_FILENAME);
            $counter = 1;

            while (file_exists($targetFilePath)) {
                $newFileName = $baseName . '_' . $counter . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $newFileName;
                $counter++;
            }

            // Verificar si la subida fue exitosa
            if (move_uploaded_file($tmpName, $targetFilePath)) {
                // Guardar la ruta relativa en la base de datos
                $relativeFilePath = 'assets/img/VIVIENDAS/' . basename($targetFilePath);
                $stmt = $conn->prepare("INSERT INTO ImagenesSubasta (id_subasta, url_imagen) VALUES (:id_subasta, :url_imagen)");
                $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
                $stmt->bindParam(':url_imagen', $relativeFilePath, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                // Manejo de errores en la subida
                error_log("Error al subir la imagen: " . $_FILES['nuevas_imagenes']['name'][$key]);
            }
        }
    }

    // Eliminar imágenes seleccionadas
    if (!empty($_POST['imagenes_a_eliminar'])) {
        $imagenesAEliminar = explode(',', $_POST['imagenes_a_eliminar']);
        foreach ($imagenesAEliminar as $idImagen) {
            // Obtener la URL de la imagen para eliminar el archivo
            $stmt = $conn->prepare("SELECT url_imagen FROM ImagenesSubasta WHERE id_imagen = :id_imagen");
            $stmt->bindParam(':id_imagen', $idImagen, PDO::PARAM_INT);
            $stmt->execute();
            $imagen = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($imagen && file_exists(__DIR__ . '/../../../' . $imagen['url_imagen'])) {
                unlink(__DIR__ . '/../../../' . $imagen['url_imagen']);  // Eliminar el archivo
            }

            // Luego, eliminar la referencia en la base de datos
            $stmt = $conn->prepare("DELETE FROM ImagenesSubasta WHERE id_imagen = :id_imagen");
            $stmt->bindParam(':id_imagen', $idImagen, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // Gestionar los documentos
    if (isset($_FILES['nuevos_documentos']) && !empty($_FILES['nuevos_documentos']['name'][0])) {
        $uploadDir = __DIR__ . '/../../../documentos/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['nuevos_documentos']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['nuevos_documentos']['name'][$key]);
            $targetFilePath = $uploadDir . $fileName;

            $fileExtension = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            $baseName = pathinfo($targetFilePath, PATHINFO_FILENAME);
            $counter = 1;

            while (file_exists($targetFilePath)) {
                $newFileName = $baseName . '_' . $counter . '.' . $fileExtension;
                $targetFilePath = $uploadDir . $newFileName;
                $counter++;
            }

            if (move_uploaded_file($tmpName, $targetFilePath)) {
                $relativeFilePath = 'assets/documentos/' . $fileName;
                $stmt = $conn->prepare("INSERT INTO Documentos (id_subasta, nombre_documento, url_documento) VALUES (:id_subasta, :nombre_documento, :url_documento)");
                $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
                $stmt->bindParam(':nombre_documento', $fileName);
                $stmt->bindParam(':url_documento', $relativeFilePath);
                $stmt->execute();
            }
        }
    }

    // Eliminar documentos seleccionados
    if (!empty($_POST['documentos_a_eliminar'])) {
        $documentosAEliminar = explode(',', $_POST['documentos_a_eliminar']);
        foreach ($documentosAEliminar as $idDocumento) {
            // Obtener la URL del documento para eliminar el archivo
            $stmt = $conn->prepare("SELECT url_documento FROM Documentos WHERE id_documento = :id_documento");
            $stmt->bindParam(':id_documento', $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
            $documento = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($documento && file_exists(__DIR__ . '/../../../' . $documento['url_documento'])) {
                unlink(__DIR__ . '/../../../' . $documento['url_documento']);  // Eliminar el archivo
            }

            // Eliminar la referencia en la base de datos
            $stmt = $conn->prepare("DELETE FROM Documentos WHERE id_documento = :id_documento");
            $stmt->bindParam(':id_documento', $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // Actualizar la tabla Catastro
    $fieldsToUpdate = [];

    if (!empty($_POST['ref_catastral'])) {
        $fieldsToUpdate['ref_catastral'] = $_POST['ref_catastral'];
    }
    if (!empty($_POST['clase'])) {
        $fieldsToUpdate['clase'] = $_POST['clase'];
    }
    if (!empty($_POST['uso_principal'])) {
        $fieldsToUpdate['uso_principal'] = $_POST['uso_principal'];
    }
    if (!empty($_POST['sup_construida'])) {
        $fieldsToUpdate['sup_construida'] = $_POST['sup_construida'];
    }
    if (!empty($_POST['vivienda'])) {
        $fieldsToUpdate['vivienda'] = $_POST['vivienda'];
    }
    if (!empty($_POST['garaje'])) {
        $fieldsToUpdate['garaje'] = $_POST['garaje'];
    }
    if (!empty($_POST['almacen'])) {
        $fieldsToUpdate['almacen'] = $_POST['almacen'];
    }
    if (!empty($_POST['ano_construccion'])) {
        $fieldsToUpdate['ano_construccion'] = $_POST['ano_construccion'];
    }
    if (!empty($_POST['enlace_catastro'])) {
        $fieldsToUpdate['enlace_catastro'] = $_POST['enlace_catastro'];
    }

    if (!empty($fieldsToUpdate)) {
        $setClause = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setClause[] = "$field = :$field";
        }

        $sql = "UPDATE Catastro SET " . implode(', ', $setClause) . " WHERE id_subasta = :id_subasta";
        $stmt = $conn->prepare($sql);

        foreach ($fieldsToUpdate as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Actualizar la tabla Valoraciones
    $fieldsToUpdate = [];

    if (!empty($_POST['fachada_y_exteriores'])) {
        $fieldsToUpdate['fachada_y_exteriores'] = $_POST['fachada_y_exteriores'];
    }
    if (!empty($_POST['techo_y_canaletas'])) {
        $fieldsToUpdate['techo_y_canaletas'] = $_POST['techo_y_canaletas'];
    }
    if (!empty($_POST['ventanas_y_puerta'])) {
        $fieldsToUpdate['ventanas_y_puerta'] = $_POST['ventanas_y_puerta'];
    }
    if (!empty($_POST['jardin_y_terrenos'])) {
        $fieldsToUpdate['jardin_y_terrenos'] = $_POST['jardin_y_terrenos'];
    }
    if (!empty($_POST['estado_estructuras'])) {
        $fieldsToUpdate['estado_estructuras'] = $_POST['estado_estructuras'];
    }
    if (!empty($_POST['instalaciones_visibles'])) {
        $fieldsToUpdate['instalaciones_visibles'] = $_POST['instalaciones_visibles'];
    }
    if (!empty($_POST['vecindario'])) {
        $fieldsToUpdate['vecindario'] = $_POST['vecindario'];
    }
    if (!empty($_POST['seguridad'])) {
        $fieldsToUpdate['seguridad'] = $_POST['seguridad'];
    }
    if (!empty($_POST['ruido_y_olores'])) {
        $fieldsToUpdate['ruido_y_olores'] = $_POST['ruido_y_olores'];
    }
    if (!empty($_POST['acceso_y_estacionamiento'])) {
        $fieldsToUpdate['acceso_y_estacionamiento'] = $_POST['acceso_y_estacionamiento'];
    }
    if (!empty($_POST['localizacion'])) {
        $fieldsToUpdate['localizacion'] = $_POST['localizacion'];
    }
    if (!empty($_POST['estado_inquilino'])) {
        $fieldsToUpdate['estado_inquilino'] = $_POST['estado_inquilino'];
    }

    if (!empty($fieldsToUpdate)) {
        $setClause = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setClause[] = "$field = :$field";
        }

        $sql = "UPDATE Valoraciones SET " . implode(', ', $setClause) . " WHERE id_subasta = :id_subasta";
        $stmt = $conn->prepare($sql);

        foreach ($fieldsToUpdate as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->execute();
    }

    header('Location: ../../../subastas.php');
    exit();
}
