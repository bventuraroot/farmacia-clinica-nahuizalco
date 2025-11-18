# 📦 Instrucciones para Publicar el Repositorio Git

## ✅ Estado Actual

Se ha inicializado un **nuevo repositorio Git local** con el commit inicial del proyecto.

```
Commit inicial: b0f77c8
- 976 archivos
- 212,138 líneas de código
```

---

## 🚀 Para Publicar en un Repositorio Remoto

### Opción 1: GitHub

1. **Crear un nuevo repositorio en GitHub:**
   - Ve a https://github.com/new
   - Nombre sugerido: `farmacia-clinica-laboratorio`
   - NO inicialices con README, .gitignore o licencia (ya los tienes localmente)
   - Crea el repositorio

2. **Conectar y subir:**

```bash
# En la terminal, dentro del directorio del proyecto
cd "/Volumes/ExternalHelp/Outside/htdocs/Farmacia Nahuizalco"

# Agregar el repositorio remoto (reemplaza TU-USUARIO con tu usuario de GitHub)
git remote add origin https://github.com/TU-USUARIO/farmacia-clinica-laboratorio.git

# O si usas SSH:
# git remote add origin git@github.com:TU-USUARIO/farmacia-clinica-laboratorio.git

# Renombrar la rama principal a 'main' (estándar actual)
git branch -M main

# Subir el código
git push -u origin main
```

### Opción 2: GitLab

1. **Crear un nuevo proyecto en GitLab:**
   - Ve a https://gitlab.com/projects/new
   - Nombre: `farmacia-clinica-laboratorio`
   - Visibilidad: Privado (recomendado)
   - NO inicialices con README
   - Crea el proyecto

2. **Conectar y subir:**

```bash
cd "/Volumes/ExternalHelp/Outside/htdocs/Farmacia Nahuizalco"

# Agregar el repositorio remoto
git remote add origin https://gitlab.com/TU-USUARIO/farmacia-clinica-laboratorio.git

# O si usas SSH:
# git remote add origin git@gitlab.com:TU-USUARIO/farmacia-clinica-laboratorio.git

git branch -M main
git push -u origin main
```

### Opción 3: Bitbucket

1. **Crear un nuevo repositorio en Bitbucket:**
   - Ve a https://bitbucket.org/repo/create
   - Nombre: `farmacia-clinica-laboratorio`
   - Privado: Sí (recomendado)
   - Crea el repositorio

2. **Conectar y subir:**

```bash
cd "/Volumes/ExternalHelp/Outside/htdocs/Farmacia Nahuizalco"

# Agregar el repositorio remoto
git remote add origin https://TU-USUARIO@bitbucket.org/TU-USUARIO/farmacia-clinica-laboratorio.git

git branch -M main
git push -u origin main
```

---

## 📋 Comandos Útiles de Git

### Ver el estado actual

```bash
git status
```

### Ver el historial de commits

```bash
git log --oneline
```

### Crear una nueva rama para desarrollo

```bash
# Crear y cambiar a una nueva rama
git checkout -b desarrollo

# O en Git moderno:
git switch -c desarrollo
```

### Hacer cambios y crear un nuevo commit

```bash
# Ver archivos modificados
git status

# Agregar archivos específicos
git add archivo1.php archivo2.php

# O agregar todos los cambios
git add .

# Crear el commit
git commit -m "Descripción de los cambios realizados"

# Subir los cambios
git push
```

### Sincronizar con el repositorio remoto

```bash
# Descargar cambios del remoto
git pull origin main

# Subir cambios locales
git push origin main
```

---

## 🔐 Configuración Recomendada

### Configurar tu identidad en Git (si no lo has hecho)

```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

### Configurar credenciales para GitHub/GitLab/Bitbucket

#### Opción 1: HTTPS con Personal Access Token (Recomendado)

1. Genera un token de acceso personal:
   - **GitHub**: Settings → Developer settings → Personal access tokens → Generate new token
   - **GitLab**: Settings → Access Tokens
   - **Bitbucket**: Settings → App passwords

2. Usa el token como contraseña al hacer push

#### Opción 2: SSH (Más seguro para uso frecuente)

1. **Generar clave SSH:**

```bash
ssh-keygen -t ed25519 -C "tu@email.com"
```

2. **Copiar la clave pública:**

```bash
cat ~/.ssh/id_ed25519.pub
```

3. **Agregar la clave en tu plataforma:**
   - **GitHub**: Settings → SSH and GPG keys → New SSH key
   - **GitLab**: Settings → SSH Keys
   - **Bitbucket**: Settings → SSH keys

---

## 🌿 Estrategia de Ramas Recomendada

### Modelo GitFlow Simplificado

```
main (producción)
  └── desarrollo (desarrollo activo)
       ├── feature/nombre-funcionalidad
       ├── bugfix/nombre-error
       └── hotfix/nombre-urgente
```

### Crear estructura de ramas

```bash
# Crear rama de desarrollo
git checkout -b desarrollo
git push -u origin desarrollo

# Para nuevas funcionalidades
git checkout -b feature/modulo-laboratorio
# ... hacer cambios ...
git add .
git commit -m "feat: Implementar módulo de laboratorio"
git push -u origin feature/modulo-laboratorio

# Luego hacer merge a desarrollo mediante Pull Request
```

---

## 📌 Archivo .gitignore

El proyecto ya incluye un `.gitignore` completo que excluye:

- ✅ Variables de entorno (`.env`)
- ✅ Dependencias (`vendor/`, `node_modules/`)
- ✅ Archivos de logs
- ✅ Backups de base de datos
- ✅ Archivos temporales
- ✅ Claves y certificados

**IMPORTANTE:** Nunca subas archivos `.env` con credenciales reales.

---

## 🔄 Flujo de Trabajo Recomendado

### Para un nuevo desarrollador en el proyecto

```bash
# 1. Clonar el repositorio
git clone https://github.com/TU-USUARIO/farmacia-clinica-laboratorio.git
cd farmacia-clinica-laboratorio

# 2. Copiar y configurar el archivo .env
cp env.farmacia-clinica.example .env
# Editar .env con tus credenciales

# 3. Instalar dependencias
composer install
npm install

# 4. Generar key de aplicación
php artisan key:generate

# 5. Levantar con Docker
chmod +x docker-start.sh
./docker-start.sh
```

---

## 📝 Convenciones de Commits

Usa prefijos para claridad:

- `feat:` - Nueva funcionalidad
- `fix:` - Corrección de error
- `docs:` - Cambios en documentación
- `style:` - Formato de código
- `refactor:` - Refactorización
- `test:` - Añadir tests
- `chore:` - Tareas de mantenimiento

**Ejemplos:**

```bash
git commit -m "feat: Agregar módulo de recetas médicas"
git commit -m "fix: Corregir cálculo de inventario en compras"
git commit -m "docs: Actualizar documentación de instalación"
```

---

## ⚠️ IMPORTANTE: Seguridad

### Antes de hacer tu primer push, verifica:

```bash
# Verificar que .env NO está en el repositorio
git status

# Si por error agregaste .env, remuévelo:
git rm --cached .env
git commit -m "chore: Remover archivo .env del repositorio"
```

### Archivos que NUNCA deben subirse:

- ❌ `.env` con credenciales reales
- ❌ Claves privadas (`.key`, `.pem`, `.ppk`)
- ❌ Backups de base de datos con datos reales
- ❌ Archivos de configuración con contraseñas

---

## 🆘 Comandos de Emergencia

### Deshacer el último commit (sin perder cambios)

```bash
git reset --soft HEAD~1
```

### Deshacer cambios en un archivo específico

```bash
git checkout -- archivo.php
```

### Ver diferencias antes de commit

```bash
git diff
```

### Limpiar archivos no rastreados

```bash
git clean -fd
```

---

## 📞 Soporte

Si tienes problemas con Git:

1. Verifica el estado: `git status`
2. Revisa los remotos configurados: `git remote -v`
3. Consulta la documentación oficial: https://git-scm.com/doc

---

**Proyecto:** Sistema Farmacia, Clínica y Laboratorio Clínico  
**Repositorio inicializado:** ✅  
**Commit inicial:** b0f77c8  
**Fecha:** Noviembre 2024

