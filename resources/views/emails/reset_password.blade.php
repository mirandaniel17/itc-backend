<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Restablecimiento de Contraseña</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hola,</h2>
        <p>Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el siguiente botón para proceder:</p>
        <p>
            <a href="{{ $resetLink }}" class="btn">Restablecer Contraseña</a>
        </p>
        <p>Si no solicitaste un restablecimiento de contraseña, puedes ignorar este mensaje.</p>
        <p>Gracias,</p>
        <p>El equipo de soporte</p>
    </div>
</body>
</html>
