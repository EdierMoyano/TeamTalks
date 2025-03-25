<?php
// buscar_alumnos.php
require_once('../../conexion/conexion.php');

// Inicializar la conexión
$conex = new database();
$con = $conex->connect();

// Obtener parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$clase_id = isset($_GET['clase_id']) ? intval($_GET['clase_id']) : 0;

if ($clase_id <= 0) {
    echo '<tr><td colspan="5">ID de clase no válido</td></tr>';
    exit;
}

// Construir la consulta
$query = "
    SELECT u.Id_user, u.Nombres, u.Correo, u.Telefono, i.docu
    FROM usuarios_clases uc
    JOIN usuarios u ON uc.id_user = u.Id_user
    JOIN identidad i ON u.id_docu = i.id_docu
    WHERE uc.id_clase = ? AND u.Id_rol = 3
";

$params = [$clase_id];

if (!empty($busqueda)) {
    $query .= " AND (u.Nombres LIKE ? OR u.Id_user LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

$query .= " ORDER BY u.Nombres";

try {
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generar HTML para la tabla
    if (count($estudiantes) > 0) {
        foreach ($estudiantes as $estudiante) {
            echo '<tr>';
            echo '<td>' . $estudiante['docu'] . '</td>';
            echo '<td>' . $estudiante['Id_user'] . '</td>';
            echo '<td>' . $estudiante['Nombres'] . '</td>';
            echo '<td>' . $estudiante['Correo'] . '</td>';
            echo '<td>' . $estudiante['Telefono'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">No hay estudiantes que coincidan con la búsqueda</td></tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="5">Error en la búsqueda: ' . $e->getMessage() . '</td></tr>';
}
?>