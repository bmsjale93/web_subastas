-- Insertar la subasta en la tabla Subastas
INSERT INTO `Subastas` (`direccion`, `valor_subasta`, `fecha_conclusion`, `id_estado`, `fecha_inicio`, `id_tipo_subasta`, `enlace_subasta`, `importe_deposito`, `puja_minima`, `tramos_pujas`, `tasacion`, `cp`, `localidad`, `provincia`, `id_usuario`)
VALUES 
('CL SIERRA MORENA 8 Es:1 Pl:00 Pt:-L', 184304.02, '2024-09-26', 1, '2024-08-06', 1, 'https://acortar.link/L25rQt', 9215.20, 0.00, 3686.08, 0.00, 29680, 'ESTEPONA', 'MÁLAGA', 2);

-- Obtener el ID de la subasta insertada
SET @id_subasta = LAST_INSERT_ID();

-- Insertar en la tabla Catastro
INSERT INTO `Catastro` (`id_subasta`, `ref_catastral`, `clase`, `uso_principal`, `sup_construida`, `vivienda`, `garaje`, `almacen`, `ano_construccion`, `enlace_catastro`)
VALUES 
(@id_subasta, '1455801UF1315N0025AH', 'URBANO', 'RESIDENCIAL', 180, 94, 0, 0, 1988, 'https://acortar.link/WTVdxv');

-- Insertar en la tabla Localizaciones
INSERT INTO `Localizaciones` (`id_subasta`, `latitud`, `altitud`)
VALUES 
(@id_subasta, '36.444536', '-5.105489');

-- Insertar en la tabla SubastaDetalles
INSERT INTO `SubastaDetalles` (`id_subasta`, `precio_medio`, `precio_venta_min`, `precio_venta_medio`, `precio_venta_max`, `url_pdf_precios`)
VALUES 
(@id_subasta, 2800.00, 236880.00, 263200.00, 289520.00, 'assets/pdf_compra/ESTEPONA');

-- Insertar en la tabla Valoraciones
INSERT INTO `Valoraciones` (`id_subasta`, `fachada_y_exteriores`, `techo_y_canaletas`, `ventanas_y_puerta`, `jardin_y_terrenos`, `estado_estructuras`, `instalaciones_visibles`, `vecindario`, `seguridad`, `ruido_y_olores`, `acceso_y_estacionamiento`, `localizacion`, `estado_inquilino`, `tipo_de_vivienda`, `puntuacion_final`)
VALUES 
(@id_subasta, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5);

-- Insertar en la tabla Documentos (solo un ejemplo, puedes añadir más si es necesario)
INSERT INTO `Documentos` (`id_subasta`, `nombre_documento`, `url_documento`)
VALUES 
(@id_subasta, 'Vivienda Apartamento Estepona', 'assets/documentos/ESTEPONA/Vivienda Apartamento Estepona.pdf');

-- Insertar en la tabla ImagenesSubasta (solo un ejemplo, puedes añadir más si es necesario)
INSERT INTO `ImagenesSubasta` (`id_subasta`, `url_imagen`, `descripcion`)
VALUES 
(@id_subasta, 'assets/img/ESTEPONA/Estepona-imagen-1.png', 'Imagen de la subasta Estepona 1');
(@id_subasta, 'assets/img/ESTEPONA/Estepona-imagen-1.png', 'Imagen de la subasta Estepona 2');
(@id_subasta, 'assets/img/ESTEPONA/Estepona-imagen-1.png', 'Imagen de la subasta Estepona 3');
(@id_subasta, 'assets/img/ESTEPONA/Estepona-imagen-1.png', 'Imagen de la subasta Estepona 4');