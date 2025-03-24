<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';

$conex = new database();
$con = $conex->connect();
?>

<?php
if (isset($_POST['ingreso'])) {
    $tipo = $_POST['tipo'];
    $docu = $_POST['docum'];
    $name = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['pass'];
    $contra = password_hash($password, PASSWORD_DEFAULT, array("cost" => 12));
    $tel = $_POST['tel'];
    $avatar = NULL;
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    $ficha = isset($_POST['ficha']) ? trim($_POST['ficha']) : ''; // Nuevo campo ficha

    $sql4 = $con->prepare("SELECT * FROM usuarios WHERE Id_user = ? AND Correo = ?");
    $sql4->execute([$docu, $correo]);
    $user = $sql4->fetch();

    if($user) {
        echo "<script>alert('El usuario ya está registrado');</script>";
        echo "<script>window.location = 'index.php';</script>";
    } else {
        // Verificar si la ficha existe cuando es estudiante y se proporciona una ficha
        if ($rol == 3 && !empty($ficha)) {
            $checkFicha = $con->prepare("SELECT * FROM fichas WHERE numero_ficha = ?");
            $checkFicha->execute([$ficha]);
            if ($checkFicha->rowCount() == 0) {
                echo "<script>alert('La ficha $ficha no existe. Debe crear la ficha antes de registrar estudiantes con esta ficha.');</script>";
                echo "<script>window.location = 'index.php';</script>";
                exit;
            }
        }

        // Iniciar transacción
        $con->beginTransaction();
        
        try {
            // Insertar usuario
            $ing = $con->prepare("INSERT INTO usuarios(Id_user, Nombres, Correo, Contrasena, Avatar, Telefono, Id_rol, Id_estado, id_docu, fecha_registro,ficha)VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?, NOW())");
            $ing->execute([$docu, $name, $correo, $contra, $avatar, $tel, $rol, $estado, $tipo]);
            
            // Si es estudiante y tiene ficha, asignarlo a la clase correspondiente
            if ($rol == 3 && !empty($ficha)) {
                // Obtener la ficha
                $getFicha = $con->prepare("SELECT * FROM fichas WHERE numero_ficha = ?");
                $getFicha->execute([$ficha]);
                $ficha_data = $getFicha->fetch(PDO::FETCH_ASSOC);
                
                if ($ficha_data) {
                    // Buscar la clase asociada a esta ficha
                    $clase_nombre = "Clase " . $ficha_data['nombre_ficha'];
                    $getClase = $con->prepare("SELECT * FROM clases WHERE Nom_clase = ?");
                    $getClase->execute([$clase_nombre]);
                    
                    if ($getClase->rowCount() > 0) {
                        $clase = $getClase->fetch(PDO::FETCH_ASSOC);
                        $id_clase = $clase['Id_clase'];
                        
                        // Asignar el estudiante a la clase
                        $insertAsignacion = $con->prepare("INSERT INTO usuarios_clases (id_user, id_clase) VALUES (?, ?)");
                        $insertAsignacion->execute([$docu, $id_clase]);
                    }
                }
            }
            
            // Confirmar la transacción
            $con->commit();
            echo "<script>alert('Usuario registrado exitosamente');</script>";
            echo "<script>window.location = 'index.php';</script>";
            
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $con->rollBack();
            echo "<script>alert('Error al registrar el usuario: " . $e->getMessage() . "');</script>";
            echo "<script>window.location = 'index.php';</script>";
        }
    }
}
?>

<?php
    $admin = $_SESSION ['documento'];
    $sql = $con->prepare("SELECT * FROM usuarios WHERE Id_user = '$admin'");
    $sql->execute();
    $fila = $sql->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar usuarios</title>
    <link type="text/css" rel="shortcut icon" href="../../styles/icon2.png"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/styles-dashboard.css">
    <link rel="stylesheet" href="styles_registro/registro.css">
    <style>
        /* Estilos adicionales para el campo de ficha */
        .ficha {
            width: 120px;
        }
        
        /* Estilo para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="menu-dashboard">
        <!-- Menu superior -->
        <div class="top-menu">
            <div class="logo">
                <img src="../img/logo.png" alt="logo">
                <span><h5><?php echo $fila['Nombres']; ?> <br>Administrador</h5></span>
            </div>
        </div>

        <!-- Menu lateral -->
        <div class="menu">
            <div class="enlace">
                <i class='bx bx-grid-alt'></i>
                <a href="../admin.php" class="boton-menu">Dashboard</a>
            </div>
            <div class="enlace">
                <i class='bx bx-user'></i>
                <a href="../usersactivos/index.php" class="boton-menu">Usuarios Activos</a>
            </div>
            <div class="enlace">
                <i class='bx bx-user-x'></i>
                <a href="../usersinactivos/index.php" class="boton-menu">Usuarios Inactivos</a>
            </div>
            <div class="enlace">
                <i class='bx bx-user-plus'></i>
                <a href="index.php" class="boton-menu">Registrar Usuarios</a>
            </div>
            <div class="enlace">
                <i class='bx bx-book'></i>
                <a href="../fichas/fichas.php" class="boton-menu">Fichas</a>
            </div>
            <div class="enlace">
                <i class='bx bx-book'></i>
                <a href="../clases/clases.php" class="boton-menu">Clases</a>
            </div>
            <div class="enlace">
                <i class='bx bx-library'></i>
                <a href="#" class="boton-menu">Materias</a>
            </div>
            <div class="enlace">
                <i class='bx bx-conversation'></i>
                <a href="#" class="boton-menu">Temas Foros</a>
            </div>
            <div class="enlace">
                <i class='bx bx-time'></i>
                <a href="#" class="boton-menu">Horarios</a>
            </div>
            <div class="enlace">
                <i class='bx bx-line-chart'></i>
                <a href="#" class="boton-menu">Analíticas</a>
            </div>
            <div class="enlace">
                <i class='bx bx-file'></i>
                <a href="#" class="boton-menu">Reportes</a>
            </div>
            <div class="enlace">
                <i class='bx bx-shield'></i>
                <a href="#" class="boton-menu">Seguridad</a>
            </div>
            <div class="enlace">
                <i class='bx bx-log-out'></i>
                <a href="../../includes/close.php" class="boton-menu">Cerrar sesión</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="title">
            <h1>Registro</h1><br>
            <p class="parrafo">Aqui puedes subir tu archivo .csv para registrar muchos usuarios a la vez</p>
        </div>

        <form action="recibe_excel_validando.php" method="POST" enctype="multipart/form-data">
            <div class="cargar">
                <label class="boton-seleccionar">
                    <strong><input type="file" name="csv_file" accept=".csv" required>
                    Seleccionar archivo</strong>
                </label>
            </div>
            <div class="subir">
                <button type="submit" name="subir">Subir archivo</button>
            </div>
        </form>

        <form action="" method="POST" autocomplete="off">
            <table class="tableB" border="2">
                <tr>
                    <th class="tipo">Tipo documento</th>
                    <th class="documento">Documento</th>
                    <th class="nombres">Nombres</th>
                    <th class="correo">Correo</th>
                    <th class="contraseña">Contraseña</th>
                    <th class="telefono">Telefono</th>
                    <th class="rol">Rol</th>
                    <th class="estado">Estado</th>
                    <th class="ficha">Ficha</th>
                </tr>

                <tr>
                    <td name="fila"><select class="act_tipo" name="tipo" required>
                        <option value="">Tipo de documento</option>
                                <?php
                                    $sql1 = $con->prepare("SELECT * FROM identidad");
                                    $sql1->execute();
                                    while ($role=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                        echo  "<option value=" . $role['id_docu']. ">" . $role['docu'] ." </option>";
                                    }
                                ?>
                        </select></td>

                    <td name="fila"><input type="number" name="docum" placeholder="N° documento" required></td>
                    <td name="fila"><input type="text" name="nombre" placeholder="Nombres" required></td>
                    <td name="fila"><input type="email" name="correo" placeholder="Correo electrónico" required></td>
                    <td name="fila"><input type="text" name="pass" placeholder="Contraseña" required></td>
                    <td name="fila"><input type="number" name="tel" placeholder="N° de teléfono" required></td>
                    <td name="fila"><select class="act_rol" name="rol" id="rol_select" required>
                        <option value="">Rol</option>
                            <?php
                            $sql1 = $con->prepare("SELECT * FROM roles");
                            $sql1->execute();
                            while ($role=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                echo  "<option value=" . $role['Id_rol']. ">" . $role['Tipo_rol'] ." </option>";
                            }
                        ?>
                        </select></td>

                    <td name="fila"><select class="act_est" name="estado" required>
                        <option value="">Estado</option>
                            <?php
                            $sql1 = $con->prepare("SELECT * FROM estado");
                            $sql1->execute();
                            while ($state=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                echo  "<option value=" . $state['Id_estado']. ">" . $state['Tipo_estado'] ." </option>";
                            }
                        ?>
                        </select></td>
                    
                    <td name="fila"><input type="text" name="ficha" id="ficha_input" placeholder="N° de ficha"></td>
                </tr>
            </table>

            <button type="submit" name="ingreso" class="registro">Registrar</button>
        </form>

        <!-- Modal para ficha no encontrada -->
        <div id="fichaModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Ficha no encontrada</h2>
                <p>La ficha ingresada no existe. Debe crear la ficha antes de registrar estudiantes con esta ficha.</p>
                <a href="../fichas/fichas.php" class="btn-crear-ficha" style="display: inline-block; margin-top: 15px; padding: 8px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Ir a crear ficha</a>
            </div>
        </div>

        <!-- Tabla para mostrar datos -->
        <table class="tableA" border="2">
            <tr>
                <th class="tipo">Tipo documento</th>
                <th class="documento">Documento</th>
                <th class="nombres">Nombres</th>
                <th class="correo">Correo</th>
                <th class="rol">Rol</th>
                <th class="estado">Estado</th>
                <th class="registro">Fecha de registro</th>
                <th class="ficha">Ficha</th>
            </tr>

            <?php
            $sql = $con->prepare("SELECT u.*, r.Tipo_rol, e.Tipo_estado, i.docu, 
                                (SELECT f.numero_ficha FROM fichas f 
                                 INNER JOIN clases c ON c.Nom_clase LIKE CONCAT('%', f.nombre_ficha, '%')
                                 INNER JOIN usuarios_clases uc ON uc.id_clase = c.Id_clase
                                 WHERE uc.id_user = u.Id_user
                                 LIMIT 1) as ficha
                                FROM usuarios u
                                INNER JOIN roles r ON u.Id_rol = r.Id_rol 
                                INNER JOIN estado e ON u.Id_estado = e.Id_estado 
                                INNER JOIN identidad i ON u.id_docu = i.id_docu 
                                WHERE r.Id_rol > 1");
            $sql->execute();
            $fila = $sql->fetchAll(PDO::FETCH_ASSOC);

            foreach ($fila as $resu) {
            ?>
            <tr>
                <td><input type="text" readonly value="<?php echo $resu['docu'] ?>"></td>
                <td><input type="text" readonly value="<?php echo $resu['Id_user'] ?>"></td>
                <td><input type="text" readonly value="<?php echo $resu['Nombres'] ?>"></td>
                <td><input type="text" readonly value="<?php echo $resu['Correo'] ?>"></td>
                <td><input type="text" readonly value="<?php echo $resu['Tipo_rol'] ?>"></td>
                <td><input type="text" readonly value="<?php echo $resu['Tipo_estado'] ?>"></td>
                <td><input type="text" readonly value="<?php echo $resu['fecha_registro'] ?>"></td>
                <td><input type="text" readonly value="<?php echo $resu['ficha'] ? $resu['ficha'] : 'N/A' ?>"></td>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>

    <script>
        // Script para manejar el campo de ficha
        document.addEventListener('DOMContentLoaded', function() {
            const rolSelect = document.getElementById('rol_select');
            const fichaInput = document.getElementById('ficha_input');
            
            // Función para verificar si la ficha existe
            function verificarFicha() {
                const fichaValue = fichaInput.value.trim();
                if (rolSelect.value == '3' && fichaValue) {
                    // Hacer una petición AJAX para verificar si la ficha existe
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'verificar_ficha.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (this.status === 200) {
                            const response = JSON.parse(this.responseText);
                            if (!response.existe) {
                                // Mostrar modal
                                document.getElementById('fichaModal').style.display = 'block';
                            }
                        }
                    };
                    xhr.send('ficha=' + fichaValue);
                }
            }
            
            // Cerrar el modal cuando se hace clic en la X
            document.querySelector('.close').addEventListener('click', function() {
                document.getElementById('fichaModal').style.display = 'none';
            });
            
            // Cerrar el modal cuando se hace clic fuera de él
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('fichaModal');
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Verificar ficha cuando se pierde el foco del campo
            fichaInput.addEventListener('blur', verificarFicha);
            
            // Hacer que el campo de ficha sea obligatorio solo para estudiantes
            rolSelect.addEventListener('change', function() {
                if (this.value == '3') { // Si es estudiante
                    fichaInput.setAttribute('required', 'required');
                    fichaInput.placeholder = "N° de ficha (obligatorio)";
                } else {
                    fichaInput.removeAttribute('required');
                    fichaInput.placeholder = "N° de ficha";
                    fichaInput.value = '';
                }
            });
        });
    </script>
</body>
</html>