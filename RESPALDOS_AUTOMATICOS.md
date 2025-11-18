# 🤖 Configuración de Respaldos Automáticos

## 📋 **Comando Creado:**
- `php artisan backup:auto` - Comando para respaldos automáticos

## ⏰ **Programación Configurada:**

### 1. **Respaldo Diario**
- **Horario:** Todos los días a las 2:00 AM
- **Configuración:** Comprimido, mantiene 7 respaldos
- **Comando:** `backup:auto --compress --keep=7`

### 2. **Respaldo Semanal**
- **Horario:** Domingos a las 3:00 AM
- **Configuración:** Sin comprimir, mantiene 4 respaldos
- **Comando:** `backup:auto --keep=4`

### 3. **Respaldo Mensual**
- **Horario:** Día 1 de cada mes a las 4:00 AM
- **Configuración:** Comprimido, mantiene 12 respaldos
- **Comando:** `backup:auto --compress --keep=12`

## 🔧 **Para Activar en cPanel:**

### **Opción 1: Cron Job en cPanel**
1. Ve a **Cron Jobs** en tu cPanel
2. Agrega esta línea:
```bash
0 2 * * * cd /home/tuusuario/public_html && php artisan schedule:run
```

### **Opción 2: Cron Job Manual**
```bash
# Respaldo diario a las 2:00 AM
0 2 * * * cd /home/tuusuario/public_html && php artisan backup:auto --compress --keep=7

# Respaldo semanal domingos a las 3:00 AM
0 3 * * 0 cd /home/tuusuario/public_html && php artisan backup:auto --keep=4

# Respaldo mensual día 1 a las 4:00 AM
0 4 1 * * cd /home/tuusuario/public_html && php artisan backup:auto --compress --keep=12
```

## 🧪 **Para Probar Manualmente:**

### **Probar el comando:**
```bash
php artisan backup:auto
```

### **Probar con opciones:**
```bash
# Respaldo comprimido
php artisan backup:auto --compress

# Respaldo manteniendo solo 3 archivos
php artisan backup:auto --keep=3

# Respaldo comprimido manteniendo 10 archivos
php artisan backup:auto --compress --keep=10
```

### **Verificar programación:**
```bash
php artisan schedule:list
```

## 📊 **Monitoreo:**

### **Ver logs:**
```bash
tail -f storage/logs/laravel.log | grep "Respaldo automático"
```

### **Verificar respaldos:**
```bash
ls -la storage/app/backups/
```

## ⚙️ **Configuración Avanzada:**

### **Cambiar horarios:**
Edita `app/Console/Kernel.php`:
```php
// Respaldo cada 6 horas
$schedule->command('backup:auto --compress --keep=7')
         ->everySixHours();

// Respaldo solo en días laborables
$schedule->command('backup:auto --compress --keep=7')
         ->weekdays()
         ->dailyAt('02:00');
```

### **Notificaciones por email:**
```php
$schedule->command('backup:auto --compress --keep=7')
         ->dailyAt('02:00')
         ->emailOutputOnFailure('admin@tudominio.com');
```

## 🚨 **Importante para cPanel:**

1. **Verificar que `mysqldump` esté disponible:**
```bash
which mysqldump
```

2. **Verificar permisos del directorio:**
```bash
chmod 755 storage/app/backups/
```

3. **Probar el cron job:**
```bash
php artisan schedule:run
```

## 📈 **Beneficios:**

- ✅ **Respaldos automáticos** sin intervención manual
- ✅ **Múltiples frecuencias** (diario, semanal, mensual)
- ✅ **Compresión opcional** para ahorrar espacio
- ✅ **Limpieza automática** de archivos antiguos
- ✅ **Logs detallados** para monitoreo
- ✅ **Sin solapamiento** de procesos

---

**¡Con esta configuración tendrás respaldos automáticos funcionando 24/7!**
