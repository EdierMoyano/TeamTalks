<?php
session_start();
unset($_SESSION['tipo']);
unset($_SESSION['documento']);
unset($_SESSION['estado']);
unset($_SESSION['rol']);
session_destroy();
session_write_close();

echo '<script>window.location = "../index.html"</script>';

?>