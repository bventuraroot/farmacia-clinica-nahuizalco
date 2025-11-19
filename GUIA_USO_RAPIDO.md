# 🚀 Guía de Uso Rápido - Sistema Modular Integrado

## 📌 Acceso al Sistema

**URL Principal:**
```
http://localhost:8003/dashboard
```

Al ingresar, verás el **Centro de Control** con 4 módulos principales.

---

## 🎯 Para Cada Usuario

### 👩‍⚕️ **DOCTORA - Atención Médica**

**Módulo:** Clínica Médica (tarjeta verde)

**Flujo de trabajo:**
1. Entra a `/dashboard`
2. Clic en tarjeta **"Clínica Médica"** (verde)
3. Ve su **agenda del día** con todas las citas
4. Selecciona paciente → Registra consulta
5. Completa:
   - Motivo de consulta
   - Signos vitales
   - Diagnóstico (CIE-10)
   - Plan de tratamiento
   - Receta (si aplica)
   - Órdenes de laboratorio (si aplica)
6. Finaliza consulta
7. **Trabaja sin ver farmacia ni laboratorio**

**URLs clave:**
- `/appointments` - Ver agenda
- `/consultations/create` - Nueva consulta
- `/patients` - Buscar paciente

---

### 🧪 **TÉCNICO DE LABORATORIO**

**Módulo:** Laboratorio Clínico (tarjeta amarilla)

**Flujo de trabajo:**
1. Entra a `/dashboard`
2. Clic en tarjeta **"Laboratorio"** (amarilla)
3. Ve **órdenes pendientes** (badge con número)
4. Procesa órdenes:
   - Registra toma de muestra
   - Procesa examen
   - Registra resultados
   - Valida resultados
5. Marca orden como "Completada"
6. **Solo ve laboratorio** → Enfoque total

**URLs clave:**
- `/lab-orders` - Todas las órdenes
- `/lab-orders/create` - Nueva orden
- `/lab-orders?estado=pendiente` - Solo pendientes

---

### 💊 **VENDEDOR DE FARMACIA**

**Módulo:** Farmacia (tarjeta azul)

**Flujo de trabajo:**
1. Entra a `/dashboard`
2. Clic en tarjeta **"Farmacia"** (azul)
3. Ve estadísticas de ventas
4. Atiende cliente:
   - Busca productos
   - Agrega al carrito
   - Procesa venta
5. **Solo ve farmacia** → Sin interrupciones

**URLs clave:**
- `/sale/create-dynamic` - Nueva venta
- `/products` - Buscar productos
- `/inventory` - Ver inventario

---

### 💰 **CAJERO/FACTURADOR - Lo Más Importante**

**Módulo:** Facturación Integral (tarjeta celeste)

**Flujo de trabajo:**
1. Entra a `/dashboard`
2. Clic en tarjeta **"Facturación"** (celeste)
3. Ve **3 pestañas**:

   **Tab 1: Farmacia**
   - Enlace a ventas de productos
   - Usa el módulo normal de ventas

   **Tab 2: Consultas Médicas** ⭐
   - **Lista de consultas completadas sin facturar**
   - Badge rojo muestra cantidad pendiente
   - Información de cada consulta:
     * Número de consulta
     * Paciente
     * Médico
     * Diagnóstico
     * Monto
   - Botón "Facturar" por cada una
   - Clic en "Facturar" → Genera factura automáticamente

   **Tab 3: Órdenes de Laboratorio** ⭐
   - **Lista de órdenes completadas sin facturar**
   - Badge rojo muestra cantidad pendiente
   - Información de cada orden:
     * Número de orden
     * Paciente
     * Exámenes realizados
     * Total
   - Botón "Facturar" por cada una
   - Clic en "Facturar" → Genera factura automáticamente

4. **Factura todo desde un solo lugar**
5. Ve total facturado del día en tiempo real

**URL clave:**
- `/facturacion-integral` - Hub de facturación

---

### 👨‍💼 **ADMINISTRADOR**

**Módulo:** Todos + Centro de Control

**Flujo de trabajo:**
1. Entra a `/dashboard` → Ve **Centro de Control**
2. Ve resumen de TODO:
   - Ventas del día
   - Citas del día
   - Órdenes de laboratorio
   - Alertas importantes
3. Puede entrar a cualquier módulo
4. Revisa métricas y reportes
5. Gestiona personal y permisos

**Tiene acceso a:**
- Todos los dashboards
- Todos los módulos
- Todas las funciones

---

## 🎨 Navegación Visual

### **Desde el Centro de Control:**

```
┌─────────────────────────────────────────────────┐
│           CENTRO DE CONTROL                     │
│                                                 │
│  ┏━━━━━━━┓  ┏━━━━━━━┓  ┏━━━━━━━┓  ┏━━━━━━━┓  │
│  ┃ 💊    ┃  ┃ 🩺    ┃  ┃ 🧪    ┃  ┃ 💰    ┃  │
│  ┃FARMACIA┃  ┃CLÍNICA┃  ┃ LAB   ┃  ┃FACTURA┃  │
│  ┃       ┃  ┃       ┃  ┃       ┃  ┃       ┃  │
│  ┃[ENTRAR]┃  ┃[ENTRAR]┃  ┃[ENTRAR]┃  ┃[ENTRAR]┃  │
│  ┗━━━━━━━┛  ┗━━━━━━━┛  ┗━━━━━━━┛  ┗━━━━━━━┛  │
└─────────────────────────────────────────────────┘
```

### **Dentro de cada módulo:**

El usuario solo ve información relevante a ese módulo.

---

## ⚡ Características Principales

### 1. **Separación Total de Contextos**
- ✅ Doctora solo ve clínica
- ✅ Técnico solo ve laboratorio
- ✅ Vendedor solo ve farmacia
- ✅ Cajero ve TODO para facturar

### 2. **Facturación Centralizada**
- ✅ Un solo lugar para facturar
- ✅ Listas de servicios pendientes
- ✅ Badges con cantidades
- ✅ Facturación con 1 clic

### 3. **Alertas Inteligentes**
- ⚠️ Stock bajo en farmacia
- ⚠️ Productos por vencer
- ⚠️ Citas pendientes
- ⚠️ Órdenes de lab pendientes

### 4. **Diseño Elegante**
- 🎨 Colores por módulo
- 🎨 Iconos grandes y claros
- 🎨 Efectos hover suaves
- 🎨 Responsive (móvil/tablet/PC)

---

## 📱 Menú Lateral

El menú lateral ahora tiene:

```
🏠 Centro de Control
👥 Administración
🏢 Empresas
👤 Clientes
📦 Producción
💰 Facturación (NUEVO)
   ├─ Facturación Integral ⭐
   ├─ Nueva Venta Farmacia
   └─ Historial de Ventas
📦 Inventario
🛒 Compras
🏥 Clínica Médica (NUEVO)
   ├─ Pacientes
   ├─ Médicos
   ├─ Citas Médicas
   └─ Consultas
🧪 Laboratorio Clínico (NUEVO)
   ├─ Órdenes de Laboratorio
   └─ Catálogo de Exámenes
💳 Créditos
📋 Cotizaciones
🤖 Chat IA
💾 Respaldos
📊 Reportes
📄 Administración DTE
```

---

## 🎯 Caso de Uso Completo

### **Día típico en el establecimiento:**

**8:00 AM - Inicio de operaciones**
- Administrador entra → Dashboard Central
- Ve resumen: 0 ventas, 5 citas programadas, 2 órdenes pendientes
- Revisa alertas: 3 productos stock bajo

**9:00 AM - Doctora llega**
- Entra a Clínica → Ve su agenda
- 5 citas programadas hoy
- Comienza atenciones

**9:30 AM - Primera consulta**
- Paciente: Juan Pérez
- Síntomas: Gripe
- Doctora registra: temperatura, presión, diagnóstico
- Genera receta digital → Farmacia lo verá
- Solicita examen de sangre → Lab lo verá
- **Consulta finalizada**

**10:00 AM - Técnico de Lab**
- Entra a Laboratorio
- Ve 1 orden nueva (la de Juan Pérez)
- Toma muestra
- Procesa examen
- Registra resultados
- Marca como "Completada"

**10:30 AM - Cajero**
- Entra a Facturación Integral
- Tab "Consultas": Ve 1 consulta pendiente (Juan Pérez)
- Clic en "Facturar" → Genera factura de $25
- Tab "Laboratorio": Ve 1 orden pendiente (Juan Pérez)
- Clic en "Facturar" → Genera factura de $50
- **Total facturado: $75**

**11:00 AM - Juan va a farmacia**
- Vendedor de farmacia
- Entra a Farmacia (o usa facturación normal)
- Busca productos de la receta
- Vende medicamentos
- Factura $30
- **Total del día ahora: $105**

**5:00 PM - Fin del día**
- Administrador revisa Dashboard Central
- Ve:
  * Ventas farmacia: $500
  * Consultas: 8
  * Exámenes: 5
  * Total facturado: $1,250

---

## ✅ Checklist de Verificación

Verifica que todo funcione:

- [ ] Puedes acceder a `/dashboard`
- [ ] Ves 4 tarjetas de módulos
- [ ] Clic en "Farmacia" te lleva a datos de farmacia
- [ ] Clic en "Clínica" muestra información de clínica
- [ ] Clic en "Laboratorio" muestra lab
- [ ] Clic en "Facturación" muestra listas de servicios
- [ ] El menú lateral tiene los nuevos módulos
- [ ] Puedes navegar entre módulos
- [ ] Las alertas se muestran correctamente

---

## 🆘 Solución de Problemas

### **No veo los módulos nuevos en el menú**
```bash
docker-compose exec app php artisan optimize:clear
```

### **No aparecen las opciones de clínica/laboratorio**
Verifica que tu usuario tenga los permisos:
```bash
docker-compose exec app php artisan tinker
>>> auth()->user()->getAllPermissions()->pluck('name');
```

### **Error de permisos**
Re-ejecuta el comando de setup:
```bash
docker-compose exec app php artisan setup:modulos-integrados --assign-admin
```

---

## 📞 Soporte

Si necesitas ayuda, revisa:
- `SISTEMA_MODULAR_INTEGRADO.md` - Documentación técnica completa
- `MODULOS_CLINICA_LABORATORIO_README.md` - Detalles de implementación
- `DASHBOARD_INTEGRADO_README.md` - Info del dashboard

---

## 🎉 ¡Listo para Usar!

El sistema está **100% funcional** con:

✅ **3 Módulos Independientes** (Farmacia, Clínica, Laboratorio)  
✅ **1 Centro de Control** (Dashboard elegante)  
✅ **1 Sistema de Facturación** (Centralizado)  
✅ **51 Permisos Creados** (Control total)  
✅ **Diseño Profesional** (Interfaz moderna)  
✅ **Alertas Inteligentes** (Notificaciones visuales)  

---

**¡Disfruta del nuevo sistema! 🚀**

*Versión 3.0 - Sistema Modular Integrado*  
*Noviembre 19, 2025*

