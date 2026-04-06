 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php 
session_start();

//validacion de usuario al inicar sesion
if(!isset($_SESSION['nombre'])){
    header("Location: ./assets/index.php");
    exit();
}
// importar conexion al servidor
include("./proyecto/");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="../css/usuariovista.css">
</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>USUARIOS</h2>
        <nav>
            <a class="active" href="../vistas/solicitudes.php">SOLICITUDES</a>
            <a href="../vistas/usuarios.php">POLITICAS</a>
            <a href="/proyecto/logout.php">CERRAR SESIÓN</a>
        </nav>
    </aside>

    <!-- CONTENIDO -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="topbar">
            <h1>POLITICAS Y INSTRUCCIONES DE USO</h1>
            <div class="user-icon">👤</div>
        </header>

        <!-- TARJETA BIENVENIDA -->
        <div class="card">
            <p>
                Hola, 
                <span class="nombre-usuario">
                    <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
                </span>
            </p>
            <br>
            <p>
                Tu rol asignado es:
                <strong>
                    <?php echo htmlspecialchars($_SESSION['rol']); ?>
                </strong>
            </p>
        </div>

        <!-- TARJETA POLITICAS -->
        <div class="card">
            <h3>Políticas de Plataforma</h3>
            <p>
                El uso de esta plataforma implica la aceptación de nuestras políticas.
                Los datos deben manejarse de forma responsable y segura.
            </p>
        </div>

        <!-- TARJETA INSTRUCCIONES -->
        <div class="card">
            <h3>Instrucciones de Uso</h3>
            <ul>
                <li>📄 Completa correctamente los formularios</li>
                <li>💳 Verifica tus datos antes de enviar</li>
                <li>📞 Contacta soporte en caso de dudas</li>
            </ul>
        </div>


    </main>

</div>

</body>
</html>