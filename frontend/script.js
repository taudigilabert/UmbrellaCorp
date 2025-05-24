// ================================ VARIABLES GLOBALES ================================
let page = 1;
let loading = false;
let noMoreImages = false;


// ================================ FUNCIONES DE INICIALIZACIÓN ================================
// ================================ INICIALIZACIÓN ================================
document.addEventListener("DOMContentLoaded", function () {
    loadImages();

    const uploadForm = document.getElementById('upload-form');
    if (!uploadForm) {
        console.error('Formulario no encontrado');
        return;
    }

    uploadForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        const imageInput = document.getElementById('image');
        const descripcionInput = document.getElementById('descripcion');

        if (!imageInput.files[0]) {
            // Notificación de error si no se seleccionó una imagen
            await Swal.fire({
                title: 'Error',
                text: 'Debes seleccionar una imagen.',
                icon: 'error',
                customClass: {
                    popup: 'alert-popup',
                    confirmButton: 'alert-confirm'
                }
            });
            return;
        }

        const formData = new FormData();
        formData.append('image', imageInput.files[0]);
        formData.append('descripcion', descripcionInput.value);

        try {
            const res = await fetch('../backend/API/imagenesAPI.php', {
                method: 'POST',
                body: formData,
            });
            const result = await res.json();

            if (result.success) {
                // Cerrar modal con la instancia existente
                const modalEl = document.getElementById('modalSubirImagen');
                bootstrap.Modal.getInstance(modalEl).hide();

                // Limpiar formulario
                imageInput.value = '';
                descripcionInput.value = '';

                // Reiniciar paginación y limpiar contenedor
                loading = false;
                page = 1;
                noMoreImages = false;
                document.getElementById('image-container').innerHTML = '';

                // Volver a cargar las primeras imágenes
                await loadImages();

                // Notificación de éxito
                await Swal.fire({
                    title: 'Imagen añadida',
                    text: 'La imagen se ha añadido correctamente.',
                    icon: 'success',
                    customClass: {
                        popup: 'alert-popup',
                        confirmButton: 'alert-confirm'
                    }
                });
            } else {
                // Notificación de error si la carga falla
                await Swal.fire({
                    title: 'Error',
                    text: 'Error al añadir la imagen: ' + result.message,
                    icon: 'error',
                    customClass: {
                        popup: 'alert-popup',
                        confirmButton: 'alert-confirm'
                    }
                });
            }
        } catch (error) {
            console.error('Error al enviar la imagen:', error);
            // Notificación de error al conectar con el servidor
            await Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al conectar con el servidor.',
                icon: 'error',
                customClass: {
                    popup: 'alert-popup',
                    confirmButton: 'alert-confirm'
                }
            });
        }
    });
});



// ================================ FUNCIONES DE IMÁGENES ================================
// -------- Cargar imágenes
async function loadImages() {
    if (loading) return;
    if (noMoreImages && page !== 1) return;

    loading = true;
    document.getElementById('loading').style.display = 'block';

    try {
        const apiUrl = `../backend/API/imagenesAPI.php?page=${page}`;
        const res = await fetch(apiUrl);
        const data = await res.json();

        if (data.length === 0) {
            noMoreImages = true;
            document.getElementById('loading').innerText = 'No hay más imágenes.';
            return;
        }

        const container = document.getElementById('image-container');

        data.forEach(img => {
            const existingImage = document.getElementById(`image-${img.id}`);
            if (existingImage) return;

            const col = document.createElement('div');
            col.className = '';
            col.id = `image-${img.id}`;
            const imageUrl = `../imagenes/${img.url}`;
            col.innerHTML = ` 
                <div class="card mb-4">
                    <img src="${imageUrl}" class="card-img-top" alt="${img.descripcion || 'Imagen sin descripción'}" onclick="mostrarImagenCompleta('${imageUrl}')">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">${img.descripcion || 'Sin descripción'}</h5>
                            <img id="icono-mg-${img.id}" src="../Elementos/biohazardGris.png"
                                style="cursor: pointer; width: 40px;"
                                onclick="toggleMeGusta(${img.id})" />
                        </div>
                        <p class="card-text mt-2">
                            <strong id="like-count-${img.id}">${img.megusta || 0}</strong> Me Gusta |
                            Comentarios: <strong id="comentario-count-${img.id}">${img.comentarios || 0}</strong>
                        </p>
                        <div id="comentarios-${img.id}" class="comentarios mt-1"></div>
                        <button class="btn btn-outline-light btn-sm" onclick="cargarComentarios(${img.id}, this)">Ver comentarios</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteImage(${img.id}, document.getElementById('image-${img.id}'))">Eliminar</button>
                    </div>
                </div>
            `;
            container.appendChild(col);
        });

        page++;

    } catch (error) {
        console.error('Error al cargar imágenes:', error);
    }

    document.getElementById('loading').style.display = 'none';
    loading = false;
}

// Cargar imagen al eliminar una
async function cargarUnaImagenExtra() {
    const apiUrl = `../backend/API/imagenesAPI.php?page=${page}&limit=1`;
    const res = await fetch(apiUrl);
    const data = await res.json();

    if (data.length > 0) {
        const container = document.getElementById('image-container');
        data.forEach(img => {
            const col = document.createElement('div');
            col.className = '';
            col.id = `image-${img.id}`;
            const imageUrl = `../imagenes/${img.url}`;
            col.innerHTML = ` 
                <div class="card mb-4">
                    <img src="${imageUrl}" class="card-img-top" alt="${img.descripcion || 'Imagen sin descripción'}" onclick="mostrarImagenCompleta('${imageUrl}')">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">${img.descripcion || 'Sin descripción'}</h5>
                            <img id="icono-mg-${img.id}" src="../Elementos/biohazardGris.png"
                                style="cursor: pointer; width: 40px;"
                                onclick="toggleMeGusta(${img.id})" />
                        </div>
                        <p class="card-text mt-2">
                            <strong id="like-count-${img.id}">${img.megusta || 0}</strong> Me Gusta |
                            Comentarios: <strong id="comentario-count-${img.id}">${img.comentarios || 0}</strong>
                        </p>
                        <div id="comentarios-${img.id}" class="comentarios mt-1"></div>
                        <button class="btn btn-outline-light btn-sm" onclick="cargarComentarios(${img.id}, this)">Ver comentarios</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteImage(${img.id}, document.getElementById('image-${img.id}'))">Eliminar</button>
                    </div>
                </div>
            `;
            container.appendChild(col);
        });
        page++;
    } else {
        noMoreImages = true;
    }
}

// -------- Eliminar imagen
async function deleteImage(id, imageElement) {
    // Mostrar la alerta de confirmación
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        imageUrl: '../Elementos/biohazardRojo.png',
        imageWidth: 120,
        imageHeight: 120,
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar',
        reverseButtons: true,


        customClass: {
            popup: 'alert-popup',
            cancelButton: 'alert-cancel',
            confirmButton: 'alert-confirm',

        },
        // Sin animaciones de entrada y salida
        showClass: {
            popup: ''
        },
        hideClass: {
            popup: ''
        }
    });

    // Esperar confirmación
    if (!result.isConfirmed) return;

    try {
        const res = await fetch('../backend/API/imagenesAPI.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id })
        });

        const result = await res.json();

        if (result.success) {
            // Notificación de éxito
            await Swal.fire({
                title: 'Eliminado',
                text: 'La imagen ha sido eliminada correctamente.',
                icon: 'success',
                customClass: {
                    popup: 'alert-popup',
                    confirmButton: 'alert-confirm'
                }
            });

            imageElement.remove();
            await cargarUnaImagenExtra();

        } else {
            // Notificación de error
            await Swal.fire({
                title: 'Error',
                text: 'No se pudo eliminar la imagen.',
                icon: 'error',
                customClass: {
                    popup: 'alert-popup',
                    confirmButton: 'alert-confirm'
                }
            });
        }
    } catch (error) {
        console.error('Error al eliminar imagen:', error);
    }
}

// -------- Ver imagen completa
function mostrarImagenCompleta(imagenUrl) {
    const modalImagenSrc = document.getElementById('modalImagenSrc');
    modalImagenSrc.src = imagenUrl;

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalImagen'));
    modal.show();
}



// ================================ CARGAR LOS COMENTARIOS ================================
// -------- Cargar los comentarios de una imagen
async function cargarComentarios(imagenId, boton) {
    const contenedor = document.getElementById(`comentarios-${imagenId}`);

    if (contenedor.style.display === 'none' || contenedor.innerHTML === '') {
        try {
            const res = await fetch(`../backend/API/comentariosAPI.php?imagen_id=${imagenId}`);
            const data = await res.json();
            const comentarios = data.comentarios || [];

            contenedor.innerHTML = '';

            if (comentarios.length === 0) {
                contenedor.innerHTML = '<p class="text-muted">No hay comentarios.</p>';
            } else {
                comentarios.forEach(com => {
                    const p = document.createElement('p');
                    p.innerHTML = `<strong>${com.usuario}</strong>: ${com.comentario} <p class=text-muted>(${com.fecha_comentario})</p>`;
                    contenedor.appendChild(p);
                });
            }

            const contador = document.getElementById(`comentario-count-${imagenId}`);
            if (contador) {
                contador.textContent = comentarios.length;
            }

            const commentForm = document.createElement('div');
            commentForm.innerHTML = `
                <textarea id="comentario-${imagenId}" class="form-control mt-3" placeholder="Escribe un comentario..."></textarea>
                <button class="btn btn-primary btn-sm my-3" onclick="addComment(${imagenId})">Añadir comentario</button>
            `;
            contenedor.appendChild(commentForm);

            boton.textContent = 'Ocultar comentarios';
            contenedor.style.display = 'block';

        } catch (error) {
            console.error('Error al cargar comentarios:', error);
        }
    } else {
        contenedor.innerHTML = '';
        if (boton) boton.textContent = 'Ver comentarios';
        contenedor.style.display = 'none';
    }
}

// -------- Añadir un comentario
async function addComment(imagenId) {
    const comentarioInput = document.getElementById(`comentario-${imagenId}`);
    const comentario = comentarioInput.value.trim();

    if (!comentario) {
        // Notificación si no se escribe un comentario
        await Swal.fire({
            title: 'Error',
            text: 'Debes escribir un comentario.',
            icon: 'error',
            customClass: {
                popup: 'alert-popup',
                confirmButton: 'alert-confirm'
            }
        });
        return;
    }

    try {
        const res = await fetch('../backend/API/comentariosAPI.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ imagen_id: imagenId, comentario })
        });

        const result = await res.json();

        if (result.success) {
            // Notificación de éxito al añadir comentario
            await Swal.fire({
                title: 'Comentario añadido',
                text: 'El comentario se ha añadido correctamente.',
                icon: 'success',
                customClass: {
                    popup: 'alert-popup',
                    confirmButton: 'alert-confirm'
                }
            });

            comentarioInput.value = '';
            await cargarComentarios(imagenId, document.querySelector(`#comentarios-${imagenId} + button`));

            const contador = document.getElementById(`comentario-count-${imagenId}`);
            if (contador) {
                contador.textContent = (parseInt(contador.textContent) + 1).toString();
            }

        } else {
            // Notificación de error si no se pudo añadir el comentario
            await Swal.fire({
                title: 'Error',
                text: 'Error al añadir el comentario: ' + result.message,
                icon: 'error',
                customClass: {
                    popup: 'alert-popup',
                    confirmButton: 'alert-confirm'
                }
            });
        }
    } catch (error) {
        console.error('Error al añadir comentario:', error);
        // Notificación de error en caso de fallo de la solicitud
        await Swal.fire({
            title: 'Error',
            text: 'Error en la solicitud. Ver consola para detalles.',
            icon: 'error',
            customClass: {
                popup: 'alert-popup',
                confirmButton: 'alert-confirm'
            }
        });
    }
}


// ================================ FUNCIONES DE "ME GUSTA" ================================
// -------- Función para manejar el "Me Gusta"
async function toggleMeGusta(imagenId) {
    try {
        const res = await fetch('../backend/API/megustaAPI.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ imagen_id: imagenId })
        });

        const text = await res.text();
        const result = JSON.parse(text);

        if (result.success) {
            const icon = document.getElementById(`icono-mg-${imagenId}`);
            const count = document.getElementById(`like-count-${imagenId}`);

            count.textContent = result.megusta_actualizado;

            if (result.estado === 'dado') {
                icon.src = '../Elementos/biohazardRojo.png';
            } else {
                icon.src = '../Elementos/biohazardGris.png';
            }

        } else {
            // Notificación de error
            await Swal.fire({
                title: 'Error',
                text: 'Error al procesar Me Gusta.',
                icon: 'error',
                customClass: {
                    popup: 'alert-popup',
                    confirmButton: 'alert-confirm'
                }
            });
        }
    } catch (err) {
        console.error('Error en toggleMeGusta:', err);
        // Notificación de error en la solicitud
        await Swal.fire({
            title: 'Error',
            text: 'Error en la solicitud. Ver consola para detalles.',
            icon: 'error',
            customClass: {
                popup: 'alert-popup',
                confirmButton: 'alert-confirm'
            }
        });
    }
}

//=============================== FUNCIONES DE SCROLL ===============================
const scrollContainer = document.getElementById('scroll-container');

scrollContainer.addEventListener('scroll', () => {
    const { scrollTop, scrollHeight, clientHeight } = scrollContainer;

    if (scrollTop + clientHeight >= scrollHeight - 100) {
        loadImages();
    }
});


/*
//================ Scroll paginado con rueda del ratón en el contenedor =================
document.addEventListener('DOMContentLoaded', () => {
    const scrollContainer = document.getElementById('scroll-container');

    if (!scrollContainer) {
        console.warn('Contenedor de scroll no encontrado');
        return;
    }

    scrollContainer.addEventListener('wheel', function (e) {
        e.preventDefault();

        const cards = scrollContainer.querySelectorAll('.card');
        if (cards.length === 0) return;

        const cardHeight = cards[0].offsetHeight + 40; // px de margen/gap
        const scrollAmount = cardHeight;

        scrollContainer.scrollBy({
            top: e.deltaY > 0 ? scrollAmount : -scrollAmount,
            behavior: 'smooth'
        });
    }, { passive: false });
});
*/

