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

function convertirDecimal($valor)
{
    // Eliminar posibles espacios en blanco y símbolos de moneda
    $valor = trim(str_replace(['€', ' '], '', $valor));

    // Reemplazar coma decimal por un punto decimal, si existe
    if (strpos($valor, ',') !== false) {
        $valor = str_replace('.', '', $valor);  // Eliminar separadores de miles si existen
        $valor = str_replace(',', '.', $valor); // Reemplazar la coma decimal por un punto
    }

    // Asegurarse de que el valor sea un número válido
    if (is_numeric($valor)) {
        return floatval($valor);
    } else {
        // En caso de que no sea numérico, devolver 0.0 como valor por defecto
        return 0.0;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_subasta = $_POST['id_subasta'];

    // Actualizar la tabla Subastas
    $fieldsToUpdate = [];

    // Construir un array con los campos y sus valores correspondientes
    $campos = [
        'direccion',
        'cp',
        'localidad',
        'provincia',
        'fecha_inicio',
        'fecha_conclusion',
        'enlace_subasta',
        'valor_subasta',
        'tasacion',
        'importe_deposito',
        'puja_minima',
        'tramos_pujas',
        'cantidad_reclamada',
        'id_tipo_subasta',
        'id_estado'
    ];

    foreach ($campos as $campo) {
        if (!empty($_POST[$campo])) {
            if (in_array($campo, ['valor_subasta', 'tasacion', 'importe_deposito', 'puja_minima', 'tramos_pujas', 'cantidad_reclamada'])) {
                $fieldsToUpdate[$campo] = convertirDecimal($_POST[$campo]);
            } else {
                $fieldsToUpdate[$campo] = $_POST[$campo];
            }
        }
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
    $camposDetalles = [
        'precio_medio',
        'puja_mas_alta',
        'carga_subastas'
    ];

    foreach ($camposDetalles as $campo) {
        if (!empty($_POST[$campo])) {
            $fieldsToUpdate[$campo] = convertirDecimal($_POST[$campo]);
        }
    }

    if (!empty($_FILES['pdf_precios']['name'])) {
        $uploadDir = __DIR__ . '/../../../assets/pdf_compra/';
        $fileName = basename($_FILES['pdf_precios']['name']);
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['pdf_precios']['tmp_name'], $targetFilePath)) {
            $relativeFilePath = 'assets/pdf_compra/' . $fileName;
            $fieldsToUpdate['url_pdf_precios'] = $relativeFilePath;
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al subir el PDF de precios.']);
            exit();
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

    // Actualizar la tabla SubastaIdealista
    $fieldsToUpdate = [];
    $camposIdealista = [
        'habitaciones',
        'banos',
        'piscina',
        'jardin',
        'ascensor',
        'garaje_idealista',
        'trastero',
        'enlace_idealista'
    ];

    foreach ($camposIdealista as $campo) {
        if (isset($_POST[$campo])) {
            // Los campos de tipo checkbox (piscina, jardin, ascensor, garaje, trastero) se deben procesar
            if (in_array($campo, ['piscina', 'jardin', 'ascensor', 'garaje_idealista', 'trastero'])) {
                $fieldsToUpdate[$campo] = isset($_POST[$campo]) ? 1 : 0;
            } else {
                $fieldsToUpdate[$campo] = $_POST[$campo];
            }
        } else {
            // Para checkboxes no enviados (no seleccionados)
            if (in_array($campo, ['piscina', 'jardin', 'ascensor', 'garaje_idealista', 'trastero'])) {
                $fieldsToUpdate[$campo] = 0;
            }
        }
    }


    if (!empty($fieldsToUpdate)) {
        $setClause = [];
        foreach ($fieldsToUpdate as $field => $value) {
            $setClause[] = "$field = :$field";
        }

        $sql = "UPDATE SubastaIdealista SET " . implode(', ', $setClause) . " WHERE id_subasta = :id_subasta";
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
            $stmt = $conn->prepare("DELETE FROM Comentarios WHERE id_subasta = :id_subasta");
            $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("SELECT id_comentario FROM Comentarios WHERE id_subasta = :id_subasta");
            $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
            $stmt->execute();
            $id_comentario_existente = $stmt->fetchColumn();

            if ($id_comentario_existente) {
                $stmt = $conn->prepare("UPDATE Comentarios SET comentario = :comentario WHERE id_comentario = :id_comentario");
                $stmt->bindValue(':comentario', $comentario, PDO::PARAM_STR);
                $stmt->bindValue(':id_comentario', $id_comentario_existente, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare("INSERT INTO Comentarios (id_subasta, comentario) VALUES (:id_subasta, :comentario)");
                $stmt->bindValue(':id_subasta', $id_subasta, PDO::PARAM_INT);
                $stmt->bindValue(':comentario', $comentario, PDO::PARAM_STR);
            }
            $stmt->execute();
        }
    }

    $nuevasMediaIds = []; // IDs de las nuevas imágenes y videos subidos

    // Subir nuevas imágenes
    if (isset($_FILES['nuevas_imagenes']) && !empty($_FILES['nuevas_imagenes']['name'][0])) {
        // Usar __DIR__ para obtener la ruta absoluta correcta en el servidor
        $uploadDir = __DIR__ . '/../../../assets/img/VIVIENDAS/';

        // Verificar si el directorio de carga existe, y si no, crearlo
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                echo json_encode(['success' => false, 'error' => 'Error al crear el directorio de imágenes.']);
                exit();
            }
        }

        foreach ($_FILES['nuevas_imagenes']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['nuevas_imagenes']['name'][$key]);
            $targetFilePath = $uploadDir . $fileName;

            // Verificar si es una imagen
            $fileType = mime_content_type($tmpName);
            if (strpos($fileType, 'image') === 0) {
                // Si el archivo ya existe, omitir la subida
                if (file_exists($targetFilePath)) {
                    continue; // Saltar este archivo si ya existe
                }

                $fileExtension = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $baseName = pathinfo($targetFilePath, PATHINFO_FILENAME);
                $counter = 1;

                // Si el archivo ya existe, agregar un número al nombre
                while (file_exists($targetFilePath)) {
                    $newFileName = $baseName . '_' . $counter . '.' . $fileExtension;
                    $targetFilePath = $uploadDir . $newFileName;
                    $counter++;
                }

                // Mover el archivo subido al directorio de destino
                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    // Guardar la ruta relativa en la base de datos
                    $relativeFilePath = 'assets/img/VIVIENDAS/' . basename($targetFilePath);
                    $stmt = $conn->prepare("INSERT INTO ImagenesSubasta (id_subasta, url_imagen) VALUES (:id_subasta, :url_imagen)");
                    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
                    $stmt->bindParam(':url_imagen', $relativeFilePath, PDO::PARAM_STR);
                    $stmt->execute();
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al mover la imagen al directorio de destino.']);
                    exit();
                }
            }
        }
    }


    // Subir nuevos videos
    if (isset($_FILES['nuevos_videos']) && !empty($_FILES['nuevos_videos']['name'][0])) {
        $uploadDir = __DIR__ . '/../../../assets/videos/VIVIENDAS/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['nuevos_videos']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['nuevos_videos']['name'][$key]);
            $targetFilePath = $uploadDir . $fileName;

            $fileType = mime_content_type($tmpName);
            if (strpos($fileType, 'video') === 0) {
                $fileExtension = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                $baseName = pathinfo($targetFilePath, PATHINFO_FILENAME);
                $counter = 1;

                while (file_exists($targetFilePath)) {
                    $newFileName = $baseName . '_' . $counter . '.' . $fileExtension;
                    $targetFilePath = $uploadDir . $newFileName;
                    $counter++;
                }

                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    $relativeFilePath = 'assets/videos/VIVIENDAS/' . basename($targetFilePath);
                    $stmt = $conn->prepare("INSERT INTO VideosSubasta (id_subasta, url_video) VALUES (:id_subasta, :url_video)");
                    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
                    $stmt->bindParam(':url_video', $relativeFilePath, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
        }
    }

    $media = []; // Inicializar la variable media

    // Obtener las imágenes y videos existentes
    $stmt = $conn->prepare("SELECT id_imagen, url_imagen FROM ImagenesSubasta WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    $imagenesExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT id_video, url_video FROM VideosSubasta WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    $videosExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combinar imágenes y videos existentes con los nuevos
    $media = array_merge($imagenesExistentes, $videosExistentes, $nuevasMediaIds);

    if (!empty($media)) {
        echo json_encode(['success' => true, 'media' => $media]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se subieron nuevos archivos.']);
    }

    // Eliminar imágenes seleccionadas
    if (!empty($_POST['imagenes_a_eliminar'])) {
        $imagenesAEliminar = explode(',', $_POST['imagenes_a_eliminar']);
        foreach ($imagenesAEliminar as $idImagen) {
            $stmt = $conn->prepare("SELECT url_imagen FROM ImagenesSubasta WHERE id_imagen = :id_imagen");
            $stmt->bindParam(':id_imagen', $idImagen, PDO::PARAM_INT);
            $stmt->execute();
            $imagen = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($imagen && file_exists(__DIR__ . '/../../../' . $imagen['url_imagen'])) {
                unlink(__DIR__ . '/../../../' . $imagen['url_imagen']);
            }

            $stmt = $conn->prepare("DELETE FROM ImagenesSubasta WHERE id_imagen = :id_imagen");
            $stmt->bindParam(':id_imagen', $idImagen, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // Eliminar videos seleccionados
    if (!empty($_POST['videos_a_eliminar'])) {
        $videosAEliminar = explode(',', $_POST['videos_a_eliminar']);
        foreach ($videosAEliminar as $idVideo) {
            $stmt = $conn->prepare("SELECT url_video FROM VideosSubasta WHERE id_video = :id_video");
            $stmt->bindParam(':id_video', $idVideo, PDO::PARAM_INT);
            $stmt->execute();
            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($video && file_exists(__DIR__ . '/../../../' . $video['url_video'])) {
                unlink(__DIR__ . '/../../../' . $video['url_video']);
            }

            $stmt = $conn->prepare("DELETE FROM VideosSubasta WHERE id_video = :id_video");
            $stmt->bindParam(':id_video', $idVideo, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // Eliminar imágenes y videos seleccionados combinados
    if (!empty($_POST['media_a_eliminar'])) {
        $mediaAEliminar = explode(',', $_POST['media_a_eliminar']);
        foreach ($mediaAEliminar as $idMedia) {
            // Eliminar imagen
            $stmt = $conn->prepare("SELECT url_imagen FROM ImagenesSubasta WHERE id_imagen = :id_imagen");
            $stmt->bindParam(':id_imagen', $idMedia, PDO::PARAM_INT);
            $stmt->execute();
            $imagen = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($imagen && file_exists(__DIR__ . '/../../../' . $imagen['url_imagen'])) {
                unlink(__DIR__ . '/../../../' . $imagen['url_imagen']);
            }

            $stmt = $conn->prepare("DELETE FROM ImagenesSubasta WHERE id_imagen = :id_imagen");
            $stmt->bindParam(':id_imagen', $idMedia, PDO::PARAM_INT);
            $stmt->execute();

            // Eliminar video
            $stmt = $conn->prepare("SELECT url_video FROM VideosSubasta WHERE id_video = :id_video");
            $stmt->bindParam(':id_video', $idMedia, PDO::PARAM_INT);
            $stmt->execute();
            $video = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($video && file_exists(__DIR__ . '/../../../' . $video['url_video'])) {
                unlink(__DIR__ . '/../../../' . $video['url_video']);
            }

            $stmt = $conn->prepare("DELETE FROM VideosSubasta WHERE id_video = :id_video");
            $stmt->bindParam(':id_video', $idMedia, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // Establecer imagen de portada
    if (!empty($_POST['imagen_portada'])) {
        $id_imagen_portada = $_POST['imagen_portada'];

        $stmt = $conn->prepare("DELETE FROM PortadaSubasta WHERE id_subasta = :id_subasta");
        $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO PortadaSubasta (id_subasta, id_imagen) VALUES (:id_subasta, :id_imagen)");
        $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
        $stmt->bindParam(':id_imagen', $id_imagen_portada, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Gestionar los documentos
    if (isset($_FILES['nuevos_documentos']) && !empty($_FILES['nuevos_documentos']['name'][0])) {
        $uploadDir = __DIR__ . '/../../../assets/documentos/';
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
                $relativeFilePath = 'assets/documentos/' . basename($targetFilePath);
                $stmt = $conn->prepare("INSERT INTO Documentos (id_subasta, nombre_documento, url_documento) VALUES (:id_subasta, :nombre_documento, :url_documento)");
                $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
                $stmt->bindParam(':nombre_documento', $fileName);
                $stmt->bindParam(':url_documento', $relativeFilePath, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al subir el documento.']);
                exit();
            }
        }
    }

    if (!empty($_POST['documentos_a_eliminar'])) {
        $documentosAEliminar = explode(',', $_POST['documentos_a_eliminar']);
        foreach ($documentosAEliminar as $idDocumento) {
            $stmt = $conn->prepare("SELECT url_documento FROM Documentos WHERE id_documento = :id_documento");
            $stmt->bindParam(':id_documento', $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
            $documento = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($documento && file_exists(__DIR__ . '/../../../' . $documento['url_documento'])) {
                unlink(__DIR__ . '/../../../' . $documento['url_documento']);
            }

            $stmt = $conn->prepare("DELETE FROM Documentos WHERE id_documento = :id_documento");
            $stmt->bindParam(':id_documento', $idDocumento, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // Actualizar la tabla Catastro
    $fieldsToUpdate = [];
    $camposCatastro = [
        'ref_catastral',
        'clase',
        'uso_principal',
        'sup_construida',
        'vivienda',
        'terraza',
        'garaje',
        'almacen',
        'ano_construccion',
        'enlace_catastro',
        'zonas_comunes'
    ];

    foreach ($camposCatastro as $campo) {
        if (!empty($_POST[$campo])) {
            // Usar convertirDecimal para campos que deben ser decimales
            if (in_array($campo, ['sup_construida', 'vivienda', 'terraza', 'garaje', 'almacen'])) {
                $fieldsToUpdate[$campo] = convertirDecimal($_POST[$campo]);
            } else {
                $fieldsToUpdate[$campo] = $_POST[$campo];  // No aplicar conversión a campos que no son decimales
            }
        }
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
    $camposValoraciones = [
        'fachada_y_exteriores',
        'techo_y_canaletas',
        'ventanas_y_puerta',
        'jardin_y_terrenos',
        'estado_estructuras',
        'instalaciones_visibles',
        'vecindario',
        'seguridad',
        'ruido_y_olores',
        'acceso_y_estacionamiento',
        'localizacion',
        'estado_inquilino'
    ];

    foreach ($camposValoraciones as $campo) {
        if (!empty($_POST[$campo])) {
            $fieldsToUpdate[$campo] = $_POST[$campo];
        }
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
