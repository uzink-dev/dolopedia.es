<?php
// Respond with 503 Unavailable status code
http_response_code(503);

// Advise client to try again after 30 minutes (in secs)
header('Retry-After: 1800');

header('Content-Type: text/html; charset=utf-8');
?>

<!doctype html>
<html lang="es">
<head>
    <title>Dolopedia</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #4ad6e9;
        }
        .wrapper {
            width: 600px;
            height: 250px;
            text-align: center;
            color: #fff;
            font-size: 18px;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <img src="/bundles/front/img/dolopedia-logo-min.png" alt="Logotipo Dolopedia">
        <div class="title">
            Estamos actualizando Dolopedia para que tengas una mejor experiencia con nosotros, en poco tiempo volveremos a estar online. Disculpa las molestias
        </div>
    </div>
</body>
</html>