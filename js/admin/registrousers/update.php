<?php


require_once('../../conexion/conexion.php');
include '../../includes/session.php';
$conex = new database();
$con = $conex->connect();

?>

<?php

$cod = $_GET['id'];

    
$docume = $_SESSION ['documento'];
    $sql = $con -> prepare("SELECT * FROM usuarios INNER JOIN roles ON usuarios.Id_rol = roles.Id_rol INNER JOIN estado ON usuarios.Id_estado = estado.Id_estado INNER JOIN identidad ON usuarios.id_docu = identidad.id_docu WHERE Id_user = '$cod'");
    $sql->execute();
    $fila = $sql ->fetch();

if (!$fila) {
    echo "<script>alert('Usuario no encontrado')</script>";
    exit();
}
?>

<?php

if (isset($_POST['submit'])) {
    $id = $_POST['id_'];
    $documen = $_POST['tipo'];
    $correo= $_POST['correo'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    

    $update = $con->prepare("UPDATE usuarios SET id_docu = $documen, Id_rol = $rol, Id_estado = $estado, Correo = '$correo' WHERE Id_user = $id");
    $update -> execute();
    echo '<script>alert("Updated Data");</script>';
    echo '<script>window.close();</script>';
    exit();
   
}
?>


<!DOCTYPE html>
<html lang="en">
    <script>
        function centrar() {
            iz = (screen.width-document.body.clientWidth) /2;
            der = (screen.height-document.body.clientHeight) /2;
            moveTo(iz, der); 
        }
    </script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles_registro/registro.css">
    <title>Document</title>
</head>
<body onload="centrar();">

<form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
    <table class="table2" border="2">
        <tr>
                <th class="tipo2">Tipo documento</th>
                <th class="documento2">Documento</th>
                <th class="nombres2">Nombres</th>
                <th class="correo2">Correo</th>
                <th class="rol2">Rol</th>
                <th class="estado2">Estado</th>
                <th class="registro2">Fecha de registro</th>
                
            
        </tr>

        <tr>
        <td name="fila"><select class="act_tipo" name="tipo">
            <option value="<?php echo $fila['id_docu']?>"><?php echo $fila['docu']?></option>
                                <?php
                                    $sql1 = $con->prepare("SELECT * FROM identidad");
                                    $sql1->execute();
                                    while ($role=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                        echo  "<option value=" . $role['id_docu']. ">" . $role['docu'] ." </option>";
                                    }

                                ?>
                                </select></td>

        <td name="fila"><input name="id_" type="text" readonly value="<?php echo $fila['Id_user']?>"></td>
        <td name="fila"><input type="text" readonly value="<?php echo $fila['Nombres']?>"></td>
        <td name="fila"><input class="act_correo" name="correo" type="text" value="<?php echo $fila['Correo']?>"></td>

        <td name="fila"><select class="act_rol" name="rol">
                                <option value="<?php echo $fila['Id_rol']?>"><?php echo $fila['Tipo_rol']?></option>
                                <?php
                                    $sql1 = $con->prepare("SELECT * FROM roles");
                                    $sql1->execute();
                                    while ($role=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                        echo  "<option value=" . $role['Id_rol']. ">" . $role['Tipo_rol'] ." </option>";
                                    }

                                ?>
                                </select></td>

        <td name="fila"><select class="act_est" name="estado">
                                <option value="<?php echo $fila['Id_estado']?>"><?php echo $fila['Tipo_estado']?></option>
                                <?php
                                    $sql1 = $con->prepare("SELECT * FROM estado");
                                    $sql1->execute();
                                    while ($state=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                        echo  "<option value=" . $state['Id_estado']. ">" . $state['Tipo_estado'] ." </option>";
                                    }

                                ?>
                                </select></td>

        <td name="fila"><input type="text" readonly value="<?php echo $fila['fecha_registro']?>"></td>
        



        </tr>
    </table>

    <button class="actualizarr" value= "submit" type="submit" name="submit">Actualizar</button>
    <button class="actualizarr" type="button" onclick="window.close();">Cancelar</button>

</form>

