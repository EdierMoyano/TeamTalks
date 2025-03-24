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

// Búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$tipo_busqueda = isset($_GET['tipo_busqueda']) ? $_GET['tipo_busqueda'] : 'nombre';

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
        }
        
        .action-btn:hover {
            background-color: #45a049;
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
            <form action="" method="GET" style="display: flex; width: 100%; gap: 10px;">
                <select name="tipo_busqueda">
                    <option value="nombre" <?php if($tipo_busqueda == 'nombre') echo 'selected'; ?>>Buscar por Nombre</option>
                    <option value="ficha" <?php if($tipo_busqueda == 'ficha') echo 'selected'; ?>>Buscar por Número de Ficha</option>
                </select>
                <input type="text" name="busqueda" placeholder="Ingrese su búsqueda..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button type="submit">Buscar</button>
            </form>
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
                <tbody>
                    <?php
                    // Construir la consulta según el tipo de búsqueda
                    $query = "
                        SELECT c.Id_clase, c.Nom_clase, f.numero_ficha,
                        (SELECT COUNT(*) FROM usuarios_clases uc 
                         JOIN usuarios u ON uc.id_user = u.Id_user 
                         WHERE uc.id_clase = c.Id_clase AND u.Id_rol = 2) as total_docentes,
                        (SELECT COUNT(*) FROM usuarios_clases uc 
                         JOIN usuarios u ON uc.id_user = u.Id_user 
                         WHERE uc.id_clase = c.Id_clase AND u.Id_rol = 3) as total_estudiantes
                        FROM clases c
                        LEFT JOIN fichas f ON c.Nom_clase LIKE CONCAT('%', f.nombre_ficha, '%')
                        WHERE 1=1
                    ";
                    
                    $params = [];
                    
                    if (!empty($busqueda)) {
                        if ($tipo_busqueda == 'nombre') {
                            $query .= " AND c.Nom_clase LIKE ?";
                            $params[] = "%$busqueda%";
                        } else if ($tipo_busqueda == 'ficha') {
                            $query .= " AND f.numero_ficha LIKE ?";
                            $params[] = "%$busqueda%";
                        }
                    }
                    
                    $query .= " ORDER BY c.Nom_clase";
                    
                    $stmt = $con->prepare($query);
                    $stmt->execute($params);
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
                            <a href="clases.php?clase_id=<?php echo $clase['Id_clase']; ?>" class="action-btn">
                                Ver Detalles
                            </a>
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
        
        <?php
        // Mostrar detalles de la clase seleccionada
        if ($clase_id > 0) {
            $stmt = $con->prepare("
                SELECT c.Id_clase, c.Nom_clase, f.numero_ficha, f.nombre_ficha
                FROM clases c
                LEFT JOIN fichas f ON c.Nom_clase LIKE CONCAT('%', f.nombre_ficha, '%')
                WHERE c.Id_clase = ?
            ");
            $stmt->execute([$clase_id]);
            $clase_detalle = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($clase_detalle) {
        ?>
        <div class="class-details">
            <h2>Detalles de la Clase: <?php echo $clase_detalle['Nom_clase']; ?></h2>
            <?php if ($clase_detalle['numero_ficha']) { ?>
            <p><strong>Número de Ficha:</strong> <?php echo $clase_detalle['numero_ficha']; ?></p>
            <?php } ?>
            
            <!-- Lista de docentes -->
            <div class="teachers-list">
                <h3>Docentes Asignados</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $con->prepare("
                            SELECT u.Id_user, u.Nombres, u.Correo, u.Telefono
                            FROM usuarios_clases uc
                            JOIN usuarios u ON uc.id_user = u.Id_user
                            WHERE uc.id_clase = ? AND u.Id_rol = 2
                            ORDER BY u.Nombres
                        ");
                        $stmt->execute([$clase_id]);
                        $docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (count($docentes) > 0) {
                            foreach ($docentes as $docente) {
                        ?>
                        <tr>
                            <td><?php echo $docente['Id_user']; ?></td>
                            <td><?php echo $docente['Nombres']; ?></td>
                            <td><?php echo $docente['Correo']; ?></td>
                            <td><?php echo $docente['Telefono']; ?></td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="4" class="no-results">No hay docentes asignados a esta clase</td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Lista de estudiantes (paginada) -->
            <div class="students-list">
                <h3>Estudiantes Matriculados</h3>
                <?php
                // Contar total de estudiantes para la paginación
                $stmt = $con->prepare("
                    SELECT COUNT(*) as total
                    FROM usuarios_clases uc
                    JOIN usuarios u ON uc.id_user = u.Id_user
                    WHERE uc.id_clase = ? AND u.Id_rol = 3
                ");
                $stmt->execute([$clase_id]);
                $total_estudiantes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                $total_paginas = ceil($total_estudiantes / $alumnos_por_pagina);
                $offset = ($pagina_actual - 1) * $alumnos_por_pagina;
                
                // Obtener estudiantes para la página actual
                $stmt = $con->prepare("
                    SELECT u.Id_user, u.Nombres, u.Correo, u.Telefono, i.docu
                    FROM usuarios_clases uc
                    JOIN usuarios u ON uc.id_user = u.Id_user
                    JOIN identidad i ON u.id_docu = i.id_docu
                    WHERE uc.id_clase = ? AND u.Id_rol = 3
                    ORDER BY u.Nombres
                    LIMIT $alumnos_por_pagina OFFSET $offset
                ");
                $stmt->execute([$clase_id]);
                $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                
                <table>
                    <thead>
                        <tr>
                            <th>Tipo Documento</th>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($estudiantes) > 0) {
                            foreach ($estudiantes as $estudiante) {
                        ?>
                        <tr>
                            <td><?php echo $estudiante['docu']; ?></td>
                            <td><?php echo $estudiante['Id_user']; ?></td>
                            <td><?php echo $estudiante['Nombres']; ?></td>
                            <td><?php echo $estudiante['Correo']; ?></td>
                            <td><?php echo $estudiante['Telefono']; ?></td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="5" class="no-results">No hay estudiantes matriculados en esta clase</td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                
                <!-- Paginación -->
                <?php if ($total_paginas > 1) { ?>
                <div class="pagination">
                    <?php if ($pagina_actual > 1) { ?>
                    <a href="clases.php?clase_id=<?php echo $clase_id; ?>&pagina=1">&laquo; Primera</a>
                    <a href="clases.php?clase_id=<?php echo $clase_id; ?>&pagina=<?php echo $pagina_actual - 1; ?>">&lsaquo; Anterior</a>
                    <?php } ?>
                    
                    <?php
                    // Mostrar enlaces de página
                    $rango = 2; // Número de páginas a mostrar antes y después de la actual
                    
                    for ($i = max(1, $pagina_actual - $rango); $i <= min($total_paginas, $pagina_actual + $rango); $i++) {
                        if ($i == $pagina_actual) {
                            echo "<a class='active'>$i</a>";
                        } else {
                            echo "<a href='clases.php?clase_id=$clase_id&pagina=$i'>$i</a>";
                        }
                    }
                    ?>
                    
                    <?php if ($pagina_actual < $total_paginas) { ?>
                    <a href="clases.php?clase_id=<?php echo $clase_id; ?>&pagina=<?php echo $pagina_actual + 1; ?>">Siguiente &rsaquo;</a>
                    <a href="clases.php?clase_id=<?php echo $clase_id; ?>&pagina=<?php echo $total_paginas; ?>">Última &raquo;</a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php
            } else {
                echo "<div class='no-results'>Clase no encontrada</div>";
            }
        }
        ?>
    </div>
</body>
</html>