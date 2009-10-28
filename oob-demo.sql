INSERT INTO `oob_modules_config` (`id`, `modulename`, `status`, `nicename`, `description`, `optional`) VALUES 
(28, 'perfil', 1, 'Perfiles', 'Perfiles', 1);

INSERT INTO `oob_modulesperspective` (`PerspectiveName`, `ModuleName`) VALUES 
('default', 'perfil');


-- 
-- Estructura de tabla para la tabla `perfil_amigo`
-- 

CREATE TABLE `perfil_amigo` (
  `id` int(14) NOT NULL auto_increment,
  `id_origen` int(14) NOT NULL,
  `id_destino` int(14) NOT NULL,
  `bloqueo_destino` int(1) NOT NULL,
  `fecha` date NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `perfil_amigo`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `perfil_mensaje`
-- 

CREATE TABLE `perfil_mensaje` (
  `id` int(14) NOT NULL auto_increment,
  `fecha` date NOT NULL,
  `mensaje` varchar(140) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `exif` text NOT NULL,
  `id_perfil` int(14) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Volcar la base de datos para la tabla `perfil_mensaje`
-- 

INSERT INTO `perfil_mensaje` (`id`, `fecha`, `mensaje`, `foto`, `exif`, `id_perfil`, `status`) VALUES 
(1, '2009-08-25', 'gfdgdfgdf', '', '', 1, 1),
(2, '2009-08-25', 'gfdgdfgdf', '', '', 1, 1);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `perfil_notificacion`
-- 

CREATE TABLE `perfil_notificacion` (
  `id` int(14) NOT NULL auto_increment,
  `id_origen` int(14) NOT NULL,
  `class_origen` varchar(255) NOT NULL,
  `id_destino` int(14) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `fecha` date NOT NULL,
  `id_tipo` int(14) NOT NULL,
  `enviado` int(1) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Volcar la base de datos para la tabla `perfil_notificacion`
-- 

INSERT INTO `perfil_notificacion` (`id`, `id_origen`, `class_origen`, `id_destino`, `mensaje`, `fecha`, `id_tipo`, `enviado`, `status`) VALUES 
(1, 2, 'perfil_mensaje', 1, '', '2009-08-25', 3, 1, 1);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `perfil_notificacion_tipo`
-- 

CREATE TABLE `perfil_notificacion_tipo` (
  `id` int(14) NOT NULL auto_increment,
  `css_class` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Volcar la base de datos para la tabla `perfil_notificacion_tipo`
-- 

INSERT INTO `perfil_notificacion_tipo` (`id`, `css_class`, `mensaje`, `status`) VALUES 
(1, 'bla', 'Mensaje', 1),
(2, 'ble', 'Mensaje', 1),
(3, 'bli', 'Mensaje', 1),
(4, 'blu', 'Mensaje', 1);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `perfil_perfil`
-- 

CREATE TABLE `perfil_perfil` (
  `id` int(14) NOT NULL auto_increment,
  `nombre` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `bio` varchar(500) NOT NULL,
  `url` varchar(500) NOT NULL,
  `foto_perfil` varchar(255) NOT NULL,
  `id_usuario` int(14) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Volcar la base de datos para la tabla `perfil_perfil`
-- 

INSERT INTO `perfil_perfil` (`id`, `nombre`, `fecha_nacimiento`, `telefono`, `bio`, `url`, `foto_perfil`, `id_usuario`, `status`) VALUES 
(1, 'pablo', '1909-08-26', '432432', 'fsfsdf fsdfsd ', 'fdsfds', '', 3, 1);

-- --------------------------------------------------------
