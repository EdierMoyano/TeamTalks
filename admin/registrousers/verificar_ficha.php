<?php
require_once('../../conexion/conexion.php');

// Verificar si se ha enviado el número de ficha
if (isset($_POST['ficha'])) {
    $numero_ficha = trim($_POST['ficha']);
    
    $conex = new database();
    $con = $conex->connect();
    
    // Verificar si la ficha existe
    $check = $con->prepare("SELECT * FROM fichas WHERE numero_ficha = ?");
    $check->execute([$numero_ficha]);
    
    // Devolver respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode(['existe' => ($check->rowCount() > 0)]);
} else {
    // Si no se envió el número de ficha, devolver error
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No se proporcionó un número de ficha']);
}
?>