<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';  

$database = new Database();
$con = $database->connect();

if (isset($_POST['subir'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['size'] > 0) {
        $fileName = $_FILES['csv_file']['tmp_name'];
        $file = fopen($fileName, "r");

        while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
            if (count($column) >= 9) { 
                $id_user = $column[0]; 
                $nombres = $column[1];
                $correo = $column[2];
                $password = $column[3];
                $passw_enc = password_hash($password, PASSWORD_DEFAULT, array("cost" => 12)); 
                $avatar = NULL; 
                $telefono = $column[5];
                $id_rol = $column[6]; 
                $id_estado = $column[7]; 
                $id_docu = $column[8]; 

                
                $checkRole = $con->prepare("SELECT * FROM roles WHERE Id_rol = $id_rol");
                $checkRole->execute();
                if ($checkRole->rowCount() == 0) {
                    echo "<script>console.log('Rol inválido para usuario: $id_user');</script>";
                    continue; 
                }

                
                $checkEstado = $con->prepare("SELECT * FROM estado WHERE Id_estado = $id_estado");
                $checkEstado->execute();
                if ($checkEstado->rowCount() == 0) {
                    echo "<script>console.log('Estado inválido para usuario: $id_user');</script>";
                    continue; 
                }

                
                $checkDocu = $con->prepare("SELECT * FROM identidad WHERE id_docu = $id_docu");
                $checkDocu->execute();
                if ($checkDocu->rowCount() == 0) {
                    echo "<script>console.log('Documento inválido para usuario: $id_user');</script>";
                    continue; 
                }

                
                $check = $con->prepare("SELECT * FROM usuarios WHERE Id_user = $id_user");
                $check->execute();

                if ($check->rowCount() > 0) {
                    
                    $update = $con->prepare("UPDATE usuarios 
                        SET Nombres = :nombres, Correo = :correo, Contrasena = :passw_enc, 
                            Avatar = :avatar, Telefono = :telefono, Id_rol = :id_rol, 
                            Id_estado = :id_estado, id_docu = :id_docu, fecha_registro = NOW()
                        WHERE Id_user = :id_user");
                    $update->bindParam(':nombres', $nombres);
                    $update->bindParam(':correo', $correo);
                    $update->bindParam(':passw_enc', $passw_enc);
                    $update->bindParam(':avatar', $avatar);
                    $update->bindParam(':telefono', $telefono);
                    $update->bindParam(':id_rol', $id_rol);
                    $update->bindParam(':id_estado', $id_estado);
                    $update->bindParam(':id_docu', $id_docu);
                    $update->bindParam(':id_user', $id_user);
                    $update->execute();
                } else {
                    // Insertar nuevo usuario
                    $insert = $con->prepare("INSERT INTO usuarios 
                        (Id_user, Nombres, Correo, Contrasena, Avatar, Telefono, Id_rol, Id_estado, id_docu, fecha_registro) 
                        VALUES (:id_user, :nombres, :correo, :passw_enc, :avatar, :telefono, :id_rol, :id_estado, :id_docu, NOW())");
                    $insert->bindParam(':id_user', $id_user);
                    $insert->bindParam(':nombres', $nombres);
                    $insert->bindParam(':correo', $correo);
                    $insert->bindParam(':passw_enc', $passw_enc);
                    $insert->bindParam(':avatar', $avatar);
                    $insert->bindParam(':telefono', $telefono);
                    $insert->bindParam(':id_rol', $id_rol);
                    $insert->bindParam(':id_estado', $id_estado);
                    $insert->bindParam(':id_docu', $id_docu);
                    $insert->execute();
                }
                
            }
        }
        fclose($file);
        echo "<script>alert('Usuarios cargados exitosamente');</script>";
        echo "<script>window.location = 'index.php';</script>";
    } else {
        echo "<script>alert('Archivo vacío o no válido');</script>";
        echo "<script>window.location = 'index.php';</script>";
    }
}
?>
