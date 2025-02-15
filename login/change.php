<?php
session_start();
require_once('../conexion/conexion.php');
$conexion = new database();
$conex = $conexion->connect();

if (!isset($_GET['token'])) {
    echo '<script>alert("Acceso no autorizado.");</script>';
    echo '<script>window.location = "recovery.php";</script>';
    exit;
}

$token = $_GET['token'];
$query = $conex->prepare("SELECT Id_user, Correo FROM usuarios WHERE reset_token = ? AND reset_expira > NOW()");
$query->execute([$token]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo '<script>alert("El token es inválido o ha expirado.");</script>';
    echo '<script>window.location = "recovery.php";</script>';
    exit;
}

$id = $user['Id_user'];
$email = $user['Correo'];

if (isset($_POST['submit'])) {
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    if (strlen($password1) < 6) {
        echo '<script>alert("La contraseña debe tener al menos 6 caracteres.");</script>';
    } elseif ($password1 !== $password2) {
        echo '<script>alert("Las contraseñas no coinciden.");</script>';
    } else {
        $hashedPassword = password_hash($password2, PASSWORD_DEFAULT, array("cost" => 12));
        $update = $conex->prepare("UPDATE usuarios SET Contrasena = ?, reset_token = NULL, reset_expira = NULL WHERE Id_user = ? AND Correo = ?");
        $update->execute([$hashedPassword, $id, $email]);

        if ($update) {
            echo '<script>alert("Contraseña actualizada exitosamente.");</script>';
            echo '<script>window.location = "login.php";</script>';
        } else {
            echo '<script>alert("Error al actualizar la contraseña.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="../styles/change.css">
    <link rel="icon" href="../styles/icon2.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container">
        <div class="welcome">
            <img src="../styles/icon2.png" alt="TeamTalks Logo" class="logo">
            <img src="../styles/2.png" alt="" class="img1">
        </div>
        <div class="login">
            <h2>Ingresa una nueva contraseña</h2>
            
            <form action="#" method="POST" autocomplete="off">
                <label for="password1">Contraseña</label>
                <input type="password" id="password1" name="password1" placeholder="Ingresa la nueva contraseña" required>
                <i class='bx bx-show' id="showpass1" onclick="showpass1()"></i>

                <label for="password2">Confirmar contraseña</label>
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
</body>
</html>
