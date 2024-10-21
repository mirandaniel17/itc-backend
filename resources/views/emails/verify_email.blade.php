<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Verificación de Correo Electrónico</title>
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
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>¡Bienvenido!</h2>
        <p>Gracias por registrarte. Por favor, verifica tu correo electrónico haciendo clic en el siguiente botón:</p>
        <p>
            <a href="{{ $verificationUrl }}" class="btn">Verificar mi Correo</a>
        </p>
        <p>Este enlace expirará en 60 minutos.</p>
        <p>Si no realizaste esta solicitud, por favor ignora este mensaje.</p>
        <p>Saludos,</p>
        <p>El equipo de soporte</p>
    </div>
</body>
</html>
