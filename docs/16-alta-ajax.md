
# Bloque 16 - Alta AJAX

## Objetivo

Crear canciones mediante AJAX sin recargar
completamente la página.

## Archivos utilizados

- canciones-ajax.php
- scripts/catalogo-ajax.js
- api/catalogo.php

## Funcionamiento

1. El usuario rellena el formulario.
2. JavaScript recoge los campos.
3. fetch() envía una petición POST.
4. PHP comprueba sesión y CSRF.
5. PHP valida título, artista, categoría,
   duración y archivo de audio.
6. La canción se añade al JSON.
7. La API devuelve HTTP 201.
8. JavaScript actualiza la tabla.

## Seguridad

- Validación del servidor.
- Comprobación de sesión.
- Protección CSRF.
- Manejo de errores de la API.

## Pruebas

Pendiente de registrar el resultado real.

## Evidencia

Añadir una captura del formulario y otra
de la tabla después de crear la canción.
