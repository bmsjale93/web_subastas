INSERT INTO ImagenesSubasta (id_subasta, url_imagen, descripcion)
VALUES 
    ((SELECT id_subasta FROM Subastas WHERE direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE'), '/assets/img/ANCORA/foto-vivienda-1.png', 'Fachada principal'),
    ((SELECT id_subasta FROM Subastas WHERE direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE'), '/assets/img/ANCORA/foto-vivienda-2.png', 'Vista lateral'),
    ((SELECT id_subasta FROM Subastas WHERE direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE'), '/assets/img/ANCORA/foto-vivienda-3.png', 'Interior del salón'),
    ((SELECT id_subasta FROM Subastas WHERE direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE'), '/assets/img/ANCORA/foto-vivienda-4.png', 'Cocina');
