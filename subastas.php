<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit();
}

include('config.php');

try {
    // Obtener todas las subastas de la base de datos
    $stmt = $conn->prepare("SELECT * FROM Subastas");
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
        "Estudiando..." => "bg-yellow-200 text-yellow-800",
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
                        <span class="<?= getProcessColor('Terminada') ?> px-3 py-2 rounded-xl">
                            Terminada
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Botón de Cerrar Sesión -->
    <div class="mt-8">
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
            Cerrar Sesión
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>