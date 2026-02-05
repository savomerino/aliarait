<?php
/**
 * ARCHIVO DE EJEMPLO - Descomentar y ejecutar si es necesario
 * Este archivo puede usarse para:
 * - Verificar conexión a BD
 * - Limpiar presupuestos de prueba
 * - Generar datos de ejemplo
 */

// DESCOMENTA SOLO SI NECESITAS VERIFICAR LA CONEXIÓN
// require_once 'config.php';
// echo "Conexión exitosa a: " . DB_NAME;
// die();

// LIMPIADOR DE DATOS DE PRUEBA
// Descomentar solo si necesitas ELIMINAR todos los presupuestos
/*
require_once 'config.php';

$pdo->query('DELETE FROM presupuesto_auditoria');
$pdo->query('DELETE FROM presupuesto_items');
$pdo->query('DELETE FROM presupuestos');

echo "Base de datos limpiada.";
*/

// GENERAR PRESUPUESTOS DE PRUEBA
// Descomentar para crear algunos presupuestos de ejemplo
/*
require_once 'config.php';

// Presupuesto 1
$stmt = $pdo->prepare('
    INSERT INTO presupuestos 
    (numero, cliente_nombre, cliente_email, cliente_empresa, estado, total, moneda, descripcion_general)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
');

$stmt->execute([
    'PRES-202602-0001',
    'Juan Pérez',
    'juan@ejemplo.com',
    'Empresa XYZ',
    'borrador',
    1500.00,
    'USD',
    'Instalación de red Ubiquiti con cableado estructurado'
]);

$pres_id = $pdo->lastInsertId();

// Agregar items
$stmt = $pdo->prepare('
    INSERT INTO presupuesto_items 
    (presupuesto_id, descripcion, cantidad, unidad, precio_unitario, subtotal)
    VALUES (?, ?, ?, ?, ?, ?)
');

$stmt->execute([$pres_id, 'Switch Ubiquiti 24 puertos', 1, 'Unidad', 500, 500]);
$stmt->execute([$pres_id, 'Cableado Cat6 (100m)', 1, 'Lote', 300, 300]);
$stmt->execute([$pres_id, 'Instalación y configuración', 8, 'Horas', 150, 1200]);

echo "Presupuestos de prueba creados.";
*/

// EXPORTAR PRESUPUESTOS
// Descomentar para exportar presupuestos en JSON
/*
require_once 'config.php';

$stmt = $pdo->prepare('SELECT * FROM presupuestos WHERE estado = ?');
$stmt->execute(['aprobado']);
$presupuestos = $stmt->fetchAll();

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="presupuestos_aprobados.json"');
echo json_encode($presupuestos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
*/

echo "Archivo de utilidades. Ver comentarios en el código.";
?>
