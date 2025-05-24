<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMBRELLA Corp.</title>
    <link rel="icon" href="../Elementos/LogoIcono.png" type="image/png">

    <link rel="stylesheet" href="styles.css">
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <div class="container mt-4">
        <!-- Título de la página -->
        <img src="../Elementos/UmbrellaBanner.png" alt="Logo" class="banner-img">

        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-danger my-3" data-bs-toggle="modal" data-bs-target="#modalSubirImagen">
            Añadir Imagen
        </button>

        <!-- CONTAINER IMAGENES -->
        <div id="scroll-container">
            <div id="image-container"></div>
            <!-- Loader de carga -->
            <div id="loading" class="text-center" style="display: none;">
                <p>Cargando...</p>
            </div>
        </div>

        <!-- MARCA DE AGUA -->
        <div id="watermark" class="text-center">
            <p>Diseño y poducto propiedad de Tomàs Audi @taudigilabert</p>
        </div>



        <!-- Modal -->
        <div class="modal fade" id="modalSubirImagen" tabindex="-1" aria-labelledby="modalSubirImagenLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <img src="../Elementos/UmbrellaBanner.jpg" alt="Banner Umbrella Corp." width="400px">
                    </div>
                    <div class="modal-body">
                        <form id="upload-form" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="image" class="form-label">Selecciona una imagen</label>
                                <input type="file" id="image" name="image" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Subir Imagen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para ver imagen a tamaño completo -->
        <div class="modal fade" id="modalImagen" tabindex="-1" aria-labelledby="modalImagenLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <img id="modalImagenSrc" src="" class="img-fluid" alt="Imagen" />

                    </div>
                    <p class="text-center">Pulsa fuera de la imagen para cerrarla.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts-->
    <script src="script.js"></script>
    <script src="sonidoTeclas.js"></script>

    <!-- INCLUDES -->
    <?php include('reproductorAudio.php'); ?>



</body>

</html>