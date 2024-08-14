<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

$isAdmin = $_SESSION['tipo_usuario'] === 'admin';

include('config.php');

try {
    // Obtener todas las subastas de la base de datos incluyendo su estado
    $stmt = $conn->prepare("
        SELECT s.*, es.estado AS estado_subasta 
        FROM Subastas s
        LEFT JOIN EstadosSubasta es ON s.id_estado = es.id_estado
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
        "Activa" => "bg-green-200 text-green-800",
        "Estudiando" => "bg-yellow-200 text-yellow-800",
        "Terminada" => "bg-red-200 text-red-800",
    ];
    return $colors[$process] ?? "bg-gray-200 text-gray-800";
}
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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-blue-800 to-black py-10 font-poppins">
    <div class="container mx-auto p-4 bg-white rounded-lg shadow-lg">
        <div class="text-center mb-8">
            <img src="assets/img/logo-fdm.png" alt="Logo" class="mx-auto mb-4 w-48 h-48 sm:w-40 sm:h-40">
        </div>
        <div id="auctionsList" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($subastas as $subasta): ?>
                <div class="bg-white p-4 rounded-lg border border-gray-400 shadow-md flex flex-col justify-between transform transition-transform duration-300 hover:scale-105 hover:shadow-xl">
                    <h3 class="text-lg font-semibold"><?= htmlspecialchars($subasta['direccion']) ?></h3>
                    <p class="text-gray-700 text-base">Valor Subasta: <?= number_format($subasta['valor_subasta'], 2) ?> €</p>
                    <p class="text-gray-700 text-base mt-[-12px]">Fin de Subasta: <?= date('d/m/Y', strtotime($subasta['fecha_conclusion'])) ?></p>
                    <div class="flex items-center justify-between mt-2">
                        <a href="subasta_detalle.php?id=<?= $subasta['id_subasta'] ?>" class="bg-blue-700 hover:bg-black text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                            Ver Subasta
                        </a>
                        <span class="<?= getProcessColor($subasta['estado_subasta']) ?> px-3 py-2 rounded-xl">
                            <?= htmlspecialchars($subasta['estado_subasta']) ?>
                        </span>
                        <?php if ($isAdmin): ?>
                            <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out" data-bs-toggle="modal" data-bs-target="#editModal<?= $subasta['id_subasta'] ?>">
                                Editar
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Modal para editar subasta -->
                <div class="modal fade" id="editModal<?= $subasta['id_subasta'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $subasta['id_subasta'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?= $subasta['id_subasta'] ?>">Editar Subasta Completa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="editar_subasta.php" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="id_subasta" value="<?= $subasta['id_subasta'] ?>">

                                    <!-- Sección de Subastas -->
                                    <h6>Detalles de la Subasta</h6>
                                    <div class="mb-3">
                                        <label for="direccion<?= $subasta['id_subasta'] ?>" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion<?= $subasta['id_subasta'] ?>" name="direccion" value="<?= htmlspecialchars($subasta['direccion']) ?>"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="valor_subasta<?= $subasta['id_subasta'] ?>" class="form-label">Valor Subasta (€)</label>
                                        <input type="number" class="form-control" id="valor_subasta<?= $subasta['id_subasta'] ?>" name="valor_subasta" value="<?= number_format($subasta['valor_subasta'], 2) ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="cantidad_reclamada<?= $subasta['id_subasta'] ?>" class="form-label">Cantidad Reclamada (€)</label>
                                        <input type="number" class="form-control" id="cantidad_reclamada<?= $subasta['id_subasta'] ?>" name="cantidad_reclamada" value="<?= number_format($subasta['cantidad_reclamada'], 2) ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="fecha_conclusion<?= $subasta['id_subasta'] ?>" class="form-label">Fecha Conclusión</label>
                                        <input type="date" class="form-control" id="fecha_conclusion<?= $subasta['id_subasta'] ?>" name="fecha_conclusion" value="<?= date('Y-m-d', strtotime($subasta['fecha_conclusion'])) ?>"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="estado_subasta<?= $subasta['id_subasta'] ?>" class="form-label">Estado Subasta</label>
                                        <select class="form-control" id="estado_subasta<?= $subasta['id_subasta'] ?>" name="estado_subasta"  >
                                            <option value="Activa" <?= $subasta['estado_subasta'] == 'Activa' ? 'selected' : '' ?>>Activa</option>
                                            <option value="Estudiando" <?= $subasta['estado_subasta'] == 'Estudiando' ? 'selected' : '' ?>>Estudiando</option>
                                            <option value="Terminada" <?= $subasta['estado_subasta'] == 'Terminada' ? 'selected' : '' ?>>Terminada</option>
                                        </select>
                                    </div>

                                    <!-- Sección de Localizaciones -->
                                    <h6>Localización</h6>
                                    <?php
                                    $stmt_loc = $conn->prepare("SELECT * FROM Localizaciones WHERE id_subasta = :id_subasta");
                                    $stmt_loc->bindParam(':id_subasta', $subasta['id_subasta']);
                                    $stmt_loc->execute();
                                    $localizacion = $stmt_loc->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="mb-3">
                                        <label for="latitud<?= $subasta['id_subasta'] ?>" class="form-label">Latitud</label>
                                        <input type="text" class="form-control" id="latitud<?= $subasta['id_subasta'] ?>" name="latitud" value="<?= htmlspecialchars($localizacion['latitud']) ?>"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="altitud<?= $subasta['id_subasta'] ?>" class="form-label">Altitud</label>
                                        <input type="text" class="form-control" id="altitud<?= $subasta['id_subasta'] ?>" name="altitud" value="<?= htmlspecialchars($localizacion['altitud']) ?>"  >
                                    </div>

                                    <!-- Sección de SubastaDetalles -->
                                    <h6>Detalles de la Subasta</h6>
                                    <?php
                                    $stmt_det = $conn->prepare("SELECT * FROM SubastaDetalles WHERE id_subasta = :id_subasta");
                                    $stmt_det->bindParam(':id_subasta', $subasta['id_subasta']);
                                    $stmt_det->execute();
                                    $detalles = $stmt_det->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="mb-3">
                                        <label for="precio_medio<?= $subasta['id_subasta'] ?>" class="form-label">Precio Medio</label>
                                        <input type="number" class="form-control" id="precio_medio<?= $subasta['id_subasta'] ?>" name="precio_medio" value="<?= number_format($detalles['precio_medio'], 2) ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="precio_venta_medio<?= $subasta['id_subasta'] ?>" class="form-label">Precio Venta Medio</label>
                                        <input type="number" class="form-control" id="precio_venta_medio<?= $subasta['id_subasta'] ?>" name="precio_venta_medio" value="<?= number_format($detalles['precio_venta_medio'], 2) ?>" step="0.01"  >
                                    </div>

                                    <!-- Sección de Catastro -->
                                    <h6>Datos Catastrales</h6>
                                    <?php
                                    $stmt_catastro = $conn->prepare("SELECT * FROM Catastro WHERE id_subasta = :id_subasta");
                                    $stmt_catastro->bindParam(':id_subasta', $subasta['id_subasta']);
                                    $stmt_catastro->execute();
                                    $catastro = $stmt_catastro->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="mb-3">
                                        <label for="ref_catastral<?= $subasta['id_subasta'] ?>" class="form-label">Referencia Catastral</label>
                                        <input type="text" class="form-control" id="ref_catastral<?= $subasta['id_subasta'] ?>" name="ref_catastral" value="<?= htmlspecialchars($catastro['ref_catastral']) ?>"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="clase<?= $subasta['id_subasta'] ?>" class="form-label">Clase</label>
                                        <input type="text" class="form-control" id="clase<?= $subasta['id_subasta'] ?>" name="clase" value="<?= htmlspecialchars($catastro['clase']) ?>"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="uso_principal<?= $subasta['id_subasta'] ?>" class="form-label">Uso Principal</label>
                                        <input type="text" class="form-control" id="uso_principal<?= $subasta['id_subasta'] ?>" name="uso_principal" value="<?= htmlspecialchars($catastro['uso_principal']) ?>"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="sup_construida<?= $subasta['id_subasta'] ?>" class="form-label">Superficie Construida (m²)</label>
                                        <input type="number" class="form-control" id="sup_construida<?= $subasta['id_subasta'] ?>" name="sup_construida" value="<?= number_format($catastro['sup_construida'], 2) ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="vivienda<?= $subasta['id_subasta'] ?>" class="form-label">Vivienda (m²)</label>
                                        <input type="number" class="form-control" id="vivienda<?= $subasta['id_subasta'] ?>" name="vivienda" value="<?= number_format($catastro['vivienda'], 2) ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="garaje<?= $subasta['id_subasta'] ?>" class="form-label">Garaje (m²)</label>
                                        <input type="number" class="form-control" id="garaje<?= $subasta['id_subasta'] ?>" name="garaje" value="<?= number_format($catastro['garaje'], 2) ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="almacen<?= $subasta['id_subasta'] ?>" class="form-label">Almacén (m²)</label>
                                        <input type="number" class="form-control" id="almacen<?= $subasta['id_subasta'] ?>" name="almacen" value="<?= number_format($catastro['almacen'], 2) ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="ano_construccion<?= $subasta['id_subasta'] ?>" class="form-label">Año de Construcción</label>
                                        <input type="number" class="form-control" id="ano_construccion<?= $subasta['id_subasta'] ?>" name="ano_construccion" value="<?= $catastro['ano_construccion'] ?>"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="enlace_catastro<?= $subasta['id_subasta'] ?>" class="form-label">Enlace Catastro</label>
                                        <input type="url" class="form-control" id="enlace_catastro<?= $subasta['id_subasta'] ?>" name="enlace_catastro" value="<?= htmlspecialchars($catastro['enlace_catastro']) ?>"  >
                                    </div>

                                    <!-- Sección de Valoraciones -->
                                    <h6>Valoraciones</h6>
                                    <?php
                                    $stmt_val = $conn->prepare("SELECT * FROM Valoraciones WHERE id_subasta = :id_subasta");
                                    $stmt_val->bindParam(':id_subasta', $subasta['id_subasta']);
                                    $stmt_val->execute();
                                    $valoracion = $stmt_val->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="mb-3">
                                        <label for="fachada_y_exteriores<?= $subasta['id_subasta'] ?>" class="form-label">Fachada y Exteriores</label>
                                        <input type="number" class="form-control" id="fachada_y_exteriores<?= $subasta['id_subasta'] ?>" name="fachada_y_exteriores" value="<?= $valoracion['fachada_y_exteriores'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="techo_y_canaletas<?= $subasta['id_subasta'] ?>" class="form-label">Techo y Canaletas</label>
                                        <input type="number" class="form-control" id="techo_y_canaletas<?= $subasta['id_subasta'] ?>" name="techo_y_canaletas" value="<?= $valoracion['techo_y_canaletas'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="ventanas_y_puerta<?= $subasta['id_subasta'] ?>" class="form-label">Ventanas y Puertas</label>
                                        <input type="number" class="form-control" id="ventanas_y_puerta<?= $subasta['id_subasta'] ?>" name="ventanas_y_puerta" value="<?= $valoracion['ventanas_y_puerta'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="jardin_y_terrenos<?= $subasta['id_subasta'] ?>" class="form-label">Jardín y Terrenos</label>
                                        <input type="number" class="form-control" id="jardin_y_terrenos<?= $subasta['id_subasta'] ?>" name="jardin_y_terrenos" value="<?= $valoracion['jardin_y_terrenos'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="estado_estructuras<?= $subasta['id_subasta'] ?>" class="form-label">Estado de Estructuras</label>
                                        <input type="number" class="form-control" id="estado_estructuras<?= $subasta['id_subasta'] ?>" name="estado_estructuras" value="<?= $valoracion['estado_estructuras'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="instalaciones_visibles<?= $subasta['id_subasta'] ?>" class="form-label">Instalaciones Visibles</label>
                                        <input type="number" class="form-control" id="instalaciones_visibles<?= $subasta['id_subasta'] ?>" name="instalaciones_visibles" value="<?= $valoracion['instalaciones_visibles'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="vecindario<?= $subasta['id_subasta'] ?>" class="form-label">Vecindario</label>
                                        <input type="number" class="form-control" id="vecindario<?= $subasta['id_subasta'] ?>" name="vecindario" value="<?= $valoracion['vecindario'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="seguridad<?= $subasta['id_subasta'] ?>" class="form-label">Seguridad</label>
                                        <input type="number" class="form-control" id="seguridad<?= $subasta['id_subasta'] ?>" name="seguridad" value="<?= $valoracion['seguridad'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="ruido_y_olores<?= $subasta['id_subasta'] ?>" class="form-label">Ruido y Olores</label>
                                        <input type="number" class="form-control" id="ruido_y_olores<?= $subasta['id_subasta'] ?>" name="ruido_y_olores" value="<?= $valoracion['ruido_y_olores'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="acceso_y_estacionamiento<?= $subasta['id_subasta'] ?>" class="form-label">Acceso y Estacionamiento</label>
                                        <input type="number" class="form-control" id="acceso_y_estacionamiento<?= $subasta['id_subasta'] ?>" name="acceso_y_estacionamiento" value="<?= $valoracion['acceso_y_estacionamiento'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="localizacion<?= $subasta['id_subasta'] ?>" class="form-label">Localización</label>
                                        <input type="number" class="form-control" id="localizacion<?= $subasta['id_subasta'] ?>" name="localizacion" value="<?= $valoracion['localizacion'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="estado_inquilino<?= $subasta['id_subasta'] ?>" class="form-label">Estado del Inquilino</label>
                                        <input type="number" class="form-control" id="estado_inquilino<?= $subasta['id_subasta'] ?>" name="estado_inquilino" value="<?= $valoracion['estado_inquilino'] ?>" step="0.01"  >
                                    </div>
                                    <div class="mb-3">
                                        <label for="tipo_de_vivienda<?= $subasta['id_subasta'] ?>" class="form-label">Tipo de Vivienda</label>
                                        <input type="text" class="form-control" id="tipo_de_vivienda<?= $subasta['id_subasta'] ?>" name="tipo_de_vivienda" value="<?= htmlspecialchars($valoracion['tipo_de_vivienda']) ?>"  >
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Botones de Crear Nueva Subasta y Cerrar Sesión -->
    <div class="mt-8 flex justify-center space-x-4">
        <?php if ($isAdmin): ?>
            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out" data-bs-toggle="modal" data-bs-target="#crearSubastaModal">
                Crear Nueva Subasta
            </button>
        <?php endif; ?>
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
            Cerrar Sesión
        </a>
    </div>

    <!-- Aquí se incluye el modal -->
    <?php include 'crear_subasta_modal.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>