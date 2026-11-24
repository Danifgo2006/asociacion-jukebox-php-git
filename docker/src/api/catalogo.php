<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/sesion.php';
require_once __DIR__ . '/../lib/almacen-json.php';
require_once __DIR__ . '/../lib/validacion-cancion.php';

iniciarSesionJukebox();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

// RESPUESTAS JSON

function responderJson(int $codigo, array $contenido): never
{
    http_response_code($codigo);

    echo json_encode(
        $contenido,
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );

    exit;
}

// AUTENTICACIÓN
// Adaptar esta comprobación si el login de RA1
// utiliza otra variable de sesión.


if (
    !isset($_SESSION['usuario_id']) ||
    !is_int($_SESSION['usuario_id'])
) {
    responderJson(401, [
        'ok' => false,
        'error' => 'Debes iniciar sesión.'
    ]);
}


// LEER CUERPO JSON

function leerPeticionJson(): array
{
    $texto = file_get_contents('php://input');

    if ($texto === false || strlen($texto) > 65536) {
        responderJson(400, [
            'ok' => false,
            'error' => 'Petición demasiado grande o ilegible.'
        ]);
    }

    try {
        $datos = json_decode(
            $texto,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    } catch (JsonException $error) {
        responderJson(400, [
            'ok' => false,
            'error' => 'JSON incorrecto.'
        ]);
    }

    if (!is_array($datos) || array_is_list($datos)) {
        responderJson(400, [
            'ok' => false,
            'error' => 'Se esperaba un objeto JSON.'
        ]);
    }

    return $datos;
}

// SEGURIDAD CSRF

function comprobarCsrf(array $entrada): void
{
    $recibido = $entrada['csrf'] ?? null;
    $guardado = $_SESSION['csrf'] ?? null;

    if (
        !is_string($recibido) ||
        !is_string($guardado) ||
        !hash_equals($guardado, $recibido)
    ) {
        responderJson(403, [
            'ok' => false,
            'error' => 'Token CSRF incorrecto.'
        ]);
    }
}

// VALIDAR ID

function obtenerId(array $entrada): int
{
    $id = filter_var(
        $entrada['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($id === false || $id === null || $id < 1) {
        responderJson(400, [
            'ok' => false,
            'error' => 'ID de canción no válido.'
        ]);
    }

    return $id;
}

// VALIDAR DATOS DE CANCIÓN

function obtenerDatosCancion(array $entrada): array
{
    $resultado = validarDatosCancion($entrada);

    if (!empty($resultado['errores'])) {
        responderJson(422, [
            'ok' => false,
            'error' => 'Datos de canción incorrectos.',
            'errores' => $resultado['errores']
        ]);
    }

    return $resultado['datos'];
}

// CONTROLADOR HTTP

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {

    // ===================================
    // GET - CONSULTAR CANCIONES
    // ===================================

    if ($metodo === 'GET') {

        $canciones = leerCanciones();

        responderJson(200, [
            'ok' => true,
            'canciones' => $canciones,
            'total' => count($canciones)
        ]);
    }

    // Los métodos de escritura reciben JSON.
    $entrada = [];

    if (in_array($metodo, ['POST', 'PUT', 'DELETE'], true)) {
        $entrada = leerPeticionJson();
        comprobarCsrf($entrada);
    }

    // ===================================
    // POST - CREAR CANCIÓN
    // ===================================

    if ($metodo === 'POST') {

        $datos = obtenerDatosCancion($entrada);

        $nueva = actualizarCanciones(
            function (array $canciones) use ($datos): array {

                $maximo = 0;

                foreach ($canciones as $cancion) {
                    $maximo = max(
                        $maximo,
                        (int) ($cancion['id'] ?? 0)
                    );
                }

                $nuevaCancion = array_merge(
                    ['id' => $maximo + 1],
                    $datos
                );

                $canciones[] = $nuevaCancion;

                return [$canciones, $nuevaCancion];
            }
        );

        responderJson(201, [
            'ok' => true,
            'mensaje' => 'Canción creada.',
            'cancion' => $nueva
        ]);
    }

    // ===================================
    // PUT - EDITAR CANCIÓN
    // ===================================

    if ($metodo === 'PUT') {

        $id = obtenerId($entrada);
        $datos = obtenerDatosCancion($entrada);

        $actualizada = actualizarCanciones(
            function (array $canciones) use ($id, $datos): array {

                foreach ($canciones as $indice => $cancion) {

                    if ((int) ($cancion['id'] ?? 0) === $id) {

                        $canciones[$indice] = array_merge(
                            $cancion,
                            $datos,
                            ['id' => $id]
                        );

                        return [
                            $canciones,
                            $canciones[$indice]
                        ];
                    }
                }

                throw new OutOfBoundsException(
                    'Canción no encontrada.'
                );
            }
        );

        responderJson(200, [
            'ok' => true,
            'mensaje' => 'Canción actualizada.',
            'cancion' => $actualizada
        ]);
    }

    // ===================================
    // DELETE - ELIMINAR CANCIÓN
    // ===================================

    if ($metodo === 'DELETE') {

        $id = obtenerId($entrada);

        actualizarCanciones(
            function (array $canciones) use ($id): array {

                foreach ($canciones as $indice => $cancion) {

                    if ((int) ($cancion['id'] ?? 0) === $id) {

                        array_splice($canciones, $indice, 1);

                        return [$canciones, true];
                    }
                }

                throw new OutOfBoundsException(
                    'Canción no encontrada.'
                );
            }
        );

        responderJson(200, [
            'ok' => true,
            'mensaje' => 'Canción eliminada.'
        ]);
    }

    header('Allow: GET, POST, PUT, DELETE');

    responderJson(405, [
        'ok' => false,
        'error' => 'Método HTTP no permitido.'
    ]);

} catch (OutOfBoundsException $error) {

    responderJson(404, [
        'ok' => false,
        'error' => 'Canción no encontrada.'
    ]);

} catch (Throwable $error) {

    error_log('Error catálogo AJAX: ' . $error->getMessage());

    responderJson(500, [
        'ok' => false,
        'error' => 'Error interno del servidor.'
    ]);
}
