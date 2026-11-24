"use strict";

// =====================================
// RA2 - CRUD AJAX COMPLETO
// =====================================

const API = "api/catalogo.php";

const tabla = document.getElementById("tabla-canciones");
const mensaje = document.getElementById("mensaje");
const total = document.getElementById("total-canciones");

const formulario = document.getElementById("form-cancion");
const campoId = document.getElementById("cancion-id");

const tituloFormulario =
    document.getElementById("titulo-formulario");

const botonGuardar =
    document.getElementById("btn-guardar");

const botonCancelar =
    document.getElementById("btn-cancelar");

const botonRecargar =
    document.getElementById("btn-recargar");

const tokenCsrf = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

let cancionesActuales = [];


// =====================================
// MOSTRAR MENSAJES
// =====================================

function mostrarMensaje(texto, tipo = "") {
    mensaje.textContent = texto;
    mensaje.className = tipo;
}


// =====================================
// FORMATEAR DURACIÓN
// =====================================

function formatearDuracion(segundos) {
    const valor = Number(segundos);

    if (!Number.isFinite(valor) || valor < 0) {
        return "-";
    }

    const minutos = Math.floor(valor / 60);
    const resto = Math.floor(valor % 60);

    return `${minutos}:${String(resto).padStart(2, "0")}`;
}


// =====================================
// AÑADIR CELDA
// =====================================

function crearCelda(fila, texto) {
    const celda = document.createElement("td");

    celda.textContent = String(texto ?? "");

    fila.appendChild(celda);

    return celda;
}


// =====================================
// CREAR BOTÓN
// =====================================

function crearBoton(texto, accion, id, clase = "") {
    const boton = document.createElement("button");

    boton.type = "button";
    boton.textContent = texto;
    boton.dataset.accion = accion;
    boton.dataset.id = String(id);

    if (clase) {
        boton.className = clase;
    }

    return boton;
}


// =====================================
// MOSTRAR TABLA
// =====================================

function mostrarCanciones(canciones) {
    tabla.replaceChildren();

    total.textContent = String(canciones.length);

    if (canciones.length === 0) {
        mostrarMensaje("No hay canciones.", "correcto");
        return;
    }

    canciones.forEach(cancion => {
        const fila = document.createElement("tr");

        crearCelda(fila, cancion.id);
        crearCelda(fila, cancion.title);
        crearCelda(fila, cancion.artist);
        crearCelda(fila, cancion.category);

        crearCelda(
            fila,
            formatearDuracion(cancion.duration)
        );

        crearCelda(fila, cancion.src);

        const acciones = document.createElement("td");
        acciones.className = "acciones";

        acciones.appendChild(
            crearBoton("Editar", "editar", cancion.id)
        );

        acciones.appendChild(
            crearBoton(
                "Eliminar",
                "eliminar",
                cancion.id,
                "eliminar"
            )
        );

        fila.appendChild(acciones);
        tabla.appendChild(fila);
    });
}


// =====================================
// PETICIONES A LA API
// =====================================

async function peticionApi(metodo, datos = null) {
    const opciones = {
        method: metodo,
        credentials: "same-origin",
        cache: "no-store"
    };

    if (datos !== null) {
        opciones.headers = {
            "Content-Type": "application/json"
        };

        opciones.body = JSON.stringify({
            ...datos,
            csrf: tokenCsrf
        });
    }

    const respuesta = await fetch(API, opciones);

    let resultado;

    try {
        resultado = await respuesta.json();
    } catch (error) {
        throw new Error(
            "El servidor no devolvió JSON válido."
        );
    }

    if (!respuesta.ok || resultado.ok !== true) {
        const mensajeError = resultado.error ||
            `Error HTTP ${respuesta.status}`;

        if (resultado.errores) {
            const detalles = Object.entries(resultado.errores)
                .map(([campo, texto]) => `${campo}: ${texto}`)
                .join(" | ");

            throw new Error(`${mensajeError} ${detalles}`);
        }

        throw new Error(mensajeError);
    }

    return resultado;
}


// =====================================
// GET - CONSULTAR CANCIONES
// =====================================

async function cargarCanciones() {
    botonRecargar.disabled = true;

    mostrarMensaje("Cargando canciones...");

    try {
        const resultado = await peticionApi("GET");

        if (!Array.isArray(resultado.canciones)) {
            throw new Error(
                "Formato incorrecto del catálogo."
            );
        }

        cancionesActuales = resultado.canciones;

        mostrarCanciones(cancionesActuales);

        if (cancionesActuales.length > 0) {
            mostrarMensaje(
                "Catálogo cargado correctamente.",
                "correcto"
            );
        }

    } catch (error) {
        cancionesActuales = [];
        mostrarCanciones([]);
        mostrarMensaje(error.message, "error");

    } finally {
        botonRecargar.disabled = false;
    }
}


// =====================================
// RECOGER DATOS DEL FORMULARIO
// =====================================

function obtenerFormulario() {
    return {
        title: document.getElementById("title").value,
        artist: document.getElementById("artist").value,
        category: document.getElementById("category").value,
        duration: document.getElementById("duration").value,
        src: document.getElementById("src").value
    };
}


// =====================================
// REINICIAR FORMULARIO
// =====================================

function cancelarEdicion() {
    formulario.reset();

    campoId.value = "";

    tituloFormulario.textContent = "Añadir canción";
    botonGuardar.textContent = "Guardar canción";

    botonCancelar.hidden = true;
}


// =====================================
// POST Y PUT - GUARDAR CANCIÓN
// =====================================

async function guardarCancion(evento) {
    evento.preventDefault();

    const datos = obtenerFormulario();
    const id = campoId.value;

    const editando = id !== "";

    const metodo = editando ? "PUT" : "POST";

    if (editando) {
        datos.id = Number(id);
    }

    botonGuardar.disabled = true;

    try {
        const resultado = await peticionApi(metodo, datos);

        cancelarEdicion();

        await cargarCanciones();

        mostrarMensaje(
            resultado.mensaje,
            "correcto"
        );

    } catch (error) {
        mostrarMensaje(error.message, "error");

    } finally {
        botonGuardar.disabled = false;
    }
}


// =====================================
// PREPARAR EDICIÓN
// =====================================

function editarCancion(id) {
    const cancion = cancionesActuales.find(
        item => Number(item.id) === id
    );

    if (!cancion) {
        mostrarMensaje(
            "No se encontró la canción.",
            "error"
        );
        return;
    }

    campoId.value = cancion.id;

    document.getElementById("title").value =
        cancion.title;

    document.getElementById("artist").value =
        cancion.artist;

    document.getElementById("category").value =
        cancion.category;

    document.getElementById("duration").value =
        cancion.duration;

    document.getElementById("src").value =
        cancion.src;

    tituloFormulario.textContent = "Editar canción";

    botonGuardar.textContent = "Guardar cambios";

    botonCancelar.hidden = false;

    formulario.scrollIntoView({
        behavior: "smooth"
    });
}


// =====================================
// DELETE - ELIMINAR CANCIÓN
// =====================================

async function eliminarCancion(id) {
    const confirmar = window.confirm(
        `¿Quieres eliminar la canción con ID ${id}?`
    );

    if (!confirmar) {
        return;
    }

    try {
        const resultado = await peticionApi(
            "DELETE",
            { id }
        );

        if (Number(campoId.value) === id) {
            cancelarEdicion();
        }

        await cargarCanciones();

        mostrarMensaje(
            resultado.mensaje,
            "correcto"
        );

    } catch (error) {
        mostrarMensaje(error.message, "error");
    }
}


// =====================================
// EVENTOS DE LA TABLA
// =====================================

tabla.addEventListener("click", evento => {
    const boton = evento.target.closest(
        "button[data-accion]"
    );

    if (!boton) {
        return;
    }

    const id = Number(boton.dataset.id);

    if (boton.dataset.accion === "editar") {
        editarCancion(id);
    }

    if (boton.dataset.accion === "eliminar") {
        eliminarCancion(id);
    }
});


// =====================================
// EVENTOS DEL FORMULARIO
// =====================================

formulario.addEventListener(
    "submit",
    guardarCancion
);

botonCancelar.addEventListener(
    "click",
    cancelarEdicion
);

botonRecargar.addEventListener(
    "click",
    cargarCanciones
);


// =====================================
// CARGA INICIAL
// =====================================

cargarCanciones();
