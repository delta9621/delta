 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php
//  Bloqueo de salida de errores para no ensuciar el JSON
error_reporting(0);
ini_set('display_errors', 0);

//  Iniciar buffer para limpiar cualquier salida accidental
ob_start();

session_start();
include("conexion.php");

// Cabecera obligatoria para respuesta JSON
header('Content-Type: application/json');

$response = ['status' => 'error', 'msg' => 'Error desconocido'];

// Verificamos que el contador tenga sesión iniciada para obtener su nombre
$nombre_contador = $_SESSION['nombre'] ?? 'Sistema';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recibir datos del FormData
    $id_solicitud = isset($_POST['id_solicitud']) ? intval($_POST['id_solicitud']) : 0;
    $nombre_solicitante = $_POST['nombre_solicitante'] ?? 'Sin nombre';
    $nombre_compra = $_POST['nombre_compra'] ?? 'Sin concepto';
    $precio = $_POST['precio'] ?? 0;
    $fecha_actual = date('Y-m-d'); // Para la columna 'fecha' de tu tabla
    
    if ($id_solicitud > 0) {
        $nombre_archivo = null;
        $estatus_pago = 'Incompleto';

        //  Manejo del archivo
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $directorio = "../uploads/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $ext = pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = "COMP_" . $id_solicitud . "_" . time() . "." . $ext;
            
            move_uploaded_file($_FILES['comprobante']['tmp_name'], $directorio . $nombre_archivo);
        }

        /**
         *  INSERTAR EN LA TABLA 'pagos'
         */
        $sql_pago = "INSERT INTO pagos (solicitud_id, nombre_contador, nombre_solicitante, nombre_compra, fecha, precio, comprobante_envio, estatus) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($sql_pago);
        
        // Tipos de datos: i (int), s (string), s, s, s, d (decimal), s, s
        $stmt->bind_param("issssdss", 
            $id_solicitud, 
            $nombre_contador, 
            $nombre_solicitante, 
            $nombre_compra, 
            $fecha_actual,
            $precio, 
            $nombre_archivo, 
            $estatus_pago
        );

        if ($stmt->execute()) {
            //  Actualizar la solicitud original
            $conexion->query("UPDATE solicitudes SET estado = 'Aprobada' WHERE id = $id_solicitud");
            
            $response = ['status' => 'success', 'msg' => 'Pago registrado correctamente con ID: ' . $id_solicitud];
        } else {
            $response['msg'] = "Error al insertar pago: " . $stmt->error;
        }
    } else {
        $response['msg'] = "ID de solicitud no válido ($id_solicitud)";
    }
}

//  Limpiar el buffer y enviar el JSON
ob_end_clean();
echo json_encode($response);
exit;