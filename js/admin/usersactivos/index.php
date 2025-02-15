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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar usuarios</title>
    <link type="text/css" rel="shortcut icon" href="../../styles/icon2.png"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/styles-dashboard.css">
    <link rel="stylesheet" href="../registrousers/styles_registro/registro.css">
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
                <a href="index.php" class="boton-menu">Usuarios Activos</a>
            </div>
            <div class="enlace">
                <i class='bx bx-user-x'></i>
                <a href="../usersinactivos/index.php" class="boton-menu">Usuarios Inactivos</a>
            </div>
            <div class="enlace">
                <i class='bx bx-user-plus'></i>
                <a href="../registrousers/index.php" class="boton-menu">Registrar Usuarios</a>
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
        <div class="titleac">
            <h1>Usuarios activos</h1>
        </div>

        
        <!-- Tabla para mostrar datos -->
        <table class="table3" border="2">
            <tr>
                <th class="tipo">Tipo documento</th>
                <th class="documento">Documento</th>
                <th class="nombres">Nombres</th>
                <th class="correo">Correo</th>
                <th class="rol">Rol</th>
                <th class="estado">Estado</th>
                <th class="registro">Fecha de registro</th>
                <th colspan="2">Acción</th>
                
            </tr>

            <?php
            $sql = $con->prepare("SELECT * FROM usuarios 
                                INNER JOIN roles ON usuarios.Id_rol = roles.Id_rol 
                                INNER JOIN estado ON usuarios.Id_estado = estado.Id_estado 
                                INNER JOIN identidad ON usuarios.id_docu = identidad.id_docu
                                WHERE estado.Id_estado = 1 AND roles.Id_rol > 1");
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
                <td name = "updat"><a  href="" onclick="window.open('../registrousers/update.php?id=<?php echo $resu['Id_user'];?>','', 'width=1200, height=500, toolbar=NO')"><img src="../registrousers/styles_registro/update.png" width=25 height= 25 alt=""></a></td>
                <td name = "del"><a  href="../registrousers/delete.php?accion=Eliminar&id=<?php echo $resu['Id_user'];?>" onclick="return confirm ('¿Quieres eliminar este usuario?')"><img src="../registrousers/styles_registro/delete.png" width=25 height= 25 alt=""></a></td>
                
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
</body>
</html>
