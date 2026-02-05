<?php
require_once 'config.php';

// Datos de prueba
$presupuesto_id = 1; // Cambiar por un ID válido
$descripcion = 'Prueba de inserción automática';
$cantidad = 2;
$unidad = 'Unidad';
$precio_unitario = 100.00;
$descuento_porcentaje = 0;
$subtotal = 200.00;

// Obtener siguiente orden
$stmtOrden = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) + 1 as siguiente_orden FROM presupuesto_items WHERE presupuesto_id = ?');
$stmtOrden->execute([$presupuesto_id]);
$resultado = $stmtOrden->fetch();
$siguiente_orden = $resultado['siguiente_orden'] ?? 1;

echo "Orden: " . $siguiente_orden . "\n";

// Intentar insertar
$sql = 'INSERT INTO presupuesto_items (presupuesto_id, descripcion, cantidad, unidad, precio_unitario, descuento_porcentaje, subtotal, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
$stmt = $pdo->prepare($sql);

echo "SQL: " . $sql . "\n";
echo "Parámetros: \n";
echo "  presupuesto_id: " . $presupuesto_id . "\n";
echo "  descripcion: " . $descripcion . "\n";
echo "  cantidad: " . $cantidad . "\n";
echo "  unidad: " . $unidad . "\n";
echo "  precio_unitario: " . $precio_unitario . "\n";
echo "  descuento_porcentaje: " . $descuento_porcentaje . "\n";
echo "  subtotal: " . $subtotal . "\n";
echo "  orden: " . $siguiente_orden . "\n";

try {
    $result = $stmt->execute([
        $presupuesto_id,
        $descripcion,
        $cantidad,
        $unidad,
        $precio_unitario,
        $descuento_porcentaje,
        $subtotal,
        $siguiente_orden
    ]);
    
    if ($result) {
        echo "✓ Item insertado correctamente. ID: " . $pdo->lastInsertId() . "\n";
    } else {
        echo "✗ Error: " . implode(', ', $stmt->errorInfo()) . "\n";
    }
} catch (Exception $e) {
    echo "✗ Excepción: " . $e->getMessage() . "\n";
}

// Verificar que esté en la BD
$stmtCheck = $pdo->prepare('SELECT * FROM presupuesto_items WHERE presupuesto_id = ? ORDER BY id DESC LIMIT 1');
$stmtCheck->execute([$presupuesto_id]);
$item = $stmtCheck->fetch();

if ($item) {
    echo "\n✓ Item encontrado en BD:\n";
    echo json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "\n✗ No se encontró el item en BD\n";
}
?>
