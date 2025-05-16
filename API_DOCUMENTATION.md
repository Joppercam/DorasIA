# Documentación de API - Dorasia

Esta documentación describe todos los endpoints disponibles en la API de Dorasia.

## Autenticación

La mayoría de los endpoints requieren autenticación. Usa el token de autenticación en el header:

```
Authorization: Bearer {token}
```

## Base URL

```
https://api.dorasia.com/api
```

## Endpoints

### 1. Autenticación

#### Login
```http
POST /auth/login
```

**Request Body:**
```json
{
    "email": "usuario@ejemplo.com",
    "password": "contraseña"
}
```

**Response:**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
        "id": 1,
        "name": "Usuario",
        "email": "usuario@ejemplo.com"
    }
}
```

#### Registro
```http
POST /auth/register
```

**Request Body:**
```json
{
    "name": "Nombre Usuario",
    "email": "usuario@ejemplo.com",
    "password": "contraseña",
    "password_confirmation": "contraseña"
}
```

**Response:**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
        "id": 1,
        "name": "Nombre Usuario",
        "email": "usuario@ejemplo.com"
    }
}
```

#### Logout
```http
POST /auth/logout
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Successfully logged out"
}
```

### 2. Catálogo

#### Listar Títulos
```http
GET /titles
```

**Query Parameters:**
- `page` (integer): Número de página
- `per_page` (integer): Items por página (default: 20)
- `genre` (string): Filtrar por ID de género
- `category` (string): Filtrar por ID de categoría
- `sort` (string): Ordenar por (popular, recent, rating)
- `search` (string): Buscar por título

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Parasite",
            "original_title": "기생충",
            "type": "movie",
            "year": 2019,
            "runtime": 132,
            "synopsis": "Una familia pobre...",
            "tmdb_rating": 8.5,
            "tmdb_vote_count": 15234,
            "poster_url": "/url/to/poster.jpg",
            "backdrop_url": "/url/to/backdrop.jpg",
            "genres": [
                {
                    "id": 1,
                    "name": "Drama"
                }
            ]
        }
    ],
    "links": {
        "first": "http://api.dorasia.com/api/titles?page=1",
        "last": "http://api.dorasia.com/api/titles?page=10",
        "prev": null,
        "next": "http://api.dorasia.com/api/titles?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 10,
        "per_page": 20,
        "to": 20,
        "total": 200
    }
}
```

#### Obtener Título Individual
```http
GET /titles/{id}
```

**Response:**
```json
{
    "id": 1,
    "title": "Parasite",
    "original_title": "기생충",
    "type": "movie",
    "year": 2019,
    "runtime": 132,
    "synopsis": "Una familia pobre...",
    "tmdb_rating": 8.5,
    "tmdb_vote_count": 15234,
    "poster_url": "/url/to/poster.jpg",
    "backdrop_url": "/url/to/backdrop.jpg",
    "trailer_url": "https://youtube.com/watch?v=...",
    "genres": [
        {
            "id": 1,
            "name": "Drama"
        }
    ],
    "people": [
        {
            "id": 1,
            "name": "Bong Joon-ho",
            "role": "Director",
            "character": null,
            "profile_url": "/url/to/profile.jpg"
        }
    ],
    "seasons": [],
    "user_data": {
        "rating": 9,
        "in_watchlist": true,
        "watched": true
    }
}
```

#### Listar Géneros
```http
GET /genres
```

**Response:**
```json
[
    {
        "id": 1,
        "name": "Drama",
        "slug": "drama",
        "tmdb_id": 18
    },
    {
        "id": 2,
        "name": "Comedia",
        "slug": "comedia",
        "tmdb_id": 35
    }
]
```

#### Listar Categorías
```http
GET /categories
```

**Response:**
```json
[
    {
        "id": 1,
        "name": "K-Drama",
        "slug": "k-drama",
        "description": "Dramas coreanos",
        "language": "ko",
        "country": "KR"
    }
]
```

### 3. Perfiles Sociales

#### Seguir a un Perfil
```http
POST /profiles/{profile}/follow
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "followers_count": 150,
    "is_following": true
}
```

#### Dejar de Seguir
```http
DELETE /profiles/{profile}/follow
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "followers_count": 149,
    "is_following": false
}
```

#### Obtener Seguidores
```http
GET /profiles/{profile}/followers
```

**Query Parameters:**
- `page` (integer): Número de página
- `per_page` (integer): Items por página (default: 20)

**Response:**
```json
{
    "data": [
        {
            "id": 2,
            "user_id": 2,
            "username": "usuario2",
            "bio": "Amante del cine asiático",
            "avatar_url": "/url/to/avatar.jpg",
            "followers_count": 45,
            "following_count": 120,
            "is_following": false
        }
    ],
    "meta": {
        "total": 150,
        "per_page": 20,
        "current_page": 1
    }
}
```

#### Obtener Seguidos
```http
GET /profiles/{profile}/following
```

**Query Parameters:**
- `page` (integer): Número de página
- `per_page` (integer): Items por página (default: 20)

**Response:**
```json
{
    "data": [
        {
            "id": 3,
            "user_id": 3,
            "username": "usuario3",
            "bio": "Crítico de cine",
            "avatar_url": "/url/to/avatar.jpg",
            "followers_count": 500,
            "following_count": 200,
            "is_following": true
        }
    ],
    "meta": {
        "total": 120,
        "per_page": 20,
        "current_page": 1
    }
}
```

#### Obtener Feed de Actividad
```http
GET /profiles/feed
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (integer): Número de página
- `per_page` (integer): Items por página (default: 20)

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "type": "rating",
            "profile": {
                "id": 3,
                "username": "usuario3",
                "avatar_url": "/url/to/avatar.jpg"
            },
            "title": {
                "id": 5,
                "title": "Kingdom",
                "poster_url": "/url/to/poster.jpg"
            },
            "data": {
                "value": 9,
                "comment": "Excelente serie!"
            },
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 20,
        "total": 250
    }
}
```

#### Obtener Sugerencias de Perfiles
```http
GET /profiles/suggestions
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `limit` (integer): Número de sugerencias (default: 5)

**Response:**
```json
[
    {
        "id": 10,
        "user_id": 10,
        "username": "cinefilo",
        "bio": "Experto en cine asiático",
        "avatar_url": "/url/to/avatar.jpg",
        "followers_count": 1000,
        "common_genres": 3,
        "mutual_followers": 5
    }
]
```

### 4. Mensajería

#### Enviar Mensaje
```http
POST /messages/send
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "receiver_id": 5,
    "message": "Hola! Te recomiendo ver Kingdom"
}
```

**Response:**
```json
{
    "id": 123,
    "sender_id": 1,
    "receiver_id": 5,
    "message": "Hola! Te recomiendo ver Kingdom",
    "read": false,
    "created_at": "2024-01-15T10:30:00Z"
}
```

#### Obtener Conversaciones
```http
GET /messages/conversations
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
[
    {
        "user": {
            "id": 5,
            "name": "Usuario 5",
            "profile": {
                "username": "usuario5",
                "avatar_url": "/url/to/avatar.jpg"
            }
        },
        "last_message": {
            "id": 123,
            "message": "Hola! Te recomiendo ver Kingdom",
            "sent_by_me": true,
            "read": false,
            "created_at": "2024-01-15T10:30:00Z"
        },
        "unread_count": 2
    }
]
```

#### Obtener Conversación Individual
```http
GET /messages/conversation/{userId}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (integer): Número de página
- `per_page` (integer): Mensajes por página (default: 50)

**Response:**
```json
{
    "data": [
        {
            "id": 123,
            "sender_id": 1,
            "receiver_id": 5,
            "message": "Hola! Te recomiendo ver Kingdom",
            "read": true,
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 50,
        "total": 100
    }
}
```

#### Marcar Conversación como Leída
```http
PUT /messages/read/{conversationId}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "updated": 5
}
```

### 5. Interacciones con Títulos

#### Calificar Título
```http
POST /titles/{title}/rate
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "value": 8,
    "comment": "Muy buena película"
}
```

**Response:**
```json
{
    "id": 456,
    "profile_id": 1,
    "title_id": 10,
    "value": 8,
    "comment": "Muy buena película",
    "created_at": "2024-01-15T10:30:00Z"
}
```

#### Añadir a Watchlist
```http
POST /titles/{title}/watchlist
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "priority": "high",
    "notes": "Recomendado por amigo"
}
```

**Response:**
```json
{
    "id": 789,
    "profile_id": 1,
    "title_id": 10,
    "priority": "high",
    "notes": "Recomendado por amigo",
    "added_at": "2024-01-15T10:30:00Z"
}
```

#### Eliminar de Watchlist
```http
DELETE /titles/{title}/watchlist
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Removed from watchlist"
}
```

#### Registrar Visualización
```http
POST /titles/{title}/watch-history
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "episode_id": 123,
    "progress": 45,
    "duration": 60,
    "completed": false
}
```

**Response:**
```json
{
    "id": 1011,
    "profile_id": 1,
    "title_id": 10,
    "episode_id": 123,
    "progress": 45,
    "duration": 60,
    "completed": false,
    "watched_at": "2024-01-15T10:30:00Z"
}
```

### 6. Comentarios

#### Crear Comentario
```http
POST /comments
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "title_id": 10,
    "parent_id": null,
    "content": "Esta serie es increíble!"
}
```

**Response:**
```json
{
    "id": 1213,
    "user_id": 1,
    "title_id": 10,
    "parent_id": null,
    "content": "Esta serie es increíble!",
    "likes_count": 0,
    "replies_count": 0,
    "created_at": "2024-01-15T10:30:00Z",
    "user": {
        "id": 1,
        "name": "Usuario",
        "profile": {
            "username": "usuario1",
            "avatar_url": "/url/to/avatar.jpg"
        }
    }
}
```

#### Dar Like a Comentario
```http
POST /comments/{comment}/like
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "likes_count": 15,
    "user_liked": true
}
```

#### Quitar Like
```http
DELETE /comments/{comment}/like
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "likes_count": 14,
    "user_liked": false
}
```

### 7. Notificaciones

#### Obtener Notificaciones
```http
GET /notifications
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (integer): Número de página
- `per_page` (integer): Items por página (default: 20)
- `unread_only` (boolean): Solo notificaciones no leídas

**Response:**
```json
{
    "data": [
        {
            "id": "abc123",
            "type": "App\\Notifications\\NewFollower",
            "data": {
                "follower_name": "usuario5",
                "follower_id": 5,
                "message": "usuario5 comenzó a seguirte"
            },
            "read_at": null,
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "total": 50,
        "unread_count": 10,
        "current_page": 1
    }
}
```

#### Obtener Notificaciones Recientes
```http
GET /notifications/recent
```

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `limit` (integer): Número de notificaciones (default: 5)

**Response:**
```json
{
    "notifications": [
        {
            "id": "abc123",
            "type": "new_follower",
            "data": {
                "follower_name": "usuario5",
                "message": "usuario5 comenzó a seguirte"
            },
            "read_at": null,
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "unread_count": 10
}
```

#### Marcar Notificación como Leída
```http
PUT /notifications/{id}/read
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "notification": {
        "id": "abc123",
        "read_at": "2024-01-15T10:35:00Z"
    }
}
```

#### Marcar Todas como Leídas
```http
PUT /notifications/read-all
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "updated": 10
}
```

#### Eliminar Notificación
```http
DELETE /notifications/{id}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification deleted"
}
```

## Códigos de Estado HTTP

- `200 OK`: Solicitud exitosa
- `201 Created`: Recurso creado exitosamente
- `204 No Content`: Solicitud exitosa sin contenido
- `400 Bad Request`: Solicitud mal formada
- `401 Unauthorized`: No autenticado
- `403 Forbidden`: No autorizado
- `404 Not Found`: Recurso no encontrado
- `422 Unprocessable Entity`: Error de validación
- `429 Too Many Requests`: Límite de rate excedido
- `500 Internal Server Error`: Error del servidor

## Manejo de Errores

Los errores se devuelven en el siguiente formato:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password must be at least 8 characters."
        ]
    }
}
```

## Rate Limiting

La API tiene los siguientes límites:

- Endpoints de autenticación: 5 solicitudes por minuto
- Endpoints de búsqueda: 30 solicitudes por minuto
- Otros endpoints: 60 solicitudes por minuto

Los límites se devuelven en los headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1642252800
```

## Paginación

Los endpoints que devuelven listas utilizan paginación con el siguiente formato:

```json
{
    "data": [...],
    "links": {
        "first": "http://api.dorasia.com/api/resource?page=1",
        "last": "http://api.dorasia.com/api/resource?page=10",
        "prev": null,
        "next": "http://api.dorasia.com/api/resource?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 10,
        "per_page": 20,
        "to": 20,
        "total": 200
    }
}
```

## Webhooks (Opcional)

Si tu aplicación soporta webhooks, puedes recibir notificaciones de eventos:

### Eventos Disponibles
- `user.followed`: Cuando alguien sigue a un usuario
- `message.received`: Cuando se recibe un mensaje
- `comment.liked`: Cuando se da like a un comentario
- `title.rated`: Cuando se califica un título

### Formato de Webhook
```json
{
    "event": "user.followed",
    "data": {
        "follower_id": 5,
        "followed_id": 1,
        "timestamp": "2024-01-15T10:30:00Z"
    },
    "signature": "sha256=..."
}
```

## SDK y Librerías

### JavaScript/TypeScript
```javascript
import DorasiaAPI from '@dorasia/api-client';

const api = new DorasiaAPI({
    token: 'your-api-token'
});

// Ejemplo de uso
const titles = await api.titles.list({ genre: 'drama' });
```

### PHP
```php
use Dorasia\ApiClient;

$client = new ApiClient(['token' => 'your-api-token']);

// Ejemplo de uso
$titles = $client->titles()->list(['genre' => 'drama']);
```

## Ejemplos de Integración

### Autenticación Completa
```javascript
// Login
const response = await fetch('https://api.dorasia.com/api/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        email: 'usuario@ejemplo.com',
        password: 'contraseña'
    })
});

const { token, user } = await response.json();

// Usar token en siguientes solicitudes
const titles = await fetch('https://api.dorasia.com/api/titles', {
    headers: {
        'Authorization': `Bearer ${token}`
    }
});
```

### Búsqueda con Filtros
```javascript
const params = new URLSearchParams({
    search: 'Kingdom',
    genre: 'drama',
    category: 'k-drama',
    sort: 'popular',
    page: 1
});

const response = await fetch(`https://api.dorasia.com/api/titles?${params}`, {
    headers: {
        'Authorization': `Bearer ${token}`
    }
});

const { data, meta } = await response.json();
```

### Real-time con WebSockets
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: 'your-pusher-key',
    cluster: 'your-cluster',
    encrypted: true,
    auth: {
        headers: {
            Authorization: `Bearer ${token}`
        }
    }
});

// Escuchar notificaciones
echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        console.log('Nueva notificación:', notification);
    });
```

## Versionado

La API usa versionado en la URL. La versión actual es v1:

```
https://api.dorasia.com/api/v1/titles
```

## Soporte

Para soporte técnico o reportar problemas:
- Email: api-support@dorasia.com
- GitHub Issues: https://github.com/dorasia/api-issues
- Documentación: https://developers.dorasia.com

---

Última actualización: Diciembre 2024