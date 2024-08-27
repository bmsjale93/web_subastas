-- Insertar la subasta en la tabla Subastas
INSERT INTO `Subastas` (`direccion`, `valor_subasta`, `fecha_conclusion`, `id_estado`, `fecha_inicio`, `id_tipo_subasta`, `enlace_subasta`, `importe_deposito`, `puja_minima`, `tramos_pujas`, `tasacion`, `cp`, `localidad`, `provincia`, `id_usuario`)
VALUES 
('AV J CLAYTON 7', 525372.48, '2024-09-02', 1, '2024-08-13', 1, 'https://acortar.link/vGZqRK', 26268.62, 0.00, 5000.00, 525372.48, 21100, 'PUNTA UMBRIA', 'HUELVA', 2);

-- Obtener el ID de la subasta insertada
SET @id_subasta = LAST_INSERT_ID();

-- Insertar en la tabla Catastro
INSERT INTO `Catastro` (`id_subasta`, `ref_catastral`, `clase`, `uso_principal`, `sup_construida`, `vivienda`, `garaje`, `almacen`, `ano_construccion`, `enlace_catastro`)
VALUES 
(@id_subasta, '9382403PB7198S0001DE', 'URBANO', 'RESIDENCIAL', 289, 289, 103, 17, 2007, 'https://acortar.link/mt0wis');

-- Insertar en la tabla Localizaciones
INSERT INTO `Localizaciones` (`id_subasta`, `latitud`, `altitud`)
VALUES 
(@id_subasta, '36.444510', '-5.105425');

-- Insertar en la tabla SubastaDetalles
INSERT INTO `SubastaDetalles` (`id_subasta`, `precio_medio`, `precio_venta_min`, `precio_venta_medio`, `precio_venta_max`, `url_pdf_precios`, `puja_mas_alta`)
VALUES 
(@id_subasta, 2250.00, 639802.50, 707700.00, 775002.50, 'assets/pdf_compra/PUNTA UMBRIA/estudio_subastas_punta_umbria.pdf', 0.00);

-- Insertar en la tabla Valoraciones
INSERT INTO `Valoraciones` (`id_subasta`, `fachada_y_exteriores`, `techo_y_canaletas`, `ventanas_y_puerta`, `jardin_y_terrenos`, `estado_estructuras`, `instalaciones_visibles`, `vecindario`, `seguridad`, `ruido_y_olores`, `acceso_y_estacionamiento`, `localizacion`, `estado_inquilino`, `puntuacion_final`)
VALUES 
(@id_subasta, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5);


-- Insertar en la tabla Documentos
INSERT INTO `Documentos` (`id_subasta`, `nombre_documento`, `url_documento`)
VALUES 
(@id_subasta, 'Documento Subasta Punta Umbría', 'assets/documentos/PUNTA UMBRIA/documento1-22.pdf');

-- Insertar en la tabla ImagenesSubasta
INSERT INTO `ImagenesSubasta` (`id_subasta`, `url_imagen`, `descripcion`)
VALUES 
(@id_subasta, 'assets/img/PUNTA UMBRIA/imagen-punta-umbria-1.png', 'Imagen de la subasta Punta Umbría 1'),
(@id_subasta, 'assets/img/PUNTA UMBRIA/imagen-punta-umbria-2.png', 'Imagen de la subasta Punta Umbría 2'),
(@id_subasta, 'assets/img/PUNTA UMBRIA/imagen-punta-umbria-3.png', 'Imagen de la subasta Punta Umbría 3'),
(@id_subasta, 'assets/img/PUNTA UMBRIA/imagen-punta-umbria-4.png', 'Imagen de la subasta Punta Umbría 4');


CREATE TABLE VideosSubasta (
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_subasta INT,
    url_video VARCHAR(255) NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (id_subasta) REFERENCES Subastas(id_subasta)
);

INSERT INTO
    EstadosSubasta (id_estado, estado)
VALUES
    (4, '¡Ganada!');