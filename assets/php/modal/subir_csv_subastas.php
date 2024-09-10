<?php
session_start();

// Verifica si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: index.html');
    exit();
}

include('../database/config.php');

// Función para convertir valores numéricos a formato adecuado para MySQL
function convertirDecimal($valor)
{
    $valor = str_replace(['€', '.', ','], ['', '', '.'], $valor);
    return floatval($valor);
}

// Función para convertir fechas a formato MySQL
function convertirFecha($fecha)
{
    $fechaObj = DateTime::createFromFormat('d/m/Y', $fecha); // Cambio a 'd/m/Y' para el formato correcto
    return $fechaObj ? $fechaObj->format('Y-m-d') : null;
}

// Función para convertir "SI" o "NO" en valores booleanos (0 o 1)
function convertirBooleano($valor)
{
    return strtolower(trim($valor)) === 'si' ? 1 : 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_subastas'])) {
    try {
        // Comprobar si el archivo CSV está cargado sin errores
        if ($_FILES['csv_subastas']['error'] === UPLOAD_ERR_OK) {
            $csvFile = fopen($_FILES['csv_subastas']['tmp_name'], 'r');

            // Saltar la primera línea (encabezados)
            fgetcsv($csvFile, 1000, ';'); // Cambio a punto y coma como delimitador

            // Iniciar transacción
            $conn->beginTransaction();

            // Procesar cada línea del archivo CSV
            while (($row = fgetcsv($csvFile, 1000, ';')) !== false) {
                // Convertir los valores antes de la inserción
                $valor_subasta = convertirDecimal($row[11]);
                $cantidad_reclamada = convertirDecimal($row[12]);
                $tasacion = convertirDecimal($row[13]);
                $importe_deposito = convertirDecimal($row[14]);
                $puja_minima = convertirDecimal($row[15]);
                $tramos_pujas = convertirDecimal($row[16]);
                $precio_medio = convertirDecimal($row[36]);
                $carga_subastas = convertirDecimal($row[12]);

                // Convertir las fechas
                $fecha_inicio = convertirFecha($row[7]);
                $fecha_conclusion = convertirFecha($row[8]);

                if (!$fecha_inicio || !$fecha_conclusion) {
                    throw new Exception("Error: Fecha inicio o fecha conclusión no válida");
                }

                // Insertar en la tabla Subastas
                $stmt = $conn->prepare("
                    INSERT INTO Subastas (direccion, cp, localidad, provincia, fecha_inicio, fecha_conclusion, enlace_subasta, valor_subasta, tasacion, importe_deposito, puja_minima, tramos_pujas, cantidad_reclamada, id_tipo_subasta, id_estado)
                    VALUES (:direccion, :cp, :localidad, :provincia, :fecha_inicio, :fecha_conclusion, :enlace_subasta, :valor_subasta, :tasacion, :importe_deposito, :puja_minima, :tramos_pujas, :cantidad_reclamada, :id_tipo_subasta, :id_estado)
                ");
                $stmt->execute([
                    ':direccion' => $row[1],
                    ':cp' => $row[4],
                    ':localidad' => $row[5],
                    ':provincia' => $row[6],
                    ':fecha_inicio' => $fecha_inicio,
                    ':fecha_conclusion' => $fecha_conclusion,
                    ':enlace_subasta' => $row[10],
                    ':valor_subasta' => $valor_subasta,
                    ':tasacion' => $tasacion,
                    ':importe_deposito' => $importe_deposito,
                    ':puja_minima' => $puja_minima,
                    ':tramos_pujas' => $tramos_pujas,
                    ':cantidad_reclamada' => $cantidad_reclamada,
                    ':id_tipo_subasta' => 1, // Asigna el tipo de subasta correcto según tu lógica
                    ':id_estado' => 2 // Asigna el estado adecuado
                ]);

                $id_subasta = $conn->lastInsertId();

                // Insertar en la tabla Localizaciones
                $stmt = $conn->prepare("
                    INSERT INTO Localizaciones (id_subasta, latitud, altitud)
                    VALUES (:id_subasta, :latitud, :altitud)
                ");
                $stmt->execute([
                    ':id_subasta' => $id_subasta,
                    ':latitud' => $row[2],
                    ':altitud' => $row[3]
                ]);

                // Insertar en la tabla SubastaDetalles
                $stmt = $conn->prepare("
                    INSERT INTO SubastaDetalles (id_subasta, precio_medio, carga_subastas)
                    VALUES (:id_subasta, :precio_medio, :carga_subastas)
                ");
                $stmt->execute([
                    ':id_subasta' => $id_subasta,
                    ':precio_medio' => $precio_medio,
                    ':carga_subastas' => $carga_subastas
                ]);

                // Insertar en la tabla Catastro
                $stmt = $conn->prepare("
                    INSERT INTO Catastro (id_subasta, ref_catastral, clase, uso_principal, sup_construida, vivienda, garaje, almacen, terraza, ano_construccion, enlace_catastro, zonas_comunes)
                    VALUES (:id_subasta, :ref_catastral, :clase, :uso_principal, :sup_construida, :vivienda, :garaje, :almacen, :terraza, :ano_construccion, :enlace_catastro, :zonas_comunes)
                ");
                $stmt->execute([
                    ':id_subasta' => $id_subasta,
                    ':ref_catastral' => $row[25],
                    ':clase' => $row[26],
                    ':uso_principal' => $row[27],
                    ':sup_construida' => $row[28],
                    ':vivienda' => $row[29],
                    ':garaje' => $row[32],
                    ':almacen' => $row[33],
                    ':terraza' => $row[30],
                    ':ano_construccion' => $row[34],
                    ':enlace_catastro' => $row[35],
                    ':zonas_comunes' => $row[31]
                ]);

                // Convertir los valores "SI"/"NO" de las columnas de booleanos
                $piscina = convertirBooleano($row[19]);
                $jardin = convertirBooleano($row[20]);
                $ascensor = convertirBooleano($row[21]);
                $garaje_idealista = convertirBooleano($row[22]);
                $trastero = convertirBooleano($row[23]);

                // Insertar en la tabla SubastaIdealista
                $stmt = $conn->prepare("
                    INSERT INTO SubastaIdealista (id_subasta, habitaciones, banos, piscina, jardin, ascensor, garaje_idealista, trastero, enlace_idealista)
                    VALUES (:id_subasta, :habitaciones, :banos, :piscina, :jardin, :ascensor, :garaje_idealista, :trastero, :enlace_idealista)
                ");
                $stmt->execute([
                    ':id_subasta' => $id_subasta,
                    ':habitaciones' => $row[17],
                    ':banos' => $row[18],
                    ':piscina' => $piscina,
                    ':jardin' => $jardin,
                    ':ascensor' => $ascensor,
                    ':garaje_idealista' => $garaje_idealista,
                    ':trastero' => $trastero,
                    ':enlace_idealista' => $row[24]
                ]);

                // Insertar en la tabla SubastaIdealista
                $stmt = $conn->prepare("
                    INSERT INTO Valoraciones (id_subasta, fachada_y_exteriores, techo_y_canaletas, ventanas_y_puerta, jardin_y_terrenos, estado_estructuras, instalaciones_visibles, vecindario, seguridad, ruido_y_olores, acceso_y_estacionamiento, localizacion, estado_inquilino)
                    VALUES (:id_subasta, :fachada_y_exteriores, :techo_y_canaletas, :ventanas_y_puerta, :jardin_y_terrenos, :estado_estructuras, :instalaciones_visibles, :vecindario, :seguridad, :ruido_y_olores, :acceso_y_estacionamiento, :localizacion, :estado_inquilino)
                ");
                $stmt->execute([
                    ':id_subasta' => $id_subasta,
                    ':fachada_y_exteriores' => $row[37],
                    ':techo_y_canaletas' => $row[38],
                    ':ventanas_y_puerta' => $row[39],
                    ':jardin_y_terrenos' => $row[40],
                    ':estado_estructuras' => $row[41],
                    ':instalaciones_visibles' => $row[42],
                    ':vecindario' => $row[43],
                    ':seguridad' => $row[44],
                    ':ruido_y_olores' => $row[45],
                    ':acceso_y_estacionamiento' => $row[46],
                    ':localizacion' => $row[47],
                    ':estado_inquilino' => $row[48],
                ]);
            }

            // Confirmar la transacción
            $conn->commit();
            fclose($csvFile);

            // Redirigir tras completar la subida
            header('Location: /subastas_fdm/subastas.php');
            exit();
        } else {
            echo "Error al subir el archivo CSV.";
        }
    } catch (PDOException $e) {
        // En caso de error, revertir la transacción
        $conn->rollBack();
        echo "Error al crear subastas: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error al procesar subasta: " . $e->getMessage();
    }
}
