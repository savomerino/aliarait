<?php
// Autenticación del sistema de presupuestos

session_start();

// Contraseña correcta
$CONTRASEÑA_CORRECTA = 'Al!araIT26';

// Si viene un POST, validar la contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    if ($password === $CONTRASEÑA_CORRECTA) {
        // Contraseña correcta - guardar sesión
        $_SESSION['autenticado'] = true;
        $_SESSION['login_time'] = time();
        
        // Redirigir a index.php
        header('Location: index.php');
        exit;
    } else {
        // Contraseña incorrecta - redirigir a login con error
        header('Location: login.html?error=1');
        exit;
    }
}

// Si llega acá, redirigir a login
header('Location: login.html');
exit;
?>
