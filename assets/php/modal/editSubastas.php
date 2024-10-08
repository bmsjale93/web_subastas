<?php if (isset($subasta)): ?>
    <?php
    global $conn;

    // Obtener el comentario actual de la subasta
    $stmt_comentarios = $conn->prepare("SELECT comentario FROM Comentarios WHERE id_subasta = :id_subasta");
    $stmt_comentarios->bindParam(':id_subasta', $subasta['id_subasta'], PDO::PARAM_INT);
    $stmt_comentarios->execute();
    $comentario = $stmt_comentarios->fetchColumn();

    // Obtener los datos de SubastaIdealista
    $stmt_idealista = $conn->prepare("SELECT * FROM SubastaIdealista WHERE id_subasta = :id_subasta");
    $stmt_idealista->bindParam(':id_subasta', $subasta['id_subasta'], PDO::PARAM_INT);
    $stmt_idealista->execute();
    $idealista = $stmt_idealista->fetch(PDO::FETCH_ASSOC);

    // Obtener los detalles de la subasta, incluyendo el campo carga_subastas
    $stmt_det = $conn->prepare("SELECT * FROM SubastaDetalles WHERE id_subasta = :id_subasta");
    $stmt_det->bindParam(':id_subasta', $subasta['id_subasta']);
    $stmt_det->execute();
    $detalles = $stmt_det->fetch(PDO::FETCH_ASSOC);
    ?>
    <!-- Modal para editar subasta -->
    <div id="editModal<?= $subasta['id_subasta'] ?>" class="modal fade hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="max-height: 80vh;">
                <div class="modal-header bg-gray-200">
                    <h5 class="modal-title font-bold text-gray-700">Editar Subasta Completa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto;">
                    <form action="assets/php/modal/editar_subasta.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_subasta" value="<?= $subasta['id_subasta'] ?>">

                        <!-- Sección de Información Básica -->
                        <h6 class="text-lg font-semibold text-gray-700">Detalles de la Subasta</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="direccion<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Dirección</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="direccion<?= $subasta['id_subasta'] ?>" name="direccion" value="<?= htmlspecialchars($subasta['direccion']) ?>">
                            </div>
                            <div>
                                <label for="cp<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Código Postal</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="cp<?= $subasta['id_subasta'] ?>" name="cp" value="<?= htmlspecialchars($subasta['cp']) ?>">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="localidad<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Localidad</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="localidad<?= $subasta['id_subasta'] ?>" name="localidad" value="<?= htmlspecialchars($subasta['localidad']) ?>">
                            </div>
                            <div>
                                <label for="provincia<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Provincia</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="provincia<?= $subasta['id_subasta'] ?>" name="provincia" value="<?= htmlspecialchars($subasta['provincia']) ?>">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="fecha_inicio<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Fecha y Hora de Inicio</label>
                                <input type="datetime-local" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="fecha_inicio<?= $subasta['id_subasta'] ?>" name="fecha_inicio" value="<?= date('Y-m-d\TH:i', strtotime($subasta['fecha_inicio'])) ?>">
                            </div>
                            <div>
                                <label for="fecha_conclusion<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Fecha y Hora de Conclusión</label>
                                <input type="datetime-local" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="fecha_conclusion<?= $subasta['id_subasta'] ?>" name="fecha_conclusion" value="<?= date('Y-m-d\TH:i', strtotime($subasta['fecha_conclusion'])) ?>">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="enlace_subasta<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Enlace Subasta</label>
                                <input type="url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="enlace_subasta<?= $subasta['id_subasta'] ?>" name="enlace_subasta" value="<?= htmlspecialchars($subasta['enlace_subasta']) ?>">
                            </div>
                            <div>
                                <label for="valor_subasta<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Valor Subasta (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="valor_subasta<?= $subasta['id_subasta'] ?>" name="valor_subasta" value="<?= number_format($subasta['valor_subasta'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="tasacion<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Tasación (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="tasacion<?= $subasta['id_subasta'] ?>" name="tasacion" value="<?= number_format($subasta['tasacion'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                            <div>
                                <label for="importe_deposito<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Importe Depósito (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="importe_deposito<?= $subasta['id_subasta'] ?>" name="importe_deposito" value="<?= number_format($subasta['importe_deposito'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="puja_minima<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Puja Mínima (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="puja_minima<?= $subasta['id_subasta'] ?>" name="puja_minima" value="<?= number_format($subasta['puja_minima'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                            <div>
                                <label for="tramos_pujas<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Tramos de Pujas</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="tramos_pujas<?= $subasta['id_subasta'] ?>" name="tramos_pujas" value="<?= number_format($subasta['tramos_pujas'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="cantidad_reclamada<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Cantidad Reclamada (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="cantidad_reclamada<?= $subasta['id_subasta'] ?>" name="cantidad_reclamada" value="<?= number_format($subasta['cantidad_reclamada'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                            <div>
                                <label for="id_tipo_subasta<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Tipo de Subasta</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="id_tipo_subasta<?= $subasta['id_subasta'] ?>" name="id_tipo_subasta">
                                    <?php
                                    $stmt_tipo = $conn->query("SELECT id_tipo_subasta, tipo_subasta FROM TiposSubasta");
                                    while ($tipo = $stmt_tipo->fetch(PDO::FETCH_ASSOC)): ?>
                                        <option value="<?= $tipo['id_tipo_subasta'] ?>" <?= $subasta['id_tipo_subasta'] == $tipo['id_tipo_subasta'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['tipo_subasta']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Sección de Idealista -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Detalles de la Propiedad</h6>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="habitaciones<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Habitaciones</label>
                                <input type="number" id="habitaciones<?= $subasta['id_subasta'] ?>" name="habitaciones" value="<?= htmlspecialchars($idealista['habitaciones']) ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="space-y-2">
                                <label for="banos<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Baños</label>
                                <input type="number" id="banos<?= $subasta['id_subasta'] ?>" name="banos" value="<?= htmlspecialchars($idealista['banos']) ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="piscina<?= $subasta['id_subasta'] ?>" name="piscina" value="1" <?= $idealista['piscina'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="piscina<?= $subasta['id_subasta'] ?>" class="text-sm font-medium text-gray-700">Piscina</label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="jardin<?= $subasta['id_subasta'] ?>" name="jardin" value="1" <?= $idealista['jardin'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="jardin<?= $subasta['id_subasta'] ?>" class="text-sm font-medium text-gray-700">Jardín</label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="ascensor<?= $subasta['id_subasta'] ?>" name="ascensor" value="1" <?= $idealista['ascensor'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="ascensor<?= $subasta['id_subasta'] ?>" class="text-sm font-medium text-gray-700">Ascensor</label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="garaje_idealista<?= $subasta['id_subasta'] ?>" name="garaje_idealista" value="1" <?= $idealista['garaje_idealista'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="garaje_idealista<?= $subasta['id_subasta'] ?>" class="text-sm font-medium text-gray-700">Garaje</label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="trastero<?= $subasta['id_subasta'] ?>" name="trastero" value="1" <?= $idealista['trastero'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="trastero<?= $subasta['id_subasta'] ?>" class="text-sm font-medium text-gray-700">Trastero</label>
                            </div>
                            <div class="space-y-2">
                                <label for="enlace_idealista<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Enlace Idealista</label>
                                <input type="url" id="enlace_idealista<?= $subasta['id_subasta'] ?>" name="enlace_idealista" value="<?= htmlspecialchars($idealista['enlace_idealista']) ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="id_estado<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Estado de la Subasta</label>
                            <select id="id_estado<?= $subasta['id_subasta'] ?>" name="id_estado" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <?php
                                $stmt_estado = $conn->query("SELECT id_estado, estado FROM EstadosSubasta");
                                while ($estado = $stmt_estado->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?= $estado['id_estado'] ?>" <?= $subasta['id_estado'] == $estado['id_estado'] ? 'selected' : '' ?>><?= htmlspecialchars($estado['estado']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Sección de Localizaciones -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Localización</h6>
                        <?php
                        $stmt_loc = $conn->prepare("SELECT * FROM Localizaciones WHERE id_subasta = :id_subasta");
                        $stmt_loc->bindParam(':id_subasta', $subasta['id_subasta']);
                        $stmt_loc->execute();
                        $localizacion = $stmt_loc->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="latitud<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Latitud</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="latitud<?= $subasta['id_subasta'] ?>" name="latitud" value="<?= htmlspecialchars($localizacion['latitud']) ?>">
                            </div>
                            <div>
                                <label for="altitud<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Altitud</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="altitud<?= $subasta['id_subasta'] ?>" name="altitud" value="<?= htmlspecialchars($localizacion['altitud']) ?>">
                            </div>
                        </div>

                        <!-- Sección de Detalles de la Subasta -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Detalles de la Subasta</h6>
                        <?php
                        $stmt_det = $conn->prepare("SELECT * FROM SubastaDetalles WHERE id_subasta = :id_subasta");
                        $stmt_det->bindParam(':id_subasta', $subasta['id_subasta']);
                        $stmt_det->execute();
                        $detalles = $stmt_det->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="precio_medio<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Precio Medio (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="precio_medio<?= $subasta['id_subasta'] ?>" name="precio_medio" value="<?= number_format($detalles['precio_medio'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                            <div>
                                <label for="puja_mas_alta<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Puja Más Alta (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="puja_mas_alta<?= $subasta['id_subasta'] ?>" name="puja_mas_alta" value="<?= number_format($detalles['puja_mas_alta'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                            <div>
                                <label for="carga_subastas<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Cargas de Subasta (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="carga_subastas<?= $subasta['id_subasta'] ?>" name="carga_subastas" value="<?= number_format($detalles['carga_subastas'], 2, ',', '.') ?>" onblur="this.value = formatearNumero(this.value);">
                            </div>
                        </div>

                        <!-- Sección de Imágenes -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Imágenes de la Subasta</h6>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Imágenes Actuales</label>
                            <div id="imagenes-actuales-<?= $subasta['id_subasta'] ?>" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <?php
                                $stmt_imgs = $conn->prepare("SELECT * FROM ImagenesSubasta WHERE id_subasta = :id_subasta");
                                $stmt_imgs->bindParam(':id_subasta', $subasta['id_subasta']);
                                $stmt_imgs->execute();
                                $imagenes = $stmt_imgs->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($imagenes as $imagen):
                                    $imagen_path = htmlspecialchars($imagen['url_imagen']);
                                ?>
                                    <div class="relative">
                                        <img src="<?= $imagen_path ?>" alt="Imagen de Subasta" class="w-full h-auto rounded">
                                        <input type="radio" name="imagen_portada" value="<?= $imagen['id_imagen'] ?>" class="absolute top-0 right-0 m-2">
                                        <button type="button" class="absolute bottom-0 left-0 m-2 bg-red-500 text-white px-2 py-1 rounded eliminar-imagen" data-id="<?= $imagen['id_imagen'] ?>">Eliminar</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Subir Nuevas Imágenes</label>
                            <div id="drop-area<?= $subasta['id_subasta'] ?>" class="border-dashed border-2 border-gray-300 rounded-md p-4 text-center">
                                Arrastra y suelta las imágenes aquí o haz clic para seleccionar
                                <input type="file" id="nuevas_imagenes<?= $subasta['id_subasta'] ?>" name="nuevas_imagenes[]" multiple>
                            </div>
                            <div id="preview<?= $subasta['id_subasta'] ?>" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4"></div>
                        </div>

                        <!-- Sección de Videos -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Videos de la Subasta</h6>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Videos Actuales</label>
                            <div id="videos-actuales-<?= $subasta['id_subasta'] ?>" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <?php
                                $stmt_videos = $conn->prepare("SELECT * FROM VideosSubasta WHERE id_subasta = :id_subasta");
                                $stmt_videos->bindParam(':id_subasta', $subasta['id_subasta']);
                                $stmt_videos->execute();
                                $videos = $stmt_videos->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($videos as $video):
                                    $video_path = htmlspecialchars($video['url_video']);
                                ?>
                                    <div class="relative">
                                        <video controls src="<?= $video_path ?>" alt="Video de Subasta" class="w-full h-auto rounded"></video>
                                        <button type="button" class="absolute bottom-0 left-0 m-2 bg-red-500 text-white px-2 py-1 rounded eliminar-video" data-id="<?= $video['id_video'] ?>">Eliminar</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Subir Nuevos Videos</label>
                            <div id="drop-area-videos<?= $subasta['id_subasta'] ?>" class="border-dashed border-2 border-gray-300 rounded-md p-4 text-center">
                                Arrastra y suelta los videos aquí o haz clic para seleccionar
                                <input type="file" id="nuevos_videos<?= $subasta['id_subasta'] ?>" name="nuevos_videos[]" multiple accept="video/*">
                            </div>
                            <div id="preview-videos<?= $subasta['id_subasta'] ?>" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4"></div>
                        </div>

                        <!-- Campo oculto para almacenar los IDs de elementos a eliminar -->
                        <input type="hidden" name="imagenes_a_eliminar" id="imagenes_a_eliminar">
                        <input type="hidden" name="videos_a_eliminar" id="videos_a_eliminar">
                        <input type="hidden" name="documentos_a_eliminar" id="documentos_a_eliminar">

                        <!-- Sección de Documentos -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Documentos de la Subasta</h6>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Documentos Actuales</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php
                                $stmt_docs = $conn->prepare("SELECT * FROM Documentos WHERE id_subasta = :id_subasta");
                                $stmt_docs->bindParam(':id_subasta', $subasta['id_subasta']);
                                $stmt_docs->execute();
                                $documentos = $stmt_docs->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($documentos as $documento):
                                ?>
                                    <div class="relative">
                                        <a href="assets/documentos/<?= htmlspecialchars(basename($documento['url_documento'])) ?>" target="_blank" class="text-blue-600 underline"><?= htmlspecialchars($documento['nombre_documento']) ?></a>
                                        <button type="button" class="absolute top-0 right-0 m-2 bg-red-500 text-white px-2 py-1 rounded eliminar-documento" data-id="<?= $documento['id_documento'] ?>">Eliminar</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nuevos_documentos" class="block text-sm font-medium text-gray-700">Subir Nuevos Documentos</label>
                            <input type="file" id="nuevos_documentos" name="nuevos_documentos[]" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="application/pdf">
                        </div>

                        <!-- Sección de Catastro -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Datos Catastrales</h6>
                        <?php
                        $stmt_catastro = $conn->prepare("SELECT * FROM Catastro WHERE id_subasta = :id_subasta");
                        $stmt_catastro->bindParam(':id_subasta', $subasta['id_subasta']);
                        $stmt_catastro->execute();
                        $catastro = $stmt_catastro->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ref_catastral<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Referencia Catastral</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="ref_catastral<?= $subasta['id_subasta'] ?>" name="ref_catastral" value="<?= htmlspecialchars($catastro['ref_catastral']) ?>">
                            </div>
                            <div>
                                <label for="clase<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Clase</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="clase<?= $subasta['id_subasta'] ?>" name="clase" value="<?= htmlspecialchars($catastro['clase']) ?>">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="uso_principal<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Uso Principal</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="uso_principal<?= $subasta['id_subasta'] ?>" name="uso_principal" value="<?= htmlspecialchars($catastro['uso_principal']) ?>">
                            </div>
                            <div>
                                <label for="sup_construida<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Superficie Construida (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="sup_construida<?= $subasta['id_subasta'] ?>" name="sup_construida" value="<?= htmlspecialchars($catastro['sup_construida']) ?>" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="vivienda<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Vivienda (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="vivienda<?= $subasta['id_subasta'] ?>" name="vivienda" value="<?= htmlspecialchars($catastro['vivienda']) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="terraza<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Terraza (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="terraza<?= $subasta['id_subasta'] ?>" name="terraza" value="<?= htmlspecialchars($catastro['terraza']) ?>" step="0.01">
                            </div>

                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="zonas_comunes<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Zonas Comunes (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="zonas_comunes<?= $subasta['id_subasta'] ?>" name="zonas_comunes" value="<?= htmlspecialchars($catastro['zonas_comunes']) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="almacen<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Almacén (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="almacen<?= $subasta['id_subasta'] ?>" name="almacen" value="<?= htmlspecialchars($catastro['almacen']) ?>" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="garaje<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Garaje (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="garaje<?= $subasta['id_subasta'] ?>" name="garaje" value="<?= htmlspecialchars($catastro['garaje']) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="ano_construccion<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Año de Construcción</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="ano_construccion<?= $subasta['id_subasta'] ?>" name="ano_construccion" value="<?= $catastro['ano_construccion'] ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="enlace_catastro<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Enlace Catastro</label>
                            <input type="url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="enlace_catastro<?= $subasta['id_subasta'] ?>" name="enlace_catastro" value="<?= htmlspecialchars($catastro['enlace_catastro']) ?>">
                        </div>

                        <!-- Sección de Valoraciones -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Valoraciones</h6>
                        <?php
                        $stmt_val = $conn->prepare("SELECT * FROM Valoraciones WHERE id_subasta = :id_subasta");
                        $stmt_val->bindParam(':id_subasta', $subasta['id_subasta']);
                        $stmt_val->execute();
                        $valoracion = $stmt_val->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="fachada_y_exteriores<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Fachada y Exteriores</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="fachada_y_exteriores<?= $subasta['id_subasta'] ?>" name="fachada_y_exteriores" value="<?= number_format($valoracion['fachada_y_exteriores'], 2) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="techo_y_canaletas<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Techo y Canaletas</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="techo_y_canaletas<?= $subasta['id_subasta'] ?>" name="techo_y_canaletas" value="<?= number_format($valoracion['techo_y_canaletas'], 2) ?>" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="ventanas_y_puerta<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Ventanas y Puertas</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="ventanas_y_puerta<?= $subasta['id_subasta'] ?>" name="ventanas_y_puerta" value="<?= number_format($valoracion['ventanas_y_puerta'], 2) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="jardin_y_terrenos<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Jardín y Terrenos</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="jardin_y_terrenos<?= $subasta['id_subasta'] ?>" name="jardin_y_terrenos" value="<?= number_format($valoracion['jardin_y_terrenos'], 2) ?>" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="estado_estructuras<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Estado de Estructuras</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="estado_estructuras<?= $subasta['id_subasta'] ?>" name="estado_estructuras" value="<?= number_format($valoracion['estado_estructuras'], 2) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="instalaciones_visibles<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Instalaciones Visibles</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="instalaciones_visibles<?= $subasta['id_subasta'] ?>" name="instalaciones_visibles" value="<?= number_format($valoracion['instalaciones_visibles'], 2) ?>" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="vecindario<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Vecindario</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="vecindario<?= $subasta['id_subasta'] ?>" name="vecindario" value="<?= number_format($valoracion['vecindario'], 2) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="seguridad<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Seguridad</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="seguridad<?= $subasta['id_subasta'] ?>" name="seguridad" value="<?= number_format($valoracion['seguridad'], 2) ?>" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="ruido_y_olores<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Ruido y Olores</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="ruido_y_olores<?= $subasta['id_subasta'] ?>" name="ruido_y_olores" value="<?= number_format($valoracion['ruido_y_olores'], 2) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="acceso_y_estacionamiento<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Acceso y Estacionamiento</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="acceso_y_estacionamiento<?= $subasta['id_subasta'] ?>" name="acceso_y_estacionamiento" value="<?= number_format($valoracion['acceso_y_estacionamiento'], 2) ?>" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label for="localizacion<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Localización</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="localizacion<?= $subasta['id_subasta'] ?>" name="localizacion" value="<?= number_format($valoracion['localizacion'], 2) ?>" step="0.01">
                            </div>
                            <div>
                                <label for="estado_inquilino<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Estado del Inquilino</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" id="estado_inquilino<?= $subasta['id_subasta'] ?>" name="estado_inquilino" value="<?= number_format($valoracion['estado_inquilino'], 2) ?>" step="0.01">
                            </div>
                        </div>

                        <!-- Sección de Comentarios -->
                        <h6 class="text-lg font-semibold text-gray-700 mt-4">Comentarios sobre la Subasta</h6>
                        <div class="mt-4">
                            <label for="comentarios<?= $subasta['id_subasta'] ?>" class="block text-sm font-medium text-gray-700">Comentarios</label>
                            <textarea id="comentarios<?= $subasta['id_subasta'] ?>" name="comentarios" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm ckeditor"><?= htmlspecialchars($comentario) ?></textarea>
                        </div>
                        <div class="modal-footer align-items-center">
                            <button type="button" class="btn btn-secondary font-semibold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary bg-blue-700">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para inicializar CKEditor en un textarea específico
        function initializeCKEditor(textarea) {
            if (CKEDITOR.instances[textarea.id]) {
                CKEDITOR.instances[textarea.id].destroy(true);
            }
            CKEDITOR.replace(textarea.id, {
                toolbar: [
                    { name: 'clipboard', items: ['Undo', 'Redo'] },
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
                    { name: 'insert', items: ['Link', 'Image'] },
                    { name: 'editing', items: ['Scayt'] },
                    { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] }
                ],
                removeButtons: 'Subscript,Superscript',
                height: 300
            });
        }

        // Al abrir un modal, inicializar CKEditor para los textareas dentro de ese modal
        $(document).ready(function() {
            $('#crearSubastaModal, #editModal<?= $subasta['id_subasta'] ?>').on('shown.bs.modal', function () {
                $(this).find('textarea.ckeditor').each(function () {
                    initializeCKEditor(this);
                });
            });

            $('#crearSubastaModal, #editModal<?= $subasta['id_subasta'] ?>').on('hidden.bs.modal', function () {
                $(this).find('textarea.ckeditor').each(function () {
                    if (CKEDITOR.instances[this.id]) {
                        CKEDITOR.instances[this.id].destroy(true);
                    }
                });
            });
        });
    });
</script>

<?php endif; ?>