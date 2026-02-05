# Aplicación de Presupuestos - Aliara IT

## 📋 Descripción

Aplicación web completa para generar presupuestos rápidamente con:
- ✅ CRUD completo (Crear, Leer, Actualizar, Eliminar)
- ✅ Guardado automático al completar campos
- ✅ Sistema de borradores
- ✅ Descarga en PDF
- ✅ Interfaz consistente con la página principal
- ✅ Gestión de múltiples presupuestos
- ✅ Base de datos MySQL

## 🛠️ Instalación

### 1. Crear Base de Datos

Ejecuta el siguiente SQL en phpMyAdmin o en tu cliente MySQL:

```sql
-- Copiar y ejecutar el contenido del archivo: db_presupuestos.sql
```

**Credenciales:**
- Host: `127.0.0.1:3306`
- Usuario: `u592897176_`
- Contraseña: `^8S>E#x1gG`
- Base de datos: `u592897176_`

### 2. Verificar Estructura de Carpetas

```
WEB/
├── presu/
│   ├── index.html          (Página principal de presupuestos)
│   ├── app.js              (Lógica del frontend)
│   ├── api.php             (API del backend)
│   ├── config.php          (Configuración de BD)
│   └── generar_pdf.php     (Generador de PDF)
├── assets/
│   ├── css/main.css
│   ├── js/main.js
│   └── img/web_logo.webp
└── index.html              (Página principal del sitio)
```

### 3. Archivos Necesarios

Los siguientes archivos ya están creados:
- ✅ `presu/index.html` - Aplicación web
- ✅ `presu/app.js` - Lógica JavaScript
- ✅ `presu/api.php` - API REST
- ✅ `presu/config.php` - Configuración
- ✅ `presu/generar_pdf.php` - Generador de PDF
- ✅ `db_presupuestos.sql` - Script de BD

## 🚀 Uso

### Acceder a la Aplicación

1. Abre `http://localhost/WEB/presu/` en tu navegador
2. O haz clic en "Presupuestos" desde la navegación principal

### Crear un Presupuesto

1. Haz clic en el botón **"+ Nuevo"**
2. Completa los datos del cliente
3. Los datos se guardan automáticamente
4. Agrega items con el botón **"+ Agregar Item"**

### Gestionar Presupuestos

- **Ver Lista**: Todos los presupuestos guardados
- **Filtrar**: Por estado (Borrador, Enviado, Aprobado, Rechazado)
- **Buscar**: Por número, cliente o email
- **Editar**: Haz clic en un presupuesto para editarlo
- **Duplicar**: Copia un presupuesto existente
- **PDF**: Descarga en formato PDF
- **Eliminar**: Borra un presupuesto

### Estados de Presupuestos

- 🟡 **Borrador**: Presupuesto en edición
- 🔵 **Enviado**: Enviado al cliente
- 🟢 **Aprobado**: Cliente aprobó
- 🔴 **Rechazado**: Cliente rechazó

## 📊 Características

### Guardado Automático
- Se guarda automáticamente 800ms después de completar un campo
- Indicador visual de estado de guardado
- Sin necesidad de hacer clic en "Guardar"

### Campos del Presupuesto

**Información General:**
- Número automático (PRES-YYYYMM-####)
- Estado
- Fecha de creación
- Moneda (USD, EUR, BOB, ARS)

**Datos del Cliente:**
- Nombre del cliente
- Empresa
- Email
- Teléfono
- Descripción del proyecto

**Items:**
- Descripción del servicio
- Cantidad
- Unidad de medida
- Precio unitario
- Subtotal
- Descuento %
- Total por item

**Condiciones:**
- Vigencia del presupuesto (días)
- Condiciones de pago
- Notas internas

### PDF
- Incluye logo de Aliara IT
- Datos completos del presupuesto
- Tabla de items con cálculos
- Total y descuentos
- Vigencia
- Notas y condiciones

## 🔌 API Endpoints

```
GET /presu/api.php?action=lista              - Listar presupuestos
GET /presu/api.php?action=obtener&id=XX      - Obtener presupuesto
POST /presu/api.php?action=crear             - Crear presupuesto
POST /presu/api.php?action=actualizar        - Actualizar presupuesto
DELETE /presu/api.php?action=eliminar&id=XX  - Eliminar presupuesto
POST /presu/api.php?action=guardar_item      - Guardar item
POST /presu/api.php?action=eliminar_item     - Eliminar item
POST /presu/api.php?action=cambiar_estado    - Cambiar estado
GET /presu/api.php?action=calcular_totales&id=XX - Calcular totales
```

## 🎨 Diseño

- Mantiene la estética y colores de la página principal
- Tema oscuro con acentos en naranja (#e7a042)
- Responsive para móviles y tablets
- Interfaz intuitiva y fácil de usar

## 🔒 Seguridad

- Prepared statements para evitar SQL injection
- Validación de entrada
- Control de acceso básico (agregar autenticación si es necesario)
- Encriptación de contraseña en config.php

## 📝 Base de Datos

### Tabla: presupuestos
```sql
- id (INT, PK)
- numero (VARCHAR, UNIQUE)
- cliente_nombre (VARCHAR)
- cliente_email (VARCHAR)
- cliente_telefono (VARCHAR)
- cliente_empresa (VARCHAR)
- descripcion_general (LONGTEXT)
- fecha_creacion (DATETIME)
- fecha_actualizacion (DATETIME)
- estado (ENUM: borrador, enviado, aprobado, rechazado)
- total (DECIMAL)
- moneda (VARCHAR)
- condiciones_pago (LONGTEXT)
- vigencia_dias (INT)
- notas_internas (LONGTEXT)
```

### Tabla: presupuesto_items
```sql
- id (INT, PK)
- presupuesto_id (INT, FK)
- descripcion (LONGTEXT)
- cantidad (DECIMAL)
- unidad (VARCHAR)
- precio_unitario (DECIMAL)
- descuento_porcentaje (DECIMAL)
- subtotal (DECIMAL)
- orden (INT)
```

### Tabla: presupuesto_auditoria
```sql
- id (INT, PK)
- presupuesto_id (INT, FK)
- accion (VARCHAR)
- detalles (LONGTEXT)
- fecha (DATETIME)
```

## 🐛 Solución de Problemas

### "Error de conexión"
- Verificar credenciales en `config.php`
- Asegurar que MySQL está corriendo
- Verificar que la base de datos existe

### Presupuestos no se guardan
- Verificar permisos del servidor web
- Revisar logs de error de PHP
- Verificar conexión a BD

### PDF no genera
- Verificar que el servidor tiene permisos de escritura
- Asegurar que PHP tiene extensión `mbstring`
- Para PDF avanzado, instalar mPDF: `composer require mpdf/mpdf`

## 📧 Próximas Mejoras

- [ ] Autenticación de usuarios
- [ ] Envío de presupuestos por email
- [ ] Firma digital
- [ ] Múltiples empresas
- [ ] Plantillas personalizables
- [ ] Seguimiento de presupuestos
- [ ] Integraciones con CRM

## 📞 Soporte

Para soporte, contacta a Aliara IT a través del formulario de contacto en la página principal.

---

**Creado para: Aliara IT**  
**Última actualización:** 2026-02-05
