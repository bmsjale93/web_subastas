<?php
session_start();
include('assets/php/database/config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    try {
        // Preparar y ejecutar la consulta SQL utilizando PDO
        $stmt = $conn->prepare("SELECT * FROM USUARIOS WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        // Si se encuentra el usuario, verificar la contraseña
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña ingresada con la hash almacenada
            if (password_verify($contrasena, $user['contrasena'])) {
                $_SESSION['usuario'] = $usuario; // Guardar el usuario en la sesión

                // Verificar y guardar el tipo de usuario en la sesión
                if ($user['id_tipo_usuario'] == 1) {
                    $_SESSION['tipo_usuario'] = 'admin';
                } else {
                    $_SESSION['tipo_usuario'] = 'usuario';
                }

                echo json_encode(['success' => true]); // Devolver éxito como JSON
            } else {
                echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
        }
    } catch (PDOException $e) {
        // En caso de error de conexión, devolver un mensaje de error
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    }
}
