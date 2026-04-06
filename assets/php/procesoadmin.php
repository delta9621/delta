 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php
session_start();
//conexion a la base de datos
include 'conexion.php';

// Cargamos la librería SweetAlert2 y estilos básicos para que la página de transición no se vea vacía
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f4f8; }
    </style>
</head>
<body>";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // El nombre se registra directamente por la sesión
    $usuario_nombre = $_SESSION['nombre'];
    
    // Recolección de los datos 
    $fecha = $_POST['fecha'];
    $concepto = $_POST['concepto'];
    $monto = $_POST['monto'];
    $proveedor = $_POST['proveedor'];
    $metodo_pago = $_POST['metodo_pago'];
    $prioridad = $_POST['prioridad'];
    $observaciones = $_POST['observaciones'];

    // Consulta a utilizar e insertar datos
    $sql = "INSERT INTO solicitudes (usuario_nombre, fecha, concepto, monto, proveedor, metodo_pago, prioridad, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);

    if($stmt){
        mysqli_stmt_bind_param($stmt, "sssdssss", $usuario_nombre, $fecha, $concepto, $monto, $proveedor, $metodo_pago, $prioridad, $observaciones);

        if(mysqli_stmt_execute($stmt)){
            //  ALERTA DE ÉXITO
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Solicitud Enviada!',
                    text: 'Los datos se registraron correctamente.',
                    confirmButtonColor: '#1c5aa6'
                }).then((result) => {
                    window.location.href='../vistas/adminsoli.php'; 
                });
            </script>";
        } else {
            //  ERROR EN EJECUCIÓN
            $error = mysqli_error($conexion);
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la solicitud',
                    text: '" . addslashes($error) . "',
                    confirmButtonColor: '#e74c3c'
                }).then(() => {
                    window.history.back();
                });
            </script>";
        }
    } else {
        //  ERROR EN PREPARACIÓN
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Error Fatal',
                text: 'No se pudo preparar la consulta en la base de datos.',
                confirmButtonColor: '#e74c3c'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
}

echo "</body></html>";
?>