<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        // Obtener todos los presupuestos
        case 'lista':
            listarPresupuestos();
            break;
        
        // Obtener un presupuesto específico
        case 'obtener':
            obtenerPresupuesto();
            break;
        
        // Crear nuevo presupuesto
        case 'crear':
            crearPresupuesto();
            break;
        
        // Actualizar presupuesto
        case 'actualizar':
            actualizarPresupuesto();
            break;
        
        // Eliminar presupuesto
        case 'eliminar':
            eliminarPresupuesto();
            break;
        
        // Guardar o actualizar item
        case 'guardar_item':
            guardarItem();
            break;
        
        // Eliminar item
        case 'eliminar_item':
            eliminarItem();
            break;
        
        // Generar número de presupuesto
        case 'generar_numero':
            generarNumero();
            break;
        
        // Cambiar estado
        case 'cambiar_estado':
            cambiarEstado();
            break;
        
        // Calcular totales
        case 'calcular_totales':
            calcularTotales();
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// ==================== FUNCIONES ====================

function listarPresupuestos() {
    global $pdo;
    
    $estado = $_GET['estado'] ?? null;
    $buscar = $_GET['buscar'] ?? null;
    
    $sql = 'SELECT * FROM presupuestos WHERE 1=1';
    $params = [];
    
    if ($estado) {
        $sql .= ' AND estado = ?';
        $params[] = $estado;
    }
    
    if ($buscar) {
        $sql .= ' AND (numero LIKE ? OR cliente_nombre LIKE ? OR cliente_email LIKE ?)';
        $searchTerm = '%' . $buscar . '%';
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    $sql .= ' ORDER BY fecha_creacion DESC';
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode($stmt->fetchAll());
}

function obtenerPresupuesto() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new Exception('ID requerido');
    }
    
    $stmt = $pdo->prepare('SELECT * FROM presupuestos WHERE id = ?');
    $stmt->execute([$id]);
    $presupuesto = $stmt->fetch();
    
    if (!$presupuesto) {
        http_response_code(404);
        throw new Exception('Presupuesto no encontrado');
    }
    
    // Obtener items
    $stmt = $pdo->prepare('SELECT * FROM presupuesto_items WHERE presupuesto_id = ? ORDER BY orden');
    $stmt->execute([$id]);
    $presupuesto['items'] = $stmt->fetchAll();
    
    echo json_encode($presupuesto);
}

function crearPresupuesto() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $numero = generarNumeroPresupuesto();
    
    $stmt = $pdo->prepare('
        INSERT INTO presupuestos 
        (numero, cliente_nombre, cliente_email, cliente_telefono, cliente_empresa, estado)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->execute([
        $numero,
        $data['cliente_nombre'] ?? '',
        $data['cliente_email'] ?? '',
        $data['cliente_telefono'] ?? '',
        $data['cliente_empresa'] ?? '',
        'borrador'
    ]);
    
    $id = $pdo->lastInsertId();
    
    registrarAuditoria($id, 'crear', 'Presupuesto creado');
    
    echo json_encode([
        'success' => true,
        'id' => $id,
        'numero' => $numero
    ]);
}

function actualizarPresupuesto() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    
    if (!$id) {
        throw new Exception('ID requerido');
    }
    
    $sql = 'UPDATE presupuestos SET ';
    $updates = [];
    $params = [];
    
    $campos = ['cliente_nombre', 'cliente_email', 'cliente_telefono', 'cliente_empresa', 
               'descripcion_general', 'total', 'moneda', 'condiciones_pago', 'vigencia_dias', 'notas_internas'];
    
    foreach ($campos as $campo) {
        if (isset($data[$campo])) {
            $updates[] = "$campo = ?";
            $params[] = $data[$campo];
        }
    }
    
    if (empty($updates)) {
        echo json_encode(['success' => true]);
        return;
    }
    
    $updates[] = 'fecha_actualizacion = NOW()';
    $sql .= implode(', ', $updates) . ' WHERE id = ?';
    $params[] = $id;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    registrarAuditoria($id, 'actualizar', 'Presupuesto actualizado');
    
    echo json_encode(['success' => true]);
}

function eliminarPresupuesto() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new Exception('ID requerido');
    }
    
    $stmt = $pdo->prepare('DELETE FROM presupuestos WHERE id = ?');
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true]);
}

function guardarItem() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $presupuesto_id = intval($data['presupuesto_id'] ?? 0);
    
    if (!$presupuesto_id) {
        throw new Exception('ID de presupuesto requerido');
    }
    
    // No guardar items vacíos
    $descripcion = trim($data['descripcion'] ?? '');
    if (empty($descripcion)) {
        throw new Exception('Descripción requerida');
    }
    
    $cantidad = floatval($data['cantidad'] ?? 0);
    $precio_unitario = floatval($data['precio_unitario'] ?? 0);
    $subtotal = floatval($data['subtotal'] ?? 0);
    $unidad = trim($data['unidad'] ?? 'Unidad');
    $descuento_porcentaje = floatval($data['descuento_porcentaje'] ?? 0);
    
    $item_id = intval($data['id'] ?? 0);
    
    if ($item_id > 0) {
        // Actualizar item existente
        $sql = 'UPDATE presupuesto_items SET descripcion = ?, cantidad = ?, unidad = ?, precio_unitario = ?, descuento_porcentaje = ?, subtotal = ? WHERE id = ? AND presupuesto_id = ?';
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $descripcion,
            $cantidad,
            $unidad,
            $precio_unitario,
            $descuento_porcentaje,
            $subtotal,
            $item_id,
            $presupuesto_id
        ]);
        
        if (!$result) {
            throw new Exception('Error al actualizar item: ' . implode(', ', $stmt->errorInfo()));
        }
    } else {
        // Crear nuevo item
        $stmtOrden = $pdo->prepare('SELECT COALESCE(MAX(orden), 0) + 1 as siguiente_orden FROM presupuesto_items WHERE presupuesto_id = ?');
        $stmtOrden->execute([$presupuesto_id]);
        $resultado = $stmtOrden->fetch();
        $siguiente_orden = $resultado['siguiente_orden'] ?? 1;
        
        $sql = 'INSERT INTO presupuesto_items (presupuesto_id, descripcion, cantidad, unidad, precio_unitario, descuento_porcentaje, subtotal, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
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
        
        if (!$result) {
            throw new Exception('Error al crear item: ' . implode(', ', $stmt->errorInfo()));
        }
        
        $item_id = $pdo->lastInsertId();
    }
    
    registrarAuditoria($presupuesto_id, 'guardar_item', 'Item ' . $item_id . ' guardado');
    
    echo json_encode(['success' => true, 'item_id' => $item_id]);
}

function eliminarItem() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $item_id = $data['id'] ?? null;
    $presupuesto_id = $data['presupuesto_id'] ?? null;
    
    if (!$item_id || !$presupuesto_id) {
        throw new Exception('ID de item y presupuesto requeridos');
    }
    
    $stmt = $pdo->prepare('DELETE FROM presupuesto_items WHERE id = ? AND presupuesto_id = ?');
    $stmt->execute([$item_id, $presupuesto_id]);
    
    registrarAuditoria($presupuesto_id, 'eliminar_item', 'Item ' . $item_id . ' eliminado');
    
    echo json_encode(['success' => true]);
}

function generarNumero() {
    $numero = generarNumeroPresupuesto();
    echo json_encode(['numero' => $numero]);
}

function generarNumeroPresupuesto() {
    global $pdo;
    
    $año = date('Y');
    $mes = date('m');
    
    $stmt = $pdo->prepare('
        SELECT MAX(CAST(SUBSTRING(numero, -4) AS UNSIGNED)) as ultimo 
        FROM presupuestos 
        WHERE numero LIKE ?
    ');
    
    $prefix = 'PRES-' . $año . $mes;
    $stmt->execute([$prefix . '%']);
    $resultado = $stmt->fetch();
    
    $siguiente = ($resultado['ultimo'] ?? 0) + 1;
    
    return $prefix . '-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
}

function cambiarEstado() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $estado = $data['estado'] ?? null;
    
    if (!$id || !$estado) {
        throw new Exception('ID y estado requeridos');
    }
    
    $estados_validos = ['borrador', 'enviado', 'aprobado', 'rechazado'];
    if (!in_array($estado, $estados_validos)) {
        throw new Exception('Estado no válido');
    }
    
    $stmt = $pdo->prepare('UPDATE presupuestos SET estado = ?, fecha_actualizacion = NOW() WHERE id = ?');
    $stmt->execute([$estado, $id]);
    
    registrarAuditoria($id, 'cambiar_estado', 'Estado cambiado a: ' . $estado);
    
    echo json_encode(['success' => true]);
}

function calcularTotales() {
    global $pdo;
    
    $presupuesto_id = $_GET['id'] ?? null;
    if (!$presupuesto_id) {
        throw new Exception('ID requerido');
    }
    
    $stmt = $pdo->prepare('
        SELECT SUM(subtotal) as total FROM presupuesto_items WHERE presupuesto_id = ?
    ');
    $stmt->execute([$presupuesto_id]);
    $resultado = $stmt->fetch();
    
    $total = $resultado['total'] ?? 0;
    
    // Actualizar total en presupuesto
    $stmt = $pdo->prepare('UPDATE presupuestos SET total = ? WHERE id = ?');
    $stmt->execute([$total, $presupuesto_id]);
    
    echo json_encode(['total' => $total]);
}

function registrarAuditoria($presupuesto_id, $accion, $detalles) {
    global $pdo;
    
    $stmt = $pdo->prepare('
        INSERT INTO presupuesto_auditoria (presupuesto_id, accion, detalles)
        VALUES (?, ?, ?)
    ');
    
    $stmt->execute([$presupuesto_id, $accion, $detalles]);
}
?>
