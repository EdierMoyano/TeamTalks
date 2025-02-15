<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';

$conex = new database();
$con = $conex->connect();


?>

<?php
    $admin = $_SESSION ['documento'];
    $sql = $con -> prepare("SELECT * FROM usuarios WHERE Id_user = '$admin'");
    $sql ->execute();
    $fila = $sql->fetch();
?>



    <!-- admin/admin.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="../css/style.css">  <!-- Asegúrate de que la ruta sea correcta -->
</head>

    <h1>Panel de administración</h1>
    <label for="footer-color">Elige el color del footer:</label>
    <input type="color" id="footer-color" name="footer-color" value="#0E4A86">
    <button onclick="saveColor()">Guardar Color</button>

    <script>
        // Guardar el color elegido en localStorage
        function saveColor() {
            var color = document.getElementById("footer-color").value;
            localStorage.setItem("footerColor", color);  // Guardamos el color en localStorage
            alert("Color guardado");
        }
    </script>
</body>
</html>
