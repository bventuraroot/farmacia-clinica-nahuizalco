# 🏥 Sistema de Farmacia, Clínica y Laboratorio Clínico

## 📋 Descripción del Proyecto

Sistema integral para la gestión de **Farmacia, Clínica Médica y Laboratorio Clínico** desarrollado en Laravel 10 con PHP 8.2. Este sistema permite la administración completa de:

- 💊 **Farmacia**: Control de inventario, ventas, control de lotes, vencimientos, medicamentos controlados
- 🏥 **Clínica**: Gestión de citas, consultas médicas, expedientes electrónicos, recetas digitales
- 🔬 **Laboratorio Clínico**: Gestión de exámenes, muestras, resultados, perfiles de laboratorio

---

## 🚀 Inicio Rápido con Docker

### Requisitos Previos

- Docker Desktop instalado
- Docker Compose instalado
- Git (opcional)

### Instalación Automática

```bash
# 1. Dar permisos de ejecución al script
chmod +x docker-start.sh

# 2. Ejecutar el script de configuración
./docker-start.sh
```

El script automático realizará:
- ✅ Verificación de Docker
- ✅ Creación de directorios necesarios
- ✅ Configuración del archivo .env
- ✅ Construcción de contenedores
- ✅ Instalación de dependencias
- ✅ Ejecución de migraciones
- ✅ Carga de datos iniciales
- ✅ Configuración de permisos

---

## 🌐 Accesos al Sistema

Una vez instalado, el sistema estará disponible en:

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| **Aplicación Principal** | http://localhost:8003 | Ver seeders para usuarios |
| **PHPMyAdmin** | http://localhost:8081 | root / root_secure_2024 |
| **MySQL (externo)** | localhost:3309 | farmacia_admin / farmacia_2024 |
| **Redis** | localhost:6380 | - |

---

## 📊 Servicios Incluidos

### Contenedores Docker

1. **farmacia-clinica-app**
   - PHP 8.2 con Apache
   - Laravel 10
   - Puerto: 8003

2. **farmacia-clinica-db**
   - MySQL 8.0
   - Puerto: 3309
   - Volumen persistente para datos

3. **farmacia-clinica-redis**
   - Redis 7 Alpine
   - Puerto: 6380
   - Cache y sesiones

4. **farmacia-clinica-phpmyadmin**
   - Gestión web de base de datos
   - Puerto: 8081

---

## ⚙️ Configuración del Sistema

### Variables de Entorno Importantes

Edita el archivo `.env` con las siguientes configuraciones:

```env
# Aplicación
APP_NAME="Farmacia y Clínica"
APP_URL=http://localhost:8003
APP_TIMEZONE=America/El_Salvador

# Base de datos
DB_DATABASE=farmacia_clinica
DB_USERNAME=farmacia_admin
DB_PASSWORD=farmacia_2024

# Módulos activos
MODULO_FARMACIA=true
MODULO_CLINICA=true
MODULO_LABORATORIO=true
```

### Archivos de Configuración

El sistema incluye configuraciones específicas en:

- `config/farmacia.php` - Configuración del módulo de farmacia
- `config/clinica.php` - Configuración del módulo de clínica
- `config/laboratorio.php` - Configuración del módulo de laboratorio

---

## 🏗️ Estructura de Módulos

### Módulo de Farmacia

**Características:**
- Control de inventario con lotes y fechas de vencimiento
- Gestión de medicamentos controlados y psicofármacos
- Alertas de vencimiento y stock mínimo
- Control de productos refrigerados
- Facturación electrónica (DTE)

**Categorías Incluidas:**
- Medicamentos de Prescripción
- Medicamentos OTC (Venta Libre)
- Antibióticos
- Psicofármacos
- Vitaminas y Suplementos
- Material Médico

### Módulo de Clínica

**Características:**
- Gestión de citas médicas
- Expedientes electrónicos de pacientes
- Historia clínica completa
- Recetas digitales
- Signos vitales
- Diagnósticos CIE-10

**Servicios:**
- Consulta Medicina General
- Consulta Pediátrica
- Control Prenatal
- Procedimientos menores
- Aplicación de vacunas

### Módulo de Laboratorio Clínico

**Características:**
- Gestión de órdenes de exámenes
- Control de muestras con código único
- Resultados digitales con valores de referencia
- Perfiles de laboratorio predefinidos
- Alertas de valores críticos

**Categorías de Exámenes:**
- Hematología
- Química Clínica
- Inmunología
- Microbiología
- Parasitología
- Urianálisis
- Coprología

---

## 🔧 Comandos Útiles

### Gestión de Contenedores

```bash
# Ver estado de servicios
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app

# Detener servicios
docker-compose down

# Reiniciar servicios
docker-compose restart

# Reconstruir contenedores
docker-compose up -d --build
```

### Acceso a Contenedores

```bash
# Entrar al contenedor de la aplicación
docker-compose exec app bash

# Ejecutar comandos Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache

# Acceder a MySQL CLI
docker-compose exec db mysql -u farmacia_admin -p farmacia_clinica
```

### Base de Datos

```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Ejecutar migraciones y seeders
docker-compose exec app php artisan migrate:fresh --seed

# Crear backup
docker-compose exec app php artisan backup:run

# Ver lista de backups
docker-compose exec app php artisan backup:list
```

### Cache y Optimización

```bash
# Limpiar todas las cachés
docker-compose exec app php artisan optimize:clear

# Optimizar aplicación
docker-compose exec app php artisan optimize

# Limpiar cache específico
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

---

## 📦 Seeders Disponibles

El sistema incluye los siguientes seeders con datos iniciales:

- `RoleSeeder` - Roles y permisos del sistema
- `UserSeeder` - Usuarios administradores
- `FarmaciaClinicaSeeder` - Configuración de farmacia y clínica
- `LaboratorioSeeder` - Configuración de laboratorio
- `UnitsSeeder` - Unidades de medida
- `TypeDocumentSeeder` - Tipos de documentos

Para ejecutar un seeder específico:

```bash
docker-compose exec app php artisan db:seed --class=FarmaciaClinicaSeeder
```

---

## 🔒 Seguridad

### Contraseñas Predeterminadas

⚠️ **IMPORTANTE**: Cambiar las siguientes contraseñas en producción:

```env
DB_PASSWORD=farmacia_2024  # Cambiar en producción
MYSQL_ROOT_PASSWORD=root_secure_2024  # Cambiar en producción
```

### Configuración de Seguridad

```env
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=480  # 8 horas
PASSWORD_MIN_LENGTH=8
```

---

## 🗄️ Backups Automáticos

El sistema está configurado para realizar backups automáticos:

```env
BACKUP_ENABLED=true
BACKUP_FREQUENCY=daily
BACKUP_RETENTION_DAYS=30
BACKUP_PATH=/backups
```

Los backups se almacenan en: `database/backups/`

---

## 🧪 Facturación Electrónica (DTE)

Configuración para El Salvador:

```env
DTE_ENABLED=true
DTE_AMBIENTE=00  # 00=Desarrollo, 01=Producción
DTE_NIT=tu_nit
DTE_NRC=tu_nrc
DTE_API_URL=https://apitest.dtes.mh.gob.sv/fesv/
```

---

## 🛠️ Solución de Problemas

### Error de permisos

```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/storage
docker-compose exec app chmod -R 775 /var/www/bootstrap/cache
```

### Base de datos no conecta

```bash
# Verificar servicios
docker-compose ps

# Ver logs de la base de datos
docker-compose logs db

# Reiniciar servicio
docker-compose restart db
```

### Reinstalar dependencias

```bash
# Entrar al contenedor
docker-compose exec app bash

# Reinstalar Composer
composer install --no-cache

# Limpiar y reinstalar npm
rm -rf node_modules
npm install
npm run build
```

### Limpiar todo y empezar de nuevo

```bash
# Detener y eliminar todo
docker-compose down -v

# Eliminar volúmenes
docker volume prune

# Reconstruir desde cero
docker-compose build --no-cache
docker-compose up -d
```

---

## 📚 Documentación Adicional

Para más información consulta:

- [README.md](README.md) - Información general del proyecto
- [README-DOCKER.md](README-DOCKER.md) - Documentación de Docker
- `config/farmacia.php` - Configuración del módulo de farmacia
- `config/clinica.php` - Configuración del módulo de clínica
- `config/laboratorio.php` - Configuración del módulo de laboratorio

---

## 🤝 Soporte

Para soporte técnico o consultas:

- Revisar logs: `docker-compose logs -f`
- Revisar configuración: `docker-compose exec app php artisan config:show`
- Verificar conexión BD: `docker-compose exec app php artisan db:show`

---

## 📝 Licencia

Sistema propietario para uso interno de la clínica y farmacia.

---

**Versión:** 1.0.0  
**Fecha:** 2024  
**Sistema:** Farmacia, Clínica y Laboratorio Clínico  

