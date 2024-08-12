<!-- crear_subasta_modal.php -->
<div class="modal fade" id="crearSubastaModal" tabindex="-1" aria-labelledby="crearSubastaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="crear_subasta.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearSubastaModalLabel">Crear Nueva Subasta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Contenido del modal dividido en pasos -->
                <div class="modal-body">
                    <!-- Paso 1: Información Básica -->
                    <div id="step1" class="step">
                        <h6>Información Básica de la Subasta</h6>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>
                        <div class="mb-3">
                            <label for="valor_subasta" class="form-label">Valor Subasta (€)</label>
                            <input type="number" class="form-control" id="valor_subasta" name="valor_subasta" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_conclusion" class="form-label">Fecha Conclusión</label>
                            <input type="date" class="form-control" id="fecha_conclusion" name="fecha_conclusion" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado_subasta" class="form-label">Estado Subasta</label>
                            <select class="form-control" id="estado_subasta" name="estado_subasta" required>
                                <option value="Activa">Activa</option>
                                <option value="Estudiando...">Estudiando...</option>
                                <option value="Terminada">Terminada</option>
                            </select>
                        </div>
                    </div>

                    <!-- Paso 2: Localización -->
                    <div id="step2" class="step d-none">
                        <h6>Localización de la Subasta</h6>
                        <div class="mb-3">
                            <label for="latitud" class="form-label">Latitud</label>
                            <input type="text" class="form-control" id="latitud" name="latitud" required>
                        </div>
                        <div class="mb-3">
                            <label for="altitud" class="form-label">Altitud</label>
                            <input type="text" class="form-control" id="altitud" name="altitud" required>
                        </div>
                    </div>

                    <!-- Paso 3: Detalles de la Subasta -->
                    <div id="step3" class="step d-none">
                        <h6>Detalles de la Subasta</h6>
                        <div class="mb-3">
                            <label for="precio_medio" class="form-label">Precio Medio (€)</label>
                            <input type="number" class="form-control" id="precio_medio" name="precio_medio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="precio_venta_min" class="form-label">Precio Venta Mínimo (€)</label>
                            <input type="number" class="form-control" id="precio_venta_min" name="precio_venta_min" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="precio_venta_medio" class="form-label">Precio Venta Medio (€)</label>
                            <input type="number" class="form-control" id="precio_venta_medio" name="precio_venta_medio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="precio_venta_max" class="form-label">Precio Venta Máximo (€)</label>
                            <input type="number" class="form-control" id="precio_venta_max" name="precio_venta_max" step="0.01" required>
                        </div>
                    </div>

                    <!-- Paso 4: Información Catastral -->
                    <div id="step4" class="step d-none">
                        <h6>Información Catastral</h6>
                        <div class="mb-3">
                            <label for="ref_catastral" class="form-label">Referencia Catastral</label>
                            <input type="text" class="form-control" id="ref_catastral" name="ref_catastral" required>
                        </div>
                        <div class="mb-3">
                            <label for="clase" class="form-label">Clase</label>
                            <input type="text" class="form-control" id="clase" name="clase" required>
                        </div>
                        <div class="mb-3">
                            <label for="uso_principal" class="form-label">Uso Principal</label>
                            <input type="text" class="form-control" id="uso_principal" name="uso_principal" required>
                        </div>
                        <div class="mb-3">
                            <label for="sup_construida" class="form-label">Superficie Construida (m²)</label>
                            <input type="number" class="form-control" id="sup_construida" name="sup_construida" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="vivienda" class="form-label">Vivienda (m²)</label>
                            <input type="number" class="form-control" id="vivienda" name="vivienda" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="garaje" class="form-label">Garaje (m²)</label>
                            <input type="number" class="form-control" id="garaje" name="garaje" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="almacen" class="form-label">Almacén (m²)</label>
                            <input type="number" class="form-control" id="almacen" name="almacen" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="ano_construccion" class="form-label">Año de Construcción</label>
                            <input type="number" class="form-control" id="ano_construccion" name="ano_construccion" required>
                        </div>
                        <div class="mb-3">
                            <label for="enlace_catastro" class="form-label">Enlace Catastro</label>
                            <input type="url" class="form-control" id="enlace_catastro" name="enlace_catastro" required>
                        </div>
                    </div>

                    <!-- Paso 5: Valoraciones -->
                    <div id="step5" class="step d-none">
                        <h6>Valoraciones de la Vivienda</h6>
                        <div class="mb-3">
                            <label for="fachada_y_exteriores" class="form-label">Fachada y Exteriores</label>
                            <input type="number" class="form-control" id="fachada_y_exteriores" name="fachada_y_exteriores" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="techo_y_canaletas" class="form-label">Techo y Canaletas</label>
                            <input type="number" class="form-control" id="techo_y_canaletas" name="techo_y_canaletas" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="ventanas_y_puerta" class="form-label">Ventanas y Puertas</label>
                            <input type="number" class="form-control" id="ventanas_y_puerta" name="ventanas_y_puerta" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="jardin_y_terrenos" class="form-label">Jardín y Terrenos</label>
                            <input type="number" class="form-control" id="jardin_y_terrenos" name="jardin_y_terrenos" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado_estructuras" class="form-label">Estado de Estructuras</label>
                            <input type="number" class="form-control" id="estado_estructuras" name="estado_estructuras" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="instalaciones_visibles" class="form-label">Instalaciones Visibles</label>
                            <input type="number" class="form-control" id="instalaciones_visibles" name="instalaciones_visibles" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="vecindario" class="form-label">Vecindario</label>
                            <input type="number" class="form-control" id="vecindario" name="vecindario" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="seguridad" class="form-label">Seguridad</label>
                            <input type="number" class="form-control" id="seguridad" name="seguridad" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="ruido_y_olores" class="form-label">Ruido y Olores</label>
                            <input type="number" class="form-control" id="ruido_y_olores" name="ruido_y_olores" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="acceso_y_estacionamiento" class="form-label">Acceso y Estacionamiento</label>
                            <input type="number" class="form-control" id="acceso_y_estacionamiento" name="acceso_y_estacionamiento" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="localizacion" class="form-label">Localización</label>
                            <input type="number" class="form-control" id="localizacion" name="localizacion" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado_inquilino" class="form-label">Estado del Inquilino</label>
                            <input type="number" class="form-control" id="estado_inquilino" name="estado_inquilino" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_de_vivienda" class="form-label">Tipo de Vivienda</label>
                            <input type="number" class="form-control" id="tipo_de_vivienda" name="tipo_de_vivienda" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="puntuacion_final" class="form-label">Puntuación Final</label>
                            <input type="number" class="form-control" id="puntuacion_final" name="puntuacion_final" step="0.01" required>
                        </div>
                    </div>
                </div>

                <!-- Navegación del modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevStepBtn" onclick="prevStep()" disabled>Anterior</button>
                    <button type="button" class="btn btn-primary" id="nextStepBtn" onclick="nextStep()">Siguiente</button>
                    <button type="submit" class="btn btn-success d-none" id="submitBtn">Crear Subasta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;

    function showStep(step) {
        document.querySelectorAll('.step').forEach((stepEl) => {
            stepEl.classList.add('d-none');
        });
        document.querySelector('#step' + step).classList.remove('d-none');
    }

    function nextStep() {
        if (currentStep < 5) {
            currentStep++;
            showStep(currentStep);
        }
        updateButtons();
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
        updateButtons();
    }

    function updateButtons() {
        document.getElementById('prevStepBtn').disabled = currentStep === 1;
        document.getElementById('nextStepBtn').classList.toggle('d-none', currentStep === 5);
        document.getElementById('submitBtn').classList.toggle('d-none', currentStep !== 5);
    }

    // Mostrar el primer paso al cargar la modal
    document.addEventListener('DOMContentLoaded', () => {
        showStep(currentStep);
    });
</script>