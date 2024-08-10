<?php
include('config.php'); // Asegúrate de que este archivo contenga la conexión PDO

$secret_token = '12345';  // Define tu token

if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    die('Acceso no autorizado');
}


/*
 * Script para insertar una nueva subasta en la base de datos.
 * 
 * Instrucciones de uso:
 * 
 * 1. Edita los valores de las variables en la sección "Datos de la nueva subasta".
 *    - Cambia los valores como dirección, coordenadas, precios, imágenes, etc., para reflejar la nueva subasta.
 * 
 * 2. Guarda este archivo en el servidor, por ejemplo como 'insert_subasta.php'.
 * 
 * 3. Ejecuta el script:
 *    - Puedes hacerlo a través de un navegador: http://tusitio.com/insert_subasta.php
 *    - También puedes ejecutarlo desde la línea de comandos si tienes acceso a PHP CLI:
 *      php insert_subasta.php
 * 
 * 4. Una vez que la subasta se ha insertado correctamente, es recomendable eliminar este archivo
 *    del servidor o restringir su acceso para evitar su ejecución no autorizada.
 * 
 * Seguridad:
 * - Asegúrate de proteger este archivo para evitar que alguien pueda ejecutarlo sin autorización.
 * - Revisa la sección "Protección del archivo" en este script para más detalles sobre cómo protegerlo.
 */

// Datos de la nueva subasta
$direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE';
$ref_catastral = '4146502QA4544E0129WJ';
$clase = 'URBANO';
$uso_principal = 'RESIDENCIAL';
$sup_construida = 92;
$vivienda = 82;
$garaje = 0;
$almacen = 0;
$ano_construccion = 2003;
$enlace_catastro = 'https://acortar.link/5MBhfg';
$latitud = 36.604440;
$altitud = -6.272388;
$tipo_subasta = 'SUBASTA BOE';
$precio_medio_min = 2700.00;
$precio_venta_min = 248400.00;
$precio_medio = 3000.00;
$precio_venta_medio = 276000.00;
$precio_max = 3300.00;
$precio_venta_max = 303600.00;
$fecha_inicio = '2024-07-19';
$fecha_conclusion = '2024-08-08';
$enlace_subasta = 'https://acortar.link/7GWoA2';
$valor_subasta = 152000.00;
$tasacion = 0.00;
$importe_deposito = 7600.00;
$puja_minima = 'Sin puja mínima';
$tramos_pujas = 3040.00;
$usuario = 'admin';
$fachada_y_exteriores = 10;
$techo_y_canaletas = 10;
$ventanas_y_puerta = 8;
$jardin_y_terrenos = 10;
$estado_estructuras = 9;
$instalaciones_visibles = 10;
$vecindario = 9;
$seguridad = 9;
$ruido_y_olores = 9;
$acceso_y_estacionamiento = 9;
$localizacion = 7;
$estado_inquilino = 9;
$tipo_de_vivienda = 3;
$puntuacion_final = 8.58;

// Imágenes de la subasta
$imagenes = [
    ['url' => '/assets/img/ANCORA/foto-vivienda-1.png', 'descripcion' => 'Fachada principal'],
    ['url' => '/assets/img/ANCORA/foto-vivienda-2.png', 'descripcion' => 'Vista lateral'],
    ['url' => '/assets/img/ANCORA/foto-vivienda-3.png', 'descripcion' => 'Interior del salón'],
    ['url' => '/assets/img/ANCORA/foto-vivienda-4.png', 'descripcion' => 'Cocina']
];

try {
    // Empezar transacción
    $conn->beginTransaction();

    // Insertar tipo de subasta si no existe
    $stmt = $conn->prepare("INSERT INTO TiposSubasta (tipo_subasta) SELECT :tipo_subasta WHERE NOT EXISTS (SELECT 1 FROM TiposSubasta WHERE tipo_subasta = :tipo_subasta)");
    $stmt->bindParam(':tipo_subasta', $tipo_subasta);
    $stmt->execute();

    // Insertar subasta
    $stmt = $conn->prepare("
        INSERT INTO Subastas (
            direccion, cp, localidad, provincia, fecha_inicio, fecha_conclusion, enlace_subasta, valor_subasta, tasacion, importe_deposito, puja_minima, tramos_pujas, id_usuario, id_tipo_subasta
        ) VALUES (
            :direccion, '11500', 'PUERTO SANTA MARÍA', 'CÁDIZ', :fecha_inicio, :fecha_conclusion, :enlace_subasta, :valor_subasta, :tasacion, :importe_deposito, :puja_minima, :tramos_pujas,
            (SELECT id_usuario FROM USUARIOS WHERE usuario = :usuario),
            (SELECT id_tipo_subasta FROM TiposSubasta WHERE tipo_subasta = :tipo_subasta)
        )
    ");
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_conclusion', $fecha_conclusion);
    $stmt->bindParam(':enlace_subasta', $enlace_subasta);
    $stmt->bindParam(':valor_subasta', $valor_subasta);
    $stmt->bindParam(':tasacion', $tasacion);
    $stmt->bindParam(':importe_deposito', $importe_deposito);
    $stmt->bindParam(':puja_minima', $puja_minima);
    $stmt->bindParam(':tramos_pujas', $tramos_pujas);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':tipo_subasta', $tipo_subasta);
    $stmt->execute();

    // Obtener id de la subasta recién insertada
    $id_subasta = $conn->lastInsertId();

    // Insertar datos del Catastro
    $stmt = $conn->prepare("
        INSERT INTO Catastro (id_subasta, ref_catastral, clase, uso_principal, sup_construida, vivienda, garaje, almacen, ano_construccion, enlace_catastro)
        VALUES (:id_subasta, :ref_catastral, :clase, :uso_principal, :sup_construida, :vivienda, :garaje, :almacen, :ano_construccion, :enlace_catastro)
    ");
    $stmt->bindParam(':id_subasta', $id_subasta);
    $stmt->bindParam(':ref_catastral', $ref_catastral);
    $stmt->bindParam(':clase', $clase);
    $stmt->bindParam(':uso_principal', $uso_principal);
    $stmt->bindParam(':sup_construida', $sup_construida);
    $stmt->bindParam(':vivienda', $vivienda);
    $stmt->bindParam(':garaje', $garaje);
    $stmt->bindParam(':almacen', $almacen);
    $stmt->bindParam(':ano_construccion', $ano_construccion);
    $stmt->bindParam(':enlace_catastro', $enlace_catastro);
    $stmt->execute();

    // Insertar imágenes de la subasta
    $stmt = $conn->prepare("
        INSERT INTO ImagenesSubasta (id_subasta, url_imagen, descripcion)
        VALUES (:id_subasta, :url_imagen, :descripcion)
    ");
    foreach ($imagenes as $imagen) {
        $stmt->bindParam(':id_subasta', $id_subasta);
        $stmt->bindParam(':url_imagen', $imagen['url']);
        $stmt->bindParam(':descripcion', $imagen['descripcion']);
        $stmt->execute();
    }

    // Insertar localización
    $stmt = $conn->prepare("
        INSERT INTO Localizaciones (id_subasta, latitud, altitud)
        VALUES (:id_subasta, :latitud, :altitud)
    ");
    $stmt->bindParam(':id_subasta', $id_subasta);
    $stmt->bindParam(':latitud', $latitud);
    $stmt->bindParam(':altitud', $altitud);
    $stmt->execute();

    // Insertar detalles de la subasta
    $stmt = $conn->prepare("
        INSERT INTO SubastaDetalles (id_subasta, id_tipo_subasta, precio_medio_min, precio_venta_min, precio_medio, precio_venta_medio, precio_max, precio_venta_max)
        VALUES (:id_subasta, (SELECT id_tipo_subasta FROM TiposSubasta WHERE tipo_subasta = :tipo_subasta), :precio_medio_min, :precio_venta_min, :precio_medio, :precio_venta_medio, :precio_max, :precio_venta_max)
    ");
    $stmt->bindParam(':id_subasta', $id_subasta);
    $stmt->bindParam(':tipo_subasta', $tipo_subasta);
    $stmt->bindParam(':precio_medio_min', $precio_medio_min);
    $stmt->bindParam(':precio_venta_min', $precio_venta_min);
    $stmt->bindParam(':precio_medio', $precio_medio);
    $stmt->bindParam(':precio_venta_medio', $precio_venta_medio);
    $stmt->bindParam(':precio_max', $precio_max);
    $stmt->bindParam(':precio_venta_max', $precio_venta_max);
    $stmt->execute();

    // Insertar valoraciones
    $stmt = $conn->prepare("
        INSERT INTO Valoraciones (id_subasta, fachada_y_exteriores, techo_y_canaletas, ventanas_y_puerta, jardin_y_terrenos, estado_estructuras, instalaciones_visibles, vecindario, seguridad, ruido_y_olores, acceso_y_estacionamiento, localizacion, estado_inquilino, tipo_de_vivienda, puntuacion_final)
        VALUES (:id_subasta, :fachada_y_exteriores, :techo_y_canaletas, :ventanas_y_puerta, :jardin_y_terrenos, :estado_estructuras, :instalaciones_visibles, :vecindario, :seguridad, :ruido_y_olores, :acceso_y_estacionamiento, :localizacion, :estado_inquilino, :tipo_de_vivienda, :puntuacion_final)
    ");
    $stmt->bindParam(':id_subasta', $id_subasta);
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
    $stmt->execute();

    // Confirmar transacción
    $conn->commit();

    echo "Subasta insertada exitosamente.";
} catch (PDOException $e) {
    // Revertir cambios en caso de error
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}

unlink(__FILE__);