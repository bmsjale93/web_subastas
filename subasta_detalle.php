<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

include('assets/php/database/config.php');

$id_subasta = $_GET['id'] ?? null;

if (!$id_subasta) {
    echo "No se proporcionó un ID de subasta.";
    exit();
}

try {
    $stmt = $conn->prepare("SELECT s.*, c.*, v.*, l.*, 
                               sd.precio_medio, sd.precio_venta_medio, 
                               sd.url_pdf_precios, sd.puja_mas_alta, 
                               sd.precio_trastero, sd.precio_garaje,
                               ts.tipo_subasta, s.cantidad_reclamada
                        FROM Subastas s
                        LEFT JOIN Catastro c ON s.id_subasta = c.id_subasta
                        LEFT JOIN Valoraciones v ON s.id_subasta = v.id_subasta
                        LEFT JOIN Localizaciones l ON s.id_subasta = l.id_subasta
                        LEFT JOIN SubastaDetalles sd ON s.id_subasta = sd.id_subasta
                        LEFT JOIN TiposSubasta ts ON s.id_tipo_subasta = ts.id_tipo_subasta
                        WHERE s.id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    $subasta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subasta) {
        echo "No se encontró la subasta.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit();
}

function getSubastaMedia($conn, $id_subasta)
{
    $stmt_images = $conn->prepare("SELECT url_imagen FROM ImagenesSubasta WHERE id_subasta = :id_subasta");
    $stmt_images->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt_images->execute();
    $images = $stmt_images->fetchAll(PDO::FETCH_COLUMN);

    $stmt_videos = $conn->prepare("SELECT url_video FROM VideosSubasta WHERE id_subasta = :id_subasta");
    $stmt_videos->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt_videos->execute();
    $videos = $stmt_videos->fetchAll(PDO::FETCH_COLUMN);

    $media = [];
    foreach ($images as $image) {
        $media[] = ['src' => htmlspecialchars($image), 'type' => 'image'];
    }
    foreach ($videos as $video) {
        $media[] = ['src' => htmlspecialchars($video), 'type' => 'video'];
    }

    return $media;
}

$mediaItems = getSubastaMedia($conn, $id_subasta);

function getSubastaDocuments($conn, $id_subasta)
{
    $stmt = $conn->prepare("SELECT nombre_documento, url_documento FROM Documentos WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$documents = getSubastaDocuments($conn, $id_subasta);

// Obtener comentarios
function getComentariosSubasta($conn, $id_subasta)
{
    $stmt = $conn->prepare("SELECT comentario FROM Comentarios WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$comentarios = getComentariosSubasta($conn, $id_subasta);


// Verificación de coordenadas
$latitud = $subasta['latitud'];
$altitud = $subasta['altitud'];

if (!is_numeric($latitud) || !is_numeric($altitud)) {
    echo "Las coordenadas no son válidas.";
    exit();
}

// Extracción de valores para la valoración de la vivienda
$fachada_y_exteriores = $subasta['fachada_y_exteriores'] ?? 0;
$techo_y_canaletas = $subasta['techo_y_canaletas'] ?? 0;
$ventanas_y_puertas = $subasta['ventanas_y_puerta'] ?? 0;
$jardin_y_terrenos = $subasta['jardin_y_terrenos'] ?? 0;
$estado_estructuras = $subasta['estado_estructuras'] ?? 0;
$instalaciones_visibles = $subasta['instalaciones_visibles'] ?? 0;

$vecindario = $subasta['vecindario'] ?? 0;
$seguridad = $subasta['seguridad'] ?? 0;
$ruido_y_olores = $subasta['ruido_y_olores'] ?? 0;
$acceso_y_estacionamiento = $subasta['acceso_y_estacionamiento'] ?? 0;
$localizacion = $subasta['localizacion'] ?? 0;
$estado_inquilino = $subasta['estado_inquilino'] ?? 0;

$puja_mas_alta = $subasta['puja_mas_alta'];
$precio_venta_estimado = ($subasta['precio_medio'] ?? 0) * ($subasta['vivienda'] ?? 0);

// Generar la ruta completa para el PDF de precios
$url_pdf_precios = $subasta['url_pdf_precios'] ? str_replace('../../', 'assets/', $subasta['url_pdf_precios']) : null;

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Subasta</title>
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-md py-4 flex items-center justify-between px-4">
        <a href="subastas.php" class="text-black p-2 rounded-full hover:bg-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="flex-grow text-center">
            <img src="assets/img/logo-fdm.png" alt="Logo" class="mx-auto" width="80" height="80">
        </div>
        <div class="w-6 h-6"></div>
    </header>

    <main class="container mx-auto p-4 py-10">
        <!-- Titulo con dirección de la subasta -->
        <div class="row">
            <div class="col-12 mt-8">
                <h2 class="text-left text-3xl font-bold text-gray-700 mb-4">
                    <?= htmlspecialchars($subasta['direccion']) ?>
                </h2>
            </div>
        </div>

        <!-- Cuatro divs con la información de la subasta -->
        <div class="row text-center mb-6">
            <!-- Subasta Actual -->
            <div class="col-12 col-xl-3 col-lg-6 mb-4 mb-lg-0">
                <div class="bg-gray-400 text-white py-6 rounded-xl shadow-md hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                    <h2 class="text-lg font-bold">SUBASTA ACTUAL</h2>
                    <p class="text-2xl font-semibold"><?= number_format($puja_mas_alta, 2, ',', '.') ?> €</p>
                </div>
            </div>
            <!-- Cierre de la Subasta -->
            <div class="col-12 col-xl-3 col-lg-6 mb-4 mb-xl-0">
                <div class="bg-white text-gray-400 py-6 rounded-xl shadow-md hover:shadow-xl hover:text-gray-500 transition duration-300 ease-in-out">
                    <h2 class="text-lg font-bold">CIERRE SUBASTA</h2>
                    <div id="countdown-container" class="text-2xl font-semibold"></div>
                </div>
            </div>
            <!-- Valor de la Subasta -->
            <div class="col-12 col-xl-3 col-lg-6 mb-4 mb-lg-0">
                <div class="bg-gray-400 text-white py-6 rounded-xl shadow-md hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                    <h2 class="text-lg font-bold">VALOR DE LA SUBASTA</h2>
                    <p class="text-2xl font-semibold"><?= number_format($subasta['valor_subasta'], 2, ',', '.') ?> €</p>
                </div>
            </div>
            <!-- Venta Estimada -->
            <div class="col-12 col-xl-3 col-lg-6 mb-4 mb-lg-0">
                <div class="bg-white text-gray-400 py-6 rounded-xl shadow-md hover:shadow-xl hover:text-gray-500 transition duration-300 ease-in-out">
                    <h2 class="text-lg font-bold">VENTA ESTIMADA</h2>
                    <p class="text-2xl font-semibold"><?= number_format($precio_venta_estimado, 2, ',', '.') ?> €</p>
                </div>
            </div>
        </div>

        <div class="row text-center mb-6">
            <!-- Galería de imágenes -->
            <div class="col-12 col-xl-6">
                <div id="gallery-container" class="mt-4"></div>
            </div>
            <!-- Localización Subasta -->
            <div class="col-12 col-xl-6">
                <div id="mapContainer" class="rounded-lg overflow-hidden mt-4 shadow-lg" style="height: 660px;"></div>
            </div>
        </div>

        <div class="row text-center mt-2">
            <div class="col-12 col-xl-6">
                <h2 class="text-2xl font-bold py-4 px-6 text-gray-800">CALCULADORA SUBASTAS</h2>
                <div class="row">
                    <!-- Datos del Cálculo -->
                    <div class="col-12 col-md-6 mb-6">
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">DATOS DEL CÁLCULO</h4>
                        <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-center hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            <div class="mb-3">
                                <label for="precio-vivienda" class="form-label text-sm font-medium">Precio m² Vivienda + Zonas Comunes:</label>
                                <input type="text" class="form-control" id="precio-vivienda" value="<?= number_format($subasta['precio_medio'], 2, ',', '.') ?> €">
                            </div>
                            <div class="mb-3">
                                <label for="precio-trastero" class="form-label text-sm font-medium">Precio m² Trastero:</label>
                                <input type="text" class="form-control" id="precio-trastero" value="<?= number_format($subasta['precio_trastero'], 2, ',', '.') ?> €">
                            </div>
                            <div class="mb-3">
                                <label for="precio-garaje" class="form-label text-sm font-medium">Precio m² Garaje:</label>
                                <input type="text" class="form-control" id="precio-garaje" value="<?= number_format($subasta['precio_garaje'], 2, ',', '.') ?> €">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-sm font-medium">Precio Venta Estimada:</label>
                                <p id="precio-venta" class="text-lg text-white font-semibold mb-3 bg-gray-400 py-2 rounded-lg"><?= number_format($precio_venta_estimado, 2, ',', '.') ?> €</p>
                            </div>
                            <button id="recalcular-btn" class="bg-blue-800 w-100 text-md text-white py-2 rounded-lg font-bold hover:bg-blue-600 transition duration-300 ease-in-out mb-1">RECALCULAR VALORES</button>
                        </div>
                    </div>
                    <!-- Costes Asociados -->
                    <div class="col-12 col-md-6">
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">COSTES ASOCIADOS</h4>
                        <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            <!-- Compra Vivienda -->
                            <div class="mb-4">
                                <h5 class="text-md font-bold text-gray-800 mb-1">COMPRA VIVIENDA</h5>
                                <hr class="border-t border-dotted mb-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Gastos Añadidos: 7,00%</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-anadidos" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">ITP: 10,00%</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="itp" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Gastos Notariales: 1.000,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-notariales-compra" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Registro de la Propiedad: 500,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="registro-propiedad-compra" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Gastos Administrativos: 800,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-administrativos-compra" checked>
                                    </div>
                                </div>
                            </div>
                            <!-- Venta Vivienda -->
                            <div>
                                <h5 class="text-md font-bold text-gray-800 mb-2">VENTA VIVIENDA</h5>
                                <hr class="border-t border-dotted mb-2">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">IRPF: 21,00%</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="irpf" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Comisión por Venta: 3,00%</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="comision-venta" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Gastos Notariales: 1.000,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-notariales-venta" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Registro de la Propiedad: 500,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="registro-propiedad-venta" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Gastos Registro: 500,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-registro" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Calculadora -->
                <div class="col-12 mt-4 mt-md-0">
                    <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                        <h4 class="text-2xl font-bold text-gray-700 mb-2">CALCULADORA</h4>
                        <div class="mb-4">
                            <label for="precio-compra" class="form-label text-sm text-gray-700 font-semibold">INGRESA UN PRECIO DE COMPRA</label>
                            <input type="text" class="form-control text-lg font-medium text-center" id="precio-compra" value="">
                        </div>
                        <button id="calcular-resultado-btn" class="bg-blue-800 w-100 text-lg text-white py-2 rounded-lg font-bold hover:bg-blue-600 transition duration-300 ease-in-out mb-4">CALCULAR RESULTADO</button>
                        <div id="resultados-calculadora" class="row"></div>
                    </div>
                </div>
            </div>

            <!-- Información General -->
            <div class="col-12 col-xl-6">
                <h2 class="text-2xl font-bold py-4 px-6 text-gray-800">INFORMACIÓN GENERAL DE LA SUBASTA</h2>
                <div class="row">
                    <!-- Información -->
                    <div class="col-12 col-md-6 mb-6">
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">INFORMACIÓN</h4>
                        <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Dirección:</h5>
                                <p class="text-base"><?= htmlspecialchars($subasta['direccion']) ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Localidad:</h5>
                                <p class="text-base"><?= htmlspecialchars($subasta['localidad']) ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Provincia:</hhe>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['provincia']) ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Código Postal:</h5>
                                <p class="text-base"><?= htmlspecialchars($subasta['cp']) ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Fecha Inicio:</h5>
                                <p class="text-base"><?= date('d/m/Y H:i', strtotime($subasta['fecha_inicio'])) ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Fecha Conclusión:</hhe>
                                    <p class="text-base font-medium"><?= date('d/m/Y H:i', strtotime($subasta['fecha_conclusion'])) ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Tipo de Subasta:</hhe>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['tipo_subasta']) ?></p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Enlace Subasta:</h5>
                                <a href="<?= htmlspecialchars($subasta['enlace_subasta']) ?>" target="_blank" class="text-base font-semibold text-blue-800">Haz click aquí</a>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Valor de Subasta:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['valor_subasta'], 2, ',', '.') ?> €</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Tasación:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['tasacion'], 2, ',', '.') ?> €</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Importe Depósito:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['importe_deposito'], 2, ',', '.') ?> €</p>
                            </div>
                            <div class="mb-3">
                                <h5 class="font-bold text-base">Puja Mínima:</hhe>
                                    <p class="text-base font-medium"><?= $subasta['puja_minima'] ? number_format($subasta['puja_minima'], 2, ',', '.') . ' €' : 'Sin puja mínima' ?></p>
                            </div>
                            <div>
                                <h5 class="font-bold text-base">Tramos entre Pujas:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['tramos_pujas'], 2, ',', '.') ?> €</p>
                            </div>
                        </div>
                    </div>

                    <!-- Catastro -->
                    <div class="col-12 col-md-6 mb-6">
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">CATASTRO</h4>
                        <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Referencia Catastral:</h5>
                                <p class="text-base"><?= htmlspecialchars($subasta['ref_catastral']) ?></p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Clase:</hhe>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['clase']) ?></p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Uso Principal:</hhe>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['uso_principal']) ?></p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Superficie Construida:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['sup_construida'], 2, ',', '.') ?> m²</p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Vivienda:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['vivienda'] ?? 0, 2, ',', '.') ?> m²</p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Garaje:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['garaje'] ?? 0, 2, ',', '.') ?> m²</p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Almacén:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['almacen'] ?? 0, 2, ',', '.') ?> m²</p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Zonas Comunes:</hhe>
                                    <p class="text-base font-medium"><?= number_format($subasta['zonas_comunes'] ?? 0, 2, ',', '.') ?> m²</p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Año de Construcción:</hhe>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['ano_construccion']) ?></p>
                            </div>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Enlace Catastro:</hhe>
                                    <a href="<?= htmlspecialchars($subasta['enlace_catastro']) ?>" target="_blank" class="text-base font-semibold text-blue-800">Haz click aquí</a>
                            </div>
                        </div>
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 mt-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">DOCUMENTOS DISPONIBLES</h4>
                        <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            <?php if ($url_pdf_precios): ?>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Estudio de la Subasta:</hhe>
                                        <a href="<?= htmlspecialchars($url_pdf_precios) ?>" target="_blank" class="text-base font-semibold text-blue-800">Haz click aquí</a>
                                </div>
                            <?php endif; ?>

                            <?php if ($documents): ?>
                                <div class="mb-0">
                                    <h5 class="font-bold text-base">Documentos Relacionados:</hhe>
                                        <?php foreach ($documents as $document): ?>
                                            <p class="text-base font-semibold text-blue-800">
                                                <a href="<?= htmlspecialchars(str_replace('../../', 'assets/', $document['url_documento'])) ?>" target="_blank">
                                                    <?= htmlspecialchars($document['nombre_documento']) ?>
                                                </a>
                                            </p>
                                        <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-base">No hay documentos disponibles.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Valoración de la Subasta y Comentarios -->
            <div class="col-12">
                <div class="row text-center">
                    <!-- Pentagrama de cualidades -->
                    <div class="col-12 col-xl-6">
                        <h4 class="text-lg font-bold py-2 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            PENTAGRAMA DE CUALIDADES DE LA VIVIENDA
                        </h4>
                        <div id="radar-chart-container" class="flex justify-center items-center rounded-xl bg-white p-4 shadow-lg">
                            <!-- El gráfico se renderiza aquí -->
                        </div>
                    </div>
                    <!-- Valoración de la Vivienda -->
                    <div class="col-12 col-xl-6 mb-6 mt-4 mt-xl-0">
                        <h4 class="text-lg font-bold py-2 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            VALORACIÓN DE LA VIVIENDA
                        </h4>
                        <div class="row">
                            <!-- Primera Columna -->
                            <div class="col-12 col-md-6 mb-3">
                                <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Fachada y Exteriores:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($fachada_y_exteriores) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Techo y Canaletas:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($techo_y_canaletas) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Ventanas y Puertas:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($ventanas_y_puertas) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Jardín y Terrenos:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($jardin_y_terrenos) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Estado de las Estructuras:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($estado_estructuras) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Instalaciones Visibles:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($instalaciones_visibles) ?></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Segunda Columna -->
                            <div class="col-12 col-md-6 mb-3">
                                <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Vecindario:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($vecindario) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Seguridad:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($seguridad) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Ruido y Olores:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($ruido_y_olores) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Acceso y Estacionamiento:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($acceso_y_estacionamiento) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Localización:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($localizacion) ?></p>
                                    </div>
                                    <div class="mb-1">
                                        <h5 class="font-bold text-base">Estado del Inquilino:</hhe>
                                            <p class="text-base font-medium"><?= htmlspecialchars($estado_inquilino) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sección de Comentarios -->
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            COMENTARIOS SOBRE LA VIVIENDA
                        </h4>
                        <div class="col-12 mb-3">
                            <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <?php if (!empty($comentarios)): ?>
                                    <?php foreach ($comentarios as $comentario): ?>
                                        <div class="mb-3">
                                            <div class="text-base">
                                                <?= $comentario // Mostrar el comentario con HTML sin escapado 
                                                ?>
                                            </div>
                                            <hr class="border-gray-300">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-base">No hay comentarios disponibles.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="assets/js/config_api.js"></script>
    <script type="text/javascript">
        const metrosVivienda = <?= json_encode(floatval($subasta['vivienda'] ?? 0)) ?>;
        const metrosTrastero = <?= json_encode(floatval($subasta['trastero'] ?? 0)) ?>;
        const metrosGaraje = <?= json_encode(floatval($subasta['garaje'] ?? 0)) ?>;
        const precioMedio = <?= json_encode(floatval($subasta['precio_medio'] ?? 0)) ?>;
        const precioTrastero = <?= json_encode(floatval($subasta['precio_trastero'] ?? 0)) ?>;
        const precioGaraje = <?= json_encode(floatval($subasta['precio_garaje'] ?? 0)) ?>;
    </script>

    <script type="module">
        import {
            renderCountdown
        } from './assets/js/components/countdown.js';
        import {
            renderRadarChart
        } from './assets/js/components/radarChart.js';
        import {
            renderGoogleMap
        } from './assets/js/components/googleMap.js';
        import {
            renderImageGallery
        } from './assets/js/components/imageGallery.js';

        document.addEventListener('DOMContentLoaded', () => {
            // Renderizado del countdown
            const countdownContainer = document.getElementById('countdown-container');
            const endDate = new Date('<?= $subasta['fecha_conclusion'] ?>');
            const countdownElement = renderCountdown(endDate);

            countdownContainer.innerHTML = '';
            countdownContainer.appendChild(countdownElement);

            // Renderizado del radar chart
            const radarChartContainer = document.getElementById('radar-chart-container');
            radarChartContainer.innerHTML = '';

            const radarChartElement = renderRadarChart({
                "Fachada y Exteriores": <?= $fachada_y_exteriores ?>,
                "Techo y Canaletas": <?= $techo_y_canaletas ?>,
                "Ventanas y Puertas": <?= $ventanas_y_puertas ?>,
                "Jardín y Terrenos": <?= $jardin_y_terrenos ?>,
                "Estructuras": <?= $estado_estructuras ?>,
                "Instalaciones": <?= $instalaciones_visibles ?>,
                "Vecindario": <?= $vecindario ?>,
                "Seguridad": <?= $seguridad ?>,
                "Ruido y Olores": <?= $ruido_y_olores ?>,
                "Estacionamiento": <?= $acceso_y_estacionamiento ?>,
                "Localización": <?= $localizacion ?>,
                "Estado Inquilino": <?= $estado_inquilino ?>
            });

            radarChartContainer.appendChild(radarChartElement); // Añade el nuevo canvas al contenedor

            // Renderizado del mapa
            const mapContainer = document.getElementById('mapContainer');
            mapContainer.innerHTML = ''; // Limpia el contenedor antes de renderizar el mapa
            renderGoogleMap(mapContainer, <?= $latitud ?>, <?= $altitud ?>);

            // Renderizado de la galería de imágenes y videos
            const galleryContainer = document.getElementById('gallery-container');
            const mediaItems = <?= json_encode($mediaItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            galleryContainer.innerHTML = ''; // Limpia el contenedor antes de añadir las imágenes y videos

            if (mediaItems.length > 0) {
                const newGallery = renderImageGallery(mediaItems);
                galleryContainer.innerHTML = ''; // Asegura que el contenedor está vacío
                galleryContainer.appendChild(newGallery);
            } else {
                galleryContainer.innerHTML = '<p>No hay medios disponibles.</p>';
            }
        });
    </script>

    <script type="module" src="assets/js/components/benefitCalculator.js"></script>

</body>

</html>