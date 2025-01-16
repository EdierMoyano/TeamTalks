<?php
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
$texto = filter_var($_POST['texto'], FILTER_SANITIZE_STRING);

if (!empty($email) && !empty($nombre) && !empty($texto)) { 
    $destino = 'santiagopistacho2@gmail.com';
    $asunto = 'Prueba correo';
$texto='hola esta es una prueba';
    $cuerpo = 
    
'<html>
        <head>
        <tittle>Prueba De Correo</tittle>
        </head>
        <body>
            <h1>Email de: '.$nombre.'</h1> 
            <p>'.$texto.'</p>   
        </body>
        </html>
        ';
        

    $headers = "MIME-Version: 1.0\r\n";

    $headers .= "Content-type: text/html; charset=utf-8\r\n";

    $headers .= "From: $nombre <$email>\r\n";

    $headers.= "Return-path: $destino\r\n";

    mail($destino,$asunto,$cuerpo,$headers);

    echo"Email Enviado Correctamente";


}
else {
    echo "Error al enviar el correo";
}
