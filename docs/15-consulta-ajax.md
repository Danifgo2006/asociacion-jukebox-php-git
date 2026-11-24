# Bloque 15 - Consulta AJAX del catálogo

## 1. Objetivo

Implementar la consulta de canciones mediante AJAX,
utilizando JavaScript Fetch API, PHP y JSON.

La consulta permite mostrar las canciones sin
recargar completamente la página.

## 2. Archivos utilizados

- docker/src/canciones-ajax.php
- docker/src/scripts/catalogo-ajax.js
- docker/src/api/catalogo.php
- docker/src/lib/almacen-json.php

## 3. Funcionamiento

1. El usuario abre canciones-ajax.php.
2. JavaScript ejecuta cargarCanciones().
3. fetch() envía una petición GET a api/catalogo.php.
4. PHP comprueba la sesión del usuario.
5. PHP ejecuta leerCanciones().
6. La API devuelve los datos en formato JSON.
7. JavaScript construye la tabla de canciones.

## 4. Funcionalidades implementadas

- Consulta mediante fetch().
- Tabla dinámica con JavaScript.
- Contador de canciones.
- Botón para actualizar el catálogo.
- Mensajes de carga.
- Gestión de errores.
- Gestión del catálogo vacío.
- Conversión de duración a minutos y segundos.
- Uso de textContent para mostrar datos.

## 5. Pruebas

Pendientes de completar después de ejecutar
la aplicación con Docker y comprobar la API.

### Resultados esperados

- La página carga correctamente.
- La API devuelve HTTP 200 con sesión válida.
- Las canciones aparecen en la tabla.
- El botón actualiza el catálogo.
- La página no se recarga completamente.

## 6. Conclusión

Se ha preparado la consulta AJAX del catálogo.
La validación final queda pendiente de las pruebas
en el navegador.
