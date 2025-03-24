<?php
require_once('../../conexion/conexion.php');

if (isset($_POST['ficha'])) {
    $ficha = trim($_POST['ficha']);
    
    $database = new Database();
    $con = $database->connect();
    
    $checkFicha = $con->prepare("SELECT * FROM fichas WHERE numero_ficha = ?");
    $checkFicha->execute([$ficha]);
    
    $response = [
        'existe' => ($checkFicha->rowCount() > 0)
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>