# 🚀 Comandos Artisan para Iniciar el Proyecto Laravel

## 📋 Orden de Ejecución

### 1. Generar APP_KEY (si no existe)
```bash
docker-compose exec app php artisan key:generate
```

### 2. Crear la base de datos (si no existe)
```bash
docker-compose exec db mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS farmacia_clinica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# Contraseña: root
```

### 3. Ejecutar migraciones
```bash
docker-compose exec app php artisan migrate
```

### 4. Ejecutar seeders (datos iniciales)
```bash
docker-compose exec app php artisan db:seed
```

### 5. Crear enlace simbólico para storage
```bash
docker-compose exec app php artisan storage:link
```

### 6. Optimizar la aplicación (opcional pero recomendado)
```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

---

## 🔄 Comandos Útiles Durante el Desarrollo

### Limpiar cachés
```bash
docker-compose exec app php artisan optimize:clear
# O individualmente:
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Ver estado de migraciones
```bash
docker-compose exec app php artisan migrate:status
```

### Crear nueva migración
```bash
docker-compose exec app php artisan make:migration nombre_de_la_migracion
```

### Crear nuevo modelo con migración
```bash
docker-compose exec app php artisan make:model NombreModelo -m
```

### Crear controlador
```bash
docker-compose exec app php artisan make:controller NombreController
```

### Crear seeder
```bash
docker-compose exec app php artisan make:seeder NombreSeeder
```

### Refrescar base de datos y ejecutar seeders
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

---

## 📝 Comandos Rápidos (Todo en uno)

Si quieres ejecutar todo de una vez:

```bash
# 1. Generar key
docker-compose exec app php artisan key:generate

# 2. Crear BD
docker-compose exec db mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS farmacia_clinica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Migraciones y seeders
docker-compose exec app php artisan migrate --seed

# 4. Storage link
docker-compose exec app php artisan storage:link

# 5. Optimizar
docker-compose exec app php artisan optimize
```

---

## 🎯 Para tu Proyecto de Farmacia, Clínica y Laboratorio

Los seeders específicos que tienes disponibles:

```bash
# Seeder principal (datos generales)
docker-compose exec app php artisan db:seed --class=FarmaciaClinicaSeeder

# Seeder de laboratorio
docker-compose exec app php artisan db:seed --class=LaboratorioSeeder

# Otros seeders disponibles
docker-compose exec app php artisan db:seed --class=RoleSeeder
docker-compose exec app php artisan db:seed --class=UserSeeder
docker-compose exec app php artisan db:seed --class=UnitsSeeder
```

---

## ⚠️ Nota Importante

**Contraseña de MySQL root:** `root`

**Base de datos:** `farmacia_clinica`

**Usuario:** `farmacia_admin`

**Contraseña:** `farmacia_2024`

