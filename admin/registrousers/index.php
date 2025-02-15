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

    $sql4 = $con -> prepare("SELECT * FROM usuarios WHERE Id_user = $docu AND Correo = '$correo'");
    $sql4 ->execute();
    $user = $sql4->fetch();

    if($user) {
        echo "<script>alert('El usuario ya está registrado');</script>";
        echo "<script>window.location = 'index.php';</script>";
    }else {
        $ing = $con ->prepare("INSERT INTO usuarios(Id_user, Nombres, Correo, Contrasena, Avatar, Telefono, Id_rol, Id_estado, id_docu, fecha_registro)VALUES ($docu, '$name', '$correo', '$contra', '$avatar', $tel, $rol, $estado, $tipo, NOW())");
        $ing ->execute();
        echo "<script>alert('Usuario registrado exitosamente');</script>";
        echo "<script>window.location = 'index.php';</script>";
    }

    
}

?>

<?php
    $admin = $_SESSION ['documento'];
    $sql = $con -> prepare("SELECT * FROM usuarios WHERE Id_user = '$admin'");
    $sql ->execute();
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
                <a href="#" class="boton-menu">Clases</a>
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

        
        <form action ="recibe_excel_validando.php" method="POST" enctype="multipart/form-data">
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
                    
                    
                    
                </tr>

                <tr>
                    <td name="fila"><select class="act_tipo" name="tipo" required>
                        <option value="<?php echo $fila['id_docu']?>">Tipo de documento</option>
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
                    <td nmae="fila"><input type="text" name="pass" placeholder="Contraseña" required></td>
                    <td name="fila"><input type="number" name="tel" placeholder="N° de teléfono" required></td>
                    <td name="fila"><select class="act_rol" name="rol" required>
                        <option value="<?php echo $fila['Id_rol']?>">Rol</option>
                            <?php
                            $sql1 = $con->prepare("SELECT * FROM roles");
                            $sql1->execute();
                            while ($role=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                echo  "<option value=" . $role['Id_rol']. ">" . $role['Tipo_rol'] ." </option>";
                            }

                        ?>
                        </select></td>

                    <td name="fila"><select class="act_est" name="estado" required>
                        <option value="<?php echo $fila['Id_estado']?>">Estado</option>
                            <?php
                            $sql1 = $con->prepare("SELECT * FROM estado");
                            $sql1->execute();
                            while ($state=$sql1->fetch(PDO::FETCH_ASSOC)) {
                                echo  "<option value=" . $state['Id_estado']. ">" . $state['Tipo_estado'] ." </option>";
                            }

                        ?>
                        </select></td>
                    
                    
                </tr>


            </table>

            <button type="submit" name="ingreso" class="registro">Registrar</button>
        </form>

        

        

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
                
            </tr>

            <?php
            $sql = $con->prepare("SELECT * FROM usuarios 
                                INNER JOIN roles ON usuarios.Id_rol = roles.Id_rol 
                                INNER JOIN estado ON usuarios.Id_estado = estado.Id_estado 
                                INNER JOIN identidad ON usuarios.id_docu = identidad.id_docu WHERE roles.Id_rol > 1");
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
                
            </tr>
            <?php
            }
            ?>
        </table>
    </div>

    

</body>
</html>
