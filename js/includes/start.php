<?php
session_start();
require_once('../conexion/conexion.php');
$conexion = new database();
$conex = $conexion->connect();


if (isset($_POST['submit'])) {
    $tipo = $_POST['role'];
    $docu = $_POST['document']; 
    $pass_desc = $_POST['password']; 

    if ($tipo == '' || $docu == '' || $pass_desc == '') {
        echo '<script>alert ("Ningún dato puede estar vacío")</script>';
        echo '<script>window.location = "../login.php"</script>';
    
    }


    
    $sql = $conex->prepare("SELECT * FROM usuarios WHERE Id_user = '$docu' AND id_docu = '$tipo'");
    $sql->execute();

    $fila = $sql->fetch(PDO::FETCH_ASSOC);

    
    if ($fila) {
        
        if (password_verify($pass_desc, $fila['Contrasena']) && ($fila['Id_estado'] == 1)) {
            
            $_SESSION ['tipo'] = $fila ['id_docu'];
            $_SESSION ['documento'] = $fila ['Id_user'];
            $_SESSION ['estado'] = $fila ['Id_estado'];
            $_SESSION ['rol'] = $fila ['Id_rol'];

            if ($_SESSION ['rol'] == 1) {
                header("Location: ../admin/admin.php");
                exit();
            }

            if ($_SESSION ['rol'] == 2) {
                header("Location: ../docente/index.html");
                exit();
            }

            if ($_SESSION ['rol'] == 3) {
                header("Location: ../estudiante/index.php");
                exit();
            }


        } else {
            
            echo '<script>alert ("Credenciales inválidas o Usuario inactivo")</script>';
            echo '<script>window.location = "../login/login.php"</script>';
        }
        }
        else {
            echo '<script>alert ("No se encontró el usuario")</script>';
            echo '<script>window.location = "../login/login.php"</script>';
        } 
}

