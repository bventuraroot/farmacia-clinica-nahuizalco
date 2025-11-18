#!/bin/bash

# Script para publicar repositorio en GitHub
# Uso: ./push-to-github.sh [TOKEN_OPCIONAL]

REPO="bventuraroot/farmacia-clinica-nahuizalco"
BRANCH="main"

# Si se pasa un token como argumento, usarlo; si no, usar el remoto actual
if [ ! -z "$1" ]; then
    TOKEN="$1"
    echo "🔧 Configurando repositorio remoto con token proporcionado..."
    git remote set-url origin https://${TOKEN}@github.com/${REPO}.git
else
    echo "🔧 Usando configuración remota actual..."
fi

echo "📦 Verificando commits..."
git log --oneline -1

echo "🚀 Intentando publicar al repositorio..."
echo ""

# Intentar push
git push -u origin ${BRANCH}

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ ¡Repositorio publicado exitosamente!"
    echo "🌐 URL: https://github.com/${REPO}"
else
    echo ""
    echo "❌ Error al publicar. Posibles causas:"
    echo "   1. Problema temporal de GitHub (espera unos minutos)"
    echo "   2. El token necesita permisos 'repo' completos"
    echo "   3. El repositorio podría tener restricciones"
    echo ""
    echo "💡 Sugerencia: Verifica el token en:"
    echo "   https://github.com/settings/tokens"
    echo ""
    echo "🔗 También puedes intentar manualmente:"
    echo "   git push -u origin main"
fi


