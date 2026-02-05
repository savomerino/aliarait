<?php
/**
 * GESTOR DE DATOS DE PRUEBA Y EJEMPLOS
 * 
 * Este archivo puede usarse para:
 * 1. Crear presupuestos de ejemplo
 * 2. Verificar funcionamiento de la BD
 * 3. Importar/Exportar datos
 * 4. Testing
 */

// Descomentar para usar

// OPCIÓN 1: Crear presupuestos de ejemplo
/*
require_once 'config.php';

// Ejemplo 1: Instalación de red
$stmt = $pdo->prepare('
    INSERT INTO presupuestos 
    (numero, cliente_nombre, cliente_email, cliente_empresa, estado, total, moneda, descripcion_general)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
');

$stmt->execute([
    'PRES-202602-0001',
    'Carlos López',
    'carlos@example.com',
    'TechCorp Bolivia',
    'borrador',
    2500.00,
    'USD',
    'Instalación y configuración de red Ubiquiti con cableado estructurado CAT6A'
]);

$pres_id = $pdo->lastInsertId();

// Agregar items
$stmt = $pdo->prepare('
    INSERT INTO presupuesto_items 
    (presupuesto_id, descripcion, cantidad, unidad, precio_unitario, subtotal, orden)
    VALUES (?, ?, ?, ?, ?, ?, ?)
');

$stmt->execute([$pres_id, 'Switch Ubiquiti EdgeSwitch 24-Port', 1, 'Unidad', 650, 650, 1]);
$stmt->execute([$pres_id, 'Access Point Ubiquiti UAP-AC-PRO', 3, 'Unidades', 280, 840, 2]);
$stmt->execute([$pres_id, 'Cableado CAT6A (500 metros)', 1, 'Lote', 400, 400, 3]);
$stmt->execute([$pres_id, 'Instalación y configuración (16 horas)', 16, 'Horas', 75, 1200, 4]);

echo "Presupuesto 1 creado: $pres_id<br>";

// Ejemplo 2: Servicio de soporte
$stmt->execute([
    'PRES-202602-0002',
    'María González',
    'maria@example.com',
    'Servicios Integrales SA',
    'borrador',
    3000.00,
    'USD',
    'Contrato de soporte técnico anual con monitoreo 24/7'
]);

$pres_id = $pdo->lastInsertId();

$stmt->execute([$pres_id, 'Monitoreo 24/7 de servidores', 1, 'Anual', 2000, 2000, 1]);
$stmt->execute([$pres_id, 'Soporte técnico on-site (8 horas/mes)', 1, 'Anual', 1000, 1000, 2]);

echo "Presupuesto 2 creado: $pres_id<br>";

// Ejemplo 3: Implementación de seguridad
$stmt->execute([
    'PRES-202602-0003',
    'Roberto Fernández',
    'roberto@example.com',
    'Industrias Bolivianas Ltd',
    'borrador',
    5000.00,
    'USD',
    'Implementación de sistema de seguridad informática con firewall y filtrado'
]);

$pres_id = $pdo->lastInsertId();

$stmt->execute([$pres_id, 'Firewall Fortinet FortiGate 200F', 1, 'Unidad', 1500, 1500, 1]);
$stmt->execute([$pres_id, 'Licencias de seguridad (1 año)', 1, 'Lote', 1500, 1500, 2]);
$stmt->execute([$pres_id, 'Análisis de seguridad y penetration testing', 1, 'Servicio', 1000, 1000, 3]);
$stmt->execute([$pres_id, 'Implementación y configuración (12 horas)', 12, 'Horas', 75, 900, 4]);

echo "Presupuesto 3 creado: $pres_id<br>";

echo "✓ Presupuestos de ejemplo creados correctamente.<br>";
echo "Accede a la aplicación en: http://localhost/WEB/presu/";

die();
*/

// OPCIÓN 2: Verificar conexión a BD
/*
require_once 'config.php';

try {
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM presupuestos');
    $result = $stmt->fetch();
    echo "✓ Conexión exitosa<br>";
    echo "Presupuestos en BD: " . $result['count'] . "<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}

die();
*/

// OPCIÓN 3: Exportar presupuestos a JSON
/*
require_once 'config.php';

$stmt = $pdo->prepare('SELECT * FROM presupuestos WHERE estado = ?');
$stmt->execute(['borrador']);
$presupuestos = $stmt->fetchAll();

foreach ($presupuestos as &$p) {
    $stmt = $pdo->prepare('SELECT * FROM presupuesto_items WHERE presupuesto_id = ? ORDER BY orden');
    $stmt->execute([$p['id']]);
    $p['items'] = $stmt->fetchAll();
}

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="presupuestos_' . date('Y-m-d') . '.json"');
echo json_encode($presupuestos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

die();
*/

// OPCIÓN 4: Limpiar BD (CUIDADO: Esto borra todos los datos)
/*
require_once 'config.php';

if ($_GET['confirm'] === 'yes') {
    $pdo->query('DELETE FROM presupuesto_auditoria');
    $pdo->query('DELETE FROM presupuesto_items');
    $pdo->query('DELETE FROM presupuestos');
    echo "✓ Base de datos limpiada.";
} else {
    echo "⚠ Esto borrará TODOS los presupuestos. <a href='?confirm=yes'>Confirmar</a>";
}

die();
*/

echo "Archivo de utilidades para datos. Descomentar la opción deseada.";
?>
