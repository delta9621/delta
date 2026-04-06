 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php
include("conexion.php");

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>body{ font-family: sans-serif; background-color: #f0f4f8; }</style>
</head>
<body>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // Lógica: ¿Se cambió la contraseña?
    if (!empty($password)) {
        // --- CAMBIO AQUÍ: Encriptamos la contraseña ---
        // PASSWORD_DEFAULT utiliza actualmente Bcrypt, que es el estándar de la industria.
        $password_encriptada = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET nombre=?, correo=?, password=?, rol=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $correo, $password_encriptada, $rol, $id);
    } else {
        // Si no escribió nada, mantenemos la contraseña actual (no tocamos el campo password)
        $sql = "UPDATE usuarios SET nombre=?, correo=?, rol=? WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $correo, $rol, $id);
    }

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'Los datos de $nombre se actualizaron con éxito.',
                confirmButtonColor: '#2196f3'
            }).then(() => {
                window.location.href = '../vistas/admin.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo actualizar el registro: " . addslashes($conexion->error) . "',
                confirmButtonColor: '#e74c3c'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
    $stmt->close();
}
echo "</body></html>";
?>