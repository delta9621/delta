 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al sistema</title>
    <link rel="stylesheet" href="assets/css/logout.css">
</head>
<body>

<div class="container">

    <div class="left">
        <div class="logo">
            <img src="lo.jpeg" alt="logo">
        </div>
    </div>

    <div class="right">
        <form action="./assets/php/login.php" method="post">
            <h2>Acceso al sistema</h2>

            <label>Correo:</label>
            <input type="email" name="correo" required placeholder="correo@ejemplo.com">

            <label>Contraseña:</label>
            <input type="password" name="password" required placeholder="********">

            <button type="submit">Ingresar</button>
        </form>
    </div>

</div>

</body>
</html>