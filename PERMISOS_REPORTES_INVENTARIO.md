# Permisos para Nuevos Reportes de Inventario

## Descripción
Este documento explica cómo crear y asignar los permisos necesarios para los nuevos reportes de inventario:
- **Movimientos de Inventario**: Reporte resumido de múltiples productos
- **Kardex**: Reporte detallado cronológico de un producto específico

## Permisos Creados

Los siguientes permisos han sido agregados al sistema:

### 1. Reporte de Movimientos de Inventario
- `report.inventory-movements` - Ver reporte de movimientos de inventario
- `report.inventory-movements-search` - Buscar en reporte de movimientos de inventario

### 2. Reporte Kardex
- `report.inventory-kardex` - Ver Kardex de inventario por producto

## Cómo Crear los Permisos en la Base de Datos

### Opción 1: Usar el Endpoint del Sistema (Recomendado)

El sistema tiene un endpoint que crea automáticamente todos los permisos de reportes:

```bash
# Usando curl
curl -X POST http://tu-dominio.com/create-reports-permissions \
  -H "Content-Type: application/json"

# O desde el navegador (si estás autenticado)
# Visita: http://tu-dominio.com/create-reports-permissions
```

Este endpoint:
- ✅ Crea todos los permisos de reportes (incluyendo los nuevos)
- ✅ Verifica si ya existen antes de crearlos
- ✅ Devuelve un JSON con los resultados

**Respuesta esperada:**
```json
{
  "success": true,
  "message": "Permisos de reportes procesados correctamente",
  "created": [
    "report.inventory-movements",
    "report.inventory-kardex",
    "report.inventory-movements-search"
  ],
  "existing": ["report.sales", "report.purchases", ...],
  "total_created": 3,
  "total_existing": 17
}
```

### Opción 2: Crear Manualmente con SQL

Si prefieres ejecutar SQL directamente:

```sql
-- Insertar permisos para Movimientos de Inventario
INSERT INTO permissions (name, guard_name, created_at, updated_at) 
VALUES 
  ('report.inventory-movements', 'web', NOW(), NOW()),
  ('report.inventory-movements-search', 'web', NOW(), NOW()),
  ('report.inventory-kardex', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE name = name;
```

### Opción 3: Usar Laravel Tinker

Desde la terminal del servidor:

```bash
php artisan tinker
```

Luego ejecuta:

```php
use Spatie\Permission\Models\Permission;

// Crear permisos
Permission::create(['name' => 'report.inventory-movements', 'guard_name' => 'web']);
Permission::create(['name' => 'report.inventory-movements-search', 'guard_name' => 'web']);
Permission::create(['name' => 'report.inventory-kardex', 'guard_name' => 'web']);

// Verificar que se crearon
Permission::where('name', 'like', 'report.inventory-%')->get();
```

## Asignar Permisos a Roles

### Opción 1: Usar el Endpoint del Sistema

```bash
# Asignar TODOS los permisos de reportes al rol Administrador (ID: 1)
curl -X POST http://tu-dominio.com/assign-reports-permissions \
  -H "Content-Type: application/json" \
  -d '{"role_id": 1}'

# Asignar solo permisos específicos
curl -X POST http://tu-dominio.com/assign-reports-permissions \
  -H "Content-Type: application/json" \
  -d '{
    "role_id": 2,
    "permissions": [
      "report.inventory-movements",
      "report.inventory-kardex"
    ]
  }'
```

### Opción 2: Desde el Panel de Administración

1. Ve a **Administración > Roles**
2. Selecciona el rol al que deseas asignar permisos
3. En la sección de permisos, busca:
   - ✅ `report.inventory-movements`
   - ✅ `report.inventory-movements-search`
   - ✅ `report.inventory-kardex`
4. Marca los permisos deseados
5. Guarda los cambios

### Opción 3: Usar Laravel Tinker

```bash
php artisan tinker
```

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Obtener el rol (ejemplo: Administrador)
$role = Role::findByName('Administrador');

// Asignar permisos
$role->givePermissionTo([
    'report.inventory-movements',
    'report.inventory-movements-search',
    'report.inventory-kardex'
]);

// Verificar permisos asignados
$role->permissions->where('name', 'like', 'report.inventory-%')->pluck('name');
```

## Verificar Permisos

### Verificar que los Permisos Existen

```sql
SELECT * FROM permissions 
WHERE name IN (
    'report.inventory-movements',
    'report.inventory-movements-search',
    'report.inventory-kardex'
);
```

### Verificar Permisos Asignados a un Rol

```sql
SELECT 
    r.name AS rol,
    p.name AS permiso
FROM roles r
INNER JOIN role_has_permissions rhp ON r.id = rhp.role_id
INNER JOIN permissions p ON rhp.permission_id = p.id
WHERE p.name LIKE 'report.inventory-%'
ORDER BY r.name, p.name;
```

### Verificar Permisos de un Usuario Específico

```sql
SELECT 
    u.name AS usuario,
    r.name AS rol,
    p.name AS permiso
FROM users u
INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
INNER JOIN roles r ON mhr.role_id = r.id
INNER JOIN role_has_permissions rhp ON r.id = rhp.role_id
INNER JOIN permissions p ON rhp.permission_id = p.id
WHERE u.id = 1 -- Cambiar por el ID del usuario
  AND p.name LIKE 'report.inventory-%'
ORDER BY p.name;
```

## Ubicación en el Sistema

### Menú de Navegación

Los nuevos reportes aparecen en:

**Reportes (menú principal)**
- Movimientos de Inventario (`/report/inventory-movements`)
- Kardex (por producto) (`/report/inventory-kardex`)

### Control de Acceso

El sistema verifica automáticamente si el usuario tiene los permisos necesarios:

- **Sin permiso `report.inventory-movements`**: El usuario NO verá el enlace en el menú
- **Sin permiso `report.inventory-kardex`**: El usuario NO verá el enlace en el menú
- **Intento de acceso directo sin permiso**: El sistema redirigirá o mostrará error 403

## Roles Recomendados

### Administrador
✅ TODOS los permisos de reportes (incluyendo los nuevos)

### Gerente / Supervisor
✅ `report.inventory-movements` (ver resumen)
✅ `report.inventory-kardex` (ver detalles)
✅ `report.inventory-movements-search` (búsqueda)

### Empleado de Inventario
✅ `report.inventory-movements` (ver resumen)
✅ `report.inventory-kardex` (ver detalles)
⚠️ Considerar si debe tener acceso a otros reportes

### Vendedor
❌ Sin acceso a reportes de inventario (opcional según política)

### Contador
✅ `report.inventory-movements` (para control contable)
✅ Otros reportes financieros

## Solución de Problemas

### Problema: El menú no muestra los nuevos reportes

**Solución**:
1. Verifica que los permisos existan en la tabla `permissions`
2. Verifica que el rol del usuario tenga los permisos asignados
3. Cierra sesión y vuelve a iniciar sesión
4. Limpia la caché del navegador (Ctrl + F5)
5. Ejecuta: `php artisan cache:clear`

### Problema: Error 403 al acceder al reporte

**Causas posibles**:
- El usuario no tiene el permiso asignado
- El permiso existe pero no está en el rol
- Middleware de permisos está bloqueando el acceso

**Solución**:
```bash
# Verificar permisos del usuario actual
php artisan tinker

$user = User::find(ID_DEL_USUARIO);
$user->getAllPermissions();
```

### Problema: Los permisos se crearon pero no aparecen

**Solución**:
```bash
# Limpiar caché de permisos
php artisan permission:cache-reset

# Limpiar toda la caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Script Completo de Instalación

Para facilitar la instalación, aquí está un script completo:

```bash
#!/bin/bash

echo "=== Instalación de Permisos para Reportes de Inventario ==="

# Crear permisos usando el endpoint
curl -X POST http://localhost/create-reports-permissions

# Asignar al rol Administrador (ID: 1)
curl -X POST http://localhost/assign-reports-permissions \
  -H "Content-Type: application/json" \
  -d '{"role_id": 1}'

# Limpiar caché
php artisan permission:cache-reset
php artisan cache:clear

echo "=== Instalación Completada ==="
echo ""
echo "Permisos creados:"
echo "  - report.inventory-movements"
echo "  - report.inventory-movements-search"
echo "  - report.inventory-kardex"
echo ""
echo "Los usuarios con rol Administrador ya tienen acceso."
echo "Para otros roles, asigna los permisos desde el panel de administración."
```

## Archivos Relacionados

### Backend
- **PermissionController**: `app/Http/Controllers/PermissionController.php`
  - Método: `createReportsPermissions()` (línea ~638)
  - Método: `getmenujson()` (línea ~44)
  
### Rutas
- **Crear permisos**: `POST /create-reports-permissions`
- **Asignar permisos**: `POST /assign-reports-permissions`

### Base de Datos
- **Tabla permisos**: `permissions`
- **Tabla roles**: `roles`
- **Tabla relación**: `role_has_permissions`

## Notas Importantes

1. **Respaldo**: Antes de modificar permisos, haz un respaldo de la base de datos
2. **Rol Administrador**: Por defecto (ID: 1) tiene TODOS los permisos
3. **Caché**: Después de crear permisos, limpia la caché: `php artisan permission:cache-reset`
4. **Middleware**: Los permisos se verifican automáticamente en las rutas
5. **Menú Dinámico**: El menú se construye según los permisos del usuario actual

## Soporte

Si tienes problemas con los permisos:
1. Revisa los logs: `storage/logs/laravel.log`
2. Verifica la conexión a la base de datos
3. Confirma que el paquete Spatie Permission esté instalado
4. Contacta al equipo de desarrollo

---

**Última actualización**: Noviembre 2024
**Versión**: 1.0

