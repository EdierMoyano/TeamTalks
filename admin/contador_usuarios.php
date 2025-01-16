<?php
// Incluir la clase database
require_once './../conexion/conexion.php'; 


$db = new database();
$conexion = $db->connect();

try {
    // Consulta para contar los usuarios
    $query = $conexion->prepare("SELECT COUNT(*) AS total FROM usuarios");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Devolver la cantidad de usuarios en formato JSON
    echo json_encode(['total' => $result['total']]);
} catch (PDOException $e) {
    // Manejo de errores
    echo json_encode(['error' => 'Error al obtener la cantidad de usuarios']);
}
?>
