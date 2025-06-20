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
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <div class="logo-container">
        <img src="../assets/logo.png" alt="TeamTalks Logo" class="logo">
        <nav>
            <ul class="nav-links">
                <li><a class="ind" href="index.php">Inicio</a></li>
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
                    <a href="../../includes/close.php">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Cerrar Sesion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
<main>
    <div class="content">
        <h5><i class='bx bxs-bell'></i>  Trabajo en equipo con TeamTalks <br>ㅤㅤMy WorkPlace</h5>
        <h1>Más <strong>participación</strong> en <br> el lugar de aprendizaje</h1>
        <h4><strong1>TeamTalks</strong1>, su plataforma de colaboración.</h4>
        <h4>Más comunicación, participación de los estudiantes y <br> docentes gracias a nuestras funciones como trabajos <br> en grupo de manera simultánea.</h4>
        <h4>Todo esto lo encuentras en <strong1>TeamTalks</strong1> sin ningún <br> coste adicional.</h4>
    </div>
    <div class="images">
        <img src="../assets/img2.jpg" alt="Imagen Grande" class="large-image">
        <img src="../assets/img1.jpg" alt="Imagen Pequeña" class="small-image">
    </div>       
</main>
<footer style="background-color: #0E4A86;" class="text-dark pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row text-center text-md-start">
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="mb-4 font-weight-bold text-primary text-white">Nosotros</h5>
                <hr class="text-white mb-4">
                <p>
                <span style="color: white !important;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Exercitationem quisquam laborum voluptatibus minima vitae, quod quia architecto amet unde doloremque eaque autem necessitatibus, ipsum et? Laudantium, quis! Officia, labore deleniti?</span>
                </p>
            </div>
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="mb-4 font-weight-bold text-primary text-white">Soporte</h5>
                <hr class="text-white mb-4">
                <p>
                    <a href="#" class="text-white">Tu cuenta</a>
                </p>
                <p>
                    <a href="#" class="text-white">Tus ordenes</a>
                </p>
                <p>
                    <a href="#" class="text-white">Manejo de cuenta</a>
                </p>
                <p>
                    <a href="#" class="text-white">Ayuda</a>
                </p>
            </div>
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="mb-4 font-weight-bold text-primary text-white">Has dinero</h5>
                <hr class="text-white mb-4">
                <p>
                    <a href="#" class="text-white">Vende productos</a>
                </p>
                <p>
                    <a href="#" class="text-white">Anuncios</a>
                </p>
                <p>
                    <a href="#" class="text-white">Afiliate</a>
                </p>
                <p>
                    <a href="#" class="text-white">Publica</a>
                </p>
            </div>
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="mb-4 font-weight-bold text-primary text-white">Contacto</h5>
                <hr class="text-white mb-4">
                <p>
                    <li class="fas fa-home me-3 text-white"></li>
                    <span style="color: white !important;">Ibague-Tolima</span>
                </p>
                <p>
                    <li class="fas fa-envelope me-3 text-white"></li>
                    <span style="color: white !important;">Test@gmail.com</span>
                </p>
                <p>
                    <li class="fas fa-phone me-3 text-white"></li>
                    <span style="color: white !important;">+555555</span>
                </p>
                <p>
                    <li class="fas fa-print me-3 text-white"></li>
                    <span style="color: white !important;">+555555</span>
                </p>
            </div>
            <hr class="text-white mb-4">
            <div class="text-center mb-2">
                <p>
                <span style="color: white !important;">Copyright, Todos los derechos reservados</span>
                    <a href="#"><strong class="text-primary">Test</strong>
                    </a>
                </p>
            </div>
            <div class="text-center">
                <ul class="list-unstyled list-inline">
                    <li class="list-inline-item">
                        <a href="#" class="text-white social-link"><i class="fab fa-facebook"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" class="text-white social-link"><i class="fab fa-instagram"></i></a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" class="text-white social-link"><i class="fab fa-linkedin-in"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<script src="../js/script.js"></script>
</body>
</html>
