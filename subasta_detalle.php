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
    $stmt = $conn->prepare("SELECT s.*, c.*, v.*, l.*, sd.precio_medio, sd.url_pdf_precios, 
        sd.puja_mas_alta, sd.carga_subastas, ts.tipo_subasta, si.habitaciones, si.banos, 
        si.piscina, si.jardin, si.ascensor, si.garaje_idealista as garaje_idealista, si.trastero, 
        si.enlace_idealista, c.zonas_comunes 
        FROM Subastas s
        LEFT JOIN Catastro c ON s.id_subasta = c.id_subasta
        LEFT JOIN Valoraciones v ON s.id_subasta = v.id_subasta
        LEFT JOIN Localizaciones l ON s.id_subasta = l.id_subasta
        LEFT JOIN SubastaDetalles sd ON s.id_subasta = sd.id_subasta
        LEFT JOIN TiposSubasta ts ON s.id_tipo_subasta = ts.id_tipo_subasta
        LEFT JOIN SubastaIdealista si ON s.id_subasta = si.id_subasta
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

// Verificar variables necesarias para mostrar
$puja_mas_alta = $subasta['puja_mas_alta'] ?? 0;
$piscina = $subasta['piscina'] ? 'Sí' : 'No';
$jardin = $subasta['jardin'] ? 'Sí' : 'No';
$ascensor = $subasta['ascensor'] ? 'Sí' : 'No';
$garaje = $subasta['garaje_idealista'] ? 'Sí' : 'No';
$trastero = $subasta['trastero'] ? 'Sí' : 'No';

$mediaItems = getSubastaMedia($conn, $id_subasta);
$documents = getSubastaDocuments($conn, $id_subasta);
$comentarios = getComentariosSubasta($conn, $id_subasta);

$latitud = $subasta['latitud'];
$altitud = $subasta['altitud'];

if (!is_numeric($latitud) || !is_numeric($altitud)) {
    echo "Las coordenadas no son válidas.";
    exit();
}

// Valores de la vivienda
$precio_medio = $subasta['precio_medio'] ?? 0;
$vivienda = $subasta['vivienda'] ?? 0;
$zonas_comunes = $subasta['zonas_comunes'] ?? 0;
$terraza = $subasta['terraza'] ?? 0;
$garaje_m2 = $subasta['garaje'] ?? 0;
$almacen = $subasta['almacen'] ?? 0;
$ano_construccion = $subasta['ano_construccion'] ?? 0;

// Cálculo del valor estimado de venta
$precio_garaje = $precio_medio * 0.5;
$precio_trastero = $precio_medio * 0.5;

$valor_vivienda = $vivienda * $precio_medio;
$valor_terraza = $terraza * $precio_medio * 0.5;
$valor_zonas_comunes = $zonas_comunes * $precio_medio * 0.4;
$valor_garaje = $garaje_m2 * $precio_garaje;
$valor_almacen = $almacen * $precio_trastero;

$precio_venta_estimado = $valor_vivienda + $valor_terraza + $valor_zonas_comunes + $valor_garaje + $valor_almacen;

$precio_venta_estimado = calcularAjusteAntiguedad($ano_construccion, $precio_venta_estimado);

$url_pdf_precios = $subasta['url_pdf_precios'] ? str_replace('../../', 'assets/', $subasta['url_pdf_precios']) : null;

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

function getSubastaDocuments($conn, $id_subasta)
{
    $stmt = $conn->prepare("SELECT nombre_documento, url_documento FROM Documentos WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getComentariosSubasta($conn, $id_subasta)
{
    $stmt = $conn->prepare("SELECT comentario FROM Comentarios WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function calcularAjusteAntiguedad($anoConstruccion, $valorEstimado)
{
    $antiguedad = 2024 - $anoConstruccion;
    if ($antiguedad > 40) {
        return $valorEstimado * 0.7;
    } elseif ($antiguedad > 35) {
        return $valorEstimado * 0.725;
    } elseif ($antiguedad > 30) {
        return $valorEstimado * 0.75;
    } elseif ($antiguedad > 25) {
        return $valorEstimado * 0.775;
    } elseif ($antiguedad > 20) {
        return $valorEstimado * 0.8;
    } elseif ($antiguedad > 15) {
        return $valorEstimado * 0.9;
    } elseif ($antiguedad > 10) {
        return $valorEstimado * 0.95;
    } else {
        return $valorEstimado;
    }
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                    <!-- DATOS DEL CÁLCULO -->
                    <div class="col-12 col-md-6 mb-6">
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">DATOS DEL CÁLCULO</h4>
                        <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-center hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            <div class="mb-3">
                                <label for="precio-metro-vivienda" class="form-label text-sm font-medium">Precio m² Vivienda + Zonas Comunes:</label>
                                <input type="text" class="form-control" id="precio-metro-vivienda" value="<?= number_format($precio_medio, 2, ',', '.') ?> €">
                            </div>
                            <div class="mb-3">
                                <label for="precio-metro-trastero" class="form-label text-sm font-medium">Precio m² Trastero:</label>
                                <input type="text" class="form-control" id="precio-metro-trastero" value="<?= number_format($precio_medio * 0.5, 2, ',', '.') ?> €">
                            </div>
                            <div class="mb-3">
                                <label for="precio-metro-garaje" class="form-label text-sm font-medium">Precio m² Garaje:</label>
                                <input type="text" class="form-control" id="precio-metro-garaje" value="<?= number_format($precio_medio * 0.5, 2, ',', '.') ?> €">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-sm font-medium">Precio Venta Estimada:</label>
                                <p id="precio-venta" class="text-lg text-white font-semibold mb-3 bg-gray-400 py-2 rounded-lg"><?= number_format($precio_venta_estimado, 2, ',', '.') ?> €</p>
                            </div>
                            <button id="recalcular-btn" class="bg-blue-800 w-100 text-md text-white py-2 rounded-lg font-bold hover:bg-blue-600 transition duration-300 ease-in-out mb-1">RECALCULAR VALORES</button>
                        </div>
                    </div>
                    <!-- Tipos de Gastos -->
                    <div class="col-12 col-md-6">
                        <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">TIPOS DE GASTOS</h4>
                        <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            <!-- Gastos de Compra -->
                            <div class="mb-4">
                                <h5 class="text-md font-bold text-gray-800 mb-2">TIPOS DE GASTOS</h5>
                                <hr class="border-t border-dotted mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium">Gastos Añadidos: 7,00%</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-anadidos" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium">ITP: 7,50%</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="itp" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium">Gastos Notariales: 1.000,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-notariales-compra" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium">Registro de la Propiedad: 500,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="registro-propiedad-compra" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium">Gastos Administrativos: 800,00€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-administrativos-compra" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium">Comisión por Venta: 3,00%</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="comision-venta" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium">Gastos de Desalojo 5.000€</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="gastos-desalojo" checked>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium">Cargas Subasta</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="carga_subastas" checked>
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
                <!-- Primera fila: Valoración de la Vivienda -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-lg font-bold py-2 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                            IDEALISTA
                        </h4>
                        <div class="row">
                            <!-- Primera columna - Habitaciones -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-bed fa-2x"></i> <!-- Icono de cama -->
                                    </div>
                                    <h5 class="text-xs font-bold"> HABITACIONES </h5>
                                    <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['habitaciones']) ?></p>
                                </div>
                            </div>
                            <!-- Segunda columna - Baños -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-bath fa-2x"></i> <!-- Icono de baño -->
                                    </div>
                                    <h5 class="text-xs font-bold"> BAÑOS </h5>
                                    <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['banos']) ?></p>
                                </div>
                            </div>
                            <!-- Tercera columna - Piscina -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-swimmer fa-2x"></i> <!-- Icono de piscina -->
                                    </div>
                                    <h5 class="text-xs font-bold"> PISCINA </h5>
                                    <p class="text-lg font-semibold"><?= $piscina ?></p>
                                </div>
                            </div>
                            <!-- Cuarta columna - Jardín -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-tree fa-2x"></i> <!-- Icono de árbol -->
                                    </div>
                                    <h5 class="text-xs font-bold"> JARDÍN </h5>
                                    <p class="text-lg font-semibold"><?= $jardin ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Quinta columna - Ascensor -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-building fa-2x"></i> <!-- Icono de ascensor -->
                                    </div>
                                    <h5 class="text-xs font-bold"> ASCENSOR </h5>
                                    <p class="text-lg font-semibold"><?= $ascensor ?></p>
                                </div>
                            </div>
                            <!-- Sexta columna - Garaje -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-car fa-2x"></i> <!-- Icono de coche -->
                                    </div>
                                    <h5 class="text-xs font-bold"> GARAJE </h5>
                                    <p class="text-lg font-semibold"><?= $garaje ?></p>
                                </div>
                            </div>
                            <!-- Séptima columna - Trastero -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-box-open fa-2x"></i> <!-- Icono de caja -->
                                    </div>
                                    <h5 class="text-xs font-bold"> TRASTERO </h5>
                                    <p class="text-lg font-semibold"><?= $trastero ?></p>
                                </div>
                            </div>
                            <!-- Octava columna - Enlace Idealista -->
                            <div class="col-6 col-md-3 mb-3 d-flex">
                                <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                    <div class="mb-2">
                                        <i class="fas fa-link fa-2x"></i> <!-- Icono de enlace -->
                                    </div>
                                    <h5 class="text-xs font-bold"> ENLACE IDEALISTA </h5>
                                    <p class="text-xs font-semibold text-blue-800">
                                        <a href="<?= htmlspecialchars($subasta['enlace_idealista']) ?>" target="_blank">Haz click aquí</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segunda fila: Información y Catastro -->
                <div class="row mt-4">
                    <!-- Información -->
                    <div class="col-12 col-md-6 d-flex align-items-stretch">
                        <div class="w-100 d-flex flex-column">
                            <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                INFORMACIÓN
                            </h4>
                            <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out flex-grow">
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Dirección:</h5>
                                    <p class="text-base"><?= htmlspecialchars($subasta['direccion']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Localidad:</h5>
                                    <p class="text-base"><?= htmlspecialchars($subasta['localidad']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Provincia:</h5>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['provincia']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Código Postal:</h5>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['cp']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Fecha Inicio:</h5>
                                    <p class="text-base font-medium"><?= date('d/m/Y H:i', strtotime($subasta['fecha_inicio'])) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Fecha Conclusión:</h5>
                                    <p class="text-base font-medium"><?= date('d/m/Y H:i', strtotime($subasta['fecha_conclusion'])) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Tipo de Subasta:</h5>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['tipo_subasta']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Enlace Subasta:</h5>
                                    <a href="<?= htmlspecialchars($subasta['enlace_subasta']) ?>" target="_blank" class="text-base font-semibold text-blue-800">Haz click aquí</a>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Valor de Subasta:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['valor_subasta'], 2, ',', '.') ?> €</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Cargas:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['carga_subastas'], 2, ',', '.') ?> €</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Tasación:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['tasacion'], 2, ',', '.') ?> €</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Importe Depósito:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['importe_deposito'], 2, ',', '.') ?> €</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Puja Mínima:</h5>
                                    <p class="text-base font-medium"><?= $subasta['puja_minima'] ? number_format($subasta['puja_minima'], 2, ',', '.') . ' €' : 'Sin puja mínima' ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Tramos entre Pujas:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['tramos_pujas'], 2, ',', '.') ?> €</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Catastro -->
                    <div class="col-12 col-md-6 d-flex align-items-stretch">
                        <div class="w-100 d-flex flex-column">
                            <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                CATASTRO
                            </h4>
                            <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out flex-grow">
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Referencia Catastral:</h5>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['ref_catastral']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Clase:</h5>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['clase']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Uso Principal:</h5>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['uso_principal']) ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Superficie Construida:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['sup_construida'] ?? 0, 2, ',', '.') ?> m²</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Vivienda:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['vivienda'] ?? 0, 2, ',', '.') ?> m²</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Terraza:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['terraza'] ?? 0, 2, ',', '.') ?> m²</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Zonas Comunes:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['zonas_comunes'] ?? 0, 2, ',', '.') ?> m²</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Garaje:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['garaje'] ?? 0, 2, ',', '.') ?> m²</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Almacén:</h5>
                                    <p class="text-base font-medium"><?= number_format($subasta['almacen'] ?? 0, 2, ',', '.') ?> m²</p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Año de Construcción:</h5>
                                    <p class="text-base font-medium"><?= htmlspecialchars($subasta['ano_construccion'] ?? 'No disponible') ?></p>
                                </div>
                                <div class="mb-2">
                                    <h5 class="font-bold text-base">Enlace Catastro:</h5>
                                    <a href="<?= htmlspecialchars($subasta['enlace_catastro']) ?>" target="_blank" class="text-base font-semibold text-blue-800">Haz click aquí</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Valoración de la Subasta y Comentarios -->
        <div class="col-12">
            <div class="row text-center mt-4">
                <!-- Primera fila: Diagrama de cualidades y Comentarios sobre la vivienda -->
                <!-- Pentagrama de cualidades -->
                <div class="col-12 col-xl-6 mb-6">
                    <h4 class="text-lg font-bold py-2 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                        PENTAGRAMA DE CUALIDADES DE LA VIVIENDA
                    </h4>
                    <div id="radar-chart-container" class="flex justify-center items-center rounded-xl bg-white p-4 shadow-lg">
                        <!-- El gráfico se renderiza aquí -->
                    </div>
                </div>
                <!-- Comentarios sobre la vivienda -->
                <div class="col-12 col-xl-6 mb-6">
                    <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                        COMENTARIOS SOBRE LA VIVIENDA
                    </h4>
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
            <div class="row text-center mt-4">
                <!-- Segunda fila: Valoración de la vivienda y Documentos disponibles -->
                <!-- Valoración de la Vivienda -->
                <div class="col-12 col-xl-6 mb-6">
                    <h4 class="text-lg font-bold py-2 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                        VALORACIÓN DE LA VIVIENDA
                    </h4>
                    <div class="row">
                        <!-- Primera Fila de valores -->
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-home fa-2x"></i> <!-- Icono de hogar -->
                                </div>
                                <h5 class="text-xs font-bold">FACHADA Y EXTERIORES</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['fachada_y_exteriores'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-water fa-2x"></i> <!-- Icono de agua -->
                                </div>
                                <h5 class="text-xs font-bold">TECHO Y CANALETAS</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['techo_y_canaletas'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-door-open fa-2x"></i> <!-- Icono de puerta abierta -->
                                </div>
                                <h5 class="text-xs font-bold">VENTANAS Y PUERTAS</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['ventanas_y_puerta'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-seedling fa-2x"></i> <!-- Icono de planta -->
                                </div>
                                <h5 class="text-xs font-bold">JARDÍN Y TERRENOS</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['jardin_y_terrenos'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Segunda Fila de valores -->
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-building fa-2x"></i> <!-- Icono de edificio -->
                                </div>
                                <h5 class="text-xs font-bold">ESTADO DE LAS ESTRUCTURAS</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['estado_estructuras'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-tools fa-2x"></i> <!-- Icono de herramientas -->
                                </div>
                                <h5 class="text-xs font-bold">INSTALACIONES VISIBLES</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['instalaciones_visibles'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-users fa-2x"></i> <!-- Icono de personas -->
                                </div>
                                <h5 class="text-xs font-bold">VECINDARIO</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['vecindario'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-shield-alt fa-2x"></i> <!-- Icono de escudo -->
                                </div>
                                <h5 class="text-xs font-bold">SEGURIDAD</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['seguridad'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Tercera Fila de valores -->
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-volume-up fa-2x"></i> <!-- Icono de sonido -->
                                </div>
                                <h5 class="text-xs font-bold">RUIDO Y OLORES</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['ruido_y_olores'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-parking fa-2x"></i> <!-- Icono de estacionamiento -->
                                </div>
                                <h5 class="text-xs font-bold">ACCESO Y ESTACIONAMIENTO</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['acceso_y_estacionamiento'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-map-marker-alt fa-2x"></i> <!-- Icono de ubicación -->
                                </div>
                                <h5 class="text-xs font-bold">LOCALIZACIÓN</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['localizacion'] ?? 0) ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 d-flex">
                            <div class="bg-white border-gray-400 border-3 p-2 rounded-lg shadow-md text-center d-flex flex-column justify-content-center align-items-center w-100 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                                <div class="mb-2">
                                    <i class="fas fa-user fa-2x"></i> <!-- Icono de persona -->
                                </div>
                                <h5 class="text-xs font-bold">ESTADO DEL INQUILINO</h5>
                                <p class="text-lg font-semibold"><?= htmlspecialchars($subasta['estado_inquilino'] ?? 0) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Documentos disponibles -->
                <div class="col-12 col-xl-6 mb-6">
                    <h4 class="text-lg font-bold py-2 px-6 bg-gray-400 text-white rounded-xl mb-3 hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                        DOCUMENTOS DISPONIBLES
                    </h4>
                    <div class="bg-white border-gray-400 border-3 p-4 rounded-lg shadow-md text-left hover:shadow-xl hover:bg-gray-500 transition duration-300 ease-in-out">
                        <?php if ($url_pdf_precios): ?>
                            <div class="mb-2">
                                <h5 class="font-bold text-base">Estudio de la Subasta:</h5>
                                <a href="<?= htmlspecialchars($url_pdf_precios) ?>" target="_blank" class="text-base font-semibold text-blue-800">Haz click aquí</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($documents): ?>
                            <div class="mb-0">
                                <h5 class="font-bold text-base">Documentos Relacionados:</h5>
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
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="assets/js/config_api.js"></script>
    <script type="text/javascript">
        const metrosVivienda = <?= json_encode(floatval($subasta['vivienda'] ?? 0)) ?>;
        const metrosZonasComunes = <?= json_encode(floatval($subasta['zonas_comunes'] ?? 0)) ?>;
        const metrosTrastero = <?= json_encode(floatval($subasta['almacen'] ?? 0)) ?>;
        const metrosGaraje = <?= json_encode(floatval($subasta['garaje'] ?? 0)) ?>;
        const precioMedio = <?= json_encode(floatval($subasta['precio_medio'] ?? 0)) ?>;
        const cargaSubastas = <?= json_encode(floatval($subasta['carga_subastas'] ?? 0)) ?>;
        const metrosTerraza = <?= json_encode(floatval($subasta['terraza'] ?? 0)) ?>;
        const anoConstruccion = <?= json_encode(intval($subasta['ano_construccion'] ?? 0)) ?>;

        // Calculando precio_garaje y precio_trastero en el lado del cliente
        const precioGaraje = precioMedio * 0.5;
        const precioTrastero = precioMedio * 0.5;
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
                "Fachada y Exteriores": <?= $subasta['fachada_y_exteriores'] ?? 0 ?>,
                "Techo y Canaletas": <?= $subasta['techo_y_canaletas'] ?? 0 ?>,
                "Ventanas y Puertas": <?= $subasta['ventanas_y_puerta'] ?? 0 ?>,
                "Jardín y Terrenos": <?= $subasta['jardin_y_terrenos'] ?? 0 ?>,
                "Estado Estructuras": <?= $subasta['estado_estructuras'] ?? 0 ?>,
                "Instalaciones Visibles": <?= $subasta['instalaciones_visibles'] ?? 0 ?>,
                "Vecindario": <?= $subasta['vecindario'] ?? 0 ?>,
                "Seguridad": <?= $subasta['seguridad'] ?? 0 ?>,
                "Ruido y Olores": <?= $subasta['ruido_y_olores'] ?? 0 ?>,
                "Acceso y Estacionamiento": <?= $subasta['acceso_y_estacionamiento'] ?? 0 ?>,
                "Localización": <?= $subasta['localizacion'] ?? 0 ?>,
                "Estado Inquilino": <?= $subasta['estado_inquilino'] ?? 0 ?>
            });

            radarChartContainer.appendChild(radarChartElement);

            // Renderizado del mapa
            const mapContainer = document.getElementById('mapContainer');
            mapContainer.innerHTML = '';
            renderGoogleMap(mapContainer, <?= $latitud ?>, <?= $altitud ?>);

            // Renderizado de la galería de imágenes y videos
            const galleryContainer = document.getElementById('gallery-container');
            const mediaItems = <?= json_encode($mediaItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            galleryContainer.innerHTML = '';

            if (mediaItems.length > 0) {
                const newGallery = renderImageGallery(mediaItems);
                galleryContainer.innerHTML = '';
                galleryContainer.appendChild(newGallery);
            } else {
                galleryContainer.innerHTML = '<p>No hay medios disponibles.</p>';
            }
        });
    </script>

    <script type="module" src="assets/js/components/benefitCalculator.js"></script>

</body>

</html>