
# Bloque 14 - Diseño de la API

## Objetivo

Definir una API HTTP que permita administrar
canciones mediante peticiones AJAX.

## Endpoint

docker/src/api/catalogo.php

## Métodos

| Método | Función |
|--------|---------|
| GET | Consultar canciones |
| POST | Crear canción |
| PUT | Actualizar canción |
| DELETE | Eliminar canción |

## Ejemplo de petición POST

{
  "title": "Canción de prueba",
  "artist": "Alumno DAW",
  "category": "rock",
  "duration": "180",
  "src": "audio/demo-02.mp3",
  "csrf": "TOKEN_DE_LA_SESION"
}

El valor csrf es ilustrativo y debe sustituirse
por el token real de la sesión.

## Seguridad

Las peticiones requieren una sesión autenticada.

POST, PUT y DELETE requieren un token CSRF válido.

Los datos se validan en el servidor antes
de realizar operaciones de escritura.

## Almacenamiento

Se utiliza leerCanciones() para las consultas
y actualizarCanciones() para las escrituras.

## Respuestas HTTP

- 200: operación correcta
- 201: creación correcta
- 400: petición incorrecta
- 401: no autenticado
- 403: CSRF incorrecto
- 404: recurso inexistente
- 405: método no permitido
- 422: error de validación
- 500: error interno

## Formato

Todas las respuestas de la API utilizan JSON.
