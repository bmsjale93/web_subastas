<?php
session_start();
if (isset($_SESSION['usuario'])) {
    // Si el usuario ha iniciado sesión, redirigir a la página de subastas
    header('Location: subastas.php');
    exit();
} else {
    // Si el usuario no ha iniciado sesión, redirigir al formulario de login
    header('Location: login.html');
    exit();
}
