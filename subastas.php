<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

$isAdmin = $_SESSION['tipo_usuario'] === 'admin';

include('assets/php/database/config.php');

try {
    // Obtener todas las subastas de la base de datos incluyendo su estado, detalles y la imagen de portada
    $stmt = $conn->prepare("
        SELECT 
            s.*, 
            es.estado AS estado_subasta,
            sd.precio_medio,
            sd.precio_venta_medio,
            im.url_imagen AS imagen_portada
        FROM 
            Subastas s
        LEFT JOIN 
            EstadosSubasta es ON s.id_estado = es.id_estado
        LEFT JOIN 
            SubastaDetalles sd ON s.id_subasta = sd.id_subasta
        LEFT JOIN 
            PortadaSubasta ps ON s.id_subasta = ps.id_subasta
        LEFT JOIN 
            ImagenesSubasta im ON ps.id_imagen = im.id_imagen
    ");
    $stmt->execute();
    $subastas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    die();
}

function getProcessColor($process)
{
    $colors = [
        "Activa" => "bg-green-100 text-green-700 border-2 border-green-700 py-2 mt-3 mb-2",
        "Estudiando" => "bg-yellow-100 text-yellow-500 border-2 border-yellow-500 py-2 mt-3",
        "Terminada" => "bg-red-100 text-red-700 border-2 border-red-500 py-2 mt-3",
    ];
    return $colors[$process] ?? "bg-gray-200 text-gray-800";
}

// Obtener todas las localidades, tipos de subasta y el rango de precios
$localidades = array_unique(array_column($subastas, 'localidad'));
$tipos_subasta = array_unique(array_column($subastas, 'tipo_subasta'));
$max_valor_subasta = max(array_column($subastas, 'valor_subasta'));
$min_valor_subasta = min(array_column($subastas, 'valor_subasta'));
$initial_valor_subasta = ($min_valor_subasta + $max_valor_subasta) / 2;

// Obtenemos los días con subastas para el calendario
$subasta_fechas = array_column($subastas, 'fecha_conclusion');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subastas FDM - Listado de Subastas</title>
    <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">

    <!-- Incluir la fuente Poppins desde Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gray-100 font-poppins">
    <!-- Cabecera -->
    <header class="bg-white shadow-md py-4">
        <div class="container-lg mx-auto flex items-center justify-between px-12">
            <div class="flex items-center space-x-4">
                <?php if ($isAdmin): ?>
                    <button class="bg-blue-700 hover:bg-blue-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out" data-bs-toggle="modal" data-bs-target="#crearSubastaModal">
                        Crear Subasta
                    </button>
                <?php endif; ?>
            </div>
            <div class="flex-grow text-center">
                <img src="assets/img/logo-fdm.png" alt="Logo" class="mx-auto" width="80" height="80">
            </div>
            <div class="flex items-center space-x-4">
                <a href="logout.php" class="bg-red-900 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out no-underline">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>


    <!-- Calendario y Filtros -->
    <div class="container mx-auto p-4 container-full-height">
        <div class="row mb-4 flex flex-col md:flex-row">
            <!-- Calendar -->
            <div class="col-lg-8 col-md-8 mb-4 w-full calendar-full-height">
                <div class="bg-white p-4 rounded-lg shadow-md calendar-container">
                    <div class="flex items-center justify-between">
                        <button id="prevMonth" class="text-xl font-bold text-gray-700 hover:text-gray-900">&lt;</button>
                        <h2 id="currentMonth" class="text-lg font-bold text-gray-800 uppercase"></h2>
                        <button id="nextMonth" class="text-xl font-bold text-gray-700 hover:text-gray-900">&gt;</button>
                    </div>
                    <div class="grid grid-cols-7 gap-1 mt-4 text-center">
                        <!-- Días de la semana -->
                        <div class="text-sm font-semibold text-gray-500">SUN</div>
                        <div class="text-sm font-semibold text-gray-500">MON</div>
                        <div class="text-sm font-semibold text-gray-500">TUE</div>
                        <div class="text-sm font-semibold text-gray-500">WED</div>
                        <div class="text-sm font-semibold text-gray-500">THU</div>
                        <div class="text-sm font-semibold text-gray-500">FRI</div>
                        <div class="text-sm font-semibold text-gray-500">SAT</div>
                    </div>
                    <div class="grid grid-cols-7 gap-1 mt-2 calendar-dates-container" id="calendarDates">
                        <!-- Fechas se generan aquí -->
                    </div>
                </div>
            </div>

            <!-- Filters and Subastas -->
            <div class="col-lg-4 col-md-4 w-full">
                <!-- Filters -->
                <div class="bg-white p-6 rounded-lg shadow-md h-auto">
                    <h3 class="font-bold text-xl mb-4">FILTRAR POR:</h3>

                    <div class="mb-4">
                        <label for="locationFilter" class="block text-gray-700 font-semibold">LOCALIDAD</label>
                        <select id="locationFilter" class="form-control">
                            <option value="">Elige una localidad...</option>
                            <?php foreach ($localidades as $localidad): ?>
                                <option value="<?= htmlspecialchars($localidad) ?>"><?= htmlspecialchars($localidad) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Campo oculto para pasar las fechas de las subastas a JavaScript -->
                        <input type="hidden" id="subastaFechas" value='<?= json_encode($subasta_fechas) ?>'>
                    </div>

                    <div class="mb-4">
                        <label for="valueFilter" class="block text-gray-700 font-semibold mb-2">VALOR DE SUBASTA</label>
                        <div class="relative">
                            <input
                                type="range"
                                id="valueFilter"
                                min="<?= $min_valor_subasta ?>"
                                max="<?= $max_valor_subasta ?>"
                                value="<?= $initial_valor_subasta ?>"
                                class="w-full bg-gray-100 rounded-lg appearance-none cursor-pointer focus:outline-none transition duration-300 ease-in-out">
                        </div>
                        <div class="flex justify-between text-xs mt-2">
                            <span class="text-gray-600"><?= number_format($min_valor_subasta, 2) ?>€</span>
                            <span id="valorSeleccionado" class="text-gray-800 font-semibold"><?= number_format($initial_valor_subasta, 2) ?>€</span>
                            <span class="text-gray-600"><?= number_format($max_valor_subasta, 2) ?>€</span>
                        </div>
                    </div>

                    <div class="flex space-x-2 justify-between">
                        <button id="applyFilters" class="bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded hover:bg-blue-600 transition duration-300 ease-in-out w-full md:w-auto">Aplicar Filtros</button>
                        <button id="resetFilters" class="bg-gray-400 text-white text-sm font-bold py-2 px-4 rounded hover:bg-gray-900 transition duration-300 ease-in-out w-full md:w-auto">Resetear Filtros</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Subastas -->
        <div class="row mt-4">
            <?php foreach ($subastas as $subasta): ?>
                <div class="col-12 col-md-12 col-lg-6 mb-4 subasta-item"
                    data-date="<?= date('Y-m-d', strtotime($subasta['fecha_conclusion'])) ?>"
                    data-location="<?= htmlspecialchars($subasta['localidad']) ?>"
                    data-value="<?= number_format($subasta['valor_subasta'], 2) ?>">
                    <div class="subasta-card d-flex flex-row">
                        <div class="subasta-card-content d-flex flex-column justify-content-between p-4">
                            <div class="subasta-card-body">
                                <h3 class="subasta-card-title"><?= htmlspecialchars($subasta['direccion']) ?></h3>
                                <h4 class="subasta-card-location"><?= htmlspecialchars($subasta['localidad']) ?></h4>
                                <div class="subasta-card-info">
                                    <p>Valor Subasta: <span class="value" data-value="<?= number_format($subasta['valor_subasta'], 2) ?>"><?= number_format($subasta['valor_subasta'], 2) ?></span> €</p>
                                    <p>Precio m²: <?= number_format($subasta['precio_medio'], 2) ?> €</p>
                                    <p>Venta Estimada: <?= number_format($subasta['precio_venta_medio'], 2) ?> €</p>
                                    <p>Fin de Subasta: <?= date('d/m/Y H:i', strtotime($subasta['fecha_conclusion'])) ?></p>
                                </div>
                                <div class="subasta-card-status <?= getProcessColor($subasta['estado_subasta']) ?>">
                                    <?= htmlspecialchars($subasta['estado_subasta']) ?>
                                </div>
                                <div class="subasta-card-buttons">
                                    <a href="subasta_detalle.php?id=<?= $subasta['id_subasta'] ?>" class="bg-blue-700 text-white d-block mb-2 no-underline text-xs">Ver Subasta</a>
                                    <?php if ($isAdmin): ?>
                                        <button class="bg-gray-700 text-white d-block mb-2 text-xs hover:bg-gray-900" data-bs-toggle="modal" data-bs-target="#editModal<?= $subasta['id_subasta'] ?>">Editar</button>
                                        <button class="bg-red-700 text-white d-block mb-2 text-xs hover:bg-red-900 eliminar-subasta" data-id="<?= $subasta['id_subasta'] ?>">Eliminar</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <img src="assets/img/VIVIENDAS/<?= htmlspecialchars(basename($subasta['imagen_portada'])) ?>" alt="Imagen de Subasta" class="w-100 rounded">
                        </div>
                    </div>
                    <!-- Aquí se incluye el modal para cada subasta -->
                    <?php include 'assets/php/modal/editSubastas.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        </main>

        <!-- Aquí se incluye el modal de creación de subasta -->
        <?php include 'assets/php/modal/crear_subasta_modal.php'; ?>

        <!-- FullCalendar JS -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.js'></script>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/components/renderCalendar.js"></script>
        <script src="assets/js/components/filterSubastas.js"></script>
        <script src="assets/js/components/eliminarSubasta.js"></script>
        <script src="assets/js/modal/crearSubastas.js"></script>
        <script src="assets/js/modal/editarSubasta.js"></script>

</body>

</html>