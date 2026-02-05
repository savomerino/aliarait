// Variables globales
let presupuestoActual = null;
let timerGuardado = null;
let cambiosPendientes = false;
let guardandoItems = false; // Flag para evitar guardos simultáneos
let ultimoEstadoItems = {}; // Guardar estado anterior para detectar cambios

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    inicializarEventos();
    cargarLista();
    
    // Ejecutar AOS
    if (typeof AOS !== 'undefined') {
        AOS.init();
    }
});

// ========== FUNCIONES GENERALES ==========

function inicializarEventos() {
    // Cambiar de vistas
    document.querySelectorAll('.vista-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const vista = this.dataset.vista;
            cambiarVista(vista);
        });
    });

    // Buscar y filtrar
    document.getElementById('btn-filtrar').addEventListener('click', cargarLista);
    document.getElementById('buscar').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            cargarLista();
        }
    });
}

function cambiarVista(vista) {
    // Ocultar todas las vistas
    document.getElementById('vista-lista').classList.remove('activo');
    document.getElementById('vista-editor').classList.remove('activo');

    // Desactivar todos los botones
    document.querySelectorAll('.vista-btn').forEach(btn => {
        btn.classList.remove('btn-primary-custom');
        btn.classList.add('btn-secondary-custom');
    });

    if (vista === 'lista') {
        document.getElementById('vista-lista').classList.add('activo');
        document.getElementById('btn-vista-lista').classList.remove('btn-secondary-custom');
        document.getElementById('btn-vista-lista').classList.add('btn-primary-custom');
        cargarLista();
    } else if (vista === 'nuevo') {
        crearNuevoPresupuesto();
    }
}

function mostrarEstadoGuardado(estado = 'guardando') {
    const el = document.getElementById('estado-guardado');
    
    if (estado === 'guardando') {
        el.className = 'estado-guardado estado-guardando';
        el.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
    } else if (estado === 'guardado') {
        el.className = 'estado-guardado';
        el.innerHTML = '<i class="bi bi-check-circle"></i> Guardado';
        setTimeout(() => {
            if (!cambiosPendientes) {
                el.style.opacity = '0.5';
            }
        }, 2000);
    } else if (estado === 'error') {
        el.className = 'estado-guardado estado-error';
        el.innerHTML = '<i class="bi bi-exclamation-circle"></i> Error al guardar';
    }
}

// ========== LISTA DE PRESUPUESTOS ==========

function cargarLista() {
    const buscar = document.getElementById('buscar').value;
    const estado = document.getElementById('filtro-estado').value;

    let url = 'api.php?action=lista';
    if (buscar) url += '&buscar=' + encodeURIComponent(buscar);
    if (estado) url += '&estado=' + encodeURIComponent(estado);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            renderizarLista(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('lista-contenedor').innerHTML = '<div class="alert-custom">Error al cargar los presupuestos</div>';
        });
}

function renderizarLista(presupuestos) {
    const contenedor = document.getElementById('lista-contenedor');

    if (presupuestos.length === 0) {
        contenedor.innerHTML = '<div class="alert-custom">No hay presupuestos. Crea uno nuevo para comenzar.</div>';
        return;
    }

    let html = '';
    presupuestos.forEach(pres => {
        const fecha = new Date(pres.fecha_creacion).toLocaleDateString('es-ES');
        const estado_class = 'estado-' + pres.estado;
        
        html += `
            <div class="presupuesto-card" onclick="abrirPresupuesto(${pres.id})">
                <div class="presupuesto-card-header">
                    <div class="presupuesto-card-numero">${pres.numero}</div>
                    <span class="presupuesto-estado ${estado_class}">${pres.estado.toUpperCase()}</span>
                </div>
                <div class="presupuesto-card-info">
                    <div class="presupuesto-card-info-item">
                        <span class="presupuesto-card-info-label">Cliente:</span>
                        <span>${pres.cliente_nombre}</span>
                    </div>
                    <div class="presupuesto-card-info-item">
                        <span class="presupuesto-card-info-label">Empresa:</span>
                        <span>${pres.cliente_empresa || '-'}</span>
                    </div>
                    <div class="presupuesto-card-info-item">
                        <span class="presupuesto-card-info-label">Fecha:</span>
                        <span>${fecha}</span>
                    </div>
                    <div class="presupuesto-card-info-item">
                        <span class="presupuesto-card-info-label">Moneda:</span>
                        <span>${pres.moneda}</span>
                    </div>
                </div>
                <div class="presupuesto-card-info" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="presupuesto-card-info-item">
                        <span class="presupuesto-card-info-label">Total:</span>
                        <span class="presupuesto-card-total">$ ${parseFloat(pres.total).toFixed(2)}</span>
                    </div>
                </div>
                <div class="presupuesto-card-acciones" style="margin-top: 15px;">
                    <button class="btn btn-primary-custom btn-sm" onclick="event.stopPropagation(); descargarPDFDirecto(${pres.id})">
                        <i class="bi bi-file-pdf"></i> PDF
                    </button>
                    <button class="btn btn-secondary-custom btn-sm" onclick="event.stopPropagation(); duplicarPresupuestoDirecto(${pres.id})">
                        <i class="bi bi-files"></i> Duplicar
                    </button>
                    <button class="btn btn-trash btn-sm" onclick="event.stopPropagation(); eliminarPresupuesto(${pres.id}, '${pres.numero}')">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        `;
    });

    contenedor.innerHTML = html;
}

// ========== CREAR/EDITAR PRESUPUESTO ==========

function crearNuevoPresupuesto() {
    fetch('api.php?action=crear', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            cliente_nombre: 'Nuevo Cliente',
            cliente_email: '',
            cliente_telefono: '',
            cliente_empresa: ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            abrirPresupuesto(data.id);
        } else {
            alert('Error al crear presupuesto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear presupuesto');
    });
}

function abrirPresupuesto(id) {
    fetch(`api.php?action=obtener&id=${id}`)
        .then(response => response.json())
        .then(data => {
            presupuestoActual = data;
            cargarFormulario(data);
            document.getElementById('vista-lista').classList.remove('activo');
            document.getElementById('vista-editor').classList.add('activo');
            window.scrollTo(0, 0);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar presupuesto');
        });
}

function cargarFormulario(presupuesto) {
    // Información General
    document.getElementById('numero').value = presupuesto.numero;
    document.getElementById('estado').value = presupuesto.estado;
    document.getElementById('fecha_creacion').value = new Date(presupuesto.fecha_creacion).toLocaleDateString('es-ES');
    document.getElementById('moneda').value = presupuesto.moneda;

    // Información del Cliente
    document.getElementById('cliente_nombre').value = presupuesto.cliente_nombre;
    document.getElementById('cliente_empresa').value = presupuesto.cliente_empresa || '';
    document.getElementById('cliente_email').value = presupuesto.cliente_email || '';
    document.getElementById('cliente_telefono').value = presupuesto.cliente_telefono || '';
    document.getElementById('descripcion_general').value = presupuesto.descripcion_general || '';

    // Condiciones y Notas
    document.getElementById('vigencia_dias').value = presupuesto.vigencia_dias;
    document.getElementById('condiciones_pago').value = presupuesto.condiciones_pago || '';
    document.getElementById('notas_internas').value = presupuesto.notas_internas || '';

    // Cargar items
    cargarItems(presupuesto.items);

    mostrarEstadoGuardado('guardado');
    cambiosPendientes = false;
}

function cargarItems(items) {
    const tbody = document.getElementById('items-tbody');
    tbody.innerHTML = '';
    
    // Limpiar estado anterior
    ultimoEstadoItems = {};

    if (!items || items.length === 0) {
        // No agregar item vacío, el usuario usará el botón "Agregar Item"
        return;
    }

    items.forEach(item => {
        agregarFilaItem(item);
        // Guardar estado inicial de cada item
        const claveUltimo = `item_${item.id}`;
        ultimoEstadoItems[claveUltimo] = JSON.stringify({
            presupuesto_id: presupuestoActual.id,
            id: item.id,
            descripcion: item.descripcion,
            cantidad: item.cantidad,
            unidad: item.unidad,
            precio_unitario: item.precio_unitario,
            subtotal: item.subtotal,
            descuento_porcentaje: item.descuento_porcentaje
        });
    });

    calcularTotales();
}

function agregarItem() {
    const tbody = document.getElementById('items-tbody');
    const nuevoItem = {
        id: null,
        descripcion: '',
        cantidad: 1,
        unidad: 'Unidad',
        precio_unitario: 0,
        descuento_porcentaje: 0,
        subtotal: 0
    };
    agregarFilaItem(nuevoItem);
}

function agregarFilaItem(item) {
    const tbody = document.getElementById('items-tbody');
    const tr = document.createElement('tr');
    
    const id_item = item.id ? `item_${item.id}` : `item_temp_${Date.now()}`;
    
    tr.innerHTML = `
        <td>
            <textarea class="form-control" style="min-height: 40px;" placeholder="Descripción del servicio o producto">${item.descripcion}</textarea>
        </td>
        <td>
            <input type="number" class="form-control cantidad-input" value="${item.cantidad}" min="1" step="0.01" onchange="calcularSubtotalFila(this, '${id_item}'); calcularTotales();">
        </td>
        <td>
            <input type="text" class="form-control" value="${item.unidad}" placeholder="Unidad">
        </td>
        <td>
            <input type="number" class="form-control precio-input" value="${item.precio_unitario}" min="0" step="0.01" onchange="calcularSubtotalFila(this, '${id_item}'); calcularTotales();">
        </td>
        <td>
            <input type="number" class="form-control subtotal-input" value="${item.subtotal}" min="0" step="0.01" readonly>
        </td>
        <td>
            <input type="number" class="form-control" value="${item.descuento_porcentaje}" min="0" max="100" step="0.01" onchange="calcularTotales();">
        </td>
        <td>
            <span class="badge-info">$ ${(item.subtotal - (item.subtotal * item.descuento_porcentaje / 100)).toFixed(2)}</span>
        </td>
        <td>
            <button class="btn btn-trash btn-sm" onclick="eliminarFilaItem(this, '${id_item}'); calcularTotales();">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tr.id = id_item;
    tbody.appendChild(tr);
}

function calcularSubtotalFila(element, idItem) {
    const tr = document.getElementById(idItem);
    const inputs = tr.querySelectorAll('input, textarea');
    
    // inputs[0] = descripcion (textarea)
    // inputs[1] = cantidad
    // inputs[2] = unidad
    // inputs[3] = precio_unitario
    // inputs[4] = subtotal
    // inputs[5] = descuento_porcentaje
    
    const cantidad = parseFloat(inputs[1].value) || 0;
    const precio_unitario = parseFloat(inputs[3].value) || 0;
    const subtotal = cantidad * precio_unitario;
    
    inputs[4].value = subtotal.toFixed(2);
}

function guardarTodosLosItems() {
    if (!presupuestoActual) return;
    if (guardandoItems) {
        alert('Espera a que termine el guardado anterior');
        return;
    }

    const tbody = document.getElementById('items-tbody');
    const filas = tbody.querySelectorAll('tr');
    let itemsAGuardar = [];

    filas.forEach(tr => {
        const idItem = tr.id;
        const inputs = tr.querySelectorAll('input, textarea');
        
        const descripcion = inputs[0].value.trim();
        if (!descripcion) return; // Saltar filas vacías

        const esItemNuevo = idItem.includes('item_temp_');
        let itemId = null;
        if (!esItemNuevo) {
            itemId = parseInt(idItem.replace('item_', ''));
        }

        const itemData = {
            presupuesto_id: presupuestoActual.id,
            id: itemId,
            descripcion: descripcion,
            cantidad: parseFloat(inputs[1].value) || 1,
            unidad: inputs[2].value.trim(),
            precio_unitario: parseFloat(inputs[3].value) || 0,
            subtotal: parseFloat(inputs[4].value) || 0,
            descuento_porcentaje: parseFloat(inputs[5].value) || 0
        };

        // Verificar si el item tiene cambios
        const itemJSON = JSON.stringify(itemData);
        const claveUltimo = `item_${itemId || idItem}`;
        
        if (ultimoEstadoItems[claveUltimo] !== itemJSON) {
            itemsAGuardar.push({
                ...itemData,
                elemento: tr,
                esNuevo: esItemNuevo,
                idItem: idItem,
                claveUltimo: claveUltimo,
                itemJSON: itemJSON
            });
        }
    });

    if (itemsAGuardar.length === 0) {
        alert('No hay cambios para guardar');
        return;
    }

    guardandoItems = true;
    const btnGuardar = document.querySelector('button[onclick="guardarTodosLosItems()"]');
    if (btnGuardar) {
        btnGuardar.disabled = true;
    }

    mostrarEstadoGuardado('guardando');
    cambiosPendientes = true;
    let itemsGuardados = 0;

    // Guardar cada item secuencialmente
    itemsAGuardar.forEach((item) => {
        fetch('api.php?action=guardar_item', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                presupuesto_id: item.presupuesto_id,
                id: item.id,
                descripcion: item.descripcion,
                cantidad: item.cantidad,
                unidad: item.unidad,
                precio_unitario: item.precio_unitario,
                subtotal: item.subtotal,
                descuento_porcentaje: item.descuento_porcentaje
            })
        })
        .then(response => response.json())
        .then(data => {
            itemsGuardados++;
            if (data.success) {
                // Guardar estado del item
                ultimoEstadoItems[item.claveUltimo] = item.itemJSON;
                
                // Actualizar ID si es nuevo
                if (item.esNuevo && data.item_id) {
                    const nuevoId = `item_${data.item_id}`;
                    item.elemento.id = nuevoId;
                    // Limpiar referencia anterior
                    delete ultimoEstadoItems[`item_${item.idItem}`];
                }
            }
            
            if (itemsGuardados === itemsAGuardar.length) {
                mostrarEstadoGuardado('guardado');
                cambiosPendientes = false;
                guardandoItems = false;
                if (btnGuardar) {
                    btnGuardar.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            itemsGuardados++;
            if (itemsGuardados === itemsAGuardar.length) {
                mostrarEstadoGuardado('error');
                guardandoItems = false;
                if (btnGuardar) {
                    btnGuardar.disabled = false;
                }
            }
        });
    });
}

function eliminarFilaItem(button, idItem) {
    const tr = document.getElementById(idItem);
    const itemId = idItem.replace('item_', '').replace('item_temp_', '');

    if (itemId !== idItem) {
        // Item guardado - eliminarlo de la BD
        if (!presupuestoActual) return;

        fetch('api.php?action=eliminar_item', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: itemId,
                presupuesto_id: presupuestoActual.id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tr.remove();
            }
        })
        .catch(error => console.error('Error:', error));
    } else {
        // Item temporal - solo remover del DOM
        tr.remove();
    }
}

function guardarCampo(campo) {
    if (!presupuestoActual) return;

    const elemento = document.getElementById(campo);
    let valor = elemento.value;

    // Convertir a número si es necesario
    if (campo === 'vigencia_dias') {
        valor = parseInt(valor) || 30;
    }

    mostrarEstadoGuardado('guardando');
    cambiosPendientes = true;

    clearTimeout(timerGuardado);
    timerGuardado = setTimeout(() => {
        fetch('api.php?action=actualizar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: presupuestoActual.id,
                [campo]: valor
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarEstadoGuardado('guardado');
                cambiosPendientes = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarEstadoGuardado('error');
        });
    }, 800);
}

function cambiarEstadoPresupuesto() {
    if (!presupuestoActual) return;

    const nuevoEstado = document.getElementById('estado').value;

    fetch('api.php?action=cambiar_estado', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id: presupuestoActual.id,
            estado: nuevoEstado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            presupuestoActual.estado = nuevoEstado;
            mostrarEstadoGuardado('guardado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarEstadoGuardado('error');
    });
}

function calcularTotales() {
    if (!presupuestoActual) return;

    const tbody = document.getElementById('items-tbody');
    const filas = tbody.querySelectorAll('tr');

    let subtotal_total = 0;
    let descuentos_total = 0;

    filas.forEach(fila => {
        const inputs = fila.querySelectorAll('input, textarea');
        const subtotal = parseFloat(inputs[4].value) || 0;
        const descuento_pct = parseFloat(inputs[5].value) || 0;
        const descuento = (subtotal * descuento_pct) / 100;

        subtotal_total += subtotal;
        descuentos_total += descuento;
    });

    const total_final = subtotal_total - descuentos_total;

    // Actualizar totales en la BD
    fetch('api.php?action=calcular_totales&id=' + presupuestoActual.id)
        .catch(error => console.error('Error:', error));

    // Mostrar en UI
    document.getElementById('subtotal-total').textContent = '$ ' + subtotal_total.toFixed(2);
    document.getElementById('descuentos-total').textContent = '$ ' + descuentos_total.toFixed(2);
    document.getElementById('total-final').textContent = '$ ' + total_final.toFixed(2);

    // Actualizar total en presupuesto actual
    presupuestoActual.total = total_final;
}

// ========== ACCIONES ==========

function descargarPDF() {
    if (!presupuestoActual) return;
    
    // Abrir en nueva ventana para que el usuario pueda imprimir a PDF
    const url = `descarga_pdf.php?id=${presupuestoActual.id}`;
    window.open(url, '_blank');
}

function descargarPDFDirecto(id) {
    // Abrir en nueva ventana para que el usuario pueda imprimir a PDF
    const url = `descarga_pdf.php?id=${id}`;
    window.open(url, '_blank');
}

function verPreview() {
    if (!presupuestoActual) return;

    const modal = new bootstrap.Modal(document.getElementById('modalPreview'));
    
    fetch(`generar_pdf.php?id=${presupuestoActual.id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            if (!html || html.length < 100) {
                throw new Error('HTML vacío o inválido');
            }
            document.getElementById('preview-contenido').innerHTML = html;
            modal.show();
            console.log('Vista previa cargada correctamente');
        })
        .catch(error => {
            console.error('Error en vista previa:', error);
            alert('Error al cargar la vista previa: ' + error.message);
        });
}

function imprimirPDFDesdePreview() {
    if (!presupuestoActual) return;
    
    // Abrir en nueva ventana para que el usuario pueda imprimir a PDF
    const url = `descarga_pdf.php?id=${presupuestoActual.id}`;
    window.open(url, '_blank');
}

function descargarComoJPG() {
    if (!presupuestoActual) return;
    
    const elemento = document.getElementById('preview-contenido');
    
    if (!elemento) {
        alert('No hay contenido para capturar');
        return;
    }

    // Crear un botón temporal de carga
    const btnJPG = event.target.closest('button');
    const textoOriginal = btnJPG.innerHTML;
    btnJPG.disabled = true;
    btnJPG.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando...';

    // Clonar el elemento para capturar todo sin afectar el original
    const clon = elemento.cloneNode(true);
    
    // Aplicar estilos al clon para capturar todo el contenido
    clon.style.position = 'absolute';
    clon.style.left = '-9999px';
    clon.style.top = '-9999px';
    clon.style.width = elemento.scrollWidth + 'px';
    clon.style.height = elemento.scrollHeight + 'px';
    clon.style.overflow = 'visible';
    clon.style.maxHeight = 'none';
    clon.style.maxWidth = 'none';
    
    // Agregar el clon al body temporalmente
    document.body.appendChild(clon);

    // Usar html2canvas para capturar todo el contenido
    html2canvas(clon, {
        backgroundColor: '#1a1a1a',
        scale: 2,
        logging: false,
        useCORS: true,
        allowTaint: true,
        windowHeight: clon.scrollHeight,
        windowWidth: clon.scrollWidth
    })
    .then(canvas => {
        // Convertir canvas a imagen JPG
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/jpeg', 0.95);
        link.download = `presupuesto_${presupuestoActual.numero}_${presupuestoActual.cliente_nombre.replace(/\s+/g, '_')}.jpg`;
        link.click();

        // Remover el clon
        document.body.removeChild(clon);

        // Restaurar botón
        btnJPG.disabled = false;
        btnJPG.innerHTML = textoOriginal;
    })
    .catch(error => {
        console.error('Error al generar JPG:', error);
        alert('Error al generar la imagen JPG');
        
        // Remover el clon en caso de error
        if (document.body.contains(clon)) {
            document.body.removeChild(clon);
        }
        
        // Restaurar botón en caso de error
        btnJPG.disabled = false;
        btnJPG.innerHTML = textoOriginal;
    });
}

function duplicarPresupuesto() {
    if (!presupuestoActual) return;
    duplicarPresupuestoDirecto(presupuestoActual.id);
}

function duplicarPresupuestoDirecto(id) {
    fetch(`api.php?action=obtener&id=${id}`)
        .then(response => response.json())
        .then(original => {
            const datosNuevo = {
                cliente_nombre: original.cliente_nombre,
                cliente_email: original.cliente_email,
                cliente_telefono: original.cliente_telefono,
                cliente_empresa: original.cliente_empresa
            };

            return fetch('api.php?action=crear', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosNuevo)
            }).then(response => response.json());
        })
        .then(nuevoPresu => {
            if (nuevoPresu.success) {
                // Cargar el presupuesto duplicado
                setTimeout(() => {
                    abrirPresupuesto(nuevoPresu.id);
                }, 500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al duplicar presupuesto');
        });
}

function eliminarPresupuesto(id, numero) {
    if (confirm(`¿Estás seguro de que deseas eliminar el presupuesto ${numero}?`)) {
        fetch(`api.php?action=eliminar&id=${id}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarLista();
                } else {
                    alert('Error al eliminar');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar');
            });
    }
}

function eliminarPresupuestoActual() {
    if (!presupuestoActual) return;
    
    if (confirm(`¿Estás seguro de que deseas eliminar el presupuesto ${presupuestoActual.numero}?`)) {
        fetch(`api.php?action=eliminar&id=${presupuestoActual.id}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    volverALista();
                } else {
                    alert('Error al eliminar');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar');
            });
    }
}

function volverALista() {
    presupuestoActual = null;
    cambiosPendientes = false;
    cambiarVista('lista');
}
