<?php
declare(strict_types=1);

require_once __DIR__ . '/config-canciones.php';

function validarDatosCancion(array $entrada): array
{
    $categorias = array_keys(categoriasCanciones());
    $audios = array_keys(audiosCanciones());

    $valores = [
        'title' => '',
        'artist' => '',
        'category' => '',
        'duration' => '',
        'src' => ''
    ];

    $datos = [];
    $errores = [];

    foreach (array_keys($valores) as $campo) {
        $raw = $entrada[$campo] ?? null;
        if (!is_string($raw)) {
            $errores[$campo] = 'El campo no tiene un formato válido.';
        } else {
            $valores[$campo] = $raw;
        }
    }

    if (!isset($errores['title'])) {
        $v = trim($valores['title']);
        if ($v === '') $errores['title'] = 'El título es obligatorio.';
        elseif (mb_strlen($v, 'UTF-8') > 80) $errores['title'] = 'Máximo 80 caracteres.';
        else { $datos['title'] = $v; $valores['title'] = $v; }
    }

    if (!isset($errores['artist'])) {
        $v = trim($valores['artist']);
        if ($v === '') $errores['artist'] = 'El artista es obligatorio.';
        elseif (mb_strlen($v, 'UTF-8') > 80) $errores['artist'] = 'Máximo 80 caracteres.';
        else { $datos['artist'] = $v; $valores['artist'] = $v; }
    }

    if (!isset($errores['category'])) {
        if (!in_array($valores['category'], $categorias, true)) $errores['category'] = 'Categoría no válida.';
        else $datos['category'] = $valores['category'];
    }

    if (!isset($errores['duration'])) {
        $v = filter_var($valores['duration'], FILTER_VALIDATE_INT);
        if ($v === false || $v < 1 || $v > 1800) $errores['duration'] = 'La duración debe estar entre 1 y 1800 segundos.';
        else $datos['duration'] = $v;
    }

    if (!isset($errores['src'])) {
        if (!in_array($valores['src'], $audios, true)) $errores['src'] = 'Audio no permitido.';
        else $datos['src'] = $valores['src'];
    }

    return ['datos' => $datos, 'valores' => $valores, 'errores' => $errores];
}
