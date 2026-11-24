<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/config-canciones.php';

exigirAcceso();

$categorias = categoriasCanciones();
$audios = audiosCanciones();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="<?= e(tokenCsrf()) ?>">

    <title>Jukebox - Administración AJAX</title>


    <!-- Misma hoja de estilos que el Jukebox original (RA1) -->
    <link rel="stylesheet" href="/assets/app.css?v=10">

    <style>
        /* Solo ajustes necesarios para el formulario y la tabla AJAX. */
        #form-cancion {
            width: 100%;
            max-width: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
            gap: 18px;
        }

        #form-cancion label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        #form-cancion > div:last-child {
            grid-column: 1 / -1;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        #btn-cancelar[hidden] {
            display: none;
        }

        #btn-cancelar {
            background: #263b3d !important;
            color: #f3f8f5 !important;
            border: 1px solid #4a6464;
            box-shadow: none;
        }

        button.eliminar {
            background: linear-gradient(120deg, #ff9aa9, #ff7489) !important;
            color: #270b10 !important;
            box-shadow: none;
        }

        button.eliminar:hover {
            background: #ffb1bd !important;
        }

        .tabla-contenedor {
            width: 100%;
            overflow-x: auto;
        }

        .acciones {
            white-space: nowrap;
        }

        .acciones button {
            margin: 3px;
        }

        #mensaje {
            margin: 20px 0;
            padding: 14px;
            border-radius: 12px;
        }

        #mensaje.error {
            border: 1px solid #ff8496;
            background: rgba(133, 36, 56, 0.20);
            color: #ffbdc8 !important;
        }

        #mensaje.correcto {
            border: 1px solid rgba(200, 251, 93, 0.40);
            background: rgba(200, 251, 93, 0.08);
            color: #deff9a !important;
        }

        #total-canciones {
            color: #c8fb5d;
            font-weight: 800;
        }

        @media (max-width: 760px) {
            #form-cancion {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="scripts/catalogo-ajax.js" defer></script>
</head>

<body>
<main>

    <h1>Administración de canciones con AJAX</h1>

    <nav>
        <a href="home.php">Inicio</a>
        <a href="canciones.php">CRUD clásico</a>
    </nav>

    <section>

        <h2 id="titulo-formulario">Añadir canción</h2>

        <form id="form-cancion">

            <input type="hidden" id="cancion-id">

            <label for="title">
                Título
                <input type="text"
                       id="title"
                       name="title"
                       maxlength="80"
                       required>
            </label>

            <label for="artist">
                Artista
                <input type="text"
                       id="artist"
                       name="artist"
                       maxlength="80"
                       required>
            </label>

            <label for="category">
                Categoría
                <select id="category"
                        name="category"
                        required>

                    <option value="">
                        Selecciona una categoría
                    </option>

                    <?php foreach ($categorias as $valor => $texto): ?>
                        <option value="<?= e($valor) ?>">
                            <?= e($texto) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </label>

            <label for="duration">
                Duración en segundos
                <input type="number"
                       id="duration"
                       name="duration"
                       min="1"
                       max="1800"
                       required>
            </label>

            <label for="src">
                Archivo de audio
                <select id="src"
                        name="src"
                        required>

                    <option value="">
                        Selecciona un audio
                    </option>

                    <?php foreach ($audios as $ruta => $nombre): ?>
                        <option value="<?= e($ruta) ?>">
                            <?= e($nombre) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </label>

            <div>
                <button type="submit" id="btn-guardar">
                    Guardar canción
                </button>

                <button type="button"
                        id="btn-cancelar"
                        class="cancelar"
                        hidden>
                    Cancelar edición
                </button>
            </div>

        </form>

    </section>

    <section>

        <h2>Catálogo de canciones</h2>

        <button type="button" id="btn-recargar">
            Actualizar catálogo
        </button>

        <p id="mensaje" role="status" aria-live="polite">
            Preparando consulta...
        </p>

        <p>
            Total:
            <strong id="total-canciones">0</strong>
        </p>

        <div class="tabla-contenedor">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Artista</th>
                        <th>Categoría</th>
                        <th>Duración</th>
                        <th>Audio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tabla-canciones">
                </tbody>
            </table>

        </div>

    </section>

</main>
</body>
</html>
