#!/bin/bash

echo "============================================================================="
echo "        PUBLICAR REPOSITORIO - FARMACIA, CLÍNICA Y LABORATORIO             "
echo "============================================================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -d ".git" ]; then
    echo -e "${RED}❌ Error: No se encontró un repositorio Git en este directorio${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Repositorio Git detectado${NC}"
echo ""

# Verificar si ya existe un remoto
REMOTE_EXISTS=$(git remote -v | grep origin)

if [ ! -z "$REMOTE_EXISTS" ]; then
    echo -e "${YELLOW}⚠️  Ya existe un repositorio remoto configurado:${NC}"
    git remote -v
    echo ""
    read -p "¿Deseas reemplazarlo? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        git remote remove origin
        echo -e "${GREEN}✅ Remoto anterior eliminado${NC}"
    else
        echo -e "${BLUE}ℹ️  Publicando en el remoto existente...${NC}"
        echo ""
        read -p "¿Deseas hacer push ahora? (y/n): " -n 1 -r
        echo ""
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            git push -u origin main
            exit 0
        else
            exit 0
        fi
    fi
fi

echo ""
echo "Selecciona la plataforma donde publicarás el repositorio:"
echo ""
echo "  1) GitHub"
echo "  2) GitLab"
echo "  3) Bitbucket"
echo "  4) Otro (URL personalizada)"
echo "  5) Cancelar"
echo ""
read -p "Selecciona una opción [1-5]: " option

case $option in
    1)
        PLATFORM="GitHub"
        echo ""
        echo -e "${BLUE}📝 Configurando para GitHub${NC}"
        echo ""
        echo "Ingresa tu nombre de usuario de GitHub:"
        read GITHUB_USER
        echo ""
        echo "Ingresa el nombre del repositorio (sin espacios):"
        read REPO_NAME
        echo ""
        echo "Selecciona el protocolo:"
        echo "  1) HTTPS (recomendado para principiantes)"
        echo "  2) SSH (requiere configuración previa)"
        read -p "Selecciona [1-2]: " protocol
        
        if [ "$protocol" = "2" ]; then
            REMOTE_URL="git@github.com:${GITHUB_USER}/${REPO_NAME}.git"
        else
            REMOTE_URL="https://github.com/${GITHUB_USER}/${REPO_NAME}.git"
        fi
        
        INSTRUCTIONS="
${GREEN}✅ Pasos para crear el repositorio en GitHub:${NC}

1. Ve a: https://github.com/new
2. Nombre del repositorio: ${REPO_NAME}
3. Descripción: Sistema de Farmacia, Clínica y Laboratorio Clínico
4. Visibilidad: Privado (recomendado)
5. NO inicialices con README, .gitignore o licencia
6. Haz clic en 'Create repository'
        "
        ;;
        
    2)
        PLATFORM="GitLab"
        echo ""
        echo -e "${BLUE}📝 Configurando para GitLab${NC}"
        echo ""
        echo "Ingresa tu nombre de usuario de GitLab:"
        read GITLAB_USER
        echo ""
        echo "Ingresa el nombre del repositorio (sin espacios):"
        read REPO_NAME
        echo ""
        echo "Selecciona el protocolo:"
        echo "  1) HTTPS"
        echo "  2) SSH"
        read -p "Selecciona [1-2]: " protocol
        
        if [ "$protocol" = "2" ]; then
            REMOTE_URL="git@gitlab.com:${GITLAB_USER}/${REPO_NAME}.git"
        else
            REMOTE_URL="https://gitlab.com/${GITLAB_USER}/${REPO_NAME}.git"
        fi
        
        INSTRUCTIONS="
${GREEN}✅ Pasos para crear el repositorio en GitLab:${NC}

1. Ve a: https://gitlab.com/projects/new
2. Nombre del proyecto: ${REPO_NAME}
3. Visibilidad: Privado
4. NO inicialices con README
5. Haz clic en 'Create project'
        "
        ;;
        
    3)
        PLATFORM="Bitbucket"
        echo ""
        echo -e "${BLUE}📝 Configurando para Bitbucket${NC}"
        echo ""
        echo "Ingresa tu nombre de usuario de Bitbucket:"
        read BITBUCKET_USER
        echo ""
        echo "Ingresa el nombre del repositorio (sin espacios):"
        read REPO_NAME
        
        REMOTE_URL="https://${BITBUCKET_USER}@bitbucket.org/${BITBUCKET_USER}/${REPO_NAME}.git"
        
        INSTRUCTIONS="
${GREEN}✅ Pasos para crear el repositorio en Bitbucket:${NC}

1. Ve a: https://bitbucket.org/repo/create
2. Nombre: ${REPO_NAME}
3. Privado: Sí
4. Haz clic en 'Create repository'
        "
        ;;
        
    4)
        echo ""
        echo "Ingresa la URL completa del repositorio remoto:"
        read REMOTE_URL
        PLATFORM="Personalizado"
        INSTRUCTIONS=""
        ;;
        
    5)
        echo -e "${BLUE}ℹ️  Operación cancelada${NC}"
        exit 0
        ;;
        
    *)
        echo -e "${RED}❌ Opción inválida${NC}"
        exit 1
        ;;
esac

echo ""
echo "============================================================================="
echo -e "${INSTRUCTIONS}"
echo "============================================================================="
echo ""
echo -e "${YELLOW}URL del repositorio remoto:${NC}"
echo -e "${BLUE}${REMOTE_URL}${NC}"
echo ""

read -p "¿Has creado el repositorio en ${PLATFORM}? (y/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo -e "${YELLOW}⚠️  Por favor, crea primero el repositorio en ${PLATFORM}${NC}"
    echo "   URL que usarás: ${REMOTE_URL}"
    echo ""
    echo "Ejecuta este script nuevamente cuando hayas creado el repositorio."
    exit 0
fi

echo ""
echo -e "${BLUE}🔗 Agregando repositorio remoto...${NC}"
git remote add origin "${REMOTE_URL}"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Remoto agregado correctamente${NC}"
else
    echo -e "${RED}❌ Error al agregar el remoto${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}🚀 Publicando rama main...${NC}"
echo ""

# Intentar hacer push
git push -u origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "============================================================================="
    echo -e "${GREEN}         ✅ ¡REPOSITORIO PUBLICADO EXITOSAMENTE! ✅${NC}"
    echo "============================================================================="
    echo ""
    echo "Tu código ha sido publicado en:"
    echo -e "${BLUE}${REMOTE_URL}${NC}"
    echo ""
    echo "Puedes ver tu repositorio en:"
    
    case $option in
        1)
            echo "https://github.com/${GITHUB_USER}/${REPO_NAME}"
            ;;
        2)
            echo "https://gitlab.com/${GITLAB_USER}/${REPO_NAME}"
            ;;
        3)
            echo "https://bitbucket.org/${BITBUCKET_USER}/${REPO_NAME}"
            ;;
    esac
    
    echo ""
    echo "📊 Estadísticas del commit inicial:"
    git log -1 --stat
    echo ""
    echo "============================================================================="
    
else
    echo ""
    echo -e "${RED}❌ Error al publicar el repositorio${NC}"
    echo ""
    echo "Posibles causas:"
    echo "  1. El repositorio remoto no existe"
    echo "  2. No tienes permisos para escribir"
    echo "  3. Necesitas autenticarte"
    echo ""
    echo "Para HTTPS:"
    echo "  - Asegúrate de tener un Personal Access Token configurado"
    echo "  - GitHub: Settings → Developer settings → Personal access tokens"
    echo ""
    echo "Para SSH:"
    echo "  - Asegúrate de tener tu clave SSH configurada"
    echo "  - Verifica con: ssh -T git@github.com (o gitlab.com)"
    echo ""
    echo "Puedes intentar publicar manualmente con:"
    echo "  git push -u origin main"
    exit 1
fi

