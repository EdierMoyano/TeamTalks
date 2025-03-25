<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';

$conex = new database();
$con = $conex->connect();

// Activar reporte de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener el usuario administrador
if (isset($_SESSION['documento'])) {
    $admin = $_SESSION['documento'];
    $sql = $con->prepare("SELECT * FROM usuarios WHERE Id_user = ?");
    $sql->execute([$admin]);
    $fila = $sql->fetch();
} else {
    // Si no hay sesión, redirigir al login
    echo "<script>alert('Sesión no iniciada');</script>";
    echo "<script>window.location = '../../index.php';</script>";
    exit;
}

// Crear una nueva ficha
if (isset($_POST['crear_ficha'])) {
    // Obtener los datos del formulario
    $numero_ficha = trim($_POST['numero_ficha']);
    $nombre_ficha = trim($_POST['nombre_ficha']);
    
    // Verificar que los campos no estén vacíos
    if (empty($numero_ficha) || empty($nombre_ficha)) {
        echo "<script>alert('Por favor complete todos los campos');</script>";
    } else {
        // Verificar si la ficha ya existe
        $check = $con->prepare("SELECT * FROM fichas WHERE numero_ficha = ?");
        $check->execute([$numero_ficha]);
        
        if ($check->rowCount() > 0) {
            echo "<script>alert('El número de ficha ya existe');</script>";
        } else {
            try {
                // Insertar la ficha
                $insert = $con->prepare("INSERT INTO fichas (numero_ficha, nombre_ficha) VALUES (?, ?)");
                $result = $insert->execute([$numero_ficha, $nombre_ficha]);
                
                if ($result) {
                    echo "<script>alert('Ficha creada exitosamente');</script>";
                    echo "<script>window.location = 'fichas.php';</script>";
                } else {
                    echo "<script>alert('Error al crear la ficha');</script>";
                }
            } catch (PDOException $e) {
                echo "<script>alert('Error: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
                echo "<script>console.error('Error SQL: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
            }
        }
    }
}

// Eliminar ficha
if (isset($_GET['eliminar_ficha'])) {
    $id_ficha = $_GET['id_ficha'];
    
    try {
        // Verificar si hay clases asociadas a esta ficha
        $checkClases = $con->prepare("SELECT COUNT(*) as total FROM clases WHERE id_ficha = ?");
        $checkClases->execute([$id_ficha]);
        $totalClases = $checkClases->fetch(PDO::FETCH_ASSOC)['total'];
        
        if ($totalClases > 0) {
            echo "<script>alert('No se puede eliminar la ficha porque tiene clases asociadas');</script>";
        } else {
            // Eliminar la ficha
            $delete = $con->prepare("DELETE FROM fichas WHERE id_ficha = ?");
            $result = $delete->execute([$id_ficha]);
            
            if ($result) {
                echo "<script>alert('Ficha eliminada exitosamente');</script>";
                echo "<script>window.location = 'fichas.php';</script>";
            } else {
                echo "<script>alert('Error al eliminar la ficha');</script>";
            }
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
    }
}

// Asignar docente a una ficha
if (isset($_POST['asignar_docente'])) {
    $id_docente = $_POST['id_docente'];
    $id_ficha = $_POST['id_ficha'];
    $id_materia = $_POST['id_materia'];
    
    // Verificar que se hayan seleccionado todos los campos
    if (empty($id_docente) || empty($id_ficha) || empty($id_materia)) {
        echo "<script>alert('Por favor seleccione todos los campos');</script>";
    } else {
        try {
            // Obtener información de la ficha
            $getFicha = $con->prepare("SELECT nombre_ficha, numero_ficha FROM fichas WHERE id_ficha = ?");
            $getFicha->execute([$id_ficha]);
            $ficha = $getFicha->fetch(PDO::FETCH_ASSOC);
            $nombre_ficha = $ficha['nombre_ficha'];
            $numero_ficha = $ficha['numero_ficha'];
            
            // Verificar si ya existe una clase para esta ficha y materia
            $checkClase = $con->prepare("
                SELECT c.Id_clase 
                FROM clases c 
                WHERE c.id_ficha = ? AND c.Id_materia = ?
            ");
            $checkClase->execute([$id_ficha, $id_materia]);
            
            if ($checkClase->rowCount() > 0) {
                // Usar la clase existente
                $clase = $checkClase->fetch(PDO::FETCH_ASSOC);
                $id_clase = $clase['Id_clase'];
            } else {
                // Crear un nombre para la clase
                $clase_nombre = "Clase " . $nombre_ficha . " - " . $numero_ficha;
                
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
                
                // Generar un nuevo ID para la clase (mayor que 0)
                $getMaxId = $con->prepare("SELECT MAX(Id_clase) as max_id FROM clases");
                $getMaxId->execute();
                $maxId = $getMaxId->fetch(PDO::FETCH_ASSOC)['max_id'];
                $newId = max(1, $maxId + 1); // Asegurarse de que sea mayor que 0
                
                // Insertar la nueva clase (usando solo Id_user, eliminando Id_miembro)
                $insertClase = $con->prepare("
                    INSERT INTO clases (Id_clase, Nom_clase, Id_tarea, Id_materia, Id_user, id_ficha) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $insertClase->execute([$newId, $clase_nombre, $id_tarea, $id_materia, $admin, $id_ficha]);
                $id_clase = $newId;
            }
            
            // Verificar si ya existe la asignación para este docente, clase y materia
            $check = $con->prepare("
                SELECT * FROM usuarios_clases 
                WHERE id_user = ? AND id_clase = ? AND id_materia = ?
            ");
            $check->execute([$id_docente, $id_clase, $id_materia]);
            
            if ($check->rowCount() > 0) {
                echo "<script>alert('El docente ya está asignado a esta ficha para esta materia');</script>";
            } else {
                // Asignar el docente a la clase
                $insert = $con->prepare("
                    INSERT INTO usuarios_clases (id_user, id_clase, id_materia) 
                    VALUES (?, ?, ?)
                ");
                $result = $insert->execute([$id_docente, $id_clase, $id_materia]);
                
                if ($result) {
                    echo "<script>alert('Docente asignado exitosamente');</script>";
                    echo "<script>window.location = 'fichas.php';</script>";
                } else {
                    echo "<script>alert('Error al asignar el docente');</script>";
                }
            }
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
            echo "<script>console.error('Error SQL: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
        }
    }
}

// Obtener todas las fichas para el formulario de asignación
$stmt = $con->prepare("SELECT * FROM fichas ORDER BY fecha_creacion DESC");
$stmt->execute();
$lista_fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Fichas</title>
    <link type="text/css" rel="shortcut icon" href="../../styles/icon2.png"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/styles-dashboard.css">
    <link rel="stylesheet" href="../registrousers/styles_registro/registro.css">
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #f2f2f2;
        }
        
        .two-columns {
            display: flex;
            gap: 20px;
        }
        
        .column {
            flex: 1;
        }
        
        .action-btn {
            margin-right: 5px;
            display: inline-block;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .action-btn.delete {
            background-color: #f44336;
        }
        
        .action-btn:hover {
            opacity: 0.8;
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
                <a href="fichas.php" class="boton-menu">Fichas</a>
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
        <h1>Gestión de Fichas</h1>
        
        <div class="two-columns">
            <div class="column">
                <!-- Formulario para crear fichas -->
                <div class="card">
                    <h2>Crear Nueva Ficha</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="numero_ficha">Número de Ficha:</label>
                            <input type="text" id="numero_ficha" name="numero_ficha" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre_ficha">Nombre de Ficha:</label>
                            <input type="text" id="nombre_ficha" name="nombre_ficha" required>
                        </div>
                        <button type="submit" name="crear_ficha" class="btn">Crear Ficha</button>
                    </form>
                </div>
                
                <!-- Buscador de fichas -->
                <div class="card">
                    <h2>Buscar Fichas</h2>
                    <div class="search-container">
                        <select id="tipo_busqueda_ficha">
                            <option value="nombre">Buscar por Nombre</option>
                            <option value="numero">Buscar por Número</option>
                        </select>
                        <input type="text" id="busqueda_ficha" placeholder="Ingrese su búsqueda...">
                        <div id="loader_fichas" class="loader"></div>
                    </div>
                </div>
                
                <!-- Tabla de fichas existentes -->
                <div class="card">
                    <h2>Fichas Existentes</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Número de Ficha</th>
                                <th>Nombre</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_fichas">
                            <?php
                            if (count($lista_fichas) > 0) {
                                foreach ($lista_fichas as $ficha) {
                            ?>
                            <tr>
                                <td><?php echo $ficha['id_ficha']; ?></td>
                                <td><?php echo $ficha['numero_ficha']; ?></td>
                                <td><?php echo $ficha['nombre_ficha']; ?></td>
                                <td><?php echo $ficha['fecha_creacion']; ?></td>
                                <td>
                                    <a href="fichas.php?eliminar_ficha=1&id_ficha=<?php echo $ficha['id_ficha']; ?>" 
                                       onclick="return confirm('¿Está seguro de eliminar esta ficha? Esta acción no se puede deshacer.')" 
                                       class="action-btn delete">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No hay fichas registradas</td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="column">
                <!-- Formulario para asignar docentes a fichas -->
                <div class="card">
                    <h2>Asignar Docente a Ficha</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="id_ficha">Seleccionar Ficha:</label>
                            <select name="id_ficha" id="id_ficha" required>
                                <option value="">Seleccione una ficha</option>
                                <?php
                                foreach ($lista_fichas as $ficha) {
                                    echo "<option value='" . $ficha['id_ficha'] . "'>" . $ficha['nombre_ficha'] . " (Ficha: " . $ficha['numero_ficha'] . ")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_docente">Seleccionar Docente:</label>
                            <select name="id_docente" id="id_docente" required>
                                <option value="">Seleccione un docente</option>
                                <?php
                                $docentes = $con->prepare("SELECT Id_user, Nombres FROM usuarios WHERE Id_rol = 2 AND Id_estado = 1 ORDER BY Nombres");
                                $docentes->execute();
                                $lista_docentes = $docentes->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($lista_docentes as $docente) {
                                    echo "<option value='" . $docente['Id_user'] . "'>" . $docente['Nombres'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_materia">Seleccionar Materia:</label>
                            <select name="id_materia" id="id_materia" required>
                                <option value="">Seleccione una materia</option>
                                <?php
                                $materias = $con->prepare("SELECT Id_materia, Materia FROM materia ORDER BY Materia");
                                $materias->execute();
                                $lista_materias = $materias->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($lista_materias as $materia) {
                                    echo "<option value='" . $materia['Id_materia'] . "'>" . $materia['Materia'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="asignar_docente" class="btn">Asignar Docente</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para buscar fichas en tiempo real
        function buscarFichas() {
            const busqueda = document.getElementById('busqueda_ficha').value;
            const tipo = document.getElementById('tipo_busqueda_ficha').value;
            const loader = document.getElementById('loader_fichas');
            
            // Mostrar indicador de carga
            loader.style.display = 'inline-block';
            
            // Realizar petición AJAX
            fetch(`buscar_fichas.php?busqueda=${encodeURIComponent(busqueda)}&tipo=${tipo}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('tabla_fichas').innerHTML = data;
                    loader.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('tabla_fichas').innerHTML = '<tr><td colspan="5">Error al buscar fichas</td></tr>';
                    loader.style.display = 'none';
                });
        }
        
        // Agregar event listeners para la búsqueda en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const inputBusqueda = document.getElementById('busqueda_ficha');
            const selectTipo = document.getElementById('tipo_busqueda_ficha');
            
            // Configurar temporizador para evitar demasiadas peticiones
            let typingTimer;
            const doneTypingInterval = 300; // tiempo en ms
            
            inputBusqueda.addEventListener('keyup', function() {
                clearTimeout(typingTimer);
                if (inputBusqueda.value) {
                    typingTimer = setTimeout(buscarFichas, doneTypingInterval);
                }
            });
            
            selectTipo.addEventListener('change', buscarFichas);
            
            // Validación básica del formulario
            const formCrearFicha = document.querySelector('form[name="crear_ficha"]');
            if (formCrearFicha) {
                formCrearFicha.addEventListener('submit', function(e) {
                    const numeroFicha = document.getElementById('numero_ficha').value.trim();
                    const nombreFicha = document.getElementById('nombre_ficha').value.trim();
                    
                    if (!numeroFicha || !nombreFicha) {
                        e.preventDefault();
                        alert('Por favor complete todos los campos');
                    }
                });
            }
        });
    </script>
</body>
</html>