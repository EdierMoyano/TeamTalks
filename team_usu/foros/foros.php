<?php
session_start();
require_once('../../conexion/conexion.php');

$conex = new database();
$con = $conex->connect();

// Verifica si existe la sesión del usuario
if (isset($_SESSION['documento'])) {
    $user = $_SESSION['documento'];
    $sql = $con->prepare("SELECT * FROM usuarios WHERE Id_user = ?");
    $sql->execute([$user]);
    $fila = $sql->fetch();
} else {
    $fila = null; // Para evitar errores si no hay sesión
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style2.css">
</head>
<body>
<header>
    <div class="logo-container">
        <img src="../assets/logo.png" alt="TeamTalks Logo" class="logo">
        <nav>
        <ul class="nav-links">
                <li><a href="../inicio/index.php">Inicio</a></li>
                <li><a href="../foros/foros.php">Foros</a></li>
                <li><a href="../mis_clases/mis.clases.php">Mis clases</a></li>
                <li><a href="../documentos/documentos.php">Documentos</a></li>
            </ul>
        </nav>
        <div class="profile-dropdown">
            <div onclick="toggle()" class="profile-dropdown-btn">
                <div class="profile-img">
                    <i class="fa-solid fa-circle"></i>
                </div>
                <span>
                <?php echo isset($fila['Nombres']) ? $fila['Nombres'] : 'Invitado'; ?>
                <i class="fa-solid fa-angle-down"></i>
                </span>
            </div>
            <ul class="profile-dropdown-list">
                <li class="profile-dropdown-list-item">
                    <a href="#">
                        <i class="fa-regular fa-user"></i>
                        Editar Perfil
                    </a>
                </li>
                <li class="profile-dropdown-list-item">
                    <a href="#">
                        <i class="fa-solid fa-sliders"></i>
                        Configuracion
                    </a>
                </li>
                <li class="profile-dropdown-list-item">
                    <a href="#">
                        <i class="fa-regular fa-circle-question"></i>
                        Ayuda y Soporte
                    </a>
                </li>
                <hr />
                <li class="profile-dropdown-list-item">
                    <a href="#">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Cerrar Sesion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
<div class="buscador-container">
        <input type="text" placeholder="Buscar...">
        <button><i class='bx bx-search-alt-2'></i></button>
    </div>

    <div class="recuadro">
        <div class="carta">
            <figure>
                <img src="../assets/mate.webp" alt="">
            </figure>
            <div class="contenido">
                <h3>Matematicas</h3>
                <h4>Profesor: Jaime</h4>
                <a href="foroM.php">Ingresar a foro</a>
            </div>
        </div>
        <div class="carta">
            <figure>
                <img src="../assets/fisic.webp" alt="">
            </figure>
            <div class="contenido">
                <h3>Cultura fisica</h3>
                <h4>Profesor: Calixto</h4>
                <a href="foroC.php">Ingresar a foro</a>
            </div>
        </div>
        <div class="carta">
            <figure>
                <img src="../assets/tl.webp" alt="">
            </figure>
            <div class="contenido">
                <h3>Algoritmia</h3>
                <h4>Profesor: Juliana</h4>
                <a href="foroA.php">Ingresar a foro</a>
            </div>
        </div>
        <div class="carta">
            <figure>
                <img src="../assets/ing.webp" alt="">
            </figure>
            <div class="contenido">
                <h3>Ingles</h3>
                <h4>Profesor: Paula</h4>
                <a href="foroI.php">Ingresar a foro</a>
            </div>
        </div>
    </div>
<script src="../js/script.js"></script>
</body>
</html>


