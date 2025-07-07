# 🎵 Guía Completa de Comandos de Soundtracks - Dorasia

## 📋 Comandos Principales

### 🎯 **1. Importar Soundtracks 100% Auténticos**

```bash
# Ver qué contenido auténtico está disponible
php artisan soundtracks:import-authentic --verify

# Preview antes de importar (RECOMENDADO)
php artisan soundtracks:import-authentic --dry-run

# Importar todos los soundtracks auténticos
php artisan soundtracks:import-authentic

# Solo series K-Drama
php artisan soundtracks:import-authentic --series-only

# Solo películas anime/asiáticas
php artisan soundtracks:import-authentic --movies-only
```

### 🧹 **2. Limpiar Soundtracks Sintéticos**

```bash
# Ver qué se eliminará (SIEMPRE EJECUTAR PRIMERO)
php artisan soundtracks:clean-synthetic --dry-run

# Ejecutar limpieza con confirmación
php artisan soundtracks:clean-synthetic

# Forzar limpieza sin confirmación
php artisan soundtracks:clean-synthetic --force
```

### 📊 **3. Comandos de Importación Masiva (Incluye Sintéticos)**

```bash
# Importar soundtracks masivos con preview
php artisan soundtracks:import-massive --dry-run --limit=50

# Importar soundtracks masivos
php artisan soundtracks:import-massive --limit=30

# Agregar soundtracks específicos
php artisan soundtracks:add-specific --dry-run
php artisan soundtracks:add-specific
```

### 🔧 **4. Comandos Específicos de Contenido**

```bash
# Agregar soundtracks a Big Mouse
php artisan soundtracks:add-bigmouse

# Importar soundtracks de K-Dramas específicos
php artisan soundtracks:add-specific
```

## 🎵 Workflow Recomendado

### **📋 Para Base de Datos Nueva:**

```bash
# 1. Importar soundtracks auténticos
php artisan soundtracks:import-authentic --dry-run
php artisan soundtracks:import-authentic

# 2. Verificar resultado
php artisan tinker --execute="echo 'Total soundtracks: ' . App\Models\Soundtrack::count();"
```

### **🧹 Para Base de Datos Existente con Contenido Sintético:**

```bash
# 1. Verificar qué hay actualmente
php artisan soundtracks:clean-synthetic --dry-run

# 2. Limpiar sintéticos
php artisan soundtracks:clean-synthetic --force

# 3. Importar auténticos
php artisan soundtracks:import-authentic --dry-run
php artisan soundtracks:import-authentic

# 4. Verificar resultado final
php artisan tinker --execute="
\$total = App\Models\Soundtrack::count();
\$authentic = App\Models\Soundtrack::where('popularity', '>=', 9.0)->count();
echo 'Total: ' . \$total . ' | Auténticos: ' . \$authentic . ' (' . round((\$authentic/\$total)*100) . '%)';
"
```

## 🔍 Información de Comandos

### **📺 Contenido Auténtico Verificado:**

#### **Series K-Drama:**
- ✅ **El juego del calamar** - 3 tracks de Jung Jae Il
- ✅ **Goblin** - 2 tracks (Chanyeol & Punch, Crush)
- ✅ **Crash Landing on You** - 2 tracks (IU, Yoon Mirae)
- ✅ **Hotel del Luna** - 1 track (Heize)

#### **Películas Anime/Asiáticas:**
- ✅ **Tu Nombre** - 4 tracks de RADWIMPS
- ✅ **El Tiempo Contigo** - 2 tracks de RADWIMPS
- ✅ **El Viaje de Chihiro** - 2 tracks (Joe Hisaishi, Youmi Kimura)
- ✅ **Train to Busan** - 1 track (Jang Young Gyu)

#### **K-Pop Hits:**
- ✅ **BTS** - Dynamite, Spring Day
- ✅ **BLACKPINK** - Kill This Love
- ✅ **PSY** - Gangnam Style

### **🎯 Criterios de Autenticidad:**

| Criterio | Auténtico ✅ | Sintético ❌ |
|----------|-------------|-------------|
| **YouTube ID** | Presente y verificado | Ausente o vacío |
| **Popularidad** | ≥ 9.0 | < 9.0 |
| **Artista** | Nombre real verificado | "Various Artists", "Unknown" |
| **Título** | Específico y verificado | "Opening Theme", "Love Theme" |
| **Fuente** | Productora oficial | Generado automáticamente |

## 📊 Verificación de Estado

### **🔍 Comandos de Verificación:**

```bash
# Ver información detallada de autenticidad
php artisan soundtracks:import-authentic --verify

# Estadísticas actuales
php artisan tinker --execute="
echo '🎵 ESTADÍSTICAS DE SOUNDTRACKS:' . PHP_EOL;
echo 'Total: ' . App\Models\Soundtrack::count() . PHP_EOL;
echo 'Con YouTube ID: ' . App\Models\Soundtrack::whereNotNull('youtube_id')->count() . PHP_EOL;
echo 'Alta popularidad (≥9.0): ' . App\Models\Soundtrack::where('popularity', '>=', 9.0)->count() . PHP_EOL;
echo 'Reproducibles: ' . App\Models\Soundtrack::whereNotNull('youtube_id')->where('youtube_id', '!=', '')->count() . PHP_EOL;
"

# Ver soundtracks de una película específica
php artisan tinker --execute="
\$movie = App\Models\Movie::find(7); // Tu Nombre
echo 'Película: ' . \$movie->display_title . PHP_EOL;
foreach(\$movie->soundtracks as \$track) {
    echo '  🎵 ' . \$track->title . ' - ' . \$track->artist . ' (Pop: ' . \$track->popularity . ')' . PHP_EOL;
}
"
```

### **📱 URLs de Prueba:**

```bash
# Películas con soundtracks auténticos
http://127.0.0.1:8000/movies/7    # Tu Nombre (RADWIMPS)
http://127.0.0.1:8000/movies/8    # El Tiempo Contigo (RADWIMPS)
http://127.0.0.1:8000/movies/71   # El Viaje de Chihiro (Joe Hisaishi)

# Series con soundtracks auténticos  
http://127.0.0.1:8000/series/1    # El juego del calamar (Jung Jae Il)
http://127.0.0.1:8000/series/32   # Crash Landing on You (IU, Yoon Mirae)
http://127.0.0.1:8000/series/77   # Goblin (Chanyeol & Punch, Crush)

# Página de prueba
http://127.0.0.1:8000/test-soundtracks
```

## 🎵 Funcionalidades del Sistema

### **📱 Interfaz de Usuario:**

- **🎵 REPRODUCIBLE** - Verde con animación, botón azul ▶️
- **🎶 NO REPRODUCIBLE** - Gris, sin botón de reproducción
- **🎭 TEMA PRINCIPAL** - Badge rojo brillante con glow
- **🎬 ENDING** - Badge azul suave
- **📱 Barra Now Playing** - Pegajosa en móvil con controles

### **🔧 Funcionalidades Técnicas:**

- **YouTube API** integrado para reproducción
- **Acordeón móvil-first** optimizado para táctil
- **Enlaces automáticos** a Spotify y Apple Music
- **Manejo de errores** con mensajes informativos
- **Auto-reproducción** de siguiente canción
- **Detalles expandibles** por canción

## 🚨 Solución de Problemas

### **❌ Problemas Comunes:**

#### **1. No se reproduce ninguna canción:**
```bash
# Verificar que hay soundtracks con YouTube ID
php artisan tinker --execute="
echo 'Reproducibles: ' . App\Models\Soundtrack::whereNotNull('youtube_id')->count();
"

# Si es 0, importar auténticos
php artisan soundtracks:import-authentic
```

#### **2. Solo aparecen soundtracks sintéticos:**
```bash
# Limpiar base de datos
php artisan soundtracks:clean-synthetic --force

# Importar auténticos
php artisan soundtracks:import-authentic
```

#### **3. Error de YouTube Player:**
- Abrir consola del navegador (F12)
- Verificar que se carga YouTube Player API
- Comprobar que el YouTube ID es válido

#### **4. No aparece el acordeón de banda sonora:**
```bash
# Verificar que la película/serie tiene soundtracks
php artisan tinker --execute="
\$movie = App\Models\Movie::with('soundtracks')->find(7);
echo 'Soundtracks: ' . \$movie->soundtracks->count();
"
```

## 📈 Métricas de Calidad

### **🎯 Estado Óptimo:**

- **✅ 100% Soundtracks auténticos** (popularidad ≥ 9.0)
- **✅ 100% Reproducibles** (con YouTube ID)
- **✅ Fuentes verificadas** (Netflix, tvN, Toho, etc.)
- **✅ 0% Contenido sintético**

### **📊 Comandos de Monitoreo:**

```bash
# Dashboard de calidad
php artisan tinker --execute="
echo '📊 DASHBOARD DE CALIDAD' . PHP_EOL;
\$total = App\Models\Soundtrack::count();
\$authentic = App\Models\Soundtrack::where('popularity', '>=', 9.0)->count();
\$reproducible = App\Models\Soundtrack::whereNotNull('youtube_id')->where('youtube_id', '!=', '')->count();
echo 'Total soundtracks: ' . \$total . PHP_EOL;
echo 'Auténticos: ' . \$authentic . ' (' . round((\$authentic/\$total)*100) . '%)' . PHP_EOL;
echo 'Reproducibles: ' . \$reproducible . ' (' . round((\$reproducible/\$total)*100) . '%)' . PHP_EOL;
echo 'Estado: ' . (\$authentic === \$total && \$reproducible === \$total ? '✅ ÓPTIMO' : '⚠️ REQUIERE LIMPIEZA') . PHP_EOL;
"
```

---

**🎵 Sistema de Soundtracks Auténticos - Dorasia** ✨  
*Garantizando contenido 100% verificado y reproducible*