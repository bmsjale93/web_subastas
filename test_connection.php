// test_connection.php
<?php
require 'db.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo 'Conexión exitosa!';
} else {
    echo 'Error en la conexión a la base de datos.';
}
?>