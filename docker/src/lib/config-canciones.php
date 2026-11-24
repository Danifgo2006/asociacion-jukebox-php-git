<?php
declare(strict_types=1);

/**
 * Configuración compartida del catálogo.
 * Utilizada tanto por RA1 como por RA2.
 */

function categoriasCanciones(): array
{
    return [
        'electronica' => 'Electrónica',
        'rock' => 'Rock',
        'bandas-sonoras' => 'Bandas sonoras'
    ];
}

function audiosCanciones(): array
{
    return [
        'audio/beat-it.mp3' => 'Beat It',
        'audio/come-as-you-are.mp3' => 'Come As You Are',
        'audio/devuelveme-a-mi-chica.mp3' => 'Devuélveme a mi chica',
        'audio/die-versteinerung.mp3' => 'Die Versteinerung',
        'audio/marta-tiene-un-marcapasos.mp3' => 'Marta tiene un marcapasos',
        'audio/mozart-symphony-40.mp3' => 'Sinfonía n.º 40 de Mozart',
        'audio/smells-like-teen-spirit.mp3' => 'Smells Like Teen Spirit',
        'audio/thriller.mp3' => 'Thriller',
        'audio/demo-02.mp3' => 'Demo 02',
        'audio/demo-03.mp3' => 'Demo 03',
        'audio/demo-04.mp3' => 'Demo 04'
    ];
}
