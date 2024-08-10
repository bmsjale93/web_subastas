INSERT INTO
    Subastas (
        direccion,
        cp,
        localidad,
        provincia,
        fecha_inicio,
        fecha_conclusion,
        enlace_subasta,
        valor_subasta,
        tasacion,
        importe_deposito,
        puja_minima,
        tramos_pujas,
        id_usuario,
        id_tipo_subasta
    )
VALUES
    (
        'CL ANCORA 4 N2-8 Es:1 Pl:01 Pt:G EDIFICIO ANCORA 1ª FASE',
        '11500',
        'PUERTO SANTA MARÍA',
        'CÁDIZ',
        '2024-07-19',
        '2024-08-08',
        'https://acortar.link/7GWoA2',
        152000.00,
        0.00,
        7600.00,
        'Sin puja mínima',
        3040.00,
        (
            SELECT
                id_usuario
            FROM
                USUARIOS
            WHERE
                usuario = 'admin'
        ),
        (
            SELECT
                id_tipo_subasta
            FROM
                TiposSubasta
            WHERE
                tipo_subasta = 'SUBASTA BOE'
        )
    );