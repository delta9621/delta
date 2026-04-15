 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->


<?php 
session_start();

// VALIDACIÓN DE SESIÓN Y ROL
if(!isset($_SESSION['nombre']) || strtolower(trim($_SESSION['rol'])) != 'contador'){
    header("Location: /proyecto/index.php?error=rol_no_valido");
    exit();
}

include("../php/conexion.php"); 
$usuario_sesion = $_SESSION['nombre'];
$rol_usuario = $_SESSION['rol'];

// FILTRO DE ESTATUS
$filtro_estatus = $_GET['estatus'] ?? 'Incompleto';

// CONSULTA DE CONTEOS
$sql_counts = "SELECT 
                COUNT(CASE WHEN estatus = 'Incompleto' THEN 1 END) as inc,
                COUNT(CASE WHEN estatus = 'Completado' THEN 1 END) as comp
               FROM pagos";
$res_counts = $conexion->query($sql_counts);
$counts = $res_counts->fetch_assoc();

// CONSULTA DE PAGOS
$sql = "SELECT id, nombre_solicitante, nombre_compra, precio, comprobante_envio, comprobante_compra, estatus, nombre_contador 
        FROM pagos WHERE estatus = ? ORDER BY id DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $filtro_estatus);
$stmt->execute();
$resultado_pagos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pagos | Sistema Alivio</title>
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
            <a href="contador.php">SOLICITUDES</a>
            <a href="pagos.php" class="active">GESTIÓN DE PAGOS</a>
        </nav>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
                <div class="topbar-title">Administración de Comprobantes</div>
            </div>
            <a href="/proyecto/index.php" class="logout-btn">Cerrar Sesión</a>
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
            <a href="?estatus=Incompleto" class="stat-card <?php echo ($filtro_estatus == 'Incompleto') ? 'active-filter' : ''; ?>">
                <h3>PAGOS INCOMPLETOS</h3>
                <div class="number color-pendiente"><?php echo $counts['inc']; ?></div>
            </a>
            <a href="?estatus=Completado" class="stat-card <?php echo ($filtro_estatus == 'Completado') ? 'active-filter' : ''; ?>">
                <h3>PAGOS COMPLETADOS</h3>
                <div class="number color-aprobada"><?php echo $counts['comp']; ?></div>
            </a>
        </div>

        <div class="card">
            <h2 class="topbar-title">Listado de <?php echo htmlspecialchars($filtro_estatus); ?>s</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Solicitante</th>
                            <th>Monto</th>
                            <th>Envío (Transf.)</th>
                            <th>Compra (Ticket)</th>
                            <th>Aprobado por</th> 
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($pago = $resultado_pagos->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Solicitante"><?php echo htmlspecialchars($pago['nombre_solicitante']); ?></td>
                            <td data-label="Monto">$<?php echo number_format($pago['precio'], 2); ?></td>
                            <td data-label="Envío">
                                <?php if ($pago['comprobante_envio']): ?>
                                    <button class="btn-accion" style="background:#3db3c7;" onclick="verArchivo('<?php echo $pago['comprobante_envio']; ?>')">👁️ Ver</button>
                                <?php else: ?>
                                    <button class="btn-accion" style="background:#95a5a6;" onclick="subirDoc(<?php echo $pago['id']; ?>, 'comprobante_envio')">📤 Subir</button>
                                <?php endif; ?>
                            </td>
                            <td data-label="Compra">
                                <?php if ($pago['comprobante_compra']): ?>
                                    <button class="btn-accion" style="background:#3db3c7;" onclick="verArchivo('<?php echo $pago['comprobante_compra']; ?>')">👁️ Ver</button>
                                <?php else: ?>
                                    <button class="btn-accion" style="background:#95a5a6;" onclick="subirDoc(<?php echo $pago['id']; ?>, 'comprobante_compra')">📤 Subir</button>
                                <?php endif; ?>
                            </td>
                            <td data-label="Aprobado por">
                                <span style="font-weight: bold; color: #2c3e50;">
                                    <?php echo !empty($pago['nombre_contador']) ? htmlspecialchars($pago['nombre_contador']) : '<i style="color:#bbb;">Pendiente</i>'; ?>
                                </span>
                            </td>
                            <td data-label="Estado">
                                <span class="badge" style="background: <?php echo ($pago['estatus'] == 'Completado') ? '#d4edda' : '#ffeaa7'; ?>;">
                                    <?php echo $pago['estatus']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleMenu() { document.getElementById('sidebar').classList.toggle('active'); }

    function verArchivo(nombre) {
        const url = `../uploads/${nombre}`;
        const esPdf = nombre.toLowerCase().endsWith('.pdf');
        Swal.fire({
            title: 'Visualización',
            html: esPdf ? `<iframe src="${url}" style="width:100%; height:450px; border:none;"></iframe>` : `<img src="${url}" style="max-width:100%; border-radius:8px;">`,
            confirmButtonText: 'Cerrar',
            width: '700px'
        });
    }

    function subirDoc(id, columna) {
        // Obtenemos el nombre del contador desde PHP a JS
        const nombreContadorActual = "<?php echo $usuario_sesion; ?>";

        Swal.fire({
            title: 'Subir Comprobante',
            text: `Responsable: ${nombreContadorActual}`,
            input: 'file',
            inputAttributes: { 'accept': 'image/*,application/pdf' },
            showCancelButton: true,
            confirmButtonText: 'Subir Ahora',
            confirmButtonColor: '#27ae60'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire({ title: 'Subiendo...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

                const formData = new FormData();
                formData.append('id_pago', id);
                formData.append('archivo', result.value); // El archivo está en result.value
                formData.append('columna', columna);
                formData.append('nombre_contador', nombreContadorActual);

                fetch('../php/procesar_dual.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text.trim());
                        if(data.status === 'success') {
                            Swal.fire({ icon: 'success', title: '¡Éxito!', timer: 1000, showConfirmButton: false }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.msg, 'error');
                        }
                    } catch (e) {
                        location.reload(); 
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                });
            }
        });
    }
</script>
</body>
</html>