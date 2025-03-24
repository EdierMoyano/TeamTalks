<?php
require_once('../../conexion/conexion.php');
include '../../includes/session.php';

$conex = new database();
$con = $conex->connect();

// Activar reporte de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener el usuario administrador - MOVER ESTO AQUÍ ARRIBA
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
                // Insertar la ficha - SOLO ESTO, nada más
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
            // Verificar si ya existe una clase para esta ficha
            $checkClase = $con->prepare("SELECT Id_clase FROM clases WHERE id_ficha = ?");
            $checkClase->execute([$id_ficha]);
            
            if ($checkClase->rowCount() > 0) {
                // Usar la clase existente
                $clase = $checkClase->fetch(PDO::FETCH_ASSOC);
                $id_clase = $clase['Id_clase'];
            } else {
                // Verificar si existe la clase con ID 0 (que ya existe en la base de datos)
                $id_clase = 0; // Usar la clase con ID 0 que ya existe
            }
            
            // Verificar si ya existe la asignación para este docente, clase y materia
            $check = $con->prepare("SELECT * FROM usuarios_clases WHERE id_user = ? AND id_clase = ? AND id_materia = ?");
            $check->execute([$id_docente, $id_clase, $id_materia]);
            
            if ($check->rowCount() > 0) {
                echo "<script>alert('El docente ya está asignado a esta ficha para esta materia');</script>";
            } else {
                // Asignar el docente a la clase
                $insert = $con->prepare("INSERT INTO usuarios_clases (id_user, id_clase, id_materia) VALUES (?, ?, ?)");
                $result = $insert->execute([$id_docente, $id_clase, $id_materia]);
                
                if ($result) {
                    // Actualizar la ficha en la clase si es necesario
                    if ($id_clase == 0) {
                        $updateClase = $con->prepare("UPDATE clases SET id_ficha = ? WHERE Id_clase = ?");
                        $updateClase->execute([$id_ficha, $id_clase]);
                    }
                    
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

// Eliminar asignación de docente
if (isset($_GET['eliminar_asignacion'])) {
    $id_asignacion = $_GET['id_asignacion'];
    
    try {
        $delete = $con->prepare("DELETE FROM usuarios_clases WHERE id_usuario_clase = ?");
        $result = $delete->execute([$id_asignacion]);
        
        if ($result) {
            echo "<script>alert('Asignación eliminada exitosamente');</script>";
            echo "<script>window.location = 'fichas.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar la asignación');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
    }
}
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $fichas = $con->query("SELECT * FROM fichas ORDER BY fecha_creacion DESC");
                            $lista_fichas = $fichas->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($lista_fichas) > 0) {
                                foreach ($lista_fichas as $ficha) {
                            ?>
                            <tr>
                                <td><?php echo $ficha['id_ficha']; ?></td>
                                <td><?php echo $ficha['numero_ficha']; ?></td>
                                <td><?php echo $ficha['nombre_ficha']; ?></td>
                                <td><?php echo $ficha['fecha_creacion']; ?></td>
                            </tr>
                            <?php
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No hay fichas registradas</td>
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
                
                <!-- Tabla de asignaciones docentes-fichas -->
                <div class="card">
                    <h2>Asignaciones de Docentes a Fichas</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ficha</th>
                                <th>Docente</th>
                                <th>Materia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // Consulta simplificada para mostrar asignaciones
                                $asignaciones = $con->prepare("
                                    SELECT uc.id_usuario_clase, u.Nombres as nombre_docente, 
                                           f.nombre_ficha, f.numero_ficha, m.Materia as nombre_materia
                                    FROM usuarios_clases uc
                                    JOIN usuarios u ON uc.id_user = u.Id_user
                                    JOIN clases c ON uc.id_clase = c.Id_clase
                                    LEFT JOIN fichas f ON c.id_ficha = f.id_ficha
                                    JOIN materia m ON uc.id_materia = m.Id_materia
                                    WHERE u.Id_rol = 2
                                    ORDER BY f.nombre_ficha, u.Nombres
                                ");
                                $asignaciones->execute();
                                $lista_asignaciones = $asignaciones->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($lista_asignaciones) > 0) {
                                    foreach ($lista_asignaciones as $asignacion) {
                                        $ficha_info = !empty($asignacion['nombre_ficha']) ? 
                                            $asignacion['nombre_ficha'] . " (" . $asignacion['numero_ficha'] . ")" : 
                                            "Sin ficha asignada";
                            ?>
                            <tr>
                                <td><?php echo $ficha_info; ?></td>
                                <td><?php echo $asignacion['nombre_docente']; ?></td>
                                <td><?php echo $asignacion['nombre_materia']; ?></td>
                                <td>
                                    <a href="fichas.php?eliminar_asignacion=1&id_asignacion=<?php echo $asignacion['id_usuario_clase']; ?>" 
                                       onclick="return confirm('¿Está seguro de eliminar esta asignación?')" 
                                       class="action-btn">
                                        <img src="../registrousers/styles_registro/delete.png" width="25" height="25" alt="Eliminar">
                                    </a>
                                </td>
                            </tr>
                            <?php
                                    }
                                } else {
                            ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No hay asignaciones registradas</td>
                            </tr>
                            <?php
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='4' style='text-align: center; color: red;'>Error al cargar asignaciones: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validación básica del formulario
        document.querySelector('form[name="crear_ficha"]').addEventListener('submit', function(e) {
            const numeroFicha = document.getElementById('numero_ficha').value.trim();
            const nombreFicha = document.getElementById('nombre_ficha').value.trim();
            
            if (!numeroFicha || !nombreFicha) {
                e.preventDefault();
                alert('Por favor complete todos los campos');
            }
        });
    </script>
</body>
</html>