<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

include('config.php');

// Obtener el ID de la subasta desde la URL
$id_subasta = $_GET['id'] ?? null;

if (!$id_subasta) {
    echo "No se proporcionó un ID de subasta.";
    exit();
}

try {
    // Consulta para obtener los detalles de la subasta incluyendo el tipo de subasta
    $stmt = $conn->prepare("SELECT s.*, c.*, v.*, l.*, 
                                   sd.precio_medio, sd.precio_venta_min, sd.precio_venta_medio, 
                                   sd.precio_venta_max, sd.url_pdf_precios, sd.puja_mas_alta, 
                                   ts.tipo_subasta
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
    die();
}

function getSubastaImages($conn, $id_subasta)
{
    $stmt = $conn->prepare("SELECT url_imagen FROM ImagenesSubasta WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $images;
}

// Función para obtener los documentos PDF asociados a la subasta
function getSubastaDocuments($conn, $id_subasta)
{
    $stmt = $conn->prepare("SELECT nombre_documento, url_documento FROM Documentos WHERE id_subasta = :id_subasta");
    $stmt->bindParam(':id_subasta', $id_subasta, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$documents = getSubastaDocuments($conn, $id_subasta);
$images = getSubastaImages($conn, $id_subasta);

$latitud = $subasta['latitud'];
$altitud = $subasta['altitud'];

if (!is_numeric($latitud) || !is_numeric($altitud)) {
    echo "Las coordenadas no son válidas.";
    exit();
}

// Extraer la Puja Más Alta directamente de la base de datos
$puja_mas_alta = $subasta['puja_mas_alta'];

// Calcular el Precio de Venta Estimado
$precio_venta_estimado = $subasta['precio_medio'] * $subasta['vivienda'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Subasta</title>
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
    <!-- Incluir la fuente Poppins desde Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="min-h-screen bg-gray-100">
    <div id="header">
        <div class="bg-white shadow-md py-4 flex items-center justify-between px-4">
            <a href="subastas.php" class="text-black p-2 rounded-full hover:bg-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-grow text-center">
                <img src="assets/img/logo-fdm.png" alt="Logo" class="mx-auto" width="80" height="80">
            </div>
            <div class="w-6 h-6"></div>
        </div>
    </div>

    <div class="container mx-auto p-4 py-10">
        <div class="grid gap-4 grid-cols-1 lg:grid-cols-4">
            <!-- Primera columna -->
            <div id="columna1" class="lg:col-span-1">
                <div class="bg-blue-700 text-white py-6 pb-8 pt-8 rounded-xl shadow-md mb-4">
                    <h2 class="text-xl text-center font-bold">SUBASTA ACTUAL</h2>
                    <p class="text-3xl text-center font-semibold"><?= number_format($puja_mas_alta, 2, ',', '.') ?> €</p>
                </div>

                <div class="bg-white text-blue-700 py-4 rounded-xl shadow-md mb-4 border-3 border-blue-700">
                    <h2 class="text-xl text-center font-bold">CIERRE SUBASTA</h2>
                    <div id="countdown-container" class="text-3xl text-center font-semibold"></div>
                </div>
                <div id="benefit-calculator"></div>

                <!-- Sección de Valoración de la Vivienda -->
                <div class="bg-white text-blue-700 px-2 py-6 pb-3 pt-8 rounded-xl shadow-md mb-4 border-3 border-blue-700">
                    <h2 class="text-xl text-center font-bold mb-2">VALORACIÓN DE LA VIVIENDA</h2>
                    <div id="radar-chart-container"></div> <!-- Contenedor para el gráfico radar -->
                </div>
            </div>

            <div id="columna2" class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl border-3 border-blue-700">
                    <h2 class="text-xl text-center text-blue-700 font-bold mb-4">INFORMACIÓN</h2>
                    <div class="bg-white rounded-lg">
                        <div class="mb-4 rounded-xl">
                            <p>Dirección: <?= htmlspecialchars($subasta['direccion']) ?></p>
                            <p>Código Postal: <?= htmlspecialchars($subasta['cp']) ?></p>
                            <p>Localidad: <?= htmlspecialchars($subasta['localidad']) ?></p>
                            <p>Provincia: <?= htmlspecialchars($subasta['provincia']) ?></p>
                            <p>Fecha Inicio: <?= date('d/m/Y', strtotime($subasta['fecha_inicio'])) ?></p>
                            <p>Fecha Conclusión: <?= date('d/m/Y', strtotime($subasta['fecha_conclusion'])) ?></p>
                            <p>Tipo de Subasta: <?= htmlspecialchars($subasta['tipo_subasta']) ?></p>
                            <p>Enlace Subasta: <a href="<?= htmlspecialchars($subasta['enlace_subasta']) ?>" class="text-blue-500">Haz click aquí</a></p>
                            <p>Valor Subasta: <?= number_format($subasta['valor_subasta'], 2, ',', '.') ?> €</p>
                            <p>Tasación: <?= number_format($subasta['tasacion'], 2, ',', '.') ?> €</p>
                            <p>Importe Depósito: <?= number_format($subasta['importe_deposito'], 2, ',', '.') ?> €</p>
                            <p>Puja Mínima: <?= number_format($subasta['puja_minima'], 2, ',', '.') ?> €</p>
                            <p>Tramos entre Pujas: <?= number_format($subasta['tramos_pujas'], 2, ',', '.') ?> €</p>
                            <p>Precio Metro Cuadrado: <?= number_format($subasta['precio_medio'], 2, ',', '.') ?> €/m²</p>
                            <p>Precio Venta Estimado: <?= number_format($precio_venta_estimado, 2, ',', '.') ?> €</p>

                            <!-- Información del Catastro -->
                            <h3 class="text-lg font-bold mt-4">Información del Catastro:</h3>
                            <p>Referencia Catastral: <?= htmlspecialchars($subasta['ref_catastral']) ?></p>
                            <p>Clase: <?= htmlspecialchars($subasta['clase']) ?></p>
                            <p>Uso Principal: <?= htmlspecialchars($subasta['uso_principal']) ?></p>
                            <p>Superficie Construida: <?= number_format($subasta['sup_construida'], 2, ',', '.') ?> m²</p>
                            <p>Vivienda: <?= number_format($subasta['vivienda'], 2, ',', '.') ?> m²</p>
                            <p>Garaje: <?= number_format($subasta['garaje'], 2, ',', '.') ?> m²</p>
                            <p>Almacén: <?= number_format($subasta['almacen'], 2, ',', '.') ?> m²</p>
                            <p>Año de Construcción: <?= htmlspecialchars($subasta['ano_construccion']) ?></p>
                            <p>Enlace Catastro: <a href="<?= htmlspecialchars($subasta['enlace_catastro']) ?>" class="text-blue-500">Haz click aquí</a></p>
                        </div>
                        <div class="text-center">
                            <?php if (!empty($subasta['url_pdf_precios'])) : ?>
                                <a href="<?= htmlspecialchars($subasta['url_pdf_precios']) ?>" target="_blank">
                                    <button class="bg-blue-700 text-sm hover:bg-black text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300 ease-in-out">
                                        DESCARGA PDF DE COMPRA Y VENTA
                                    </button>
                                </a>
                            <?php else : ?>
                                <button class="bg-gray-400 text-white font-bold py-2 px-4 rounded w-full cursor-not-allowed" disabled>
                                    PDF NO DISPONIBLE
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sección de Documentos -->
                <?php if (!empty($documents)) : ?>
                    <div class="bg-white p-6 rounded-xl border-3 border-blue-700 mt-4">
                        <h2 class="text-xl text-center text-blue-700 font-bold mb-4">DOCUMENTOS DISPONIBLES</h2>
                        <div class="bg-white rounded-lg">
                            <?php foreach ($documents as $document) : ?>
                                <div class="mb-2">
                                    <a href="<?= htmlspecialchars($document['url_documento']) ?>" class="text-blue-500 hover:text-blue-700 font-medium" target="_blank">
                                        <?= htmlspecialchars($document['nombre_documento']) ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>


            <!-- Tercera columna -->
            <div id="columna3" class="lg:col-span-2">
                <div class="text-blue-700 p-6 rounded-xl mb-4 bg-white border-3 border-blue-700">
                    <h2 class="text-xl text-center font-bold mb-4">LOCALIZACIÓN SUBASTA</h2>
                    <div id="mapContainer" style="height: 300px; width: 100%;"></div>
                </div>
                <div id="gallery-container" class="mt-4 lg:col-span-2"></div> <!-- Añadir aquí la galería de imágenes -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/config_api.js"></script>
    <script type="module">
        import {
            renderCountdown
        } from './assets/js/components/countdown.js';
        import {
            renderBenefitCalculator
        } from './assets/js/components/benefitCalculator.js';
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
            // Countdown
            const countdownContainer = document.getElementById('countdown-container');
            const endDate = new Date('<?= $subasta['fecha_conclusion'] ?>');
            countdownContainer.appendChild(renderCountdown(endDate));

            // Benefit Calculator
            const benefitCalculatorContainer = document.getElementById('benefit-calculator');
            benefitCalculatorContainer.appendChild(renderBenefitCalculator({
                viviendaArea: <?= $subasta['vivienda'] !== null ? $subasta['vivienda'] : 0 ?>,
                squareMeterValue: <?= $subasta['precio_medio'] !== null ? $subasta['precio_medio'] : 0 ?>,
                garageArea: <?= $subasta['garaje'] !== null ? $subasta['garaje'] : 0 ?>,
                garageSquareMeterValue: <?= $subasta['precio_medio'] !== null ? $subasta['precio_medio'] : 0 ?>,
                storageRoomArea: <?= $subasta['almacen'] !== null ? $subasta['almacen'] : 0 ?>,
                storageRoomSquareMeterValue: <?= $subasta['precio_medio'] !== null ? $subasta['precio_medio'] : 0 ?>
            }));

            // Radar Chart - Valoración de la Vivienda
            const radarChartContainer = document.getElementById('radar-chart-container');
            radarChartContainer.appendChild(renderRadarChart({
                "Fachada y Exteriores": <?= $subasta['fachada_y_exteriores'] !== null ? $subasta['fachada_y_exteriores'] : 0 ?>,
                "Techo y Canaletas": <?= $subasta['techo_y_canaletas'] !== null ? $subasta['techo_y_canaletas'] : 0 ?>,
                "Ventanas y Puertas": <?= $subasta['ventanas_y_puerta'] !== null ? $subasta['ventanas_y_puerta'] : 0 ?>,
                "Jardín y Terrenos": <?= $subasta['jardin_y_terrenos'] !== null ? $subasta['jardin_y_terrenos'] : 0 ?>,
                "Estructuras": <?= $subasta['estado_estructuras'] !== null ? $subasta['estado_estructuras'] : 0 ?>,
                "Instalaciones": <?= $subasta['instalaciones_visibles'] !== null ? $subasta['instalaciones_visibles'] : 0 ?>,
                "Vecindario": <?= $subasta['vecindario'] !== null ? $subasta['vecindario'] : 0 ?>,
                "Seguridad": <?= $subasta['seguridad'] !== null ? $subasta['seguridad'] : 0 ?>,
                "Ruido y Olores": <?= $subasta['ruido_y_olores'] !== null ? $subasta['ruido_y_olores'] : 0 ?>,
                "Estacionamiento": <?= $subasta['acceso_y_estacionamiento'] !== null ? $subasta['acceso_y_estacionamiento'] : 0 ?>,
                "Localización": <?= $subasta['localizacion'] !== null ? $subasta['localizacion'] : 0 ?>,
                "Estado Inquilino": <?= $subasta['estado_inquilino'] !== null ? $subasta['estado_inquilino'] : 0 ?>,
                "Tipo Vivienda": <?= $subasta['tipo_de_vivienda'] !== null ? $subasta['tipo_de_vivienda'] : 0 ?>
            }));

            // Google Map
            const mapContainer = document.getElementById('mapContainer');
            renderGoogleMap(mapContainer, <?= $subasta['latitud'] ?>, <?= $subasta['altitud'] ?>);

            // Image Gallery
            const galleryContainer = document.getElementById('gallery-container');
            const images = <?= json_encode($images, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            galleryContainer.appendChild(renderImageGallery(images));
        });
    </script>

</body>

</html>