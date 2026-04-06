 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NUEVA SOLICITUD</title>
    <link rel="stylesheet" href="../css/foralta.css">
</head>
<body>

    <div class="welcome-card">
        <span class="nombre-usuario">
            <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
        </span>
    </div>

    <section class="form-container">
        <h2 class="titulo-formulario">Nueva Solicitud de Pago</h2>
        
        <form action="../php/proceso.php" method="post">
            
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>

            <div class="form-group">
                <label for="concepto">Concepto del Pago:</label>
                <input type="text" id="concepto" name="concepto" placeholder="Ej: Pago de servicios" required>
            </div>

            <div class="form-row-double">
                <div class="inline-input">
                    <label for="monto">Monto:</label>
                    <input type="number" id="monto" name="monto" step="0.01" placeholder="0.00" required>
                </div>
                
                <div class="inline-input">
                    <label for="proveedor">Proveedor:</label>
                    <input type="text" id="proveedor" name="proveedor" placeholder="Nombre de la empresa" required>
                </div>
            </div>

            <div class="form-group-radio">
                <label>Método de Pago:</label>
                <div class="radio-options">
                    <div class="radio-item">
                        <input type="radio" id="transferencia" name="metodo_pago" value="transferencia" checked>
                        <label for="transferencia">Transferencia</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="efectivo" name="metodo_pago" value="efectivo">
                        <label for="efectivo">Efectivo</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="cheque" name="metodo_pago" value="cheque">
                        <label for="cheque">Cheque</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group-radio">
                <label>Prioridad:</label>
                <div class="radio-options">
                    <div class="radio-item">
                        <input type="radio" id="alta" name="prioridad" value="alta" checked>
                        <label for="alta">Alta</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="media" name="prioridad" value="media">
                        <label for="media">Media</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" id="baja" name="prioridad" value="baja">
                        <label for="baja">Baja</label>
                    </div>
                </div>
            </div>

            <div class="form-group vertical">
                <label for="observaciones">Observaciones:</label>
                <textarea id="observaciones" name="observaciones" rows="3" placeholder="Detalles adicionales..."></textarea>
            </div>

            <div class="button-container">
                <button type="submit">Enviar Solicitud</button>
            </div>
        </form>
        
        <a href="../vistas/solicitudes.php" class="logout-link">Regresar al Panel</a>
    </section>

</body>
</html>