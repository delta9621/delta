 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

 
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("conexion.php");

// Cargamos la librería SweetAlert2 al inicio
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f4f7; }
    </style>
</head>
<body>";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // Encriptación de contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // INSERTAR USUARIO
    $sql = "INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        die("Error en la preparación: " . $conexion->error);
    }

    $stmt->bind_param("ssss", $nombre, $correo, $password_hash, $rol);

    // Verificación y ejecución
    if($stmt->execute()){
        // ÉXITO: Usuario registrado
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro Exitoso!',
                    text: 'El usuario $nombre ha sido creado correctamente.',
                    confirmButtonColor: '#1c5aa6'
                }).then(() => {
                    window.location.href = '../vistas/admin.php'; // Cambia esto a tu página de lista de usuarios
                });
            }, 100);
        </script>";
    } else {
        if($conexion->errno == 1062){
            // ERROR: Correo duplicado
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Correo ya registrado',
                        text: 'El correo $correo ya existe en el sistema.',
                        confirmButtonColor: '#e74c3c'
                    }).then(() => {
                        window.history.back();
                    });
                }, 100);
            </script>";
        } else {
            // OTROS ERRORES
            $error_msg = addslashes($stmt->error);
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al registrar',
                        text: '$error_msg',
                        confirmButtonColor: '#e74c3c'
                    }).then(() => {
                        window.history.back();
                    });
                }, 100);
            </script>";
        }
    }

    $stmt->close();
    $conexion->close();
}

echo "</body></html>";
?>