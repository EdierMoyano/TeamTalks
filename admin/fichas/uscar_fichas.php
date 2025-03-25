<?php
// buscar_fichas.php
require_once('../../conexion/conexion.php');

// Inicializar la conexión
$conex = new database();
$con = $conex->connect();

// Obtener parámetros de búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$tipo_busqueda = isset($_GET['tipo']) ? $_GET['tipo'] : 'nombre';

// Construir la consulta según el tipo de búsqueda
$query = "SELECT * FROM fichas WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    if ($tipo_busqueda == 'nombre') {
        $query .= " AND nombre_ficha LIKE ?";
        $params[] = "%$busqueda%";
    } else if ($tipo_busqueda == 'numero') {
        $query .= " AND numero_ficha LIKE ?";
        $params[] = "%$busqueda%";
    }
}

$query .= " ORDER BY fecha_creacion DESC";

try {
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    $fichas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generar HTML para la tabla
    if (count($fichas) > 0) {
        foreach ($fichas as $ficha) {
            echo '<tr>';
            echo '<td>' . $ficha['id_ficha'] . '</td>';
            echo '<td>' . $ficha['numero_ficha'] . '</td>';
            echo '<td>' . $ficha['nombre_ficha'] . '</td>';
            echo '<td>' . $ficha['fecha_creacion'] . '</td>';
            echo '<td>
                    <a href="fichas.php?eliminar_ficha=1&id_ficha=' . $ficha['id_ficha'] . '" 
                       onclick="return confirm(\'¿Está seguro de eliminar esta ficha? Esta acción no se puede deshacer.\')" 
                       class="action-btn delete">
                        Eliminar
                    </a>
                  </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5" style="text-align: center;">No hay fichas que coincidan con la búsqueda</td></tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="5" style="text-align: center;">Error en la búsqueda: ' . $e->getMessage() . '</td></tr>';
}
?>