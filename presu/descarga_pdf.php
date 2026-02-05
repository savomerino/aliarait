<?php
// Descargar presupuesto como PDF usando técnica de impresión
// Este archivo genera HTML optimizado para impresión a PDF

// Conexión a BD
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

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    die('ID requerido');
}

try {
    // Obtener presupuesto
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
    
    // Datos formateados
    $fecha_vigencia = date('d/m/Y', strtotime('+' . $presupuesto['vigencia_dias'] . ' days', strtotime($presupuesto['fecha_creacion'])));
    $fecha_creacion = date('d/m/Y', strtotime($presupuesto['fecha_creacion']));
    
    $tabla_items = '';
    $total_items = 0;
    $total_descuentos = 0;
    $tieneDescuentos = false;
    
    // Verificar descuentos
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
        $tabla_items .= '<td>' . htmlspecialchars($item['descripcion']) . '</td>';
        $tabla_items .= '<td style="text-align: center;">' . number_format($item['cantidad'], 2, '.', ',') . '</td>';
        $tabla_items .= '<td style="text-align: center;">' . htmlspecialchars($item['unidad']) . '</td>';
        $tabla_items .= '<td style="text-align: right;">$ ' . number_format($item['precio_unitario'], 2, '.', ',') . '</td>';
        $tabla_items .= '<td style="text-align: right;">$ ' . number_format($item['subtotal'], 2, '.', ',') . '</td>';
        
        if ($tieneDescuentos) {
            if ($item['descuento_porcentaje'] > 0) {
                $tabla_items .= '<td style="text-align: center;">' . $item['descuento_porcentaje'] . '%</td>';
            } else {
                $tabla_items .= '<td style="text-align: center;">-</td>';
            }
        }
        
        $tabla_items .= '<td style="text-align: right;">$ ' . number_format($subtotal_final, 2, '.', ',') . '</td>';
        $tabla_items .= '</tr>';
    }
    
    // Calcular totales
    $totalSinDescuento = 0;
    foreach ($items as $item) {
        $totalSinDescuento += $item['subtotal'];
    }
    $porcentajeDescuentoTotal = $totalSinDescuento > 0 ? ($total_descuentos / $totalSinDescuento) * 100 : 0;
    $colspan = $tieneDescuentos ? 6 : 5;
    
    // Datos escapados
    $numero = htmlspecialchars($presupuesto['numero']);
    $cliente = htmlspecialchars($presupuesto['cliente_nombre']);
    $empresa = htmlspecialchars($presupuesto['cliente_empresa'] ?? '');
    $email = htmlspecialchars($presupuesto['cliente_email'] ?? '');
    $telefono = htmlspecialchars($presupuesto['cliente_telefono'] ?? '');
    $moneda = htmlspecialchars($presupuesto['moneda']);
    $estado = htmlspecialchars($presupuesto['estado']);
    
    // Generar HTML para impresión
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Presupuesto ' . $numero . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { 
            width: 100%;
            height: 100%;
            background: white;
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            background: white; 
            color: #000;
            font-size: 12px;
            line-height: 1.5;
            padding: 0;
            margin: 0;
        }
        @page {
            margin: 15mm;
            size: A4;
        }
        @media print {
            body { 
                margin: 0; 
                padding: 15mm;
                background: white !important;
                color: #000 !important;
            }
            .no-print { display: none !important; }
            * { 
                color: #000 !important;
                background-color: transparent !important;
                background-image: none !important;
            }
            .header h1 { color: #e7a042 !important; }
            .header p { color: #333 !important; }
            .info-box { background: #f5f5f5 !important; color: #000 !important; }
            .info-box strong { color: #333 !important; }
            .info-box span { color: #000 !important; }
            .section-box { background: #f5f5f5 !important; color: #000 !important; }
            .section-box strong { color: #333 !important; }
            table { border-collapse: collapse; background: white !important; }
            thead { background: #e7a042 !important; color: white !important; }
            thead th { color: white !important; background: #e7a042 !important; }
            tbody tr { background: white !important; }
            tbody td { color: #000 !important; background: white !important; }
            .total-row { background: #e7a042 !important; color: white !important; }
            .total-row td { color: white !important; background: #e7a042 !important; }
        }
        .contenedor { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
            background: white;
            color: #000;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 3px solid #e7a042; 
            padding-bottom: 15px;
            page-break-after: avoid;
        }
        .header h1 { 
            color: #e7a042; 
            font-size: 28px; 
            margin-bottom: 5px; 
            font-weight: bold;
        }
        .header p { 
            color: #333; 
            font-size: 11px; 
            margin: 0;
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 15px; 
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .info-box { 
            background: #f5f5f5; 
            padding: 10px; 
            border-left: 3px solid #e7a042;
            border-radius: 2px;
            color: #000;
        }
        .info-box strong { 
            color: #333; 
            display: block; 
            margin-bottom: 3px; 
            font-weight: bold;
        }
        .info-box span { 
            display: block; 
            color: #000; 
            font-weight: normal;
        }
        .cliente-box { 
            background: #f5f5f5; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-left: 3px solid #e7a042;
            border-radius: 2px;
            page-break-inside: avoid;
            color: #000;
        }
        .cliente-box strong { 
            color: #333; 
            display: block; 
            margin-bottom: 8px; 
            font-size: 12px; 
            font-weight: bold;
        }
        .cliente-row { 
            margin-bottom: 5px; 
            color: #000; 
            font-weight: normal;
        }
        .cliente-row strong { 
            color: #333; 
            display: inline; 
            font-weight: bold;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        table thead { 
            background: #e7a042; 
            color: #fff;
        }
        table th { 
            padding: 10px; 
            text-align: left; 
            font-weight: bold; 
            border: 1px solid #999;
            color: #fff;
            background: #e7a042;
        }
        table td { 
            padding: 8px; 
            border: 1px solid #999; 
            background: white; 
            color: #000;
        }
        table tbody tr:nth-child(even) { 
            background: #fafafa;
        }
        table tbody tr:nth-child(odd) { 
            background: white;
        }
        .total-row { 
            background: #e7a042 !important; 
            color: #fff !important; 
            font-weight: bold;
            page-break-inside: avoid;
        }
        .total-row td { 
            border: 1px solid #d4860f;
            color: #fff !important;
            background: #e7a042 !important;
        }
        .section-box { 
            background: #f5f5f5; 
            padding: 12px; 
            margin-bottom: 15px; 
            border-left: 3px solid #e7a042;
            border-radius: 2px;
            page-break-inside: avoid;
            color: #000;
        }
        .section-box strong { 
            color: #333; 
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold;
        }
        .section-box div { 
            color: #000; 
            white-space: pre-wrap; 
            font-size: 11px; 
            line-height: 1.4;
        }
        .footer { 
            text-align: center; 
            font-size: 10px; 
            color: #666; 
            margin-top: 25px; 
            padding-top: 15px; 
            border-top: 1px solid #999;
            page-break-inside: avoid;
        }
        .footer p { 
            margin: 3px 0; 
            color: #000;
        }
        .btn-container { 
            text-align: center; 
            margin: 20px 0; 
            padding: 20px; 
            border-top: 1px solid #ddd; 
            background: white;
        }
        .btn { 
            padding: 10px 20px; 
            background: #e7a042; 
            color: white; 
            border: none; 
            border-radius: 3px; 
            cursor: pointer; 
            font-size: 14px; 
            margin: 0 5px; 
            font-weight: bold;
        }
        .btn:hover { 
            background: #d4860f;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="header">
            <h1>PRESUPUESTO</h1>
            <p>Aliara IT - Soluciones en Redes, Servidores y Soporte</p>
        </div>
        
        <div class="info-grid">
            <div class="info-box">
                <strong>Número:</strong><span>' . $numero . '</span>
                <strong>Fecha:</strong><span>' . $fecha_creacion . '</span>
                <strong>Vigencia:</strong><span>' . $fecha_vigencia . '</span>
            </div>
            <div class="info-box">
                <strong>Moneda:</strong><span>' . $moneda . '</span>
                <strong>Estado:</strong><span style="background: #e7a042; color: white; padding: 2px 5px; border-radius: 2px; display: inline-block;">' . strtoupper($estado) . '</span>
            </div>
        </div>
        
        <div class="cliente-box">
            <strong>INFORMACIÓN DEL CLIENTE</strong>
            <div class="cliente-row"><strong style="color: #333; display: inline;">Nombre:</strong> ' . $cliente . '</div>';
    
    if (!empty($empresa)) {
        $html .= '<div class="cliente-row"><strong style="color: #333; display: inline;">Empresa:</strong> ' . $empresa . '</div>';
    }
    if (!empty($email)) {
        $html .= '<div class="cliente-row"><strong style="color: #333; display: inline;">Email:</strong> ' . $email . '</div>';
    }
    if (!empty($telefono)) {
        $html .= '<div class="cliente-row"><strong style="color: #333; display: inline;">Teléfono:</strong> ' . $telefono . '</div>';
    }
    
    $html .= '</div>';
    
    // Tabla de items
    $encabezado = '<th style="width: 35%;">Descripción</th>
                    <th style="width: 8%;">Cantidad</th>
                    <th style="width: 8%;">Unidad</th>
                    <th style="width: 15%;">P. Unitario</th>
                    <th style="width: 15%;">Subtotal</th>';
    
    if ($tieneDescuentos) {
        $encabezado .= '<th style="width: 8%;">Desc. %</th>';
    }
    
    $encabezado .= '<th style="width: 11%;">Total</th>';
    
    $html .= '<table>
        <thead>
            <tr>' . $encabezado . '</tr>
        </thead>
        <tbody>
            ' . $tabla_items;
    
    $html .= '<tr class="total-row">
                <td colspan="' . $colspan . '" style="text-align: right;">SUBTOTAL:</td>
                <td style="text-align: right;">$ ' . number_format($totalSinDescuento, 2, '.', ',') . '</td>
            </tr>';
    
    if ($total_descuentos > 0) {
        $html .= '<tr class="total-row">
                    <td colspan="' . $colspan . '" style="text-align: right;">DESCUENTO TOTAL (' . number_format($porcentajeDescuentoTotal, 1) . '%):</td>
                    <td style="text-align: right;">-$ ' . number_format($total_descuentos, 2, '.', ',') . '</td>
                </tr>';
    }
    
    $html .= '<tr class="total-row">
                <td colspan="' . $colspan . '" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">$ ' . number_format($total_items, 2, '.', ',') . ' ' . $moneda . '</td>
            </tr>
        </tbody>
    </table>';
    
    if (!empty($presupuesto['condiciones_pago'])) {
        $html .= '<div class="section-box"><strong>CONDICIONES DE PAGO</strong><div>' . htmlspecialchars($presupuesto['condiciones_pago']) . '</div></div>';
    }
    
    if (!empty($presupuesto['notas_internas'])) {
        $html .= '<div class="section-box"><strong>NOTAS INTERNAS</strong><div>' . htmlspecialchars($presupuesto['notas_internas']) . '</div></div>';
    }
    
    $html .= '<div class="footer">
        <p>Este presupuesto es válido por ' . $presupuesto['vigencia_dias'] . ' días a partir de la fecha de emisión.</p>
        <p>Aliara IT - Soluciones en Tecnología</p>
        <p>Generado: ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <div class="btn-container no-print" style="margin-top: 30px; padding: 20px; border-top: 1px solid #ddd;">
        <button class="btn" onclick="window.print()">Descargar como PDF</button>
        <button class="btn" style="background: #666;" onclick="window.close()">Cerrar</button>
    </div>
    </div>
    
    <script>
        // Auto-disparar print dialog si viene con parámetro ?autoprint=1
        window.addEventListener('load', function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>';
    
    // Enviar HTML
    header('Content-Type: text/html; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo $html;
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . htmlspecialchars($e->getMessage());
    die();
}
?>
