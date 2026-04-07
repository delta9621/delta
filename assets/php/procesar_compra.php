<?php
// 1. Desactivar salida de errores visuales para que no ensucien el JSON
error_reporting(0); 
ini_set('display_errors', 0);

session_start();
include("../php/conexion.php");

// 2. Asegurar el encabezado JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {
    
    $id_pago = intval($_POST['id_pago']);
    $archivo = $_FILES['archivo'];
    
    $folder = "../uploads/";
    if (!file_exists($folder)) { mkdir($folder, 0777, true); }

    $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nuevo_nombre = "ticket_" . $id_pago . "_" . time() . "." . $ext;
    $ruta_destino = $folder . $nuevo_nombre;

    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        // Actualizamos estatus y el nombre del archivo
        $sql = "UPDATE pagos SET comprobante_compra = ?, estatus = 'Completado' WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $nuevo_nombre, $id_pago);

        if ($stmt->execute()) {
            // 3. Solo imprimir el JSON y nada más
            echo json_encode(['status' => 'success', 'msg' => 'Ticket subido correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Error en base de datos.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'No se pudo guardar el archivo físico.']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Petición inválida.']);
}
// 4. Salir inmediatamente para evitar cualquier eco accidental
exit; 
?>