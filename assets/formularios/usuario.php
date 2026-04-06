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
        <title>Registro de nuevo usuario</title>
        <link rel="stylesheet" href="../css/forusu.css">
    </head>
    <body>

        <form action="../php/alta.php" method="post">
                <h2>Registro de usuario</h2>

                <div>
                    <label for="nombre">Nombre Del Usuario:</label><br>
                    <input type="text" id="nombre" name="nombre" placeholder="Usuario nuevo" required><br>
                </div><br>

                <div>
                    <label for="corro">Correo Electronico:</label><br>
                    <input type="email" id="correo" name="correo" placeholder="correo" required><br>
                </div><br>

                <div>
                    <label for="password">Contraseña:</label><br>
                    <input type="password" id="password" name="password" minlength="8" required>
                </div><br>

                <div>
                    <label for="rol">Asignacion de Rol:</label><br>
                    <select id="rol" name="rol" required>
                        <option value="" disabled selected>Selecciona una opción</option>
                        <option value="administrador">Administrador</option>
                        <option value="contador">Contador</option>
                        <option value="usuario">Usuario</option>
                    </select>
                 </div><br>

                <button type="submit">Registrar</button>

                
        </form>

    </body>
</html>