<?php
session_start();
require_once('../conexion/conexion.php');
$conexion = new database();
$conex = $conexion->connect();

if (isset($_POST['submit'])) {
    $correo = trim($_POST['correo']);
    $documento = trim($_POST['docu']);

    if (empty($correo) || empty($documento)) {
        echo '<script>alert("Ningún dato puede estar vacío");</script>';
    } else {
        // Verificar si el usuario existe
        $sql = $conex->prepare("SELECT * FROM usuarios WHERE Correo = ? AND Id_user = ?");
        $sql->execute([$correo, $documento]);
        $fila = $sql->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            $_SESSION['id'] = $fila['Id_user'];
            $_SESSION['email'] = $fila['Correo'];
            // Redirigir con los datos usando POST
            echo '<form id="sendForm" action="enviar_recuperacion.php" method="POST">
                    <input type="hidden" name="correo" value="' . htmlspecialchars($correo) . '">
                    <input type="hidden" name="docu" value="' . htmlspecialchars($documento) . '">
                </form>
                <script>docuxment.getElementById("sendForm").submit();</script>';
            exit;
        } else {
            echo '<script>alert("Correo o número de documento incorrectos");</script>';
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
    <link rel="stylesheet" href="../styles/recovery.css">
    <link rel="icon" href="../styles/icon2.png">
</head>
<body>
    <div class="container">
        <div class="welcome">
            <img src="../styles/icon2.png" alt="TeamTalks Logo" class="logo">
            <img src="../styles/1.png" alt="" class="img1">
        </div>
        <div class="login">
            <h2>¿Olvidaste tu contraseña?</h2>
            <p>No te preocupes, restableceremos tu contraseña, <br>
            solo dinos con qué dirección de e-mail te registraste <br>
            en TeamTalks.</p>
            <form action="" method="POST" autocomplete="off">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="Ingresa tu correo electrónico" required>

                <label for="docu">Documento</label>
                <input type="number" id="docu" name="docu" placeholder="Ingresa tu número de documento" required>

                <div class="buttons">
                    <a href="login.php"><button type="button" class="secondary-btn">Regresar</button></a>
                    <button type="submit" class="primary-btn" name="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
