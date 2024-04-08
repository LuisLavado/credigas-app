CREATE TABLE `master_tipo_persona` (
  `id` int(11) NOT NULL,
  `codigo` varchar(8) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `orden` int(11) NOT NULL,
  `estado` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;