 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php 

session_start();

//  VALIDACIÓN DE SESIÓN Y ROL
if(!isset($_SESSION['nombre']) || strtolower(trim($_SESSION['rol'])) != 'contador'){
    header("Location: ../index.php?error=rol_no_valido");
    exit();
}

include("../php/conexion.php"); 
$usuario_sesion = $_SESSION['nombre'];
$rol_usuario = $_SESSION['rol'];

// LÓGICA PARA RECHAZAR (Vía GET)
if (isset($_GET['accion']) && $_GET['accion'] === 'rechazar' && isset($_GET['id'])) {
    $id_solicitud = intval($_GET['id']);
    $estado_actual_filtro = $_GET['ver_estado'] ?? 'En proceso';

    $sql_update = "UPDATE solicitudes SET estado = 'Rechazada' WHERE id = ?";
    $stmt_upd = $conexion->prepare($sql_update);
    $stmt_upd->bind_param("i", $id_solicitud);
    
    if ($stmt_upd->execute()) {
        header("Location: contador.php?ver_estado=" . $estado_actual_filtro . "&status=success&msg=Rechazada");
        exit();
    }
}

//  CONSULTA DE CONTEOS
$sql_counts = "SELECT 
                COUNT(CASE WHEN estado = 'En proceso' THEN 1 END) as pendientes,
                COUNT(CASE WHEN estado = 'Rechazada' THEN 1 END) as rechazadas,
                COUNT(CASE WHEN estado = 'Aprobada' THEN 1 END) as aprobadas
               FROM solicitudes";
$res_counts = $conexion->query($sql_counts);
$counts = $res_counts->fetch_assoc();

//  FILTRO DE LA TABLA
$estado_filtro = $_GET['ver_estado'] ?? 'En proceso';
$sql_detalle = "SELECT * FROM solicitudes WHERE estado = ? ORDER BY id DESC";
$stmt_det = $conexion->prepare($sql_detalle);
$stmt_det->bind_param("s", $estado_filtro);
$stmt_det->execute();
$solicitudes_detalle = $stmt_det->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Contador | Sistema Alivio</title>
    <link rel="stylesheet" href="../css/contadorvista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>CONTADOR</h2>
            <div class="close-menu" onclick="toggleMenu()">×</div>
        </div>
        <nav>
            <a href="contador.php" class="<?php echo ($estado_filtro == 'En proceso') ? 'active' : ''; ?>">SOLICITUDES</a>
            <a href="pagos.php">GESTIÓN DE PAGOS</a>
        </nav>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
                <div class="topbar-title">Revisión de Gastos</div>
            </div>
            <a href="/proyecto/logout.php" class="logout-btn">Cerrar Sesión</a>
        </div>

        <div class="card">
            <div class="profile">
                <img src="https://cdn-icons-png.flaticon.com/512/5989/5989226.png" alt="usuario">
                <div>
                    <p>Nombre: <span class="nombre-usuario"><?php echo htmlspecialchars($usuario_sesion); ?></span></p>
                    <p>Rol: <strong><?php echo htmlspecialchars($rol_usuario); ?></strong></p>
                </div>
            </div>
        </div>

        <div class="stats-container">
            <a href="?ver_estado=En proceso" class="stat-card <?php echo ($estado_filtro == 'En proceso') ? 'active-filter' : ''; ?>">
                <h3>PENDIENTES</h3>
                <div class="number color-pendiente"><?php echo $counts['pendientes']; ?></div>
            </a>
            <a href="?ver_estado=Rechazada" class="stat-card <?php echo ($estado_filtro == 'Rechazada') ? 'active-filter' : ''; ?>">
                <h3>RECHAZADAS</h3>
                <div class="number color-rechazada"><?php echo $counts['rechazadas']; ?></div>
            </a>
            <a href="?ver_estado=Aprobada" class="stat-card <?php echo ($estado_filtro == 'Aprobada') ? 'active-filter' : ''; ?>">
                <h3>APROBADAS</h3>
                <div class="number color-aprobada"><?php echo $counts['aprobadas']; ?></div>
            </a>
        </div>

        <div class="card">
            <h2 class="topbar-title" style="margin-bottom: 20px;">Listado: <?php echo htmlspecialchars($estado_filtro); ?></h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <?php if($estado_filtro === 'En proceso'): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($solicitudes_detalle->num_rows > 0): ?>
                            <?php while($row = $solicitudes_detalle->fetch_assoc()): ?>
                            <tr>
                                <td data-label="Usuario"><?php echo htmlspecialchars($row['usuario_nombre']); ?></td>
                                <td data-label="Concepto"><?php echo htmlspecialchars($row['concepto']); ?></td>
                                <td data-label="Monto">$<?php echo number_format($row['monto'], 2); ?></td>
                                <td data-label="Fecha"><?php echo $row['fecha']; ?></td>
                                
                                <?php if($estado_filtro === 'En proceso'): ?>
                                <td data-label="Acciones">
                                    <button class="btn-accion btn-aprobar" onclick="confirmarAccion(<?php echo $row['id']; ?>, 'aprobar', '<?php echo $row['usuario_nombre']; ?>', '<?php echo $row['concepto']; ?>', '<?php echo $row['monto']; ?>')">Aprobar</button>
                                    <button class="btn-accion btn-rechazar" onclick="confirmarAccion(<?php echo $row['id']; ?>, 'rechazar')">Rechazar</button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="no-data" style="text-align: center; padding: 40px; color: #999;">No hay registros para mostrar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleMenu() { 
        document.getElementById('sidebar').classList.toggle('active'); 
    }

    function confirmarAccion(id, accion, solicitante = '', conceptoDefecto = '', montoDefecto = '') {
        if (accion === 'aprobar') {
            Swal.fire({
                title: 'Registrar Pago',
                html: `
                    <div style="text-align: left; font-size: 14px;">
                        <p><b>Solicitante:</b> ${solicitante}</p>
                        <label>Nombre de la compra:</label>
                        <input id="nombre_compra" class="swal2-input" value="${conceptoDefecto}">
                        <label>Precio final ($):</label>
                        <input id="precio" type="number" step="0.01" class="swal2-input" value="${montoDefecto}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Siguiente',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('nombre_compra').value;
                    const precio = document.getElementById('precio').value;
                    if (!nombre || !precio) {
                        Swal.showValidationMessage('Por favor completa los campos');
                    }
                    return { nombre_compra: nombre, precio: precio, nombre_solicitante: solicitante };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const datos = result.value;

                    Swal.fire({
                        title: '¿Subir comprobante?',
                        text: "Si eliges 'No', el estatus quedará como 'Incompleto'",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, subir',
                        cancelButtonText: 'No, solo registrar'
                    }).then((uploadResult) => {
                        if (uploadResult.isConfirmed) {
                            Swal.fire({
                                title: 'Seleccionar Archivo',
                                input: 'file',
                                inputAttributes: { 'accept': 'image/*,application/pdf' },
                                showCancelButton: true
                            }).then((fileResult) => {
                                if (fileResult.value) {
                                    enviarDatosPago(id, datos, fileResult.value);
                                }
                            });
                        } else {
                            enviarDatosPago(id, datos, null);
                        }
                    });
                }
            });
        } else {
            Swal.fire({
                title: '¿Rechazar solicitud?',
                text: "Esta acción enviará la solicitud al historial de rechazadas.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                confirmButtonText: 'Sí, rechazar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `contador.php?id=${id}&accion=rechazar&ver_estado=En proceso`;
                }
            });
        }
    }

    function enviarDatosPago(idSolicitud, datos, archivo) {
        const formData = new FormData();
        formData.append('id_solicitud', idSolicitud);
        formData.append('nombre_solicitante', datos.nombre_solicitante);
        formData.append('nombre_compra', datos.nombre_compra);
        formData.append('precio', datos.precio);
        
        if (archivo) {
            formData.append('comprobante', archivo);
        }

        fetch('../php/procesar_pago.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) 
        .then(text => {
            try {
        
                const jsonStart = text.indexOf('{');
                if (jsonStart === -1) throw new Error("No JSON found");
                const data = JSON.parse(text.substring(jsonStart));

                if (data.status === 'success') {
                    window.location.href = `contador.php?ver_estado=En proceso&status=success&msg=Aprobada`;
                } else {
                    Swal.fire('Error', data.msg || 'Error desconocido', 'error');
                }
            } catch (error) {
                console.error('Error de servidor:', text);
                Swal.fire('Error', 'El servidor respondió incorrectamente. Revisa la consola.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
        });
    }

    window.addEventListener('load', () => {
        const params = new URLSearchParams(window.location.search);
        if (params.get('status') === 'success') {
            Swal.fire({
                title: '¡Hecho!',
                text: 'Solicitud ' + params.get('msg') + ' con éxito.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?ver_estado=" + params.get('ver_estado'));
        }
    });
</script>
</body>
</html>