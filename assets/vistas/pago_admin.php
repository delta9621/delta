 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->

<?php 
session_start();

// SEGURIDAD: Solo usuarios con rol 'administrador'
if(!isset($_SESSION['rol']) || strcasecmp(trim($_SESSION['rol']), 'administrador') !== 0){
    header("Location: /proyecto/index.php");
    exit();
}

include("../php/conexion.php"); 
$usuario_sesion = $_SESSION['nombre'] ?? 'Admin';
$rol_usuario = $_SESSION['rol'] ?? 'administrador';
$filtro_estatus = $_GET['estatus'] ?? 'Incompleto';

// CONTEO: Filtrado por el nombre del administrador actual para sus propias solicitudes
$sql_counts = "SELECT 
                COUNT(CASE WHEN estatus = 'Incompleto' THEN 1 END) as inc,
                COUNT(CASE WHEN estatus = 'Completado' THEN 1 END) as comp
               FROM pagos WHERE nombre_solicitante = ?";
$stmt_c = $conexion->prepare($sql_counts);
$stmt_c->bind_param("s", $usuario_sesion);
$stmt_c->execute();
$counts = $stmt_c->get_result()->fetch_assoc();

// DATOS: Filtrados para mostrar SOLO lo que pertenece al administrador logueado
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
    <title>Mis Comprobantes Admin | Alivio</title>
    <link rel="stylesheet" href="../css/pago_simplificado.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>ADMINISTRADOR</h2>
            <div class="close-menu" onclick="toggleMenu()">×</div>
        </div>
        <nav>
            <a href="../vistas/admin.php" class="active">GESTOR DE USUARIOS</a>
            <a href="../vistas/adminsoli.php">SOLICITUDES</a>
            <a href="../formularios/solicitudadmin.php">NUEVA SOLICITUD</a>
            <a href="../vistas/pago_admin.php" >COMPROBANTES</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar-panel">
            <div class="topbar-left">
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
                <span class="topbar-title">Mis Comprobantes Generados</span>
            </div>
            <div class="topbar-right">
                <a href="/proyecto/index.php" class="logout-link">Cerrar Sesión</a>
            </div>
        </header>

        <div class="card-profile">
            <div class="user-profile-header">
                <div class="user-icon">🛡️</div>
                <div class="user-info">
                    <p>Usuario: <span class="nombre-highlight"><?php echo htmlspecialchars($usuario_sesion); ?></span></p>
                    <p>Nivel de acceso: <strong><?php echo strtoupper(htmlspecialchars($rol_usuario)); ?></strong></p>
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
                            <td colspan="4" style="text-align:center; padding: 20px;">No tienes registros personales en esta sección.</td>
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
            title: 'Subir Comprobante Personal',
            text: 'Seleccione imagen o PDF del ticket de compra',
            input: 'file',
            inputAttributes: { 'accept': 'image/*,application/pdf' },
            showCancelButton: true,
            confirmButtonText: 'Subir',
            confirmButtonColor: '#27ae60',
            cancelButtonColor: '#d33',
            inputValidator: (value) => { if (!value) return '¡Selecciona un archivo!' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ 
                    title: 'Subiendo...', 
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); } 
                });

                const formData = new FormData();
                formData.append('id_pago', id);
                formData.append('archivo', result.value);
                formData.append('columna', 'comprobante_compra');
                
                // ESTA ES LA MODIFICACIÓN CLAVE:
                // Enviamos esta bandera para que el PHP sepa que NO debe tocar el nombre del contador.
                formData.append('solo_archivo', 'true'); 

                fetch('../php/procesar_dual.php', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(text => {
                    const cleanText = text.trim();
                    try {
                        const data = JSON.parse(cleanText);
                        if(data.status === 'success') {
                            Swal.fire({ 
                                icon: 'success', 
                                title: '¡Listo!', 
                                text: 'Ticket cargado correctamente',
                                showConfirmButton: false, 
                                timer: 1500 
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.msg, 'error');
                        }
                    } catch (e) {
                        console.warn("Respuesta no JSON:", text);
                        location.reload(); 
                    }
                })
                .catch(error => {
                    console.error('Error de conexión:', error);
                    Swal.fire('Error de Red', 'No se pudo contactar con el servidor', 'error');
                });
            }
        });
    }
</script>
</body>
</html>