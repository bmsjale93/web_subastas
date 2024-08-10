INSERT INTO SubastaDetalles (id_subasta, id_tipo_subasta, precio_medio_min, precio_venta_min, precio_medio, precio_venta_medio, precio_max, precio_venta_max)
VALUES 
    ((SELECT id_subasta FROM Subastas WHERE direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE'), (SELECT id_tipo_subasta FROM TiposSubasta WHERE tipo_subasta = 'SUBASTA BOE'), 2700.00, 248400.00, 3000.00, 276000.00, 3300.00, 303600.00);
