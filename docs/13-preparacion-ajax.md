
# Bloque 13 - Preparación de RA2

## Objetivo

Preparar la ampliación AJAX de la aplicación Jukebox
conservando las funcionalidades de RA1.

## Proyecto

asociacion-jukebox-php-git

## Rama de trabajo

ra2-ajax

## Tecnologías

- PHP
- Apache
- JavaScript
- Fetch API
- JSON
- Docker Compose
- Git

## Preparación realizada

Se reutiliza el almacén JSON y el sistema de sesiones
de la aplicación original.

Se centralizan las categorías y los audios permitidos
en lib/config-canciones.php.

Se adapta la validación de canciones y el formulario
para compartir esta configuración.

## Archivos

- docker/src/lib/config-canciones.php
- docker/src/lib/validacion-cancion.php
- docker/src/lib/formulario-cancion.php

## Compatibilidad

Los formularios clásicos de RA1 se conservan.

La ampliación AJAX utiliza los mismos datos JSON
y las mismas reglas de validación.
