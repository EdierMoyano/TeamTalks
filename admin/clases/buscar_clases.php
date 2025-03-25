<?php
// buscar_clases.php
require_once('../../conexion/conexion.php');

// Inicializar la conexión
$conex = new database();
$con = $conex->connect();

// Obtener parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$tipo_busqueda = isset($_GET['tipo']) ? $_GET['tipo'] : 'nombre';

// Construir la consulta según el tipo de búsqueda
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

try {
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generar HTML para la tabla
    if (count($clases) > 0) {
        foreach ($clases as $clase) {
            echo '<tr>';
            echo '<td>' . $clase['Id_clase'] . '</td>';
            echo '<td>' . $clase['Nom_clase'] . '</td>';
            echo '<td>' . ($clase['numero_ficha'] ? $clase['numero_ficha'] : 'N/A') . '</td>';
            echo '<td>' . $clase['total_docentes'] . '</td>';
            echo '<td>' . $clase['total_estudiantes'] . '</td>';
            echo '<td>
                    <button onclick="mostrarDetallesClase(' . $clase['Id_clase'] . ')" class="action-btn">
                        Ver Detalles
                    </button>
                  </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6" class="no-results">No se encontraron clases</td></tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="6" class="no-results">Error en la búsqueda: ' . $e->getMessage() . '</td></tr>';
}
?>