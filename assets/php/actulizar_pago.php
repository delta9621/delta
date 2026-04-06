 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
 
<?php
session_start();
header('Content-Type: application/json');
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pago'])) {
    $id_pago = intval($_POST['id_pago']);
    
    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['comprobante']['name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Extensiones permitidas
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (in_array($ext, $allowed)) {
            $nuevo_nombre = "COMPROBANTE_" . $id_pago . "_" . time() . "." . $ext;
            $ruta_destino = "../uploads/" . $nuevo_nombre;
            
            if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $ruta_destino)) {
                $sql = "UPDATE pagos SET comprobante_envio = ?, estatus = 'Completado' WHERE id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("si", $nuevo_nombre, $id_pago);
                
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'msg' => 'Archivo subido correctamente']);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar base de datos']);
                }
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Error al mover el archivo al servidor']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Formato no permitido. Solo JPG, PNG o PDF.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Error en la carga del archivo.']);
    }
}