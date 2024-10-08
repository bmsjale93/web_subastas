<!-- crear_subasta_modal.php mejorado -->
<div class="modal fade fixed inset-0 z-50 overflow-y-auto" id="crearSubastaModal" tabindex="-1" aria-labelledby="crearSubastaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content max-h-screen">
            <form id="crearSubastaForm" action="assets/php/modal/crear_subasta.php" method="POST" enctype="multipart/form-data">
                <!-- Encabezado del modal -->
                <div class="modal-header bg-gray-200 px-6 py-4">
                    <h5 class="modal-title text-xl font-bold text-gray-700" id="crearSubastaModalLabel">Crear Nueva Subasta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="progress-container" style="position: relative; height: 1rem; background-color: #f3f4f6; border-radius: 9999px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <div class="progress-bar" role="progressbar" style="width: 20%; background-color: #1E3A8A; height: 100%; border-radius: 9999px; transition: width 0.3s ease-in-out;" id="progressBar"></div>
                </div>

                <!-- Cuerpo del modal -->
                <div class="modal-body px-6 py-4 space-y-6 overflow-y-auto">
                    <!-- Paso 1: Información Básica -->
                    <div id="step1" class="step">
                        <h6 class="text-lg font-semibold text-gray-700">Información Básica de la Subasta</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="direccion" name="direccion" required>
                            </div>
                            <div>
                                <label for="cp" class="block text-sm font-medium text-gray-700">Código Postal</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="cp" name="cp" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="localidad" class="block text-sm font-medium text-gray-700">Localidad</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="localidad" name="localidad" required>
                            </div>
                            <div>
                                <label for="provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="provincia" name="provincia" required>
                            </div>
                        </div>
                        <!-- agregar campos de hora en crear_subasta_modal.php -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha y Hora de Inicio</label>
                                <input type="datetime-local" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                            <div>
                                <label for="fecha_conclusion" class="block text-sm font-medium text-gray-700">Fecha y Hora de Conclusión</label>
                                <input type="datetime-local" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="fecha_conclusion" name="fecha_conclusion" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="enlace_subasta" class="block text-sm font-medium text-gray-700">Enlace Subasta</label>
                                <input type="url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="enlace_subasta" name="enlace_subasta">
                            </div>
                            <div>
                                <label for="valor_subasta" class="block text-sm font-medium text-gray-700">Valor Subasta (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="valor_subasta" name="valor_subasta" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="tasacion" class="block text-sm font-medium text-gray-700">Tasación (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="tasacion" name="tasacion" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                            <div>
                                <label for="importe_deposito" class="block text-sm font-medium text-gray-700">Importe Depósito (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="importe_deposito" name="importe_deposito" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="puja_minima" class="block text-sm font-medium text-gray-700">Puja Mínima (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="puja_minima" name="puja_minima" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                            <div>
                                <label for="tramos_pujas" class="block text-sm font-medium text-gray-700">Tramos de Pujas (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="tramos_pujas" name="tramos_pujas" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="cantidad_reclamada" class="block text-sm font-medium text-gray-700">Cantidad Reclamada (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="cantidad_reclamada" name="cantidad_reclamada" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                            <div>
                                <label for="id_tipo_subasta" class="block text-sm font-medium text-gray-700">Tipo de Subasta</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="id_tipo_subasta" name="id_tipo_subasta" required>
                                    <?php
                                    try {
                                        include('assets/php/database/config.php'); // Incluye la conexión a la base de datos
                                        $stmt = $conn->query("SELECT id_tipo_subasta, tipo_subasta FROM TiposSubasta");
                                        while ($tipo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='{$tipo['id_tipo_subasta']}'>{$tipo['tipo_subasta']}</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<option value=''>Error al cargar tipos de subasta</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="id_estado" class="block text-sm font-medium text-gray-700">Estado de la Subasta</label>
                            <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="id_estado" name="id_estado" required>
                                <?php
                                try {
                                    $stmt = $conn->query("SELECT id_estado, estado FROM EstadosSubasta");
                                    while ($estado = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$estado['id_estado']}'>{$estado['estado']}</option>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<option value=''>Error al cargar estados de subasta</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Paso 2: Localización -->
                    <div id="step2" class="step hidden">
                        <h6 class="text-lg font-semibold text-gray-700">Localización</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="latitud" class="block text-sm font-medium text-gray-700">Latitud</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="latitud" name="latitud" required>
                            </div>
                            <div>
                                <label for="altitud" class="block text-sm font-medium text-gray-700">Altitud</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="altitud" name="altitud" required>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Detalles de la Subasta -->
                    <div id="step3" class="step hidden">
                        <h6 class="text-lg font-semibold text-gray-700">Detalles de la Subasta</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="precio_medio" class="block text-sm font-medium text-gray-700">Precio Medio (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="precio_medio" name="precio_medio" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                            <div>
                                <label for="puja_mas_alta" class="block text-sm font-medium text-gray-700">Puja Más Alta (€)</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="puja_mas_alta" name="puja_mas_alta" onblur="this.value = formatearNumero(this.value);" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="pdf_precios" class="block text-sm font-medium text-gray-700">PDF de Precios</label>
                                <input type="file" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="pdf_precios" name="pdf_precios" accept="application/pdf">
                            </div>
                            <div>
                                <label for="carga_subastas" class="block text-sm font-medium text-gray-700">Carga Subastas</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="carga_subastas" name="carga_subastas">
                            </div>
                        </div>
                    </div>

                    <!-- Paso 4: Información Catastral -->
                    <div id="step4" class="step hidden">
                        <h6 class="text-lg font-semibold text-gray-700">Información Catastral</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="ref_catastral" class="block text-sm font-medium text-gray-700">Referencia Catastral</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="ref_catastral" name="ref_catastral" required>
                            </div>
                            <div>
                                <label for="clase" class="block text-sm font-medium text-gray-700">Clase</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="clase" name="clase" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="uso_principal" class="block text-sm font-medium text-gray-700">Uso Principal</label>
                                <input type="text" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="uso_principal" name="uso_principal" required>
                            </div>
                            <div>
                                <label for="sup_construida" class="block text-sm font-medium text-gray-700">Superficie Construida (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="sup_construida" name="sup_construida" step="0.01" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="vivienda" class="block text-sm font-medium text-gray-700">Vivienda (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="vivienda" name="vivienda" step="0.01" required>
                            </div>
                            <div>
                                <label for="terraza" class="block text-sm font-medium text-gray-700">Terraza (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="terraza" name="terraza" step="0.01">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="zonas_comunes" class="block text-sm font-medium text-gray-700">Zonas Comunes (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="zonas_comunes" name="zonas_comunes" step="1" required>
                            </div>
                            <div>
                                <label for="almacen" class="block text-sm font-medium text-gray-700">Almacén (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="almacen" name="almacen" step="0.01" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="garaje" class="block text-sm font-medium text-gray-700">Garaje (m²)</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="garaje" name="garaje" step="0.01" required>
                            </div>
                            <div>
                                <label for="ano_construccion" class="block text-sm font-medium text-gray-700">Año de Construcción</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="ano_construccion" name="ano_construccion" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="enlace_catastro" class="block text-sm font-medium text-gray-700">Enlace Catastro</label>
                                <input type="url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="enlace_catastro" name="enlace_catastro" required>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 5: Valoraciones -->
                    <div id="step5" class="step hidden">
                        <h6 class="text-lg font-semibold text-gray-700">Valoraciones</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="fachada_y_exteriores" class="block text-sm font-medium text-gray-700">Fachada y Exteriores</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="fachada_y_exteriores" name="fachada_y_exteriores" step="0.01" required>
                            </div>
                            <div>
                                <label for="techo_y_canaletas" class="block text-sm font-medium text-gray-700">Techo y Canaletas</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="techo_y_canaletas" name="techo_y_canaletas" step="0.01" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="ventanas_y_puerta" class="block text-sm font-medium text-gray-700">Ventanas y Puertas</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="ventanas_y_puerta" name="ventanas_y_puerta" step="0.01" required>
                            </div>
                            <div>
                                <label for="jardin_y_terrenos" class="block text-sm font-medium text-gray-700">Jardín y Terrenos</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="jardin_y_terrenos" name="jardin_y_terrenos" step="0.01" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="estado_estructuras" class="block text-sm font-medium text-gray-700">Estado de Estructuras</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="estado_estructuras" name="estado_estructuras" step="0.01" required>
                            </div>
                            <div>
                                <label for="instalaciones_visibles" class="block text-sm font-medium text-gray-700">Instalaciones Visibles</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="instalaciones_visibles" name="instalaciones_visibles" step="0.01" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="vecindario" class="block text-sm font-medium text-gray-700">Vecindario</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="vecindario" name="vecindario" step="0.01" required>
                            </div>
                            <div>
                                <label for="seguridad" class="block text-sm font-medium text-gray-700">Seguridad</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="seguridad" name="seguridad" step="0.01" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="ruido_y_olores" class="block text-sm font-medium text-gray-700">Ruido y Olores</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="ruido_y_olores" name="ruido_y_olores" step="0.01" required>
                            </div>
                            <div>
                                <label for="acceso_y_estacionamiento" class="block text-sm font-medium text-gray-700">Acceso y Estacionamiento</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="acceso_y_estacionamiento" name="acceso_y_estacionamiento" step="0.01" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="localizacion" class="block text-sm font-medium text-gray-700">Localización</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="localizacion" name="localizacion" step="0.01" required>
                            </div>
                            <div>
                                <label for="estado_inquilino" class="block text-sm font-medium text-gray-700">Estado del Inquilino</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="estado_inquilino" name="estado_inquilino" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 6: Detalles de Idealista -->
                    <div id="step6" class="step hidden">
                        <h6 class="text-lg font-semibold text-gray-700">Detalles de Idealista</h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="habitaciones" class="block text-sm font-medium text-gray-700">Habitaciones</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="habitaciones" name="habitaciones" required>
                            </div>
                            <div>
                                <label for="banos" class="block text-sm font-medium text-gray-700">Baños</label>
                                <input type="number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="banos" name="banos" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="piscina" class="block text-sm font-medium text-gray-700">Piscina</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="piscina" name="piscina" required>
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                            <div>
                                <label for="jardin" class="block text-sm font-medium text-gray-700">Jardín</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="jardin" name="jardin" required>
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="ascensor" class="block text-sm font-medium text-gray-700">Ascensor</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="ascensor" name="ascensor" required>
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                            <div>
                                <label for="garaje_idealista" class="block text-sm font-medium text-gray-700">Garaje</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="garaje_idealista" name="garaje_idealista" required>
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="trastero" class="block text-sm font-medium text-gray-700">Trastero</label>
                                <select class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="trastero" name="trastero" required>
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <label for="enlace_idealista" class="block text-sm font-medium text-gray-700">Enlace Idealista</label>
                                <input type="url" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" id="enlace_idealista" name="enlace_idealista" required>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 7: Subir Imágenes, Videos y Documentos -->
                    <div id="step7" class="step hidden">
                        <h6 class="text-lg font-semibold text-gray-700">Imágenes, Videos y Documentos</h6>
                        <div class="mb-4">
                            <label for="imagenes_subasta" class="block text-sm font-medium text-gray-700">Imágenes de la Subasta</label>
                            <div class="drop-zone mt-2" id="imagenesDropZone">
                                <input type="file" id="imagenes_subasta" name="imagenes_subasta[]" multiple accept="image/*" class="hidden">
                                <p class="text-center text-gray-500">Arrastra los archivos aquí o haz clic para seleccionar</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="videos_subasta" class="block text-sm font-medium text-gray-700">Videos de la Subasta</label>
                            <div class="drop-zone mt-2" id="videosDropZone">
                                <input type="file" id="videos_subasta" name="videos_subasta[]" multiple accept="video/*" class="hidden">
                                <p class="text-center text-gray-500">Arrastra los archivos aquí o haz clic para seleccionar</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="documentos_subasta" class="block text-sm font-medium text-gray-700">Documentos PDF</label>
                            <div class="drop-zone mt-2" id="documentosDropZone">
                                <input type="file" id="documentos_subasta" name="documentos_subasta[]" multiple accept="application/pdf" class="hidden">
                                <p class="text-center text-gray-500">Arrastra los archivos aquí o haz clic para seleccionar</p>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 8: Sección de Comentarios -->
                    <div id="step8" class="step hidden">
                        <h6 class="text-lg font-semibold text-gray-700">Comentarios sobre la Subasta</h6>
                        <div class="mt-4">
                            <label for="comentarios" class="block text-sm font-medium text-gray-700">Comentarios</label>
                            <textarea id="comentarios" name="comentarios" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm ckeditor"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Navegación del modal -->
                <div class="modal-footer bg-gray-100 px-6 py-3">
                    <button id="prevStepBtn" class="custom-prev-step-btn">Atrás</button>
                    <button id="nextStepBtn" class="custom-next-step-btn">Siguiente</button>
                    <button type="submit" class="btn btn-success hidden" id="submitBtn">Crear Subasta</button>
                </div>
            </form>
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
        $('#crearSubastaModal, #editModal<?= $subasta['id_subasta'] ?>').on('shown.bs.modal', function () {
            $(this).find('textarea.ckeditor').each(function () {
                initializeCKEditor(this);
            });
        });

        // Al cerrar un modal, destruir CKEditor en los textareas
        $('#crearSubastaModal, #editModal<?= $subasta['id_subasta'] ?>').on('hidden.bs.modal', function () {
            $(this).find('textarea.ckeditor').each(function () {
                if (CKEDITOR.instances[this.id]) {
                    CKEDITOR.instances[this.id].destroy(true);
                }
            });
        });
    });
</script>

<script>
    function formatearNumero(valor) {
        valor = valor.replace(/[^\d.,-]/g, '');

        if (valor.indexOf(',') > valor.indexOf('.')) {
            valor = valor.replace(/\./g, '').replace(/,/g, '.');
        } else {
            valor = valor.replace(/,/g, '');
        }

        let numero = parseFloat(valor);

        if (isNaN(numero)) return '';

        return numero.toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' €';
    }
</script>