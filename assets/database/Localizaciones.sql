INSERT INTO Localizaciones (id_subasta, latitud, altitud)
VALUES (
    (SELECT id_subasta FROM Subastas WHERE direccion = 'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE'), 
    36.604440, 
    -6.272388
);
