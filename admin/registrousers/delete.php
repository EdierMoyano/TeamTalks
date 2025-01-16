<?php


require_once('../../conexion/conexion.php');
include '../../includes/session.php';
$conex = new database();
$con = $conex->connect();

?>

<?php


if (isset($_GET['accion']) && $_GET['accion'] == 'Eliminar') {
    $codi = $_GET['id'];
    $origen = $_GET['origen'];
    $sql = $con -> prepare("DELETE FROM usuarios WHERE Id_user = $codi");
    $sql->execute();
    echo "<script>alert('Usuario Eliminado')</script>";
    echo "<script>window.location='../$origen/index.php'</script>";
    exit();
}

?>