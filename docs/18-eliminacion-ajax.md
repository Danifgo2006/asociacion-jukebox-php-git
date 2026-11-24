
# Bloque 18 - Eliminación AJAX

## Objetivo

Eliminar registros de canciones mediante AJAX.

## Funcionamiento

1. El usuario pulsa Eliminar.
2. JavaScript muestra una confirmación.
3. Si acepta, se realiza una petición DELETE.
4. PHP comprueba sesión y CSRF.
5. Se busca el registro por ID.
6. El registro se elimina del catálogo JSON.
7. Se actualiza la tabla.

## Seguridad

La eliminación requiere confirmación
y token CSRF válido.

## Archivos de audio

Solo se elimina el registro del catálogo.
No se borran los archivos MP3 físicos.

## Pruebas

Pendiente de registrar el resultado real.

## Evidencias

Añadir capturas antes y después
de eliminar una canción de prueba.
