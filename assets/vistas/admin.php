 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
<?php 
session_start();

//  Validación de sesión
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php"); 
    exit();
}

// 2. Incluir conexión
include("../php/conexion.php"); 

//  Consulta SQL (Ya no necesitamos traer el password para la tabla)
$sql = "SELECT id, nombre, correo, rol FROM usuarios";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Gestión de Usuarios</title>
    
    <link rel="stylesheet" href="../css/adminvista.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>ADMINISTRADOR</h2>
            <div class="close-menu" onclick="toggleMenu()">×</div>
        </div>
        <nav>
            <a href="admin.php" class="active">GESTOR DE USUARIOS</a>
            <a href="adminsoli.php">SOLICITUDES</a>
            <a href="../formularios/solicitudadmin.php">NUEVA SOLICITUD</a>
            <a href="../vistas/pago_admin.php">COMPROBANTES</a>
        </nav>
    </aside>

    <div class="main">
        
        <div class="topbar">
            <div class="topbar-left">
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
                <div class="topbar-title">Gestión de Usuarios</div>
            </div>
            <a href="/proyecto/logout.php" class="logout-btn">Cerrar Sesión</a>
        </div>

        <div class="card">
            <div class="profile">
                <img src="https://cdn-icons-png.flaticon.com/512/5989/5989226.png" alt="usuario">
                <div>
                    <p>Nombre: <span class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Admin'); ?></span></p>
                    <p>Rol: <strong><?php echo htmlspecialchars($_SESSION['rol'] ?? 'Administrador'); ?></strong></p>
                </div>
            </div>
        </div>

        <div class="search">
            <input type="text" id="searchInput" placeholder="Buscar por nombre o correo...">
            <button class="btn-new" onclick="window.location.href='usuario.php'">+ Nuevo Usuario</button>
        </div>

        <div class="table-container">
            <h3>Usuarios Registrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                <?php
                if ($resultado && $resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        ?>
                        <tr>
                            <td data-label="Nombre"><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td data-label="Correo"><?php echo htmlspecialchars($row['correo']); ?></td>
                            <td data-label="Rol"><?php echo htmlspecialchars($row['rol']); ?></td>
                            <td data-label="Acciones" class="actions-cell">
                                <a href="../formularios/editarusu.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-edit">Actualizar</a>
                                
                                <button class="btn btn-delete" 
                                        onclick="confirmarBaja(<?php echo $row['id']; ?>, '<?php echo $row['nombre']; ?>')">
                                    Baja
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='4' class='no-data'>No se encontraron usuarios.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Buscador en tiempo real
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#userTableBody tr");
            
            rows.forEach(row => {
                if(row.cells.length > 1) {
                    let nombre = row.cells[0].textContent.toLowerCase();
                    let correo = row.cells[1].textContent.toLowerCase();
                    row.style.display = (nombre.includes(filter) || correo.includes(filter)) ? "" : "none";
                }
            });
        });

        // Alerta de confirmación
        function confirmarBaja(id, nombre) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                text: "Estás por dar de baja a: " + nombre,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#3db3c7',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../php/eliminarusu.php?id=' + id;
                }
            })
        }
    </script>
</body>
</html>