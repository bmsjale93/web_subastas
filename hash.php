<?php
include('assets/php/database/config.php'); // Incluye la conexión a la base de datos

try {
    // Seleccionar todos los usuarios
    $stmt = $conn->prepare("SELECT * FROM USUARIOS");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($usuarios as $usuario) {
        // Hashear la contraseña actual (que se asume está en texto plano)
        $hashed_password = password_hash($usuario['contrasena'], PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $updateStmt = $conn->prepare("UPDATE USUARIOS SET contrasena = :contrasena WHERE usuario = :usuario");
        $updateStmt->bindParam(':contrasena', $hashed_password);
        $updateStmt->bindParam(':usuario', $usuario['usuario']);
        $updateStmt->execute();
    }

    echo "Contraseñas actualizadas con éxito";
} catch (PDOException $e) {
    echo "Error al actualizar las contraseñas: " . $e->getMessage();
}
