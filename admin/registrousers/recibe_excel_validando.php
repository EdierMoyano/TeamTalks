<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';  

$database = new Database();
$con = $database->connect();

if (isset($_POST['subir'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['size'] > 0) {
        $fileName = $_FILES['csv_file']['tmp_name'];
        
        // Leer el contenido del archivo
        $fileContent = file_get_contents($fileName);
        
        // Detectar el tipo de fin de línea (Windows o Unix)
        $lineEnding = (strpos($fileContent, "\r\n") !== false) ? "\r\n" : "\n";
        
        // Dividir el contenido en líneas
        $lines = explode($lineEnding, $fileContent);
        
        // Contador para seguimiento de filas procesadas
        $contador = 0;
        $errores = 0;
        $mensajes_error = [];
        $usuarios_procesados = 0;
        
        // Verificar si la primera línea es un encabezado
        $primera_linea = explode(";", $lines[0]);
        $es_encabezado = false;
        
        if (isset($primera_linea[0]) && strtolower($primera_linea[0]) === 'id_user') {
            $es_encabezado = true;
            // Saltar la primera línea si es un encabezado
            array_shift($lines);
        }
        
        // Procesar cada línea
        foreach ($lines as $line) {
            $contador++;
            
            // Saltar líneas vacías
            if (trim($line) === '') {
                continue;
            }
            
            // Dividir la línea por punto y coma
            $column = explode(";", $line);
            
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
                $numero_ficha = (count($column) >= 10) ? trim($column[9]) : null;
                
                // Verificar que los campos no estén vacíos
                if (empty($id_user) || empty($nombres) || empty($correo) || empty($password) || 
                    empty($id_rol) || empty($id_estado) || empty($id_docu)) {
                    $mensajes_error[] = "Fila $contador: Faltan campos obligatorios.";
                    $errores++;
                    continue;
                }
                
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
                
                // Si es estudiante (rol 3), verificar que tenga ficha
                if ($id_rol == 3 && empty($numero_ficha)) {
                    $mensajes_error[] = "Fila $contador: El estudiante con ID $id_user debe tener una ficha asignada.";
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
                            $id_ficha = $ficha['id_ficha'];
                            
                            // Buscar todas las clases asociadas a esta ficha
                            $getClases = $con->prepare("SELECT * FROM clases WHERE id_ficha = ?");
                            $getClases->execute([$id_ficha]);
                            $clases = $getClases->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($clases) > 0) {
                                // Hay clases existentes para esta ficha
                                foreach ($clases as $clase) {
                                    $id_clase = $clase['Id_clase'];
                                    $id_materia = $clase['Id_materia'];
                                    
                                    // Verificar si el estudiante ya está asignado a esta clase
                                    $checkAsignacion = $con->prepare("SELECT * FROM usuarios_clases WHERE id_user = ? AND id_clase = ?");
                                    $checkAsignacion->execute([$id_user, $id_clase]);
                                    
                                    if ($checkAsignacion->rowCount() == 0) {
                                        // Asignar el estudiante a la clase
                                        $insertAsignacion = $con->prepare("INSERT INTO usuarios_clases (id_user, id_clase, id_materia) VALUES (?, ?, ?)");
                                        $insertAsignacion->execute([$id_user, $id_clase, $id_materia]);
                                        
                                        // Registrar en el log
                                        $mensajes_error[] = "INFO: Usuario $id_user asignado a clase existente $id_clase con materia $id_materia";
                                    } else {
                                        $mensajes_error[] = "INFO: Usuario $id_user ya estaba asignado a clase $id_clase";
                                    }
                                }
                            } else {
                                // No hay clases para esta ficha, crear una clase predeterminada
                                $clase_nombre = "Clase " . $ficha['nombre_ficha'];
                                
                                // Verificar si hay tareas
                                $checkTareas = $con->prepare("SELECT COUNT(*) as total FROM tareas");
                                $checkTareas->execute();
                                $totalTareas = $checkTareas->fetch(PDO::FETCH_ASSOC)['total'];
                                
                                if ($totalTareas == 0) {
                                    // Crear una tarea predeterminada
                                    $insertTarea = $con->prepare("INSERT INTO tareas (Titulo_tarea, Desc_tarea, Fecha_entreg) VALUES (?, ?, NOW())");
                                    $insertTarea->execute(["Tarea Predeterminada", "Tarea creada automáticamente"]);
                                    $id_tarea = $con->lastInsertId();
                                } else {
                                    // Obtener una tarea existente
                                    $getTarea = $con->prepare("SELECT Id_tarea FROM tareas LIMIT 1");
                                    $getTarea->execute();
                                    $tarea = $getTarea->fetch(PDO::FETCH_ASSOC);
                                    $id_tarea = $tarea['Id_tarea'];
                                }
                                
                                // Obtener materias
                                $getMaterias = $con->prepare("SELECT Id_materia FROM materia LIMIT 1");
                                $getMaterias->execute();
                                
                                if ($getMaterias->rowCount() > 0) {
                                    $materia = $getMaterias->fetch(PDO::FETCH_ASSOC);
                                    $id_materia = $materia['Id_materia'];
                                    
                                    // Generar un nuevo ID para la clase (mayor que 0)
                                    $getMaxId = $con->prepare("SELECT MAX(Id_clase) as max_id FROM clases");
                                    $getMaxId->execute();
                                    $maxId = $getMaxId->fetch(PDO::FETCH_ASSOC)['max_id'];
                                    $newId = max(1, ($maxId ? $maxId : 0) + 1); // Asegurarse de que sea mayor que 0
                                    
                                    // Insertar la nueva clase
                                    $insertClase = $con->prepare("
                                        INSERT INTO clases (Id_clase, Nom_clase, Id_tarea, Id_materia, Id_user, id_ficha) 
                                        VALUES (?, ?, ?, ?, ?, ?)
                                    ");
                                    $insertClase->execute([$newId, $clase_nombre, $id_tarea, $id_materia, $_SESSION['documento'], $id_ficha]);
                                    $id_clase = $newId;
                                    
                                    // Asignar el estudiante a la clase
                                    $insertAsignacion = $con->prepare("INSERT INTO usuarios_clases (id_user, id_clase, id_materia) VALUES (?, ?, ?)");
                                    $insertAsignacion->execute([$id_user, $id_clase, $id_materia]);
                                    
                                    // Registrar en el log
                                    $mensajes_error[] = "INFO: Creada nueva clase $id_clase ($clase_nombre) para ficha $numero_ficha y asignado usuario $id_user";
                                } else {
                                    $mensajes_error[] = "ERROR: No se encontraron materias en el sistema para crear una clase para la ficha $numero_ficha";
                                    throw new Exception("No se encontraron materias en el sistema");
                                }
                            }
                        }
                    }
                    
                    // Confirmar la transacción
                    $con->commit();
                    $usuarios_procesados++;
                    
                } catch (Exception $e) {
                    // Revertir la transacción en caso de error
                    $con->rollBack();
                    $mensajes_error[] = "Fila $contador: Error: " . $e->getMessage();
                    $errores++;
                }
            } else {
                // Solo reportar error si la línea no está vacía
                if (trim($line) !== '') {
                    $mensajes_error[] = "Fila $contador tiene formato incorrecto: " . count($column) . " columnas. Contenido: " . htmlspecialchars($line);
                    $errores++;
                }
            }
        }
        
        // Guardar los mensajes de error en la sesión para mostrarlos
        if ($errores > 0 || !empty($mensajes_error)) {
            $_SESSION['csv_errores'] = $mensajes_error;
            echo "<script>
                console.log('Información del proceso:');
                " . implode("\n", array_map(function($msg) { return "console.log('" . str_replace("'", "\\'", $msg) . "');"; }, $mensajes_error)) . "
                alert('Proceso completado con $errores errores y $usuarios_procesados usuarios procesados correctamente. Revise la consola para más detalles.');
                window.location = 'index.php';
            </script>";
        } else {
            echo "<script>alert('$usuarios_procesados usuarios cargados exitosamente');</script>";
            echo "<script>window.location = 'index.php';</script>";
        }
    } else {
        echo "<script>alert('Archivo vacío o no válido');</script>";
        echo "<script>window.location = 'index.php';</script>";
    }
}
?>