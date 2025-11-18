#!/bin/bash

echo "============================================================================="
echo "  CONFIGURANDO SISTEMA FARMACIA, CLÍNICA Y LABORATORIO CLÍNICO CON DOCKER  "
echo "============================================================================="
echo ""

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado. Por favor instala Docker primero."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado. Por favor instala Docker Compose primero."
    exit 1
fi

echo "✅ Docker y Docker Compose detectados"
echo ""

# Crear directorio para backups si no existe
if [ ! -d "database/backups" ]; then
    echo "📁 Creando directorio para backups..."
    mkdir -p database/backups
    echo "✅ Directorio de backups creado"
fi

# Crear archivo .env si no existe
if [ ! -f .env ]; then
    echo "📋 Creando archivo .env desde env.farmacia-clinica.example..."
    if [ -f env.farmacia-clinica.example ]; then
        cp env.farmacia-clinica.example .env
        echo "✅ Archivo .env creado"
        echo "⚠️  IMPORTANTE: Edita el archivo .env con tus credenciales antes de continuar"
        echo ""
        read -p "¿Deseas continuar? (y/n): " -n 1 -r
        echo ""
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "❌ Instalación cancelada"
            exit 1
        fi
    else
        echo "❌ No se encontró el archivo env.farmacia-clinica.example"
        exit 1
    fi
else
    echo "📋 Archivo .env ya existe"
fi

echo ""
echo "🧹 Limpiando contenedores anteriores..."
docker-compose down -v 2>/dev/null

# Construir y levantar contenedores
echo ""
echo "🐳 Construyendo contenedores Docker (esto puede tardar varios minutos)..."
docker-compose build --no-cache

echo ""
echo "🚀 Levantando servicios..."
docker-compose up -d

# Esperar a que la base de datos esté lista
echo ""
echo "⏳ Esperando que los servicios estén listos (30 segundos)..."
for i in {1..30}; do
    echo -n "."
    sleep 1
done
echo ""

# Instalar dependencias de Composer
echo ""
echo "📦 Instalando dependencias de Composer..."
docker-compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader

# Generar key de aplicación si no existe
echo ""
echo "🔑 Generando key de aplicación Laravel..."
docker-compose exec -T app php artisan key:generate --force

# Ejecutar migraciones
echo ""
echo "📊 Ejecutando migraciones de base de datos..."
docker-compose exec -T app php artisan migrate --force

# Ejecutar seeders
echo ""
echo "🌱 Ejecutando seeders (datos iniciales)..."
docker-compose exec -T app php artisan db:seed --force

# Crear enlace simbólico para storage
echo ""
echo "🔗 Creando enlace simbólico para storage..."
docker-compose exec -T app php artisan storage:link

# Optimizar aplicación
echo ""
echo "⚡ Optimizando aplicación..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Configurar permisos
echo ""
echo "🔧 Configurando permisos..."
docker-compose exec -T app chown -R www-data:www-data /var/www/storage
docker-compose exec -T app chown -R www-data:www-data /var/www/bootstrap/cache
docker-compose exec -T app chmod -R 775 /var/www/storage
docker-compose exec -T app chmod -R 775 /var/www/bootstrap/cache

echo ""
echo "============================================================================="
echo "                    ✅ ¡CONFIGURACIÓN COMPLETADA!                           "
echo "============================================================================="
echo ""
echo "🌐 ACCESOS AL SISTEMA:"
echo "   - Aplicación Principal:  http://localhost:8003"
echo "   - PHPMyAdmin:            http://localhost:8081"
echo ""
echo "🗄️  CREDENCIALES DE BASE DE DATOS:"
echo "   - Host:       localhost:3309 (externo) / db:3306 (interno)"
echo "   - Base de datos: farmacia_clinica"
echo "   - Usuario:    farmacia_admin"
echo "   - Contraseña: farmacia_2024"
echo "   - Root:       root_secure_2024"
echo ""
echo "📊 SERVICIOS ACTIVOS:"
echo "   - ✅ Aplicación Laravel (PHP 8.2 + Apache)"
echo "   - ✅ MySQL 8.0"
echo "   - ✅ Redis 7"
echo "   - ✅ PHPMyAdmin"
echo ""
echo "📋 COMANDOS ÚTILES:"
echo "   docker-compose ps                    # Ver estado de servicios"
echo "   docker-compose logs -f app           # Ver logs en tiempo real"
echo "   docker-compose exec app bash         # Entrar al contenedor"
echo "   docker-compose exec app php artisan  # Ejecutar comandos Artisan"
echo "   docker-compose down                  # Detener servicios"
echo "   docker-compose up -d                 # Levantar servicios"
echo "   docker-compose restart               # Reiniciar servicios"
echo ""
echo "📚 DOCUMENTACIÓN:"
echo "   Ver archivo FARMACIA_CLINICA_SETUP.md para más información"
echo ""
echo "============================================================================="
echo ""
