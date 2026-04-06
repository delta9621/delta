 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
 
<?php 
session_start();

// Validación de sesión
if(!isset($_SESSION['nombre'])){
    header("Location: /proyecto/index.php");
    exit();
}

// Conexión
include("../php/conexion.php"); 

$usuario_sesion = $_SESSION['nombre'];
$rol_usuario = $_SESSION['rol'] ?? 'Usuario';

// CONSULTA CONTADORES
$sql_counts = "SELECT 
                COUNT(CASE WHEN estado = 'En proceso' THEN 1 END) as pendientes,
                COUNT(CASE WHEN estado = 'Rechazada' THEN 1 END) as rechazadas,
                COUNT(CASE WHEN estado = 'Aprobada' THEN 1 END) as aprobadas
               FROM solicitudes 
               WHERE usuario_nombre = ?";

$stmt_counts = $conexion->prepare($sql_counts);
$stmt_counts->bind_param("s", $usuario_sesion);
$stmt_counts->execute();
$res_counts = $stmt_counts->get_result();
$counts = $res_counts->fetch_assoc();

// FILTRO PARA LA TABLA
$estado_filtro = $_GET['ver_estado'] ?? '';
$solicitudes_detalle = null;

if($estado_filtro !== ''){
    $stmt = $conexion->prepare("SELECT * FROM solicitudes WHERE estado = ? AND usuario_nombre = ?");
    $stmt->bind_param("ss", $estado_filtro, $usuario_sesion);
    $stmt->execute();
    $solicitudes_detalle = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Solicitudes</title>
    <link rel="stylesheet" href="../css/solicitudes.css">
</head>
<body>

<div class="container">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>USUARIO</h2>
            <div class="close-menu" onclick="toggleMenu()">×</div>
        </div>
        <nav>
            <a class="<?php echo ($estado_filtro == '') ?  : ''; ?>" href="solicitudes.php">SOLICITUDES</a>
            <a href="../formularios/solicitud.php">NUEVA SOLICITUD</a>
            <a href="../vistas/pago_simplificado.php">COMPROBANTES</a>
            <a href="/proyecto/index.php" class="logout-mobile">CERRAR SESIÓN</a>
        </nav>
    </aside>

    <main class="main-content">

        <header class="topbar-panel">
            <div class="topbar-left">
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
                <span class="topbar-title">Gestión de Solicitudes Enviadas</span>
            </div>
            <div class="topbar-right">
             
            </div>
        </header>

        <div class="card-profile">
            <div class="user-profile-header">
                <div class="user-icon">👤</div>
                <div class="user-info">
                    <p>Nombre: <span class="nombre-highlight"><?php echo htmlspecialchars($usuario_sesion); ?></span></p>
                    <p>Rol: <strong><?php echo htmlspecialchars($rol_usuario); ?></strong></p>
                </div>
            </div>
        </div>

        <div class="stats-container">
            <a href="?ver_estado=En proceso" class="stat-card">
                <h3>PENDIENTES</h3>
                <div class="number color-pendiente"><?php echo $counts['pendientes'] ?? 0; ?></div>
            </a>

            <a href="?ver_estado=Rechazada" class="stat-card">
                <h3>RECHAZADAS</h3>
                <div class="number color-rechazada"><?php echo $counts['rechazadas'] ?? 0; ?></div>
            </a>

            <a href="?ver_estado=Aprobada" class="stat-card">
                <h3>APROBADAS</h3>
                <div class="number color-aprobada"><?php echo $counts['aprobadas'] ?? 0; ?></div>
            </a>
        </div>

        <?php if ($estado_filtro !== '' && $solicitudes_detalle): ?>
        <div class="tabla-container">
            <h2 class="tabla-title">Mis Solicitudes: <?php echo htmlspecialchars($estado_filtro); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($solicitudes_detalle->num_rows > 0): ?>
                    <?php while($row = $solicitudes_detalle->fetch_assoc()): ?>
                    <tr>
                        <td data-label="ID"><?php echo $row['id']; ?></td>
                        <td data-label="Concepto"><?php echo htmlspecialchars($row['concepto']); ?></td>
                        <td data-label="Monto">$<?php echo number_format($row['monto'], 2); ?></td>
                        <td data-label="Proveedor"><?php echo htmlspecialchars($row['proveedor']); ?></td>
                        <td data-label="Fecha"><?php echo $row['fecha']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">No hay solicitudes en este estado.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </main>
</div>

<script>
    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('active');
    }
</script>

</body>
</html>