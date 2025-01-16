<?php

session_start();

$timeout = 200000000;

if (!isset($_SESSION['documento'])) {
    
    echo '<script>alert("Credenciales incorrectas.")</script>';
    echo '<script>window.location = "../../Index/index.html"</script>';
    exit();
}

if (isset($_SESSION['afk']) && (time() - $_SESSION['afk']) > $timeout) {
    
    unset($_SESSION['documento']);
    unset($_SESSION['tipo']);
    unset($_SESSION['estado']);
    unset($_SESSION['rol']);
    session_destroy();
    session_write_close();

    
    echo '<script>alert("Session expired. Log in again.")</script>';
    echo '<script>window.location = "../../login.php"</script>';
    exit();
}


$_SESSION['afk'] = time();