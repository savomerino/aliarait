<?php
// Verificar autenticación
session_start();

// Si no está autenticado, redirigir a login
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: login.html');
    exit;
}

// Manejo de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.html');
    exit;
}

// Si llegó acá, está autenticado. Incluir el contenido del index.html
?><!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Generador de Presupuestos - Aliara IT</title>
  <meta content="Generador rápido de presupuestos con guardado automático" name="description">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="../assets/css/main.css" rel="stylesheet">
  
  <!-- jsPDF para generación de PDFs -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  
  <!-- html2canvas para captura de pantalla -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  
  <!-- Custom CSS para Presupuestos -->
  <style>
    .presupuesto-container {
      min-height: 100vh;
      padding-top: 80px;
      padding-bottom: 40px;
    }

    .presupuesto-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 15px;
    }

    .presupuesto-title {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .presupuesto-title h1 {
      margin: 0;
      color: var(--accent-color);
      font-size: 28px;
    }

    .presupuesto-title img {
      height: 50px;
    }

    .vista-selector {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .vista-selector .btn {
      padding: 8px 15px;
      font-size: 14px;
    }

    .btn-primary-custom {
      background-color: var(--accent-color);
      border: none;
      color: var(--background-color);
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
      background-color: var(--accent-color-hover);
      color: var(--background-color);
    }

    .btn-secondary-custom {
      background-color: transparent;
      border: 1px solid var(--accent-color);
      color: var(--accent-color);
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-secondary-custom:hover {
      background-color: var(--accent-color);
      color: var(--background-color);
    }

    .btn-warning-custom {
      background-color: #ffc107;
      border: none;
      color: #000;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-warning-custom:hover {
      background-color: #ffb300;
      color: #000;
    }

    .btn-outline-custom {
      background-color: transparent;
      border: 1px solid var(--surface-color);
      color: var(--default-color);
      transition: all 0.3s ease;
    }

    .btn-outline-custom:hover {
      border-color: var(--accent-color);
      color: var(--accent-color);
    }

    .btn-logout {
      background-color: #dc3545;
      border: none;
      color: white;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-logout:hover {
      background-color: #c82333;
      color: white;
      text-decoration: none;
    }

    .presupuesto-lista {
      display: none;
    }

    .presupuesto-lista.activo {
      display: block;
    }

    .presupuesto-editor {
      display: none;
    }

    .presupuesto-editor.activo {
      display: block;
    }

    .presupuesto-card {
      background-color: var(--surface-color);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .presupuesto-card:hover {
      border-color: var(--accent-color);
      box-shadow: 0 0 20px rgba(231, 160, 66, 0.2);
    }

    .presupuesto-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .presupuesto-card-numero {
      color: var(--accent-color);
      font-weight: 700;
      font-size: 16px;
    }

    .presupuesto-estado {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .estado-borrador {
      background-color: rgba(255, 193, 7, 0.2);
      color: #ffc107;
    }

    .estado-enviado {
      background-color: rgba(23, 162, 184, 0.2);
      color: #17a2b8;
    }

    .estado-aprobado {
      background-color: rgba(40, 167, 69, 0.2);
      color: #28a745;
    }

    .estado-rechazado {
      background-color: rgba(220, 53, 69, 0.2);
      color: #dc3545;
    }

    .presupuesto-card-info {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .presupuesto-card-info-item {
      display: flex;
      justify-content: space-between;
    }

    .presupuesto-card-info-label {
      color: rgba(255, 255, 255, 0.6);
    }

    .presupuesto-card-total {
      color: var(--accent-color);
      font-weight: 700;
      font-size: 18px;
    }

    .presupuesto-card-acciones {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .presupuesto-card-acciones .btn {
      padding: 6px 12px;
      font-size: 13px;
    }

    .form-section {
      background-color: var(--surface-color);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      padding: 25px;
      margin-bottom: 25px;
    }

    .form-section-title {
      color: var(--accent-color);
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--accent-color);
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      color: var(--default-color);
      font-weight: 600;
      margin-bottom: 8px;
      display: block;
    }

    .form-control, .form-select {
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: var(--default-color);
      padding: 10px 15px;
      border-radius: 5px;
      transition: all 0.3s ease;
      font-family: var(--default-font);
    }

    .form-control:focus, .form-select:focus {
      background-color: rgba(255, 255, 255, 0.15);
      border-color: var(--accent-color);
      color: var(--default-color);
      box-shadow: 0 0 0 0.2rem rgba(231, 160, 66, 0.25);
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .form-select option {
      background-color: var(--background-color);
      color: var(--default-color);
    }

    .items-container {
      margin-top: 20px;
    }

    .item-row {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 5px;
      padding: 15px;
      margin-bottom: 15px;
      display: grid;
      grid-template-columns: 1fr 100px 80px 100px 100px 80px;
      gap: 10px;
      align-items: flex-end;
    }

    @media (max-width: 1200px) {
      .item-row {
        grid-template-columns: 1fr;
      }
    }

    .item-row .form-control {
      width: 100%;
    }

    .btn-trash {
      background-color: rgba(220, 53, 69, 0.2);
      border: 1px solid rgba(220, 53, 69, 0.5);
      color: #dc3545;
      padding: 8px 12px;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .btn-trash:hover {
      background-color: #dc3545;
      border-color: #dc3545;
      color: white;
    }

    .editor-acciones {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .editor-acciones .btn {
      padding: 10px 20px;
      font-weight: 600;
    }

    .estado-guardado {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 15px;
      background-color: rgba(40, 167, 69, 0.2);
      color: #28a745;
      border-radius: 5px;
      font-size: 13px;
      font-weight: 600;
    }

    .estado-guardando {
      color: var(--accent-color);
    }

    .estado-error {
      background-color: rgba(220, 53, 69, 0.2);
      color: #dc3545;
    }

    .tabla-items {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .tabla-items thead {
      background-color: rgba(231, 160, 66, 0.1);
      border-bottom: 2px solid var(--accent-color);
    }

    .tabla-items th {
      color: var(--accent-color);
      padding: 12px;
      text-align: left;
      font-weight: 600;
    }

    .tabla-items td {
      padding: 12px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .tabla-items tbody tr:hover {
      background-color: rgba(255, 255, 255, 0.05);
    }

    .badge-info {
      display: inline-block;
      padding: 3px 8px;
      background-color: rgba(231, 160, 66, 0.2);
      color: var(--accent-color);
      border-radius: 3px;
      font-size: 12px;
    }

    .logo-presu {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .logo-presu img {
      height: 40px;
    }

    .buscar-container {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .buscar-container .form-control {
      flex: 1;
      min-width: 200px;
    }

    .totales-resumen {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }

    .totales-card {
      background-color: rgba(231, 160, 66, 0.1);
      border: 1px solid var(--accent-color);
      border-radius: 5px;
      padding: 15px;
      text-align: center;
    }

    .totales-card-label {
      color: rgba(255, 255, 255, 0.7);
      font-size: 12px;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .totales-card-valor {
      color: var(--accent-color);
      font-size: 24px;
      font-weight: 700;
    }

    .alert-custom {
      background-color: rgba(220, 53, 69, 0.2);
      border: 1px solid rgba(220, 53, 69, 0.5);
      color: #ffc107;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .presupuesto-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .presupuesto-title {
        flex-direction: column;
      }

      .vista-selector {
        width: 100%;
        justify-content: flex-start;
      }

      .presupuesto-card-info {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="../index.html" class="logo d-flex align-items-center me-auto">
        <img src="../assets/img/web_logo.webp" alt="Aliara IT Logo" style="height:40px;">
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="../index.html">Inicio</a></li>
          <li><a href="../index.html#about">Nosotros</a></li>
          <li><a href="../index.html#services">Servicios</a></li>
          <li class="active"><a href="index.php">Presupuestos</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <a class="btn-getstarted" href="../index.html#contact">Contáctanos</a>
      <a href="?logout=1" class="btn-logout" style="margin-left: 10px;">🚪 Logout</a>
    </div>
  </header>

  <main class="main">
    <div class="presupuesto-container">
      <div class="container-fluid container-xl">
        
        <!-- Header -->
        <div class="presupuesto-header" data-aos="fade-down">
          <div class="presupuesto-title">
            <img src="../assets/img/web_logo.webp" alt="Aliara IT" style="height:40px;">
            <h1>Generador de Presupuestos</h1>
          </div>
          <div class="vista-selector">
            <button class="btn btn-primary-custom vista-btn" data-vista="lista" id="btn-vista-lista">
              <i class="bi bi-list-ul"></i> Lista
            </button>
            <button class="btn btn-secondary-custom vista-btn" data-vista="nuevo" id="btn-vista-nuevo">
              <i class="bi bi-plus-circle"></i> Nuevo
            </button>
          </div>
        </div>

        <!-- VISTA: LISTA DE PRESUPUESTOS -->
        <div class="presupuesto-lista activo" id="vista-lista">
          <div class="form-section" data-aos="fade-up">
            <div class="buscar-container">
              <input type="text" id="buscar" class="form-control" placeholder="Buscar por número, cliente o email...">
              <select id="filtro-estado" class="form-select" style="max-width: 150px;">
                <option value="">Todos los estados</option>
                <option value="borrador">Borradores</option>
                <option value="enviado">Enviados</option>
                <option value="aprobado">Aprobados</option>
                <option value="rechazado">Rechazados</option>
              </select>
              <button class="btn btn-primary-custom" id="btn-filtrar">
                <i class="bi bi-funnel"></i> Filtrar
              </button>
            </div>
          </div>

          <div id="lista-contenedor">
            <!-- Presupuestos se cargan aquí con JavaScript -->
          </div>
        </div>

        <!-- VISTA: EDITOR DE PRESUPUESTO -->
        <div class="presupuesto-editor" id="vista-editor" data-aos="fade-up">
          
          <!-- Información General -->
          <div class="form-section">
            <div class="form-section-title">
              Información General
              <span class="estado-guardado" id="estado-guardado" style="float: right; margin-top: 0;">
                <i class="bi bi-check-circle"></i> Guardado
              </span>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="numero">Número de Presupuesto</label>
                  <input type="text" class="form-control" id="numero" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="estado">Estado</label>
                  <select class="form-select" id="estado" onchange="cambiarEstadoPresupuesto()">
                    <option value="borrador">Borrador</option>
                    <option value="enviado">Enviado</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="rechazado">Rechazado</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="fecha_creacion">Fecha de Creación</label>
                  <input type="text" class="form-control" id="fecha_creacion" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="moneda">Moneda</label>
                  <select class="form-select" id="moneda" onchange="guardarCampo('moneda')">
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="BOB">BOB</option>
                    <option value="ARS">ARS</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Información del Cliente -->
          <div class="form-section">
            <div class="form-section-title">Datos del Cliente</div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="cliente_nombre">Nombre del Cliente *</label>
                  <input type="text" class="form-control" id="cliente_nombre" onchange="guardarCampo('cliente_nombre')" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="cliente_empresa">Empresa</label>
                  <input type="text" class="form-control" id="cliente_empresa" onchange="guardarCampo('cliente_empresa')">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="cliente_email">Email</label>
                  <input type="email" class="form-control" id="cliente_email" onchange="guardarCampo('cliente_email')">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="cliente_telefono">Teléfono</label>
                  <input type="tel" class="form-control" id="cliente_telefono" onchange="guardarCampo('cliente_telefono')">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="descripcion_general">Descripción General del Proyecto</label>
              <textarea class="form-control" id="descripcion_general" rows="3" onchange="guardarCampo('descripcion_general')" placeholder="Describe brevemente el proyecto o servicio..."></textarea>
            </div>
          </div>

          <!-- Items del Presupuesto -->
          <div class="form-section">
            <div class="form-section-title">Items del Presupuesto</div>

            <table class="tabla-items" id="tabla-items">
              <thead>
                <tr>
                  <th>Descripción</th>
                  <th>Cantidad</th>
                  <th>Unidad</th>
                  <th>P. Unitario</th>
                  <th>Subtotal</th>
                  <th>Descuento %</th>
                  <th>Total</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="items-tbody">
                <!-- Items se cargan aquí -->
              </tbody>
            </table>

            <button class="btn btn-secondary-custom" onclick="agregarItem()" style="margin-top: 15px;">
              <i class="bi bi-plus-circle"></i> Agregar Item
            </button>
          </div>

          <!-- Resumen de Totales -->
          <div class="totales-resumen">
            <div class="totales-card">
              <div class="totales-card-label">Subtotal</div>
              <div class="totales-card-valor" id="subtotal-total">$ 0.00</div>
            </div>
            <div class="totales-card">
              <div class="totales-card-label">Descuentos</div>
              <div class="totales-card-valor" id="descuentos-total">$ 0.00</div>
            </div>
            <div class="totales-card">
              <div class="totales-card-label">Total Final</div>
              <div class="totales-card-valor" id="total-final">$ 0.00</div>
            </div>
          </div>

          <!-- Condiciones y Notas -->
          <div class="form-section">
            <div class="form-section-title">Condiciones de Pago y Notas</div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label" for="vigencia_dias">Vigencia del Presupuesto (días)</label>
                  <input type="number" class="form-control" id="vigencia_dias" value="30" onchange="guardarCampo('vigencia_dias')">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="condiciones_pago">Condiciones de Pago</label>
              <textarea class="form-control" id="condiciones_pago" rows="3" onchange="guardarCampo('condiciones_pago')" placeholder="Ej: 50% al firmar, 50% a la entrega..."></textarea>
            </div>

            <div class="form-group">
              <label class="form-label" for="notas_internas">Notas Internas</label>
              <textarea class="form-control" id="notas_internas" rows="3" onchange="guardarCampo('notas_internas')" placeholder="Notas solo visibles internamente..."></textarea>
            </div>
          </div>

          <!-- Acciones -->
          <div class="editor-acciones">
            <button class="btn btn-primary-custom" onclick="guardarTodosLosItems()">
              <i class="bi bi-save"></i> Guardar Presupuesto
            </button>
            <button class="btn btn-primary-custom" onclick="descargarPDF()">
              <i class="bi bi-file-pdf"></i> Descargar PDF
            </button>
            <button class="btn btn-secondary-custom" onclick="verPreview()">
              <i class="bi bi-eye"></i> Vista Previa
            </button>
            <button class="btn btn-outline-custom" onclick="duplicarPresupuesto()">
              <i class="bi bi-files"></i> Duplicar
            </button>
            <button class="btn btn-outline-custom" onclick="volverALista()">
              <i class="bi bi-arrow-left"></i> Volver
            </button>
            <button class="btn btn-trash" onclick="eliminarPresupuestoActual()">
              <i class="bi bi-trash"></i> Eliminar
            </button>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- Modal para Vista Previa -->
  <div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="background-color: var(--surface-color); border: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
          <h5 class="modal-title">Vista Previa del Presupuesto</h5>
          <div>
            <button type="button" class="btn btn-sm btn-warning-custom" onclick="descargarComoJPG()" style="margin-right: 10px;">
              <i class="bi bi-image"></i> Descargar como JPG
            </button>
            <button type="button" class="btn btn-sm btn-warning-custom" onclick="imprimirPDFDesdePreview()" style="margin-right: 10px;">
              <i class="bi bi-printer"></i> Descargar PDF
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
        </div>
        <div class="modal-body" id="preview-contenido" style="max-height: 70vh; overflow-y: auto;">
          <!-- Contenido se carga aquí -->
        </div>
      </div>
    </div>
  </div>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>

  <!-- Main JS File -->
  <script src="../assets/js/main.js"></script>

  <!-- App JS -->
  <script src="app.js"></script>

</body>

</html>
<?php
?>
