<?php
session_start();
require_once('conexion/conexion.php');
$conexion = new database();
$conex = $conexion->connect();

if (!isset($_SESSION['id']) || !isset($_SESSION['email'])) {
    echo '<script>alert("Acceso no autorizado.");</script>';
    echo '<script>window.location = "recovery.php";</script>';
    exit;
}

if (isset($_POST['submit'])) {
    $id = $_SESSION['id'];
    $email = $_SESSION['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    if ($password1 !== $password2) {
        echo '<script>alert("Las contraseñas no coinciden.");</script>';
        echo '<script>window.location = "change.php";</script>';
        exit;
    }

    
    $hashedPassword = password_hash($password2, PASSWORD_DEFAULT, array("cost" => 12)); 

    
    $update = $conex->prepare("UPDATE usuarios SET Contrasena = '$hashedPassword' WHERE Id_user = '$id' AND Correo = '$email'");
    $update ->execute();

    if ($update) {
        echo '<script>alert("Contraseña actualizada exitosamente.");</script>';
        echo '<script>window.location = "login.php";</script>';
    } else {
        echo '<script>alert("Error al actualizar la contraseña.");</script>';
        echo '<script>window.location = "change.php";</script>';
    }
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="styles/change.css">
    <link rel="icon" href="styles/icon2.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
</head>
<body>
    <div class="container">
        <div class="welcome">
            <img src="styles/icon2.png" alt="TeamTalks Logo" class="logo">
            <img src="styles/2.png" alt="" class="img1">
        </div>
        <div class="login">
            <h2>Ingresa una nueva contraseña</h2>
            
            <form action="#" method="POST" autocomplete="off">
                <label for="documentId">Contraseña</label>
                <input type="password" id="password1" name="password1" placeholder="Ingresa la nueva contraseña" required>
                <i class='bx bx-show' id="showpass1" onclick="showpass1()"></i>

                <label for="documentId">Confirmar contraseña</label>
                <input type="password" id="password2" name="password2" placeholder="Vuelve a ingresar la nueva contraseña" required>
                <i class='bx bx-show' id="showpass2" onclick="showpass2()"></i>
                <p class="coincide" id="coincide">¡Las contraseñas no coinciden!</p>
            
                <div class="buttons">
                    
                    <button name="submit" type="submit" class="primary-btn">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showpass1() {
            const passw = document.getElementById("password1");
            const iconshow = document.getElementById("showpass1");
            
            if (passw.type === "password") {
                passw.type = "text";
                iconshow.classList.replace("bx-show", "bx-hide");
            } else {
                passw.type = "password";
                iconshow.classList.replace("bx-hide", "bx-show");
            }
        }
    </script>

    <script>
        function showpass2() {
            const passw = document.getElementById("password2");
            const iconshow = document.getElementById("showpass2");
            
            if (passw.type === "password") {
                passw.type = "text";
                iconshow.classList.replace("bx-show", "bx-hide");
            } else {
                passw.type = "password";
                iconshow.classList.replace("bx-hide", "bx-show");
            }
        }
    </script>

    <script src="scripts/change.js"></script>

</body>
</html>