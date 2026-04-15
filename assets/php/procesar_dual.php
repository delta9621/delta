 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php
ob_start(); 
session_start();
include("conexion.php");
header('Content-Type: application/json');

$response = ["status" => "error", "msg" => "Error desconocido"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pago = $_POST['id_pago'] ?? null;
    $columna = $_POST['columna'] ?? null;
    $solo_archivo = $_POST['solo_archivo'] ?? 'false'; // Capturamos la nueva bandera

    if (isset($_FILES['archivo']) && $id_pago && $columna) {
        $ext = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre = "pago_" . $id_pago . "_" . time() . "." . $ext;
        $ruta = "../uploads/" . $nuevo_nombre;

        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta)) {
            
            // LÓGICA DE ACTUALIZACIÓN
            if ($solo_archivo === 'true') {
                // CASO ADMIN: Solo actualizamos el archivo, mantenemos el contador intacto
                $sql = "UPDATE pagos SET $columna = ? WHERE id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("si", $nuevo_nombre, $id_pago);
            } else {
                // CASO CONTADOR: Actualizamos archivo Y grabamos quién aprobó
                $nombre_contador = $_POST['nombre_contador'] ?? $_SESSION['nombre'] ?? 'Sistema';
                $sql = "UPDATE pagos SET $columna = ?, nombre_contador = ? WHERE id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssi", $nuevo_nombre, $nombre_contador, $id_pago);
            }
            
            if ($stmt->execute()) {
                // Verificamos si con esta subida ya están ambos comprobantes
                $res = $conexion->query("SELECT comprobante_envio, comprobante_compra FROM pagos WHERE id = $id_pago");
                $fila = $res->fetch_assoc();
                
                if (!empty($fila['comprobante_envio']) && !empty($fila['comprobante_compra'])) {
                    $conexion->query("UPDATE pagos SET estatus = 'Completado' WHERE id = $id_pago");
                }
                
                $response = ["status" => "success"];
            } else {
                $response["msg"] = "Error en base de datos";
            }
        } else {
            $response["msg"] = "Error al mover archivo";
        }
    }
}

ob_end_clean(); 
echo json_encode($response);
exit;