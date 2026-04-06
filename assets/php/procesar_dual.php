 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php
//  Evitar que cualquier error de PHP ensucie la respuesta JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

session_start();
include("conexion.php");

header('Content-Type: application/json');
$response = ['status' => 'error', 'msg' => 'Error desconocido'];

//  Verificar sesión del contador
if (!isset($_SESSION['nombre'])) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'msg' => 'Sesión no válida']);
    exit;
}

$nombre_contador_activo = $_SESSION['nombre'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pago'])) {
    
    $id_pago = intval($_POST['id_pago']);
    $columna = $_POST['columna']; // comprobante_envio o comprobante_compra
    
    // Validar que la columna sea una de las permitidas
    $columnas_validas = ['comprobante_envio', 'comprobante_compra'];
    
    if (in_array($columna, $columnas_validas) && isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        
        $directorio = "../uploads/";
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
        $nombre_final = "PAGO_" . $id_pago . "_" . uniqid() . "." . $extension;
        $ruta_destino = $directorio . $nombre_final;

        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_destino)) {
            
            //  ACTUALIZACIÓN CLAVE: Insertamos el nombre del contador aquí
            // Esto llenará el campo que actualmente ves vacío en tu BD
            $sql = "UPDATE pagos SET $columna = ?, nombre_contador = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssi", $nombre_final, $nombre_contador_activo, $id_pago);
            
            if ($stmt->execute()) {
                
                //  Verificar si ambos comprobantes ya existen para completar el estatus
                $check = $conexion->query("SELECT comprobante_envio, comprobante_compra FROM pagos WHERE id = $id_pago")->fetch_assoc();
                
                if (!empty($check['comprobante_envio']) && !empty($check['comprobante_compra'])) {
                    $conexion->query("UPDATE pagos SET estatus = 'Completado' WHERE id = $id_pago");
                }

                $response = ['status' => 'success', 'msg' => 'Comprobante registrado con éxito'];
            } else {
                $response['msg'] = "Error al actualizar la base de datos";
            }
        } else {
            $response['msg'] = "No se pudo guardar el archivo en el servidor";
        }
    } else {
        $response['msg'] = "Archivo no válido o columna incorrecta";
    }
}

// Limpiar buffer y enviar JSON
ob_end_clean();
echo json_encode($response);
exit;