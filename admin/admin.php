<?php
// Incluir la conexión a la base de datos
require '../conexion/conexion.php';
include '../includes/session.php';

// Obtener la conexión
$database = new Database();
$pdo = $database->connect();

// Consulta SQL para obtener los usuarios por mes en los últimos 3 meses
$stmt = $pdo->query("
    SELECT 
        YEAR(fecha_registro) AS anio,
        MONTH(fecha_registro) AS mes,
        COUNT(*) AS cantidad
    FROM usuarios
    WHERE fecha_registro >= CURDATE() - INTERVAL 3 MONTH
    GROUP BY YEAR(fecha_registro), MONTH(fecha_registro)
    ORDER BY fecha_registro ASC
");

// Obtener los resultados de la consulta
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
    $admin = $_SESSION ['documento'];
    $sql = $pdo -> prepare("SELECT * FROM usuarios WHERE Id_user = '$admin'");
    $sql ->execute();
    $fila = $sql->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Administrador</title>
    <link type="text/css" rel="shortcut icon" href="../styles/icon2.png"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./styles/styles-dashboard.css">
    <link rel="stylesheet" href="./styles/styles-analiticas.css">
    <link rel="stylesheet" href="./styles/styles-contador.css">
    <link rel="stylesheet" href="./styles/myChart.css">
</head>
<body>
<!-- Menu Dashboard -->
    <div>
        <div class="menu-dashboard">
            <!-- Menu superior -->
            <div class="top-menu">
                <div class="logo">
                    <img src="./img/logo.png" alt="">
                    <span><h5><?php echo $fila['Nombres']; ?> <br>Administrador</h5></span>
                </div>
            </div>
            
            <!-- Menu Dashboard -->
            <div class="menu">
    <div class="enlace">
        <i class='bx bx-grid-alt'></i>
        <a href="admin.php" class="boton-menu">Dashboard</a>
    </div>
    <div class="enlace">
        <i class='bx bx-user'></i>
        <a href="usersactivos/index.php" class="boton-menu">Usuarios Activos</a>
    </div>
    <div class="enlace">
        <i class='bx bx-user-x'></i>
        <a href="usersinactivos/index.php" class="boton-menu">Usuarios Inactivos</a>
    </div>
    <div class="enlace">
    <i class='bx bx-user-plus'></i>
    <a href="registrousers/index.php" class="boton-menu">Registrar Usuarios</a>
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
        <a href="../includes/close.php" class="boton-menu">Cerrar sesión</a>
    </div>
</div>

        </div>
                <div id="contador-usuarios">
                    <h2>Usuarios Registrados</h2>
                    <p id="cantidad-usuarios">Cargando...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para el contador -->

    <script>
        // Función para obtener la cantidad de usuarios
        async function actualizarUsuarios() {
            try {
                const respuesta = await fetch('contador_usuarios.php'); // Llama al archivo PHP
                const datos = await respuesta.json(); // Convierte la respuesta a JSON
                document.getElementById('cantidad-usuarios').textContent = datos.total; // Muestra el número
            } catch (error) {
                console.error('Error al obtener la cantidad de usuarios:', error);
                document.getElementById('cantidad-usuarios').textContent = 'Error';
            }
        }

        // Llama la función al cargar la página
        actualizarUsuarios();

        // Actualiza cada 10 segundos
        setInterval(actualizarUsuarios, 10000);
    </script>
    <!--Funcion para cantidad de users ultimos 3 meses -->
<div>
        <canvas id="myChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('myChart').getContext('2d');

        // Datos obtenidos de PHP en formato JSON
        const dataFromPHP = <?php echo json_encode($usuarios); ?>;

        const labels = dataFromPHP.map(item => `${item.mes}-${item.anio}`); // Mes-Año
        const data = dataFromPHP.map(item => item.cantidad); // Cantidad de usuarios

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Usuarios Registrados en los últimos 3 meses',
                    data: data,
                    backgroundColor: ['#ffeb3b', '#2196f3', '#f44336'], 
                    borderWidth: 3
                }]
            },
            options: {
                scales: {
                    y: {
                beginAtZero: true
            
                    }
                }
            }
        });
    </script>

    </div>
</body>
</html>