 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php
session_start();
include("../php/conexion.php");

// Verificar si se recibió un ID
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = $_GET['id'];

// Obtener datos actuales del usuario
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Usuario</title>
    <link rel="stylesheet" href="../css/forusu.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <form action="../php/actualizarusu.php" method="post">
        <h2>Actualizar Usuario</h2>
        
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

        <div>
            <label for="nombre">Nombre Del Usuario:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
        </div><br>

        <div>
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
        </div><br>

        <div>
            <label for="password">Nueva Contraseña (dejar vacío para no cambiar):</label>
            <input type="password" id="password" name="password" minlength="8">
            <small style="color: #666; font-size: 11px;">Mínimo 8 caracteres</small>
        </div><br>

        <div>
            <label for="rol">Asignación de Rol:</label>
            <select id="rol" name="rol" required>
                <option value="administrador" <?php echo ($usuario['rol'] == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                <option value="Contador" <?php echo ($usuario['rol'] == 'Contador') ? 'selected' : ''; ?>>Contador</option>
                <option value="usuario" <?php echo ($usuario['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
            </select>
        </div><br>

        <button type="submit">Guardar Cambios</button>
        <div style="text-align: center; margin-top: 15px;">
            <a href="../vistas/admin.php" style="text-decoration: none; color: #6a1b9a; font-size: 14px;">Cancelar y Volver</a>
        </div>
    </form>

</body>
</html>