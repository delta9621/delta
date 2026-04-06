 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
 
<?php
session_start();
include("conexion.php");

//  Cargamos la librería al inicio
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>
        body { font-family: Arial; background-color: #e6e6e6; }
    </style>
</head>
<body>";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $correo = $_POST['correo'];
    $pass = $_POST['password'];

    $sql = "SELECT id, nombre, password, rol FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($usuario = $resultado->fetch_assoc()){
        if(password_verify($pass, $usuario['password'])){
            
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre']; 
            $_SESSION['rol'] = $usuario['rol'];

            $ruta = "";
            switch($usuario['rol']){
                case 'administrador': $ruta = "../vistas/admin.php"; break;
                case 'Contador':      $ruta = "../vistas/contador.php"; break;
                case 'usuario':       $ruta = "../vistas/solicitudes.php"; break;
                default:              $ruta = "/proyecto/logout.php?error=rol_no_valido"; break;
            }

            // ÉXITO: Usamos JS para redirigir, NO header de PHP
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: 'Ingresando al sistema...',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '$ruta';
                    });
                }, 100);
            </script>";
            exit(); 
        }
    }

    // ERROR: Credenciales incorrectas
    echo "<script>
        setTimeout(function() {
            Swal.fire({
                icon: 'error',
                title: 'Acceso Denegado',
                text: 'Correo o contraseña incorrectos',
                confirmButtonColor: '#1e3c72'
            }).then(() => {
                window.location.href = '../../logout.php'; 
            });
        }, 100);
    </script>";
}

echo "</body></html>";
?>