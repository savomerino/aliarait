<?php
// Conexión directa a la BD sin headers JSON para evitar conflictos
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=u592897176_presu;charset=utf8mb4',
        'u592897176_presu',
        '^8S>E#x1gG',
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Error de conexión: ' . htmlspecialchars($e->getMessage());
    die();
}

date_default_timezone_set('America/La_Paz');

// Verificar que se especifique un ID
$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    die('ID de presupuesto requerido');
}

// Obtener presupuesto
try {
    $stmt = $pdo->prepare('SELECT * FROM presupuestos WHERE id = ?');
    $stmt->execute([$id]);
    $presupuesto = $stmt->fetch();

    if (!$presupuesto) {
        http_response_code(404);
        die('Presupuesto no encontrado');
    }

    // Obtener items
    $stmt = $pdo->prepare('SELECT * FROM presupuesto_items WHERE presupuesto_id = ? ORDER BY orden');
    $stmt->execute([$id]);
    $items = $stmt->fetchAll();

    // Crear HTML del PDF
    $html = generarHTML($presupuesto, $items);

    // Retornar HTML limpio que será convertido a PDF en el navegador
    header('Content-Type: text/html; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo $html;
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . htmlspecialchars($e->getMessage());
    die();
}

function generarHTML($presupuesto, $items) {
    // Calcular fechas
    $fecha_vigencia = date('d/m/Y', strtotime('+' . $presupuesto['vigencia_dias'] . ' days', strtotime($presupuesto['fecha_creacion'])));
    $fecha_creacion = date('d/m/Y', strtotime($presupuesto['fecha_creacion']));
    
    // Variables de cálculo
    $tabla_items = '';
    $total_items = 0;
    $total_descuentos = 0;
    $tieneDescuentos = false;
    
    // Verificar si hay descuentos
    foreach ($items as $item) {
        if ($item['descuento_porcentaje'] > 0) {
            $tieneDescuentos = true;
            break;
        }
    }
    
    // Generar filas de tabla
    foreach ($items as $item) {
        $descuento = ($item['subtotal'] * $item['descuento_porcentaje']) / 100;
        $subtotal_final = $item['subtotal'] - $descuento;
        $total_items += $subtotal_final;
        $total_descuentos += $descuento;
        
        $tabla_items .= '<tr>';
        $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; color: #f5f5f5;">' . htmlspecialchars($item['descripcion']) . '</td>';
        $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; text-align: center; color: #f5f5f5;">' . number_format($item['cantidad'], 2, '.', ',') . '</td>';
        $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; text-align: center; color: #f5f5f5;">' . htmlspecialchars($item['unidad']) . '</td>';
        $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; text-align: right; color: #f5f5f5;">$ ' . number_format($item['precio_unitario'], 2, '.', ',') . '</td>';
        $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; text-align: right; color: #f5f5f5;">$ ' . number_format($item['subtotal'], 2, '.', ',') . '</td>';
        
        if ($tieneDescuentos) {
            if ($item['descuento_porcentaje'] > 0) {
                $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; text-align: center; color: #ffa500;">' . $item['descuento_porcentaje'] . '%</td>';
            } else {
                $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; text-align: center; color: #999;">-</td>';
            }
        }
        
        $tabla_items .= '<td style="border: 1px solid #555; padding: 10px; text-align: right; color: #f5f5f5; font-weight: bold;">$ ' . number_format($subtotal_final, 2, '.', ',') . '</td>';
        $tabla_items .= '</tr>';
    }
    
    // Calcular porcentaje total de descuento
    $totalSinDescuento = 0;
    foreach ($items as $item) {
        $totalSinDescuento += $item['subtotal'];
    }
    $porcentajeDescuentoTotal = $totalSinDescuento > 0 ? ($total_descuentos / $totalSinDescuento) * 100 : 0;
    
    // Escapar variables
    $numero = htmlspecialchars($presupuesto['numero']);
    $cliente = htmlspecialchars($presupuesto['cliente_nombre']);
    $empresa = htmlspecialchars($presupuesto['cliente_empresa'] ?? '');
    $email = htmlspecialchars($presupuesto['cliente_email'] ?? '');
    $telefono = htmlspecialchars($presupuesto['cliente_telefono'] ?? '');
    $moneda = htmlspecialchars($presupuesto['moneda']);
    $estado = htmlspecialchars($presupuesto['estado']);
    $descripcion = nl2br(htmlspecialchars($presupuesto['descripcion'] ?? ''));
    $condiciones = nl2br(htmlspecialchars($presupuesto['condiciones_pago'] ?? ''));
    $notas = nl2br(htmlspecialchars($presupuesto['notas_internas'] ?? ''));
    
    // Formatear números
    $subtotal = number_format($totalSinDescuento, 2, '.', ',');
    $desc_total = number_format($total_descuentos, 2, '.', ',');
    $pct_desc = number_format($porcentajeDescuentoTotal, 1);
    $total = number_format($total_items, 2, '.', ',');
    $vigencia = htmlspecialchars($presupuesto['vigencia_dias']);
    $colspan = $tieneDescuentos ? 5 : 4;
    
    $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; }
        body { font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; background-color: #1d1d1b; color: #f5f5f5; line-height: 1.6; font-size: 11px; }
        .contenedor { background-color: #1d1d1b; color: #f5f5f5; padding: 20px; width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #e7a042; page-break-after: avoid; }
        .header-logo { flex: 0 0 auto; margin-right: 20px; }
        .header-logo img { max-height: 70px; width: auto; display: block; }
        .header-titulo { flex: 1; text-align: center; }
        .header-titulo h1 { color: #e7a042; font-size: 32px; margin-bottom: 5px; font-weight: 700; }
        .header-titulo p { color: #c0c0c0; font-size: 12px; margin: 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; font-size: 11px; page-break-inside: avoid; }
        .info-item { background-color: #252523; padding: 12px; border-left: 4px solid #e7a042; border-radius: 3px; color: #f5f5f5; }
        .info-item strong { color: #e7a042; display: block; margin: 5px 0 3px 0; font-weight: 600; }
        .cliente-box { background-color: #252523; padding: 15px; margin-bottom: 25px; border-left: 4px solid #e7a042; border-radius: 3px; font-size: 11px; color: #f5f5f5; page-break-inside: avoid; }
        .cliente-box strong { color: #e7a042; display: block; margin-top: 8px; margin-bottom: 3px; font-weight: 600; }
        .cliente-row { margin-bottom: 8px; color: #f5f5f5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 11px; background-color: #1d1d1b; }
        table thead { background-color: #e7a042; color: #1d1d1b; }
        table th { padding: 12px 10px; text-align: left; font-weight: 700; border: 1px solid #d4860f; }
        table tbody tr { border-bottom: 1px solid #555; background-color: #1d1d1b; page-break-inside: avoid; }
        table tbody tr:nth-child(even) { background-color: #2a2a28; }
        table td { padding: 10px; color: #f5f5f5; border: 1px solid #555; }
        .total-row { background-color: #e7a042; color: #1d1d1b; font-weight: 700; }
        .total-row td { padding: 12px 10px; border: 2px solid #d4860f; }
        .section-box { background-color: #252523; padding: 15px; margin-bottom: 20px; border-left: 4px solid #e7a042; border-radius: 3px; font-size: 10px; line-height: 1.7; color: #f5f5f5; page-break-inside: avoid; }
        .section-box strong { color: #e7a042; display: block; margin-bottom: 8px; font-weight: 600; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #555; font-size: 10px; color: #c0c0c0; page-break-inside: avoid; }
        .footer p { margin: 5px 0; color: #c0c0c0; }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="header">
            <div class="header-logo">
                <img src="../assets/img/web_logo.webp" alt="Aliara IT" />
            </div>
            <div class="header-titulo">
                <h1>PRESUPUESTO</h1>
                <p>Aliara IT - Soluciones en Redes, Servidores y Soporte</p>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <strong>Número:</strong>' . $numero . '
                <strong>Fecha:</strong>' . $fecha_creacion . '
                <strong>Vigencia hasta:</strong>' . $fecha_vigencia . '
            </div>
            <div class="info-item">
                <strong>Moneda:</strong>' . $moneda . '
                <strong>Estado:</strong>
                <span style="background-color: #e7a042; color: #1d1d1b; padding: 2px 8px; border-radius: 3px; font-weight: 600;">' . strtoupper($estado) . '</span>
            </div>
        </div>
        
        <div class="cliente-box">
            <strong>INFORMACIÓN DEL CLIENTE</strong>
            <div class="cliente-row"><strong>Nombre:</strong>' . $cliente . '</div>';
    
    if (!empty($empresa)) {
        $html .= '<div class="cliente-row"><strong>Empresa:</strong>' . $empresa . '</div>';
    }
    if (!empty($email)) {
        $html .= '<div class="cliente-row"><strong>Email:</strong>' . $email . '</div>';
    }
    if (!empty($telefono)) {
        $html .= '<div class="cliente-row"><strong>Teléfono:</strong>' . $telefono . '</div>';
    }
    
    $html .= '        </div>';
    
    if (!empty($descripcion)) {
        $html .= '<div class="section-box"><strong>DESCRIPCIÓN GENERAL</strong>' . $descripcion . '</div>';
    }
    
    $encabezado = '<th style="width: 40%;">Descripción</th>
                        <th style="width: 8%;">Cantidad</th>
                        <th style="width: 8%;">Unidad</th>
                        <th style="width: 12%;">P. Unitario</th>
                        <th style="width: 12%;">Subtotal</th>';
    
    if ($tieneDescuentos) {
        $encabezado .= '<th style="width: 8%;">Desc. %</th>';
    }
    
    $encabezado .= '<th style="width: ' . ($tieneDescuentos ? '12' : '20') . '%;">Total</th>';
    
    $html .= '<table>
            <thead>
                <tr>' . $encabezado . '</tr>
            </thead>
            <tbody>
                ' . $tabla_items;
    
    $html .= '<tr class="total-row">
                        <td colspan="' . $colspan . '" style="text-align: right;">SUBTOTAL:</td>';
    
    if ($tieneDescuentos) {
        $html .= '<td style="text-align: center;">-</td>';
    }
    
    $html .= '<td style="text-align: right;">$ ' . $subtotal . '</td>
                    </tr>';
    
    if ($total_descuentos > 0) {
        $html .= '<tr class="total-row">
                        <td colspan="' . $colspan . '" style="text-align: right;">DESCUENTO TOTAL (' . $pct_desc . '%):</td>';
        
        if ($tieneDescuentos) {
            $html .= '<td style="text-align: center;">-</td>';
        }
        
        $html .= '<td style="text-align: right;">-$ ' . $desc_total . '</td>
                    </tr>';
    }
    
    $html .= '<tr class="total-row">
                        <td colspan="' . $colspan . '" style="text-align: right; font-size: 13px;"><strong>TOTAL:</strong></td>';
    
    if ($tieneDescuentos) {
        $html .= '<td style="text-align: center;">-</td>';
    }
    
    $html .= '<td style="text-align: right; font-size: 13px;"><strong>$ ' . $total . ' ' . $moneda . '</strong></td>
                    </tr>
            </tbody>
        </table>';
    
    if (!empty($condiciones)) {
        $html .= '<div class="section-box"><strong>CONDICIONES DE PAGO</strong>' . $condiciones . '</div>';
    }
    
    if (!empty($notas)) {
        $html .= '<div class="section-box"><strong>NOTAS INTERNAS</strong>' . $notas . '</div>';
    }
    
    $html .= '<div class="footer">
                <p>Este presupuesto es válido por ' . $vigencia . ' días a partir de la fecha de emisión.</p>
                <p><strong>Aliara IT</strong> - Soluciones en Tecnología</p>
                <p>Generado el ' . date('d/m/Y H:i:s') . '</p>
            </div>
        </div>
    </body>
</html>';
    
    return $html;
}
?>
