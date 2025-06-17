# 📡 API DOCUMENTATION - DORASIA

## 🔗 Base URL
```
Production: https://dorasia.com/api
Development: http://localhost:8000/api
```

## 🔑 Autenticación

### Headers Requeridos
```http
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: {csrf_token}  # Para rutas web
Authorization: Bearer {token}  # Para futuras APIs autenticadas
```

---

## 🔍 SEARCH API

### **GET** `/api/search`
Búsqueda general de contenido (series, películas, actores)

#### Rate Limiting
- **Límite**: 30 requests por minuto por IP
- **Headers de respuesta**:
  ```http
  X-RateLimit-Limit: 30
  X-RateLimit-Remaining: 29
  X-RateLimit-Reset: 1640995200
  ```

#### Parámetros de Query
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `q` | string | ✅ | Término de búsqueda (min 2 caracteres) |
| `type` | string | ❌ | Filtro por tipo: `series`, `movies`, `actors` |
| `limit` | integer | ❌ | Límite de resultados (default: 10, max: 50) |

#### Ejemplo de Request
```bash
curl -X GET "http://localhost:8000/api/search?q=squid+game&type=series&limit=5" \
     -H "Accept: application/json"
```

#### Ejemplo de Response
```json
{
    "success": true,
    "data": {
        "series": [
            {
                "id": 1,
                "title": "El juego del calamar",
                "original_title": "오징어 게임",
                "display_title": "El juego del calamar",
                "poster_path": "/s4VuENQ0VD0D0NT2HcXg2xluqaW.jpg",
                "vote_average": 8.0,
                "first_air_date": "2021-09-17",
                "url": "/series/1"
            }
        ],
        "movies": [],
        "actors": []
    },
    "meta": {
        "query": "squid game",
        "total_results": 1,
        "execution_time": "0.15s"
    }
}
```

#### Códigos de Error
| Código | Descripción |
|--------|-------------|
| `400` | Query parameter `q` requerido o muy corto |
| `429` | Rate limit excedido |
| `500` | Error interno del servidor |

---

## 🎭 ACTORS AUTOCOMPLETE API

### **GET** `/api/actors/autocomplete`
Autocompletado de actores para formularios de búsqueda

#### Rate Limiting
- **Límite**: 60 requests por minuto por IP

#### Parámetros de Query
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `q` | string | ✅ | Nombre del actor (min 2 caracteres) |
| `limit` | integer | ❌ | Límite de resultados (default: 10, max: 20) |

#### Ejemplo de Request
```bash
curl -X GET "http://localhost:8000/api/actors/autocomplete?q=song&limit=5" \
     -H "Accept: application/json"
```

#### Ejemplo de Response
```json
{
    "success": true,
    "data": [
        {
            "id": 123,
            "name": "Song Kang",
            "display_name": "Song Kang",
            "profile_path": "/profile123.jpg",
            "known_for_department": "Acting",
            "popularity": 85.5,
            "url": "/actores/123"
        },
        {
            "id": 456,
            "name": "Song Hye-kyo",
            "display_name": "Song Hye-kyo", 
            "profile_path": "/profile456.jpg",
            "known_for_department": "Acting",
            "popularity": 92.1,
            "url": "/actores/456"
        }
    ],
    "meta": {
        "query": "song",
        "total_results": 2
    }
}
```

---

## 📅 UPCOMING API

### **GET** `/api/upcoming`
Lista de próximos estrenos de K-Dramas

#### Parámetros de Query
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `month` | string | ❌ | Filtrar por mes (formato: YYYY-MM) |
| `limit` | integer | ❌ | Límite de resultados (default: 20) |
| `status` | string | ❌ | Estado: `announced`, `filming`, `post_production` |

#### Ejemplo de Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Kingdom: Legacy",
            "spanish_title": "Reino: Legado",
            "air_date": "2024-07-15",
            "status": "filming",
            "poster_path": "/kingdom_legacy.jpg",
            "overview": "Secuela de la popular serie Kingdom...",
            "network": "Netflix",
            "episode_count": 8,
            "genres": ["Drama", "Histórico", "Zombies"]
        }
    ]
}
```

### **GET** `/api/upcoming/widget`
Widget compacto de próximos estrenos para embeds

#### Ejemplo de Response
```json
{
    "success": true,
    "data": {
        "next_release": {
            "title": "Reino: Legado",
            "air_date": "2024-07-15",
            "days_remaining": 45,
            "poster_path": "/kingdom_legacy.jpg"
        },
        "coming_soon": [
            {
                "title": "My Demon 2",
                "air_date": "2024-08-20",
                "days_remaining": 81
            }
        ]
    }
}
```

### **GET** `/api/upcoming/calendar`
Formato calendario para mostrar estrenos por fecha

#### Ejemplo de Response
```json
{
    "success": true,
    "data": {
        "2024-07": [
            {
                "date": "2024-07-15",
                "releases": [
                    {
                        "id": 1,
                        "title": "Reino: Legado",
                        "time": "15:00 KST"
                    }
                ]
            }
        ]
    }
}
```

### **GET** `/api/upcoming/by-date`
Estrenos filtrados por fecha específica

#### Parámetros de Query
| Parámetro | Tipo | Requerido | Descripción |
|-----------|------|-----------|-------------|
| `date` | string | ✅ | Fecha en formato YYYY-MM-DD |

---

## 🔐 AUTHENTICATION ENDPOINTS

### **POST** `/register`
Registro de nuevo usuario

#### Request Body
```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com", 
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Response Success (201)
```json
{
    "success": true,
    "message": "¡Bienvenido a Dorasia! Tu cuenta ha sido creada.",
    "user": {
        "id": 123,
        "name": "Juan Pérez",
        "email": "juan@example.com",
        "created_at": "2024-06-17T10:30:00Z"
    }
}
```

#### Response Error (422)
```json
{
    "success": false,
    "message": "Error de validación",
    "errors": {
        "email": ["Este email ya está registrado"],
        "password": ["La contraseña debe tener al menos 6 caracteres"]
    }
}
```

### **POST** `/login`
Iniciar sesión

#### Request Body
```json
{
    "email": "juan@example.com",
    "password": "password123"
}
```

#### Rate Limiting
- **Límite**: 5 intentos por minuto por IP

---

## ⭐ USER INTERACTION ENDPOINTS

### **POST** `/series/{id}/rate`
Calificar una serie (requiere autenticación)

#### Request Body
```json
{
    "rating_type": "love"  // "love", "like", "dislike"
}
```

#### Response
```json
{
    "success": true,
    "message": "¡Te encantó esta serie!",
    "rating_type": "love",
    "rating_counts": {
        "love": 245,
        "like": 189,
        "dislike": 12
    }
}
```

### **POST** `/series/{id}/watchlist`
Agregar/quitar serie de watchlist (requiere autenticación)

#### Request Body
```json
{
    "status": "watching"  // "pending", "watching", "completed"
}
```

### **POST** `/episodes/{id}/watched`
Marcar episodio como visto (requiere autenticación)

#### Request Body
```json
{
    "progress": 100,  // Porcentaje visto (0-100)
    "completed": true
}
```

---

## 📝 COMMENT ENDPOINTS

### **POST** `/series/{id}/comments`
Agregar comentario a una serie

#### Request Body
```json
{
    "content": "¡Excelente serie! La recomiendo mucho.",
    "rating": 5  // Opcional: 1-5 estrellas
}
```

#### Response
```json
{
    "success": true,
    "message": "Comentario agregado exitosamente",
    "comment": {
        "id": 456,
        "content": "¡Excelente serie! La recomiendo mucho.",
        "user": {
            "name": "Juan Pérez",
            "avatar": "/avatars/default.png"
        },
        "created_at": "2024-06-17T10:30:00Z"
    }
}
```

---

## 📊 ERROR HANDLING

### Estructura de Error Estándar
```json
{
    "success": false,
    "message": "Descripción del error",
    "error_code": "SPECIFIC_ERROR_CODE",
    "details": {
        "field": ["Error específico del campo"]
    }
}
```

### Códigos de Error Comunes
| Código HTTP | Error Code | Descripción |
|-------------|------------|-------------|
| `400` | `BAD_REQUEST` | Request malformado |
| `401` | `UNAUTHORIZED` | No autenticado |
| `403` | `FORBIDDEN` | Sin permisos |
| `404` | `NOT_FOUND` | Recurso no encontrado |
| `422` | `VALIDATION_ERROR` | Error de validación |
| `429` | `RATE_LIMIT` | Rate limit excedido |
| `500` | `INTERNAL_ERROR` | Error interno del servidor |

---

## 📈 RESPONSE METADATA

### Headers de Respuesta Estándar
```http
Content-Type: application/json
X-Response-Time: 150ms
X-Request-ID: req_abc123xyz
X-API-Version: 1.0
Cache-Control: public, max-age=300
```

### Estructura de Metadata
```json
{
    "meta": {
        "request_id": "req_abc123xyz",
        "execution_time": "0.15s", 
        "timestamp": "2024-06-17T10:30:00Z",
        "version": "1.0",
        "pagination": {
            "current_page": 1,
            "total_pages": 5,
            "per_page": 20,
            "total_results": 98
        }
    }
}
```

---

## 🧪 TESTING

### Postman Collection
```json
// Disponible en: /docs/dorasia_api.postman_collection.json
```

### cURL Examples
```bash
# Test de búsqueda
curl -X GET "http://localhost:8000/api/search?q=kingdom" \
     -H "Accept: application/json"

# Test de autocompletado
curl -X GET "http://localhost:8000/api/actors/autocomplete?q=song" \
     -H "Accept: application/json"

# Test de próximos estrenos
curl -X GET "http://localhost:8000/api/upcoming" \
     -H "Accept: application/json"
```

---

## 🔄 VERSIONADO

### Estrategia de Versionado
- **Versión Actual**: v1.0
- **Endpoint**: `/api/v1/...` (futuro)
- **Header**: `X-API-Version: 1.0`

### Política de Deprecación
- **Aviso Previo**: 3 meses antes de deprecar
- **Header de Deprecación**: `X-API-Deprecated: true`
- **Migración**: Documentación completa de cambios

---

**Documentación actualizada**: Junio 2025  
**Contacto API**: api@dorasia.com  
**Status Page**: https://status.dorasia.com