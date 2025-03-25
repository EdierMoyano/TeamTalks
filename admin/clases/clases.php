<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';

$conex = new database();
$con = $conex->connect();

// Configuración de paginación
$alumnos_por_pagina = 15;
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

// Obtener clase seleccionada
$clase_id = isset($_GET['clase_id']) ? intval($_GET['clase_id']) : 0;

?>

<?php
    $admin = $_SESSION ['documento'];
    $sql = $con -> prepare("SELECT * FROM usuarios WHERE Id_user = '$admin'");
    $sql ->execute();
    $fila = $sql->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clases</title>
    <link type="text/css" rel="shortcut icon" href="../../styles/icon2.png"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/styles-dashboard.css">
    <link  rel='stylesheet'>
    <link rel="stylesheet" href="../styles/styles-dashboard.css">
    <link rel="stylesheet" href="../registrousers/styles_registro/registro.css">
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-container input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex-grow: 1;
        }
        
        .search-container select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .search-container button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .search-container button:hover {
            background-color: #45a049;
        }
        
        .class-list {
            margin-bottom: 20px;
        }
        
        .class-list table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .class-list th, .class-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .class-list th {
            background-color: #f2f2f2;
        }
        
        .class-list tr:hover {
            background-color: #f5f5f5;
        }
        
        .class-details {
            margin-top: 30px;
        }
        
        .class-details h2 {
            margin-bottom: 15px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 5px;
        }
        
        .teachers-list, .students-list {
            margin-bottom: 20px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination a {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        
        .no-results {
            text-align: center;
            padding: 20px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .action-btn:hover {
            background-color: #45a049;
        }
        
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
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 1000px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        
        .modal-header {
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .modal-body {
            margin-bottom: 20px;
        }
        
        .modal-footer {
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: right;
        }
        
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        
        /* Estilos para el indicador de carga */
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 2s linear infinite;
            display: none;
            margin-left: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
                <a href="../registrousers/index.php" class="boton-menu">Registrar Usuarios</a>
            </div>
            <div class="enlace">
                <i class='bx bx-book'></i>
                <a href="../fichas/fichas.php" class="boton-menu">Fichas</a>
            </div>
            <div class="enlace">
                <i class='bx bx-book'></i>
                <a href="clases.php" class="boton-menu">Clases</a>
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
            <h1>Gestión de Clases</h1>
        </div>
        
        <!-- Formulario de búsqueda -->
        <div class="search-container">
            <select id="tipo_busqueda">
                <option value="nombre">Buscar por Nombre</option>
                <option value="ficha">Buscar por Número de Ficha</option>
            </select>
            <input type="text" id="busqueda_clase" placeholder="Ingrese su búsqueda...">
            <div id="loader_clases" class="loader"></div>
        </div>
        
        <!-- Lista de clases -->
        <div class="class-list">
            <h2>Clases Disponibles</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Clase</th>
                        <th>Número de Ficha</th>
                        <th>Docentes</th>
                        <th>Estudiantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla_clases">
                    <?php
                    // Obtener todas las clases inicialmente
                    $query = "
                        SELECT c.Id_clase, c.Nom_clase, f.numero_ficha, f.nombre_ficha,
                        (SELECT COUNT(*) FROM usuarios_clases uc 
                         JOIN usuarios u ON uc.id_user = u.Id_user 
                         WHERE uc.id_clase = c.Id_clase AND u.Id_rol = 2) as total_docentes,
                        (SELECT COUNT(*) FROM usuarios_clases uc 
                         JOIN usuarios u ON uc.id_user = u.Id_user 
                         WHERE uc.id_clase = c.Id_clase AND u.Id_rol = 3) as total_estudiantes
                        FROM clases c
                        LEFT JOIN fichas f ON c.id_ficha = f.id_ficha
                        ORDER BY c.Nom_clase
                    ";
                    
                    $stmt = $con->prepare($query);
                    $stmt->execute();
                    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($clases) > 0) {
                        foreach ($clases as $clase) {
                    ?>
                    <tr>
                        <td><?php echo $clase['Id_clase']; ?></td>
                        <td><?php echo $clase['Nom_clase']; ?></td>
                        <td><?php echo $clase['numero_ficha'] ? $clase['numero_ficha'] : 'N/A'; ?></td>
                        <td><?php echo $clase['total_docentes']; ?></td>
                        <td><?php echo $clase['total_estudiantes']; ?></td>
                        <td>
                            <button onclick="mostrarDetallesClase(<?php echo $clase['Id_clase']; ?>)" class="action-btn">
                                Ver Detalles
                            </button>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" class="no-results">No se encontraron clases</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal para detalles de clase -->
    <div id="modalDetallesClase" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="cerrarModal()">&times;</span>
                <h2 id="modalTitulo">Detalles de la Clase</h2>
            </div>
            <div class="modal-body" id="modalContenido">
                <!-- El contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button onclick="cerrarModal()" class="action-btn">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Función para buscar clases en tiempo real
        function buscarClases() {
            const busqueda = document.getElementById('busqueda_clase').value;
            const tipo = document.getElementById('tipo_busqueda').value;
            const loader = document.getElementById('loader_clases');
            
            // Mostrar indicador de carga
            loader.style.display = 'inline-block';
            
            // Realizar petición AJAX
            fetch(`buscar_clases.php?busqueda=${encodeURIComponent(busqueda)}&tipo=${tipo}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('tabla_clases').innerHTML = data;
                    loader.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('tabla_clases').innerHTML = '<tr><td colspan="6" class="no-results">Error al buscar clases</td></tr>';
                    loader.style.display = 'none';
                });
        }
        
        // Función para mostrar detalles de la clase
        function mostrarDetallesClase(claseId) {
            // Hacer una petición AJAX para obtener los detalles
            fetch('obtener_detalles_clase.php?clase_id=' + claseId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modalContenido').innerHTML = data;
                    document.getElementById('modalDetallesClase').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles de la clase');
                });
        }
        
        // Función para buscar alumnos en tiempo real dentro del modal
        function buscarAlumnos(claseId) {
            const busqueda = document.getElementById('busqueda_alumno').value;
            
            // Realizar petición AJAX
            fetch(`buscar_alumnos.php?clase_id=${claseId}&busqueda=${encodeURIComponent(busqueda)}`)
                .then(response => response.text())
                .then(data => {
                    const tablaAlumnos = document.querySelector('#modalContenido .card:last-child table tbody');
                    if (tablaAlumnos) {
                        tablaAlumnos.innerHTML = data;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            
            return false; // Evitar que se envíe el formulario
        }
        
        // Función para cerrar el modal
        function cerrarModal() {
            document.getElementById('modalDetallesClase').style.display = 'none';
        }
        
        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modalDetallesClase');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Agregar event listeners para la búsqueda en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const inputBusqueda = document.getElementById('busqueda_clase');
            const selectTipo = document.getElementById('tipo_busqueda');
            
            // Configurar temporizador para evitar demasiadas peticiones
            let typingTimer;
            const doneTypingInterval = 300; // tiempo en ms
            
            inputBusqueda.addEventListener('keyup', function() {
                clearTimeout(typingTimer);
                if (inputBusqueda.value) {
                    typingTimer = setTimeout(buscarClases, doneTypingInterval);
                }
            });
            
            selectTipo.addEventListener('change', buscarClases);
        });
    </script>
</body>
</html>