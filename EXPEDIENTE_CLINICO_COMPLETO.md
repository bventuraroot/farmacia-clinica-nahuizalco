# 📋 Expediente Clínico Completo - Guía de Uso

## 🎯 Descripción

El **Expediente Clínico Electrónico** es el corazón del módulo de clínica. Aquí se documenta toda la información médica del paciente desde su primera visita hasta la actualidad.

---

## 🏥 Cómo Funciona

### **1. Crear un Nuevo Paciente**

**URL:** `/patients/create`

#### **Tab 1: Datos Personales**
- ✅ Primer nombre (requerido)
- ✅ Segundo nombre (opcional)
- ✅ Primer apellido (requerido)
- ✅ Segundo apellido (opcional)
- ✅ Tipo de documento (DUI, NIT, Pasaporte, Carnet)
- ✅ Número de documento (requerido, validación única)
- ✅ Fecha de nacimiento (requerido)
  - ⭐ **Calcula edad automáticamente**
- ✅ Sexo (M/F)
- ✅ Tipo de sangre (8 opciones: A+, A-, B+, B-, AB+, AB-, O+, O-)

#### **Tab 2: Contacto**
- ✅ Teléfono principal (requerido)
- ✅ Teléfono de emergencia (opcional)
- ✅ Correo electrónico (opcional)
- ✅ Dirección completa (requerido)

#### **Tab 3: Información Médica** ⭐
- ✅ Alergias conocidas (IMPORTANTE)
- ✅ Enfermedades crónicas (IMPORTANTE)

**Al Guardar:**
- ✅ Genera código único: `PAC-XXXXX`
- ✅ Genera número de expediente: `EXP-20251119-00001`
- ✅ Estado: Activo

**Opciones después de guardar:**
- Ver lista de pacientes
- Agendar cita inmediatamente

---

### **2. Ver Expediente Clínico Completo**

**URL:** `/patients/{id}` (donde {id} es el ID del paciente)

**Desde lista de pacientes:**
- Clic en botón 👁️ "Ver" → Abre expediente completo

#### **Header del Expediente** (Gradiente morado elegante)
Muestra:
- Avatar con iniciales
- Nombre completo del paciente
- Documento de identidad
- Edad actual
- Sexo
- **Número de Expediente** (destacado)
- **Código de Paciente**

#### **Botones de Acciones Rápidas:**
- ← Volver a lista
- 📅 Agendar Cita
- 📝 Nueva Consulta
- 🧪 Solicitar Exámenes
- ✏️ Editar Datos
- 🖨️ Imprimir Expediente

---

### **3. Información Lateral (Columna Izquierda)**

#### **Card 1: Datos Personales**
- Documento
- Fecha de nacimiento
- Edad actual
- Sexo
- Tipo de sangre (destacado en rojo)

#### **Card 2: Contacto**
- Teléfono principal
- Teléfono de emergencia
- Email
- Dirección completa

#### **Card 3: Información Médica Importante** ⚠️
**Fondo rojo/amarillo para destacar:**
- **Alergias** (alerta roja)
  - Ej: "Penicilina, Mariscos"
  - Si no hay: "Sin alergias registradas"
- **Enfermedades Crónicas** (alerta amarilla)
  - Ej: "Diabetes tipo 2, Hipertensión"
  - Si no hay: "Sin enfermedades crónicas"

#### **Card 4: Estadísticas**
- Total de consultas realizadas
- Citas programadas (próximas)
- Órdenes de laboratorio
- Fecha de primera consulta

---

### **4. Historial Clínico (Columna Derecha)**

#### **4 Tabs de Información:**

##### **📝 Tab 1: Historial de Consultas** (Principal)

**Diseño Timeline (línea de tiempo):**
- Consultas ordenadas de más reciente a más antigua
- Cada consulta en una tarjeta con:

**Información mostrada:**
- 📅 Fecha y hora de la consulta
- ⏰ Hace cuánto tiempo ("Hace 3 días")
- 👨‍⚕️ Médico tratante con especialidad
- 📋 Número de consulta
- 🔍 Motivo de consulta
- 🤒 Síntomas presentados
- 💓 **Signos vitales completos:**
  - Temperatura
  - Presión arterial
  - Frecuencia cardíaca
  - Frecuencia respiratoria
  - Peso
  - Altura
  - **IMC** (destacado)
  - Saturación de oxígeno
- 🩺 **Diagnóstico** (CIE-10 + descripción)
- 🔬 Exploración física
- 💊 Plan de tratamiento
- 📜 **Receta médica** (si se generó)
- 💡 Indicaciones para el paciente
- 📆 Seguimiento requerido (si aplica)

**Botones por consulta:**
- 🖨️ Imprimir consulta
- 👁️ Ver detalle completo

**Si no hay consultas:**
- Mensaje: "Sin historial de consultas"
- Botón: "Registrar Primera Consulta"

##### **📅 Tab 2: Citas**

Tabla con todas las citas del paciente:
- Código de cita
- Fecha y hora
- Médico asignado
- Tipo de cita
- Estado (Programada, Completada, Cancelada)
- Botón para ver detalles

##### **🧪 Tab 3: Exámenes de Laboratorio**

Cards con cada orden de laboratorio:
- Número de orden
- Fecha de solicitud
- Médico solicitante
- Lista de exámenes realizados con precios
- Estado de la orden
- Total de la orden
- Botones:
  - Ver resultados
  - Imprimir orden

##### **📁 Tab 4: Documentos**

Sección para subir:
- Radiografías
- Estudios externos
- Análisis de otros laboratorios
- Documentos escaneados
- Imágenes médicas

---

## 📊 Flujo Completo de Uso

### **Escenario: Paciente Nuevo que Llega a la Clínica**

#### **Paso 1: Registro del Paciente**
1. Recepcionista va a `/patients`
2. Clic en "Nuevo Paciente"
3. Completa **3 tabs**:
   - Datos personales (Juan Pérez, 35 años)
   - Contacto (teléfono, dirección)
   - Info médica: **Alergias: "Penicilina"**, **Enfermedades: "Hipertensión"**
4. Clic en "Guardar Paciente"
5. ✅ Paciente creado con expediente `EXP-20251119-00001`

#### **Paso 2: Agendar Primera Cita**
1. En notificación, clic en "Agendar Cita"
2. O ir a `/appointments/create?patient_id=1`
3. Seleccionar médico
4. Fecha: Hoy 10:00 AM
5. Duración: 30 minutos
6. Tipo: Primera vez
7. ✅ Cita agendada

#### **Paso 3: Primera Consulta** (Doctora)
1. Doctora va a `/consultations/create?appointment_id=1`
2. **Tab Paciente:**
   - Motivo: "Control de presión arterial"
   - Síntomas: "Dolor de cabeza leve"

3. **Tab Signos Vitales:**
   - Temperatura: 36.5°C
   - Presión: **150/95** (alta)
   - FC: 80 lpm
   - FR: 16 rpm
   - Peso: 85 kg
   - Altura: 175 cm
   - ⭐ IMC: 27.76 (Sobrepeso) - **Se calcula solo**
   - SpO₂: 98%

4. **Tab Diagnóstico:**
   - Exploración: "PA elevada, paciente ansioso"
   - CIE-10: I10
   - Diagnóstico: "Hipertensión arterial esencial"

5. **Tab Tratamiento:**
   - Plan: "Tratamiento antihipertensivo, control en 15 días"
   - ✅ Generar receta
   - Receta: "Losartán 50mg - 1 tableta cada 12 horas"
   - ✅ Requiere seguimiento
   - Próximo control: 15 días

6. Clic en "Solicitar Examen de Laboratorio"
   - Nueva ventana se abre
   - Selecciona: Perfil Lipídico + Glucosa
   - Crea orden

7. Clic en "Guardar Consulta"
8. ✅ **Primera consulta documentada**

#### **Paso 4: Ver Expediente Completo**
1. Ir a `/patients/1` o clic en "Ver" desde lista
2. **Se muestra:**
   - Header elegante con foto y datos
   - Columna izquierda:
     - Datos personales
     - **Alerta roja: "Alergia a Penicilina"** ⚠️
     - **Alerta amarilla: "Hipertensión"** ⚠️
     - Estadísticas: 1 consulta, 1 orden lab
   
   - Columna derecha (Timeline):
     - **Consulta de hoy:**
       - Fecha y hora
       - Médico: Dra. María López - Medicina General
       - Motivo: Control de presión
       - Signos vitales completos (badges coloridos)
       - **Diagnóstico destacado: Hipertensión**
       - Plan de tratamiento
       - **Receta: Losartán 50mg**
       - **Próximo control: 15 días**
     
     - **Orden de laboratorio:**
       - 2 exámenes: Perfil Lipídico + Glucosa
       - Total: $16.00
       - Estado: Pendiente

3. ✅ **Todo el historial visible en un solo lugar**

#### **Paso 5: Segunda Consulta (15 días después)**
1. Doctora crea nueva consulta
2. En expediente ahora se ven:
   - **2 consultas en timeline**
   - Puede comparar signos vitales
   - Ve evolución del paciente
   - Ve si cumplió tratamiento

#### **Paso 6: Más Adelante**
El expediente va creciendo:
- 5 consultas
- 3 órdenes de lab
- 10 citas en historial
- Documentos adjuntos

**Todo visible en orden cronológico** ⭐

---

## 🎨 Características del Expediente

### **1. Timeline Visual**
- Línea de tiempo con puntos
- Consultas más recientes arriba
- Efecto hover en cada card
- Borde izquierdo de color
- Animación al pasar el mouse

### **2. Organización por Tabs**
- **Consultas**: Historial médico completo
- **Citas**: Agenda histórica
- **Laboratorio**: Todos los exámenes
- **Documentos**: Archivos adjuntos

### **3. Información Destacada**
- ⚠️ **Alergias en rojo** (para evitar errores)
- ⚠️ **Enfermedades crónicas en amarillo**
- 🩺 Signos vitales con badges coloridos
- 💊 Diagnóstico en alerta azul
- 📜 Recetas en alerta amarilla

### **4. Acciones Rápidas**
Desde el expediente puedes:
- Agendar nueva cita
- Registrar nueva consulta
- Solicitar exámenes
- Editar datos del paciente
- Imprimir expediente completo

---

## 💡 Beneficios para la Doctora

### **Vista 360° del Paciente:**
✅ Ve toda la historia clínica  
✅ No necesita buscar en papeles  
✅ Todo digital y ordenado  
✅ Acceso inmediato a consultas anteriores  

### **Información Crítica Visible:**
✅ Alergias destacadas (evita errores)  
✅ Enfermedades crónicas visibles  
✅ Tipo de sangre disponible  
✅ Teléfono de emergencia a mano  

### **Seguimiento de Pacientes:**
✅ Ve evolución en el tiempo  
✅ Compara signos vitales  
✅ Verifica si cumplió tratamientos  
✅ Revisa diagnósticos previos  

### **Documentación Completa:**
✅ Cada consulta queda registrada  
✅ Recetas digitales guardadas  
✅ Exámenes vinculados  
✅ Timeline cronológico  

---

## 📝 Qué se Documenta en Cada Consulta

### **Información Básica:**
- Fecha y hora exacta
- Médico que atendió
- Número de consulta único
- Motivo de la consulta
- Síntomas presentados

### **Signos Vitales:**
- Temperatura (°C)
- Presión arterial (mmHg)
- Frecuencia cardíaca (lpm)
- Frecuencia respiratoria (rpm)
- Peso (kg)
- Altura (cm)
- **IMC calculado automáticamente**
- Saturación de oxígeno (%)

### **Evaluación Médica:**
- Exploración física detallada
- Código CIE-10 del diagnóstico
- Diagnóstico principal (descripción)
- Diagnósticos secundarios

### **Tratamiento:**
- Plan de tratamiento completo
- Indicaciones para el paciente
- Receta médica digital (si aplica)
- Seguimiento requerido (sí/no)
- Fecha de próximo control

### **Exámenes:**
- Órdenes de laboratorio solicitadas
- Vinculadas a la consulta
- Resultados disponibles en el expediente

---

## 🔍 Cómo Ver el Historial Completo

### **Opción 1: Desde Lista de Pacientes**
1. Ir a `/patients`
2. Buscar paciente en la tabla
3. Clic en botón 👁️ "Ver"
4. ✅ Se abre expediente completo

### **Opción 2: URL Directa**
```
http://localhost:8003/patients/{id}
```

### **Opción 3: Desde una Consulta**
1. En el formulario de consulta
2. Después de guardar
3. Clic en "Ver Expediente del Paciente"

---

## 📊 Vista del Expediente

### **Diseño Visual:**

```
╔═══════════════════════════════════════════════════════════════╗
║  👤 JUAN PÉREZ GARCÍA                                         ║
║  📄 DUI: 00000000-0  │  📅 35 años  │  ♂️ Masculino          ║
║  📋 Expediente: EXP-20251119-00001                           ║
║                                                               ║
║  [← Volver] [📅 Cita] [📝 Consulta] [🧪 Lab] [✏️ Edit] [🖨️]  ║
╚═══════════════════════════════════════════════════════════════╝

┌─────────────────────┬──────────────────────────────────────────┐
│ DATOS PERSONALES    │  HISTORIAL CLÍNICO                       │
│                     │                                          │
│ 📇 Documento        │  [Consultas] [Citas] [Lab] [Docs]       │
│ 📅 Fecha Nac.       │                                          │
│ 🩸 Tipo Sangre: O+  │  ═══════════════════════════════════    │
│                     │  ● 19/11/2025 10:00 AM                  │
│ ☎️ CONTACTO         │    Dr. María López - Med. General       │
│ 📞 7000-0000        │    CONS-20251119-00001                  │
│ 📧 email@mail.com   │                                          │
│                     │    🔍 Motivo: Control presión            │
│ ⚠️ ALERGIAS         │    🤒 Síntomas: Dolor cabeza             │
│ 🔴 Penicilina       │                                          │
│                     │    💓 Signos Vitales:                    │
│ ⚠️ ENF. CRÓNICAS    │    [36.5°C] [150/95] [80lpm] [IMC:27.7] │
│ 🟡 Hipertensión     │                                          │
│                     │    🩺 Diagnóstico: Hipertensión (I10)   │
│ 📊 ESTADÍSTICAS     │    💊 Receta: Losartán 50mg              │
│ Consultas: 5        │    📆 Próximo control: 15 días          │
│ Citas: 8            │                                          │
│ Órdenes Lab: 3      │  ═══════════════════════════════════    │
│                     │  ● 04/11/2025 09:00 AM                  │
│                     │    [Consulta anterior...]                │
│                     │                                          │
└─────────────────────┴──────────────────────────────────────────┘
```

---

## 🎯 Información Clave del Expediente

### **✅ Siempre Visible:**
1. **Alergias** - Destacadas en rojo para evitar errores
2. **Enfermedades crónicas** - Contexto médico importante
3. **Tipo de sangre** - Emergencias
4. **Edad actual** - Se calcula automáticamente
5. **Contacto de emergencia** - Acceso rápido

### **✅ Historial Completo:**
1. **Todas las consultas** - Orden cronológico
2. **Todos los diagnósticos** - Evolución del paciente
3. **Todos los tratamientos** - Qué se ha recetado
4. **Todos los exámenes** - Resultados de laboratorio
5. **Todas las citas** - Historial de agenda

### **✅ Datos para Análisis:**
1. Evolución de signos vitales
2. Diagnósticos recurrentes
3. Tratamientos previos
4. Alergias a medicamentos
5. Respuesta a tratamientos

---

## 🔐 Confidencialidad

El expediente clínico está protegido:
- ✅ Solo personal autorizado puede acceder
- ✅ Se requiere login
- ✅ Sistema de permisos activo
- ✅ Información sensible protegida
- ✅ Cumple con normativas médicas

**Mensaje en formulario:**
"Información protegida por confidencialidad médica"

---

## 📱 Acceso Rápido al Expediente

### **Desde Diferentes Lugares:**

#### 1. **Lista de Pacientes:**
```
/patients → Clic en 👁️ Ver
```

#### 2. **Durante una Consulta:**
```
/consultations/create → Link "Ver expediente"
```

#### 3. **Desde Dashboard:**
```
/dashboard-clinica → Buscar paciente
```

#### 4. **URL Directa:**
```
/patients/1
/patients/2
/patients/3
```

---

## 🎨 Elementos Visuales del Expediente

### **Colores por Tipo de Información:**
- 🔴 **Rojo**: Alergias, temperatura, frecuencia cardíaca
- 🟡 **Amarillo**: Enfermedades crónicas, recetas, peso
- 🔵 **Azul**: Diagnósticos, presión arterial
- 🟢 **Verde**: Tratamientos, altura, estado activo
- 🟣 **Morado**: Header principal

### **Iconos por Sección:**
- 👤 Datos personales
- 📞 Contacto
- ⚠️ Información médica crítica
- 📊 Estadísticas
- 📝 Consultas
- 📅 Citas
- 🧪 Laboratorio
- 📁 Documentos

---

## 💊 Ejemplo de Consulta en el Historial

```
╔════════════════════════════════════════════════════════╗
║  📅 19/11/2025 10:30 AM                                ║
║  ⏰ Hace 2 horas                          ✅ Finalizada ║
╠════════════════════════════════════════════════════════╣
║                                                        ║
║  👨‍⚕️ Dra. María López Rodríguez                        ║
║     Medicina General                                   ║
║                                                        ║
║  📋 CONS-20251119-00005                                ║
║                                                        ║
║  🔍 Motivo: Control de hipertensión                    ║
║  🤒 Síntomas: Dolor de cabeza leve                     ║
║                                                        ║
║  💓 SIGNOS VITALES:                                    ║
║  [36.5°C] [150/95] [80 lpm] [16 rpm]                  ║
║  [85 kg] [175 cm] [IMC: 27.76] [SpO₂: 98%]           ║
║                                                        ║
║  🩺 DIAGNÓSTICO:                                       ║
║  ┌──────────────────────────────────────────────┐     ║
║  │ [CIE-10: I10]                                │     ║
║  │ Hipertensión arterial esencial               │     ║
║  └──────────────────────────────────────────────┘     ║
║                                                        ║
║  💊 PLAN:                                              ║
║  Tratamiento antihipertensivo, control en 15 días     ║
║                                                        ║
║  📜 RECETA:                                            ║
║  ┌──────────────────────────────────────────────┐     ║
║  │ Losartán 50mg                                │     ║
║  │ 1 tableta cada 12 horas                      │     ║
║  │ Duración: 30 días                            │     ║
║  └──────────────────────────────────────────────┘     ║
║                                                        ║
║  📆 SEGUIMIENTO:                                       ║
║  ⚠️ Próximo control: 04/12/2025                        ║
║                                                        ║
║  [🖨️ Imprimir]  [👁️ Ver Detalle]                       ║
╚════════════════════════════════════════════════════════╝
```

---

## 🎯 Para el Cliente

### **Lo que puede hacer:**

1. ✅ **Registrar pacientes** con toda su información
2. ✅ **Documentar cada consulta** completamente
3. ✅ **Ver historial completo** en orden cronológico
4. ✅ **Consultas anteriores** siempre disponibles
5. ✅ **Alertas médicas** (alergias, enfermedades)
6. ✅ **Seguimiento de tratamientos**
7. ✅ **Evolución del paciente** visible
8. ✅ **Imprimir expedientes** para archivo físico

### **Ventajas:**

- 📱 **Acceso rápido** desde cualquier módulo
- 🔍 **Búsqueda fácil** de información
- 📊 **Estadísticas automáticas**
- ⚠️ **Alertas de seguridad** (alergias)
- 💾 **Respaldo digital** automático
- 📈 **Análisis de evolución**

---

## 🚀 Prueba el Expediente AHORA

### **1. Crear Paciente:**
```
http://localhost:8003/patients/create
```
Completa formulario y guarda

### **2. Crear Consulta:**
```
http://localhost:8003/consultations/create
```
Documenta primera consulta

### **3. Ver Expediente:**
```
http://localhost:8003/patients/1
```
✅ **Ve toda la información organizada**

---

## 🎉 RESULTADO

Un **Expediente Clínico Electrónico Completo** que:

✅ Documenta todo  
✅ Organiza cronológicamente  
✅ Destaca información crítica  
✅ Facilita el trabajo médico  
✅ Cumple normativas  
✅ Es fácil de usar  
✅ Se ve profesional  

**¡La doctora va a AMAR trabajar con este expediente!** 💚

---

## 📞 Acceso Directo

```
http://localhost:8003/patients
```

1. Clic en "Nuevo Paciente"
2. Registra paciente
3. Clic en "Ver" (👁️)
4. ✅ Expediente completo con timeline

**¡Pruébalo ahora!** 🚀

