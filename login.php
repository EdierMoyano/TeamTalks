<?php

session_start();
require_once('conexion/conexion.php');
$conexion = new database();
$conex = $conexion->connect();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles/login.css">
    <link rel="icon" href="styles/icon2.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
</head>
<body>
    <div class="container">
        <div class="welcome">
            <img src="styles/icon2.png" alt="TeamTalks Logo" class="logo">
            <h1>Hola</h1>
            <h2>Bienvenido!</h2>
            <p>
                Gracias por preferir <strong>TeamTalks.</strong><br>
                Estamos comprometidos con el desarrollo de un <br> 
                ambiente de estudio especial para nuestros <br>
                usuarios.
            </p>
        </div>
        <div class="login">
            <h2>Iniciar sesión</h2>
            <form action="includes/start.php" method="POST" autocomplete="off" id="formulario">
                
                <label for="documentType">Tipo de documento</label>
                <select name="role" required>
                            <option value="">Selecciona</option>
                            <?php
                                $sql = $conex->prepare("SELECT * FROM identidad");
                                $sql->execute();
                                while ($fila=$sql->fetch(PDO::FETCH_ASSOC)) {
                                    echo  "<option value=" . $fila['id_docu']. ">" . $fila['docu'] ." </option>";
                                }

                            ?>
                            
                    </select>

                <label for="documentId">Documento de identidad</label>
                <input type="text" id="documentId" name="document" value="" placeholder="Ingresa tu documento" required>
                <p class="docu_error " id="docu_error" >¡Documento inválido!</p>


                <label for="password" class="contra">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                <i class='bx bx-show' id="showpass" onclick="showpass()"></i>

                <a href="recovery.php" class="forgot-password">¿Olvidaste la contraseña?</a>

                <div class="buttons">
                    <a href="Index/index.html"><button type="button" class="secondary-btn">Regresar</button></a>
                    <button type="submit" class="primary-btn " name="submit">Iniciar sesión</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showpass() {
            const passw = document.getElementById("password");
            const iconshow = document.getElementById("showpass");
            
            if (passw.type === "password") {
                passw.type = "text";
                iconshow.classList.replace("bx-show", "bx-hide");
            } else {
                passw.type = "password";
                iconshow.classList.replace("bx-hide", "bx-show");
            }
        }
    </script>

    <script src="scripts/login.js"></script>

</body>
</html>
