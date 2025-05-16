#!/bin/bash

# Script para importar noticias de actores y películas para Dorasia
# Este script permite ejecutar fácilmente los comandos para obtener noticias
# desde diferentes fuentes (NewsAPI, TMDB o generación con IA)

# Colores para mensajes
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # Sin color

echo -e "${BLUE}===========================================================${NC}"
echo -e "${BLUE}       IMPORTADOR DE NOTICIAS PARA DORASIA                 ${NC}"
echo -e "${BLUE}===========================================================${NC}"

# Verificar que esté instalado PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}Error: PHP no está instalado o no se encuentra en el PATH${NC}"
    exit 1
fi

# Verificar que el archivo .env existe
if [ ! -f .env ]; then
    echo -e "${RED}Error: No se encontró el archivo .env${NC}"
    echo "Asegúrate de que estás ejecutando este script desde el directorio raíz del proyecto"
    exit 1
fi

# Verificar que las claves de API están configuradas
if ! grep -q "NEWSAPI_KEY" .env || ! grep -q "OPENAI_API_KEY" .env; then
    echo -e "${YELLOW}Advertencia: No se encontraron las claves de API en el archivo .env${NC}"
    echo "Para usar NewsAPI, añade NEWSAPI_KEY=[tu clave] en el archivo .env"
    echo "Para usar OpenAI, añade OPENAI_API_KEY=[tu clave] en el archivo .env"
    echo
fi

# Función de ayuda
show_help() {
    echo "Uso: ./import-news.sh [opciones]"
    echo
    echo "Opciones:"
    echo "  --actor-news [fuente]   Importar noticias de actores (fuente: newsapi, ai)"
    echo "  --movie-news [fuente]   Importar noticias de películas (fuente: newsapi, tmdb, ai)"
    echo "  --limit [número]        Límite de noticias a importar (predeterminado: 10)"
    echo "  --images                Añadir imágenes a las noticias (cuando estén disponibles)"
    echo "  --actor [nombre/id]     Importar noticias de un actor específico"
    echo "  --title [nombre/id]     Importar noticias de una película/serie específica"
    echo "  --genre [género]        Importar noticias de un género específico"
    echo "  --days [número]         Buscar noticias de los últimos X días (predeterminado: 7)"
    echo "  --help                  Mostrar este mensaje de ayuda"
    echo
    echo "Ejemplos:"
    echo "  ./import-news.sh --actor-news newsapi --limit 15 --images"
    echo "  ./import-news.sh --movie-news tmdb --limit 5"
    echo "  ./import-news.sh --actor-news ai --actor \"Son Ye-jin\""
    echo
}

# Verificar si no hay argumentos
if [ $# -eq 0 ]; then
    show_help
    exit 0
fi

# Valores predeterminados
COMMAND=""
SOURCE=""
LIMIT="10"
IMAGES=""
SPECIFIC=""
DAYS="7"

# Procesar argumentos
while [[ $# -gt 0 ]]; do
    case "$1" in
        --actor-news)
            COMMAND="dorasia:fetch-news"
            SOURCE="$2"
            shift 2
            ;;
        --movie-news)
            COMMAND="dorasia:fetch-movie-news"
            SOURCE="$2"
            shift 2
            ;;
        --limit)
            LIMIT="$2"
            shift 2
            ;;
        --images)
            IMAGES="--add-images"
            shift
            ;;
        --actor)
            if [ "$COMMAND" = "dorasia:fetch-news" ]; then
                SPECIFIC="--actor=\"$2\""
            else
                echo -e "${YELLOW}Advertencia: --actor solo se puede usar con --actor-news${NC}"
            fi
            shift 2
            ;;
        --title)
            if [ "$COMMAND" = "dorasia:fetch-movie-news" ]; then
                SPECIFIC="--title=\"$2\""
            else
                echo -e "${YELLOW}Advertencia: --title solo se puede usar con --movie-news${NC}"
            fi
            shift 2
            ;;
        --genre)
            if [ "$COMMAND" = "dorasia:fetch-movie-news" ]; then
                SPECIFIC="--genre=\"$2\""
            else
                echo -e "${YELLOW}Advertencia: --genre solo se puede usar con --movie-news${NC}"
            fi
            shift 2
            ;;
        --days)
            DAYS="$2"
            shift 2
            ;;
        --help)
            show_help
            exit 0
            ;;
        *)
            echo -e "${RED}Error: Argumento desconocido $1${NC}"
            show_help
            exit 1
            ;;
    esac
done

# Verificar que se haya especificado un comando y una fuente
if [ -z "$COMMAND" ] || [ -z "$SOURCE" ]; then
    echo -e "${RED}Error: Debes especificar tanto el tipo de noticias como la fuente${NC}"
    show_help
    exit 1
fi

# Validar la fuente según el comando
if [ "$COMMAND" = "dorasia:fetch-news" ] && [[ "$SOURCE" != "newsapi" && "$SOURCE" != "ai" ]]; then
    echo -e "${RED}Error: Para noticias de actores, la fuente debe ser 'newsapi' o 'ai'${NC}"
    exit 1
fi

if [ "$COMMAND" = "dorasia:fetch-movie-news" ] && [[ "$SOURCE" != "newsapi" && "$SOURCE" != "tmdb" && "$SOURCE" != "ai" ]]; then
    echo -e "${RED}Error: Para noticias de películas, la fuente debe ser 'newsapi', 'tmdb' o 'ai'${NC}"
    exit 1
fi

# Construir y ejecutar el comando
FULL_COMMAND="php artisan $COMMAND --source=$SOURCE --limit=$LIMIT --days=$DAYS $IMAGES $SPECIFIC"

echo -e "${GREEN}Ejecutando: $FULL_COMMAND${NC}"
echo -e "${YELLOW}Importando noticias, por favor espera...${NC}"
echo

eval $FULL_COMMAND

# Verificar si el comando se ejecutó correctamente
if [ $? -eq 0 ]; then
    echo
    echo -e "${GREEN}¡Importación de noticias completada con éxito!${NC}"
else
    echo
    echo -e "${RED}La importación de noticias falló. Revisa los errores anteriores.${NC}"
fi

echo -e "${BLUE}===========================================================${NC}"