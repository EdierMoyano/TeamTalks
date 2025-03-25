<?php
require_once('../../conexion/conexion.php');

// Verificar si se ha proporcionado un ID de clase
if (!isset($_GET['clase_id']) || empty($_GET['clase_id'])) {
    echo "<p>No se ha especificado una clase</p>";
    exit;
}

$clase_id = intval($_GET['clase_id']);
$conex = new database();
$con = $conex->connect();

// Buscar información de la clase
try {
    $stmt = $con->prepare("
        SELECT c.Id_clase, c.Nom_clase, f.numero_ficha, f.nombre_ficha
        FROM clases c
        LEFT JOIN fichas f ON c.id_ficha = f.id_ficha
        WHERE c.Id_clase = ?
    ");
    $stmt->execute([$clase_id]);
    $clase = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$clase) {
        echo "<p>Clase no encontrada</p>";
        exit;
    }
    
    // Buscar información de la materia
    $stmt = $con->prepare("
        SELECT m.Materia
        FROM clases c
        LEFT JOIN materia m ON c.Id_materia = m.Id_materia
        WHERE c.Id_clase = ?
    ");
    $stmt->execute([$clase_id]);
    $materia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Buscar docentes asignados a esta clase
    $stmt = $con->prepare("
        SELECT u.Id_user, u.Nombres, u.Correo, u.Telefono, m.Materia
        FROM usuarios_clases uc
        JOIN usuarios u ON uc.id_user = u.Id_user
        JOIN materia m ON uc.id_materia = m.Id_materia
        WHERE uc.id_clase = ? AND u.Id_rol = 2
        ORDER BY u.Nombres
    ");
    $stmt->execute([$clase_id]);
    $docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar estudiantes asignados a esta clase
    $busqueda_alumno = isset($_GET['busqueda_alumno']) ? $_GET['busqueda_alumno'] : '';
    
    $query = "
        SELECT u.Id_user, u.Nombres, u.Correo, u.Telefono, i.docu
        FROM usuarios_clases uc
        JOIN usuarios u ON uc.id_user = u.Id_user
        JOIN identidad i ON u.id_docu = i.id_docu
        WHERE uc.id_clase = ? AND u.Id_rol = 3
    ";
    
    $params = [$clase_id];
    
    if (!empty($busqueda_alumno)) {
        $query .= " AND (u.Nombres LIKE ? OR u.Id_user LIKE ?)";
        $params[] = "%$busqueda_alumno%";
        $params[] = "%$busqueda_alumno%";
    }
    
    $query .= " ORDER BY u.Nombres";
    
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mostrar la información
    ?>
    <div class="card">
        <h3>Información de la Clase</h3>
        <p><strong>Nombre de la Clase:</strong> <?php echo $clase['Nom_clase']; ?></p>
        <?php if ($clase['numero_ficha']): ?>
        <p><strong>Ficha:</strong> <?php echo $clase['nombre_ficha'] . ' (' . $clase['numero_ficha'] . ')'; ?></p>
        <?php endif; ?>
        <?php if ($materia && $materia['Materia']): ?>
        <p><strong>Materia Principal:</strong> <?php echo $materia['Materia']; ?></p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h3>Docentes Asignados</h3>
        <?php if (count($docentes) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Materia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($docentes as $docente): ?>
                <tr>
                    <td><?php echo $docente['Id_user']; ?></td>
                    <td><?php echo $docente['Nombres']; ?></td>
                    <td><?php echo $docente['Correo']; ?></td>
                    <td><?php echo $docente['Telefono']; ?></td>
                    <td><?php echo $docente['Materia']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No hay docentes asignados a esta clase</p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h3>Estudiantes Matriculados</h3>
        
        <!-- Buscador de estudiantes -->
        <div class="search-container" style="margin-bottom: 15px;">
            <form id="formBuscarAlumno" onsubmit="buscarAlumnos(event, <?php echo $clase_id; ?>)">
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="busqueda_alumno" placeholder="Buscar por nombre o documento..." value="<?php echo htmlspecialchars($busqueda_alumno); ?>">
                    <button type="submit">Buscar</button>
                </div>
            </form>
        </div>
        
        <?php if (count($estudiantes) > 0): ?>
        <table class="table">
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
                <?php foreach ($estudiantes as $estudiante): ?>
                <tr>
                    <td><?php echo $estudiante['docu']; ?></td>
                    <td><?php echo $estudiante['Id_user']; ?></td>
                    <td><?php echo $estudiante['Nombres']; ?></td>
                    <td><?php echo $estudiante['Correo']; ?></td>
                    <td><?php echo $estudiante['Telefono']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No hay estudiantes matriculados en esta clase</p>
        <?php endif; ?>
    </div>
    
    <script>
        function buscarAlumnos(event, claseId) {
            event.preventDefault();
            const busqueda = document.getElementById('busqueda_alumno').value;
            mostrarDetallesClase(claseId, busqueda);
        }
    </script>
    <?php
} catch (PDOException $e) {
    echo "<p>Error al cargar los detalles: " . $e->getMessage() . "</p>";
}
?>