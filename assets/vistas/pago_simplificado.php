 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
<?php 
session_start();

/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte
 * @date 2026-04-07
 */

// SEGURIDAD: Solo usuarios con rol 'usuario'
if(!isset($_SESSION['rol']) || strcasecmp(trim($_SESSION['rol']), 'usuario') !== 0){
    header("Location: /proyecto/index.php");
    exit();
}

include("../php/conexion.php"); 
$usuario_sesion = $_SESSION['nombre'] ?? 'Usuario';
$rol_usuario = $_SESSION['rol'] ?? 'usuario';
$filtro_estatus = $_GET['estatus'] ?? 'Incompleto';

// Conteo de registros pertenecientes solo a este usuario
$sql_counts = "SELECT 
                COUNT(CASE WHEN estatus = 'Incompleto' THEN 1 END) as inc,
                COUNT(CASE WHEN estatus = 'Completado' THEN 1 END) as comp
               FROM pagos WHERE nombre_solicitante = ?";
$stmt_c = $conexion->prepare($sql_counts);
$stmt_c->bind_param("s", $usuario_sesion);
$stmt_c->execute();
$counts = $stmt_c->get_result()->fetch_assoc();

// Datos filtrados por estatus y usuario
$sql = "SELECT id, nombre_compra, precio, comprobante_compra, estatus 
        FROM pagos WHERE estatus = ? AND nombre_solicitante = ? ORDER BY id DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $filtro_estatus, $usuario_sesion);
$stmt->execute();
$resultado_pagos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Comprobantes | Sistema Alivio</title>
    <link rel="stylesheet" href="../css/pago_simplificado.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Panel Usuario</h2>
            <div class="close-menu" onclick="toggleMenu()">×</div>
        </div>
        <nav>
            <a href="solicitudes.php">SOLICITUDES</a>
            <a href="../formularios/solicitud.php">NUEVA SOLICITUD</a>
            <a href="pago_simplificado.php" class="active">COMPROBANTES</a>
            <a href="../php/logout.php" class="logout-mobile">CERRAR SESIÓN</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar-panel">
            <div class="topbar-left">
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
                <span class="topbar-title">Gestión de Comprobantes</span>
            </div>
            <div class="topbar-right">
                <a href="../php/logout.php" style="color: white; text-decoration: none; font-size: 0.9rem;">Cerrar Sesión</a>
            </div>
        </header>

        <div class="card-profile">
            <div class="user-profile-header">
                <div class="user-icon">👤</div>
                <div class="user-info">
                    <p>Usuario: <span class="nombre-highlight"><?php echo htmlspecialchars($usuario_sesion); ?></span></p>
                    <p>Rol: <strong><?php echo htmlspecialchars($rol_usuario); ?></strong></p>
                </div>
            </div>
        </div>

        <div class="stats-container">
            <a href="?estatus=Incompleto" class="stat-card <?php echo ($filtro_estatus == 'Incompleto') ? 'active-filter' : ''; ?>">
                <h3>MIS PENDIENTES</h3>
                <div class="number color-pendiente"><?php echo $counts['inc']; ?></div>
            </a>
            <a href="?estatus=Completado" class="stat-card <?php echo ($filtro_estatus == 'Completado') ? 'active-filter' : ''; ?>">
                <h3>MIS FINALIZADOS</h3>
                <div class="number color-aprobada"><?php echo $counts['comp']; ?></div>
            </a>
        </div>

        <div class="tabla-container">
            <h2 class="tabla-title">Listado Personal: <?php echo htmlspecialchars($filtro_estatus); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Ticket</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($resultado_pagos->num_rows > 0): ?>
                        <?php while($p = $resultado_pagos->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Concepto"><strong><?php echo htmlspecialchars($p['nombre_compra']); ?></strong></td>
                            <td data-label="Monto">$<?php echo number_format($p['precio'], 2); ?></td>
                            <td data-label="Ticket">
                                <?php if($p['comprobante_compra']): ?>
                                    <button class="btn-accion btn-ver" onclick="verDoc('<?php echo $p['comprobante_compra']; ?>')">👁️ Ver</button>
                                <?php else: ?>
                                    <button class="btn-accion btn-subir" onclick="subirDoc(<?php echo $p['id']; ?>)">📤 Subir</button>
                                <?php endif; ?>
                            </td>
                            <td data-label="Estado">
                                <span class="badge-estado <?php echo ($p['estatus'] == 'Completado') ? 'badge-completado' : 'badge-incompleto'; ?>">
                                    <?php echo $p['estatus']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 40px; color: #999;">No hay comprobantes registrados aquí.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
    function toggleMenu() { 
        document.getElementById('sidebar').classList.toggle('active'); 
    }

    function verDoc(f) {
        const url = `../uploads/${f}`;
        const esPdf = f.toLowerCase().endsWith('.pdf');

        Swal.fire({
            title: 'Vista de Comprobante',
            html: esPdf 
                ? `<iframe src="${url}" width="100%" height="450px" style="border:none;"></iframe>` 
                : `<img src="${url}" style="max-width:100%; border-radius:8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">`,
            width: esPdf ? '800px' : '600px',
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#3085d6'
        });
    }

    function subirDoc(id) {
        Swal.fire({
            title: 'Subir Ticket de Compra',
            text: 'Seleccione el archivo (Imagen o PDF)',
            input: 'file',
            inputAttributes: { 'accept': 'image/*,application/pdf' },
            showCancelButton: true,
            confirmButtonText: 'Subir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#27ae60',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // Alerta de carga
                Swal.fire({
                    title: 'Subiendo archivo...',
                    html: 'Por favor espere mientras procesamos su ticket',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const formData = new FormData();
                formData.append('id_pago', id);
                formData.append('archivo', result.value);

                fetch('../php/procesar_compra.php', { 
                    method: 'POST', 
                    body: formData 
                })
                .then(response => response.text()) // Leemos como texto para manejar respuestas sucias del servidor
                .then(text => {
                    const cleanText = text.trim(); // Limpieza de espacios en blanco
                    try {
                        const data = JSON.parse(cleanText);
                        if(data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: data.msg,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => { location.reload(); });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error en Proceso',
                                text: data.msg
                            });
                        }
                    } catch (e) {
                        // Si el JSON falla pero el archivo se insertó (comportamiento reportado), recargamos
                        console.warn("Respuesta no JSON detectada:", text);
                        location.reload(); 
                    }
                })
                .catch(error => {
                    console.error('Error de red:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Red',
                        text: 'No se pudo establecer contacto con el servidor.'
                    });
                });
            }
        });
    }
</script>
</body>
</html>