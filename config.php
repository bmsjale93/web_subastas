<?php
$servername = "localhost"; 
$username = "u738526118_subastasfdm"; 
$password = "FDM@2020alejandro"; 
$dbname = "u738526118_subastas_fdm"; 

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
