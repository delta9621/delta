 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
 
<?php
include("../php/conexion.php");

// Estructura mínima para que SweetAlert funcione
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <style>body{ font-family: 'Segoe UI', sans-serif; background-color: #f2f4f7; }</style>
</head>
<body>";

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Usamos sentencia preparada por seguridad
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Usuario Eliminado',
                text: 'El registro ha sido borrado correctamente.',
                showConfirmButton: false,
                timer: 1500
            }).then(() => { 
                window.location.href='../vistas/admin.php'; 
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo eliminar al usuario.',
                confirmButtonColor: '#e74c3c'
            }).then(() => { 
                window.location.href='../vistas/admin.php'; 
            });
        </script>";
    }
    $stmt->close();
}
echo "</body></html>";
?>