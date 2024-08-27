<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../index.html');
    exit();
}

if (isset($_POST['id_subasta'])) {
    $id_subasta = $_POST['id_subasta'];

    include('database/config.php');

    try {
        // Comenzar una transacción
        $conn->beginTransaction();

        // Eliminar registros relacionados en la tabla `ImagenesSubasta`
        $stmt = $conn->prepare("SELECT url_imagen FROM ImagenesSubasta WHERE id_subasta = :id_subasta");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();
        $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eliminar imágenes físicas si existen
        foreach ($imagenes as $imagen) {
            if (file_exists('../' . $imagen['url_imagen'])) {
                unlink('../' . $imagen['url_imagen']);
            }
        }

        // Eliminar las entradas de la tabla `ImagenesSubasta`
        $conn->prepare("DELETE FROM ImagenesSubasta WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Eliminar registros relacionados en la tabla `VideosSubasta`
        $stmt = $conn->prepare("SELECT url_video FROM VideosSubasta WHERE id_subasta = :id_subasta");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();
        $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eliminar videos físicos si existen
        foreach ($videos as $video) {
            if (file_exists('../' . $video['url_video'])) {
                unlink('../' . $video['url_video']);
            }
        }

        // Eliminar las entradas de la tabla `VideosSubasta`
        $conn->prepare("DELETE FROM VideosSubasta WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Eliminar registros relacionados en la tabla `documentos`
        $stmt = $conn->prepare("SELECT url_documento FROM documentos WHERE id_subasta = :id_subasta");
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->execute();
        $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Eliminar documentos físicos si existen
        foreach ($documentos as $documento) {
            if (file_exists('../' . $documento['url_documento'])) {
                unlink('../' . $documento['url_documento']);
            }
        }

        // Eliminar las entradas de la tabla `documentos`
        $conn->prepare("DELETE FROM documentos WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Eliminar registros relacionados en la tabla `catastro`
        $conn->prepare("DELETE FROM catastro WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Eliminar registros relacionados en la tabla `localizaciones`
        $conn->prepare("DELETE FROM localizaciones WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Eliminar registros relacionados en la tabla `valoraciones`
        $conn->prepare("DELETE FROM valoraciones WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Eliminar registros relacionados en la tabla `PortadaSubasta`
        $conn->prepare("DELETE FROM PortadaSubasta WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Eliminar registros relacionados en la tabla `SubastaDetalles`
        $conn->prepare("DELETE FROM SubastaDetalles WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Finalmente, eliminar la entrada de la tabla `Subastas`
        $conn->prepare("DELETE FROM Subastas WHERE id_subasta = :id_subasta")->execute([':id_subasta' => $id_subasta]);

        // Confirmar la transacción
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID de subasta no proporcionado']);
}
