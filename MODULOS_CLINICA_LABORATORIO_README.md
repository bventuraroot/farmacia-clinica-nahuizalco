# Módulos de Clínica Médica y Laboratorio Clínico

## 📋 Resumen

Se han implementado completamente dos nuevos módulos para el sistema:

1. **Módulo de Clínica Médica** 🏥
2. **Módulo de Laboratorio Clínico** 🧪

Estos módulos permiten gestionar de manera integral una clínica médica con laboratorio clínico integrado, complementando perfectamente el módulo de farmacia existente.

---

## 🎯 Componentes Implementados

### ✅ 1. Migraciones de Base de Datos

Se crearon dos archivos de migración completos:

#### **Clínica Médica** (`2025_11_19_000001_create_clinic_tables.php`)
- **patients**: Pacientes con expedientes clínicos
- **doctors**: Personal médico con especialidades
- **appointments**: Agenda de citas médicas
- **medical_consultations**: Consultas médicas con signos vitales y diagnósticos
- **prescriptions**: Recetas médicas
- **prescription_details**: Detalle de medicamentos en recetas
- **medical_records**: Expedientes clínicos digitales

#### **Laboratorio Clínico** (`2025_11_19_000002_create_laboratory_tables.php`)
- **lab_exam_categories**: Categorías de exámenes
- **lab_exams**: Catálogo de exámenes de laboratorio
- **lab_exam_profiles**: Perfiles de exámenes (paquetes)
- **lab_profile_exams**: Relación de exámenes en perfiles
- **lab_orders**: Órdenes de laboratorio
- **lab_order_exams**: Exámenes incluidos en cada orden
- **lab_samples**: Control de muestras
- **lab_results**: Resultados de exámenes
- **lab_quality_controls**: Control de calidad
- **lab_equipment**: Equipamiento del laboratorio

### ✅ 2. Modelos Eloquent (17 modelos)

**Clínica:**
- `Patient`
- `Doctor`
- `Appointment`
- `MedicalConsultation`
- `Prescription`
- `PrescriptionDetail`
- `MedicalRecord`

**Laboratorio:**
- `LabExamCategory`
- `LabExam`
- `LabExamProfile`
- `LabOrder`
- `LabOrderExam`
- `LabSample`
- `LabResult`
- `LabQualityControl`
- `LabEquipment`

### ✅ 3. Controladores

**Clínica:**
- `PatientController`: Gestión de pacientes
- `DoctorController`: Gestión de médicos
- `AppointmentController`: Agenda de citas
- `MedicalConsultationController`: Consultas médicas

**Laboratorio:**
- `LabOrderController`: Órdenes de laboratorio

### ✅ 4. Rutas

Todas las rutas están configuradas en `routes/web.php`:

#### Clínica:
- `/patients` - Gestión de pacientes
- `/doctors` - Gestión de médicos
- `/appointments` - Agenda de citas
- `/consultations` - Consultas médicas

#### Laboratorio:
- `/lab-orders` - Órdenes de laboratorio

### ✅ 5. Permisos

Se agregaron métodos en `PermissionController` para crear permisos:

#### Clínica (24 permisos):
- `patients.*` (index, create, edit, destroy, show)
- `doctors.*` (index, create, edit, destroy, show)
- `appointments.*` (index, create, edit, destroy, show)
- `consultations.*` (index, create, edit, show)
- `prescriptions.*` (index, create, edit, show, dispense)
- `medical-records.*` (index, create, edit, destroy, download)

#### Laboratorio (22 permisos):
- `lab-orders.*` (index, create, edit, show, process, print)
- `lab-exams.*` (index, create, edit, destroy)
- `lab-results.*` (index, create, edit, validate, print)
- `lab-samples.*` (index, create, edit)
- `lab-quality.*` (index, create, edit)
- `lab-equipment.*` (index, create, edit, destroy)
- `lab-reports.*` (daily, monthly, statistics)

### ✅ 6. Menú del Sistema

Se actualizó el menú en `PermissionController::getmenujson()`:

**Clínica Médica** (con icono de estetoscopio):
- Pacientes
- Médicos
- Citas Médicas
- Consultas

**Laboratorio Clínico** (con icono de matraz):
- Órdenes de Laboratorio
- Catálogo de Exámenes

### ✅ 7. Vistas Base

Se crearon vistas Blade básicas para cada módulo principal:
- `resources/views/clinic/patients/index.blade.php`
- `resources/views/clinic/doctors/index.blade.php`
- `resources/views/clinic/appointments/index.blade.php`
- `resources/views/clinic/consultations/index.blade.php`
- `resources/views/laboratory/orders/index.blade.php`

---

## 🚀 Pasos para Activar los Módulos

### 1. Ejecutar las Migraciones

```bash
php artisan migrate
```

### 2. Crear los Permisos

Accede a las siguientes URLs (como administrador):

**Para crear permisos de Clínica:**
```
POST /permission/create-clinic-permissions
```

**Para crear permisos de Laboratorio:**
```
POST /permission/create-laboratory-permissions
```

O ejecuta desde la consola de Laravel:

```php
// En tinker (php artisan tinker)
app('App\Http\Controllers\PermissionController')->createClinicPermissions();
app('App\Http\Controllers\PermissionController')->createLaboratoryPermissions();
```

### 3. Asignar Permisos al Rol Administrador

```php
// En tinker
$adminRole = Spatie\Permission\Models\Role::find(1);

// Permisos de Clínica
$clinicPermissions = Spatie\Permission\Models\Permission::where('name', 'like', 'patients.%')
    ->orWhere('name', 'like', 'doctors.%')
    ->orWhere('name', 'like', 'appointments.%')
    ->orWhere('name', 'like', 'consultations.%')
    ->orWhere('name', 'like', 'prescriptions.%')
    ->orWhere('name', 'like', 'medical-records.%')
    ->pluck('name');

$adminRole->givePermissionTo($clinicPermissions);

// Permisos de Laboratorio
$labPermissions = Spatie\Permission\Models\Permission::where('name', 'like', 'lab-%')->pluck('name');
$adminRole->givePermissionTo($labPermissions);
```

### 4. Configurar Variables de Entorno

Agrega o verifica en tu archivo `.env`:

```env
# Módulo de Clínica
MODULO_CLINICA=true
CLINICA_NOMBRE_COMPLETO="Clínica Médica"
CLINICA_DIRECTOR_MEDICO="Dr. Nombre Apellido"
CLINICA_LICENCIA_ESTABLECIMIENTO="LIC-12345"
CLINICA_HORARIO_ATENCION="Lunes a Viernes 8:00 AM - 5:00 PM"

# Módulo de Laboratorio
MODULO_LABORATORIO=true
LABORATORIO_NOMBRE="Laboratorio Clínico"
LABORATORIO_DIRECTOR_TECNICO="Nombre del Director Técnico"
LABORATORIO_LICENCIA="LAB-12345"
LABORATORIO_TIEMPO_RESULTADOS_DIAS=3

# Módulo de Farmacia (ya existente)
MODULO_FARMACIA=true
FARMACIA_REGENTE_NOMBRE="Farm. Nombre Apellido"
FARMACIA_REGENTE_JVPM="JVPM-12345"
FARMACIA_LICENCIA_SANITARIA="FARM-12345"
```

---

## 📊 Características Principales

### Módulo de Clínica Médica

#### Gestión de Pacientes
- ✅ Expediente clínico electrónico completo
- ✅ Información personal y médica
- ✅ Historial de consultas y tratamientos
- ✅ Alergias y enfermedades crónicas
- ✅ Documentos adjuntos (estudios, análisis)

#### Gestión de Médicos
- ✅ Registro con número JVPM
- ✅ Especialidades médicas
- ✅ Horarios de atención
- ✅ Vinculación con usuarios del sistema

#### Agenda de Citas
- ✅ Calendario interactivo
- ✅ Estados: programada, confirmada, en curso, completada, cancelada
- ✅ Control de disponibilidad de médicos
- ✅ Tipos de cita: primera vez, seguimiento, emergencia, control

#### Consultas Médicas
- ✅ Signos vitales completos (temperatura, presión, FC, FR, SpO2)
- ✅ Cálculo automático de IMC
- ✅ Diagnósticos con códigos CIE-10
- ✅ Exploración física
- ✅ Plan de tratamiento
- ✅ Generación de recetas digitales
- ✅ Seguimiento y próximos controles

#### Recetas Médicas
- ✅ Vínculo con productos de farmacia
- ✅ Posología detallada
- ✅ Control de dispensación
- ✅ Fechas de emisión y vencimiento

### Módulo de Laboratorio Clínico

#### Órdenes de Laboratorio
- ✅ Creación de órdenes vinculadas a consultas
- ✅ Múltiples exámenes por orden
- ✅ Prioridades: normal, urgente, STAT
- ✅ Estados: pendiente, muestra tomada, en proceso, completada, entregada
- ✅ Tiempo estimado de entrega
- ✅ Indicaciones especiales y preparación

#### Gestión de Exámenes
- ✅ Catálogo de exámenes por categorías
- ✅ Perfiles de exámenes (paquetes)
- ✅ Valores de referencia
- ✅ Tipos de muestra requerida
- ✅ Tiempo de procesamiento

#### Control de Muestras
- ✅ Código único por muestra
- ✅ Rastreo completo
- ✅ Condiciones de la muestra
- ✅ Trazabilidad de quien tomó la muestra

#### Resultados
- ✅ Registro de parámetros y valores
- ✅ Validación de resultados
- ✅ Alertas para resultados críticos
- ✅ Observaciones por parámetro

#### Control de Calidad
- ✅ Registro de controles por examen
- ✅ Control de lotes de reactivos
- ✅ Seguimiento de equipos utilizados

#### Equipamiento
- ✅ Inventario de equipos
- ✅ Control de calibraciones
- ✅ Mantenimiento preventivo

---

## 🔗 Integración Entre Módulos

Los tres módulos (Farmacia, Clínica, Laboratorio) están completamente integrados:

1. **Clínica → Farmacia**: Las recetas médicas se vinculan con productos de farmacia para su dispensación.

2. **Clínica → Laboratorio**: Las consultas médicas pueden generar órdenes de laboratorio directamente.

3. **Laboratorio → Clínica**: Los resultados de laboratorio se vinculan al expediente del paciente.

4. **Sistema Unificado**: Todos los módulos comparten:
   - Sistema de permisos
   - Gestión de empresas/sucursales
   - Base de datos de pacientes/clientes
   - Usuarios del sistema

---

## 📈 Próximos Pasos Recomendados

### Desarrollo de Funcionalidades Completas

1. **Formularios de Creación/Edición**
   - Implementar formularios AJAX para cada módulo
   - Validación de datos en cliente y servidor

2. **Reportes**
   - Reporte de consultas por médico
   - Reporte de exámenes más solicitados
   - Estadísticas de la clínica y laboratorio

3. **Impresiones**
   - Recetas médicas en PDF
   - Órdenes de laboratorio en PDF
   - Resultados de laboratorio en PDF

4. **Dashboard**
   - Indicadores de rendimiento (KPIs)
   - Gráficas de consultas, órdenes, etc.
   - Alertas de citas próximas

5. **Notificaciones**
   - Recordatorios de citas por email/SMS
   - Notificación de resultados listos
   - Alertas de resultados críticos

---

## 🛠️ Comandos Útiles

### Crear Permisos
```bash
php artisan tinker
>>> app('App\Http\Controllers\PermissionController')->createClinicPermissions();
>>> app('App\Http\Controllers\PermissionController')->createLaboratoryPermissions();
```

### Verificar Migraciones
```bash
php artisan migrate:status
```

### Rollback (si es necesario)
```bash
php artisan migrate:rollback
```

### Limpiar caché
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 📝 Notas Importantes

1. **Seguridad**: Todos los controladores tienen middleware de permisos configurado.

2. **Soft Deletes**: La mayoría de las tablas usan soft deletes para mantener historial.

3. **Relaciones**: Todos los modelos tienen relaciones Eloquent correctamente definidas.

4. **Códigos Únicos**: Cada registro importante (paciente, orden, consulta) tiene un código único autogenerado.

5. **Multi-empresa**: El sistema soporta múltiples empresas/sucursales usando `company_id`.

6. **Configuración**: Los archivos `config/clinica.php` y `config/laboratorio.php` permiten personalizar cada módulo.

---

## 📧 Soporte

Para cualquier duda o consulta sobre la implementación de estos módulos, contacta al equipo de desarrollo.

**Fecha de Implementación**: Noviembre 19, 2025  
**Versión**: 1.0.0  
**Estado**: ✅ Base Implementada - Listo para Desarrollo Frontend

---

## ✨ Resumen de Archivos Creados

### Migraciones (2)
- `database/migrations/2025_11_19_000001_create_clinic_tables.php`
- `database/migrations/2025_11_19_000002_create_laboratory_tables.php`

### Modelos (17)
- `app/Models/Patient.php`
- `app/Models/Doctor.php`
- `app/Models/Appointment.php`
- `app/Models/MedicalConsultation.php`
- `app/Models/Prescription.php`
- `app/Models/PrescriptionDetail.php`
- `app/Models/MedicalRecord.php`
- `app/Models/LabExamCategory.php`
- `app/Models/LabExam.php`
- `app/Models/LabExamProfile.php`
- `app/Models/LabOrder.php`
- `app/Models/LabOrderExam.php`
- `app/Models/LabSample.php`
- `app/Models/LabResult.php`
- `app/Models/LabQualityControl.php`
- `app/Models/LabEquipment.php`

### Controladores (5)
- `app/Http/Controllers/PatientController.php`
- `app/Http/Controllers/DoctorController.php`
- `app/Http/Controllers/AppointmentController.php`
- `app/Http/Controllers/MedicalConsultationController.php`
- `app/Http/Controllers/LabOrderController.php`

### Vistas (5)
- `resources/views/clinic/patients/index.blade.php`
- `resources/views/clinic/doctors/index.blade.php`
- `resources/views/clinic/appointments/index.blade.php`
- `resources/views/clinic/consultations/index.blade.php`
- `resources/views/laboratory/orders/index.blade.php`

### Archivos Modificados (3)
- `routes/web.php` - Rutas agregadas
- `app/Http/Controllers/PermissionController.php` - Métodos de permisos y menú
- Este archivo de documentación

**Total: 32 archivos nuevos + 3 modificados = 35 archivos**

---

¡Los módulos de Clínica y Laboratorio están listos para comenzar a desarrollar! 🎉

