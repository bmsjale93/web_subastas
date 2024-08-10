INSERT INTO Catastro (id_subasta, ref_catastral, clase, uso_principal, sup_construida, vivienda, garaje, almacen, ano_construccion, enlace_catastro)
VALUES (
    (SELECT id_subasta FROM Subastas WHERE direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE'), 
    '4146502QA4544E0129WJ', 
    'URBANO', 
    'RESIDENCIAL', 
    92, 
    82, 
    0, 
    0, 
    2003, 
    'https://acortar.link/5MBhfg'
);
