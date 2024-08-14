<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: index.html');
    exit();
}

include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_subasta = $_POST['id_subasta'];

    // Actualizar la tabla Subastas
    $fieldsToUpdate = [];

    if (!empty($_POST['direccion'])) {
        $fieldsToUpdate['direccion'] = $_POST['direccion'];
    }
    if (!empty($_POST['valor_subasta'])) {
        $fieldsToUpdate['valor_subasta'] = $_POST['valor_subasta'];
    }
    if (!empty($_POST['fecha_conclusion'])) {
        $fieldsToUpdate['fecha_conclusion'] = $_POST['fecha_conclusion'];
    }
    if (!empty($_POST['estado_subasta'])) {
        $fieldsToUpdate['estado_subasta'] = $_POST['estado_subasta'];
    }
    if (!empty($_POST['cantidad_reclamada'])) {
        $fieldsToUpdate['cantidad_reclamada'] = $_POST['cantidad_reclamada'];
    }

    if (!empty($fieldsToUpdate)) {
        $setClause = [];
        foreach ($fieldsToUpdate as $field => $value) {
            if ($field == 'estado_subasta') {
                $setClause[] = "id_estado = (SELECT id_estado FROM EstadosSubasta WHERE estado = :$field)";
            } else {
                $setClause[] = "$field = :$field";
            }
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
        $fieldsToUpdate['precio_medio'] = $_POST['precio_medio'];
    }
    if (!empty($_POST['precio_venta_medio'])) {
        $fieldsToUpdate['precio_venta_medio'] = $_POST['precio_venta_medio'];
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
    if (!empty($_POST['tipo_de_vivienda'])) {
        $fieldsToUpdate['tipo_de_vivienda'] = $_POST['tipo_de_vivienda'];
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

    header('Location: subastas.php');
    exit();
}
