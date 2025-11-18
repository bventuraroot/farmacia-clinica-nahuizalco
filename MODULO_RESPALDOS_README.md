# 📊 Módulo de Respaldos de Base de Datos

## 🎯 Descripción
El módulo de respaldos permite crear, gestionar y descargar respaldos completos de la base de datos del sistema de manera segura y eficiente.

## ✨ Características Principales

### 🔧 Funcionalidades Web
- **Panel de Control**: Interfaz intuitiva para gestionar respaldos
- **Crear Respaldos**: Generar respaldos con opciones de compresión
- **Descargar**: Descargar archivos de respaldo directamente
- **Restaurar**: Restaurar la base de datos desde un respaldo
- **Eliminar**: Gestionar el espacio eliminando respaldos antiguos
- **Estadísticas**: Ver información detallada sobre respaldos

### 🛠️ Comandos Artisan
- `php artisan backup:database` - Crear respaldo manual
- `php artisan backup:restore` - Restaurar desde respaldo
- `php artisan backup:list` - Listar respaldos disponibles
- `php artisan backup:scheduled` - Ejecutar respaldos programados

## 🚀 Instalación y Configuración

### 1. Configurar Permisos
```bash
# Ejecutar el script de configuración automática
php scripts/setup_backup_permissions.php
```

### 2. Configuración Manual de Permisos
Si prefieres configurar los permisos manualmente:

1. Ve a **Administración > Permisos**
2. Haz clic en "Crear Permisos de Respaldos"
3. Asigna los permisos a los roles correspondientes

### 3. Permisos Disponibles
- `backups.index` - Ver lista de respaldos
- `backups.create` - Crear respaldos
- `backups.download` - Descargar respaldos
- `backups.destroy` - Eliminar respaldos
- `backups.restore` - Restaurar respaldos
- `backups.list` - Listar respaldos
- `backups.stats` - Ver estadísticas
- `backups.scheduled` - Gestionar respaldos programados
- `backups.automated` - Configurar respaldos automáticos
- `backups.compression` - Configurar compresión
- `backups.retention` - Gestionar política de retención
- `backups.notifications` - Configurar notificaciones

## 📖 Uso del Módulo

### Acceso Web
1. Navega a **Respaldos** en el menú principal
2. Verás el panel de control con estadísticas
3. Usa los controles para gestionar respaldos

### Crear un Respaldo
1. En el panel de control, configura las opciones:
   - ✅ **Comprimir**: Reduce el tamaño del archivo
   - 📊 **Mantener**: Número de respaldos a conservar
2. Haz clic en **"Crear Nuevo Respaldo"**
3. Espera a que se complete el proceso

### Descargar un Respaldo
1. En la lista de respaldos, haz clic en el botón de descarga 📥
2. El archivo se descargará automáticamente

### Restaurar un Respaldo
⚠️ **ADVERTENCIA**: Esta acción sobrescribirá la base de datos actual.

1. Haz clic en el botón de restaurar 🔄
2. Confirma la acción en el modal
3. Espera a que se complete la restauración

### Eliminar un Respaldo
1. Haz clic en el botón de eliminar 🗑️
2. Confirma la eliminación
3. El archivo será eliminado permanentemente

## 🔧 Configuración Avanzada

### Respaldos Programados
Para configurar respaldos automáticos:

1. Edita el archivo `app/Console/Kernel.php`
2. Agrega la tarea programada:
```php
$schedule->command('backup:scheduled')->daily();
```

### Configuración de Compresión
Los respaldos se pueden comprimir usando gzip para reducir el tamaño:
- Archivos sin comprimir: `.sql`
- Archivos comprimidos: `.sql.gz`

### Política de Retención
El sistema mantiene automáticamente solo los últimos N respaldos configurados, eliminando los más antiguos.

## 📁 Ubicación de Archivos
- **Directorio**: `storage/app/backups/`
- **Formato**: `backup_{database}_{fecha}_{hora}.sql[.gz]`
- **Ejemplo**: `backup_agroservicio_2024-01-15_14-30-25.sql.gz`

## 🔒 Seguridad

### Validaciones
- Solo archivos de respaldo válidos pueden ser eliminados
- Confirmación requerida para operaciones destructivas
- Logs de todas las operaciones realizadas

### Permisos
- Acceso controlado por sistema de permisos
- Diferentes niveles de acceso según el rol
- Protección contra acceso no autorizado

## 🐛 Solución de Problemas

### Error: "mysqldump: command not found"
En algunos servidores, `mysqldump` puede no estar disponible. Verifica:
1. Que MySQL esté instalado correctamente
2. Que `mysqldump` esté en el PATH del sistema
3. Contacta al administrador del servidor

### Error: "Permission denied"
Verifica que:
1. El directorio `storage/app/backups/` tenga permisos de escritura
2. El usuario web tenga acceso a crear archivos
3. Los permisos del sistema estén configurados correctamente

### Error: "Database connection failed"
Verifica la configuración de base de datos en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

## 📊 Monitoreo y Logs

### Logs del Sistema
Los logs se guardan en `storage/logs/laravel.log` con información sobre:
- Creación de respaldos
- Errores durante el proceso
- Operaciones de restauración
- Eliminación de archivos

### Estadísticas Disponibles
- Total de respaldos creados
- Espacio total utilizado
- Número de respaldos comprimidos
- Fecha del último respaldo

## 🔄 Mantenimiento

### Limpieza Automática
El sistema elimina automáticamente respaldos antiguos según la política configurada.

### Limpieza Manual
Si necesitas liberar espacio manualmente:
1. Ve a la lista de respaldos
2. Elimina los respaldos más antiguos
3. O ajusta la política de retención

## 📞 Soporte

Para problemas o preguntas sobre el módulo de respaldos:
1. Revisa los logs del sistema
2. Verifica la configuración de permisos
3. Consulta la documentación técnica
4. Contacta al administrador del sistema

---

**⚠️ Importante**: Siempre prueba la restauración de respaldos en un entorno de desarrollo antes de usarla en producción.
