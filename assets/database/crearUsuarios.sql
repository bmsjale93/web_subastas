-- Insertar tipos de usuario si no están ya en la base de datos
INSERT INTO `TiposUsuario` (`id_tipo_usuario`, `tipo_usuario`) VALUES
(1, 'admin'),
(2, 'usuario');

-- Insertar usuarios
INSERT INTO `Usuarios` (`usuario`, `contrasena`, `id_tipo_usuario`) VALUES
('ad_subastas', '$2y$10$CmQXy2fR/VfDWhCEwWnnDO6DRX4XB7FHF6NVQm3S5c3ZBg/n4iV8e', 1),
('ja_subastas', '$2y$10$5nZ58OISNF0.M2nHPFy7YOSlnypDwi1/mNYd7uC8h6Dh8Z.KWq8Fe', 2),
('jdelgado_subastas', '$2y$10$u6TIFETNdDJeZzHYcbx/teB/FyJd1KqNN6TYWq6QqPlZXpHzbHpiC', 2);
