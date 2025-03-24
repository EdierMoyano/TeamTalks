<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';  

$database = new Database();
$con = $database->connect();

if (isset($_POST['subir'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['size'] > 0) {
        $fileName = $_FILES['csv_file']['tmp_name'];
        $file = fopen($fileName, "r");

        // Contador para seguimiento de filas procesadas
        $contador = 0;
        $errores = 0;
        $mensajes_error = [];

        // Leer la primera línea para verificar si son encabezados
        $primera_linea = fgetcsv($file, 10000, ";");
        
        // Si la primera columna es "Id_user", asumimos que es una línea de encabezados y la ignoramos
        $es_encabezado = false;
        if ($primera_linea && isset($primera_linea[0]) && strtolower($primera_linea[0]) === 'id_user') {
            $es_encabezado = true;
        } else {
            // Si no es encabezado, rebobinamos el archivo para leer desde el principio
            rewind($file);
        }

        while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
            $contador++;
            
            // Verificar si hay al menos 9 columnas (formato original)
            if (count($column) >= 9) { 
                $id_user = trim($column[0]); 
                $nombres = trim($column[1]);
                $correo = trim($column[2]);
                $password = trim($column[3]);
                $passw_enc = password_hash($password, PASSWORD_DEFAULT, array("cost" => 12)); 
                $avatar = NULL; 
                $telefono = trim($column[5]);
                $id_rol = trim($column[6]); 
                $id_estado = trim($column[7]); 
                $id_docu = trim($column[8]); 
                
                // La ficha es opcional - si existe una columna 10, la usamos
                // Reemplazamos comas por puntos en el número de ficha
                $numero_ficha = (count($column) >= 10) ? str_replace(',', '.', trim($column[9])) : null;
                
                // Verificar que los campos numéricos sean realmente números
                if (!is_numeric($id_user)) {
                    $mensajes_error[] = "Fila $contador: El ID de usuario '$id_user' no es un número válido.";
                    $errores++;
                    continue;
                }
                
                if (!is_numeric($id_rol)) {
                    $mensajes_error[] = "Fila $contador: El ID de rol '$id_rol' no es un número válido.";
                    $errores++;
                    continue;
                }
                
                if (!is_numeric($id_estado)) {
                    $mensajes_error[] = "Fila $contador: El ID de estado '$id_estado' no es un número válido.";
                    $errores++;
                    continue;
                }
                
                if (!is_numeric($id_docu)) {
                    $mensajes_error[] = "Fila $contador: El ID de documento '$id_docu' no es un número válido.";
                    $errores++;
                    continue;
                }
                
                // Verificar rol
                $checkRole = $con->prepare("SELECT * FROM roles WHERE Id_rol = ?");
                $checkRole->execute([$id_rol]);
                if ($checkRole->rowCount() == 0) {
                    $mensajes_error[] = "Fila $contador: Rol inválido ($id_rol) para usuario: $id_user";
                    $errores++;
                    continue; 
                }

                // Verificar estado
                $checkEstado = $con->prepare("SELECT * FROM estado WHERE Id_estado = ?");
                $checkEstado->execute([$id_estado]);
                if ($checkEstado->rowCount() == 0) {
                    $mensajes_error[] = "Fila $contador: Estado inválido ($id_estado) para usuario: $id_user";
                    $errores++;
                    continue; 
                }

                // Verificar documento
                $checkDocu = $con->prepare("SELECT * FROM identidad WHERE id_docu = ?");
                $checkDocu->execute([$id_docu]);
                if ($checkDocu->rowCount() == 0) {
                    $mensajes_error[] = "Fila $contador: Documento inválido ($id_docu) para usuario: $id_user";
                    $errores++;
                    continue; 
                }
                
                // Si es estudiante y tiene ficha, verificar que la ficha exista
                if ($id_rol == 3 && !empty($numero_ficha)) {
                    $checkFicha = $con->prepare("SELECT * FROM fichas WHERE numero_ficha = ?");
                    $checkFicha->execute([$numero_ficha]);
                    if ($checkFicha->rowCount() == 0) {
                        $mensajes_error[] = "Fila $contador: Ficha no encontrada ($numero_ficha) para el estudiante: $id_user";
                        $errores++;
                        continue;
                    }
                }

                // Verificar si el usuario existe
                $check = $con->prepare("SELECT * FROM usuarios WHERE Id_user = ?");
                $check->execute([$id_user]);

                // Iniciar transacción
                $con->beginTransaction();
                
                try {
                    if ($check->rowCount() > 0) {
                        // Actualizar usuario existente
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
                    
                    // Procesar la ficha solo para estudiantes (rol 3) que tengan ficha asignada
                    if ($id_rol == 3 && !empty($numero_ficha)) {
                        // Obtener la ficha
                        $getFicha = $con->prepare("SELECT * FROM fichas WHERE numero_ficha = ?");
                        $getFicha->execute([$numero_ficha]);
                        $ficha = $getFicha->fetch(PDO::FETCH_ASSOC);
                        
                        if ($ficha) {
                            // Buscar la clase asociada a esta ficha
                            $clase_nombre = "Clase " . $ficha['nombre_ficha'];
                            $getClase = $con->prepare("SELECT * FROM clases WHERE Nom_clase = ?");
                            $getClase->execute([$clase_nombre]);
                            
                            if ($getClase->rowCount() > 0) {
                                $clase = $getClase->fetch(PDO::FETCH_ASSOC);
                                $id_clase = $clase['Id_clase'];
                                
                                // Verificar si el estudiante ya está asignado a esta clase
                                $checkAsignacion = $con->prepare("SELECT * FROM usuarios_clases WHERE id_user = ? AND id_clase = ?");
                                $checkAsignacion->execute([$id_user, $id_clase]);
                                
                                if ($checkAsignacion->rowCount() == 0) {
                                    // Asignar el estudiante a la clase
                                    $insertAsignacion = $con->prepare("INSERT INTO usuarios_clases (id_user, id_clase) VALUES (?, ?)");
                                    $insertAsignacion->execute([$id_user, $id_clase]);
                                }
                            }
                        }
                    }
                    
                    // Confirmar la transacción
                    $con->commit();
                    
                } catch (Exception $e) {
                    // Revertir la transacción en caso de error
                    $con->rollBack();
                    $mensajes_error[] = "Fila $contador: Error: " . $e->getMessage();
                    $errores++;
                }
            } else {
                $mensajes_error[] = "Fila $contador tiene formato incorrecto: " . count($column) . " columnas";
                $errores++;
            }
        }
        fclose($file);
        
        // Guardar los mensajes de error en la sesión para mostrarlos
        if ($errores > 0) {
            $_SESSION['csv_errores'] = $mensajes_error;
            echo "<script>
                console.log('Errores encontrados:');
                " . implode("\n", array_map(function($msg) { return "console.log('$msg');"; }, $mensajes_error)) . "
                alert('Proceso completado con $errores errores. Revise la consola para más detalles.');
                window.location = 'index.php';
            </script>";
        } else {
            echo "<script>alert('Usuarios cargados exitosamente');</script>";
            echo "<script>window.location = 'index.php';</script>";
        }
    } else {
        echo "<script>alert('Archivo vacío o no válido');</script>";
        echo "<script>window.location = 'index.php';</script>";
    }
}
?>