-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-07-2026 a las 02:08:16
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `eduplat_g09`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `docente_id` int(11) NOT NULL,
  `semestre` varchar(30) DEFAULT NULL,
  `icono` varchar(10) NOT NULL DEFAULT '?',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre`, `descripcion`, `docente_id`, `semestre`, `icono`, `estado`, `fecha_creacion`) VALUES
(1, 'Desarrollo de Aplicaciones Web', 'HTML, CSS, JavaScript y frameworks modernos.', 1, '6to Sem.', '💻', 'activo', '2026-07-12 23:24:48'),
(2, 'Base de Datos II', 'SQL avanzado, normalización y transacciones.', 1, '4to Sem.', '🗄', 'activo', '2026-07-12 23:24:48'),
(3, 'Algoritmos y Estructuras', 'Estructuras de datos, recursividad y complejidad.', 1, '3er Sem.', '🧮', 'activo', '2026-07-12 23:24:48'),
(4, 'Redes de Computadores', 'Protocolos TCP/IP, OSI y diagnóstico de redes.', 1, '4to Sem.', '📡', 'inactivo', '2026-07-12 23:24:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas`
--

CREATE TABLE `entregas` (
  `id_entrega` int(11) NOT NULL,
  `id_tarea` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `enlace` varchar(255) DEFAULT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `retroalimentacion` text DEFAULT NULL,
  `estado` enum('entregada','calificada') NOT NULL DEFAULT 'entregada',
  `fecha_entrega` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entregas`
--

INSERT INTO `entregas` (`id_entrega`, `id_tarea`, `id_estudiante`, `comentario`, `enlace`, `archivo_nombre`, `nota`, `retroalimentacion`, `estado`, `fecha_entrega`) VALUES
(1, 2, 2, 'Entrega completa con todos los requisitos.', 'https://github.com/luis-morejon/js-dom', NULL, 9.00, 'Excelente manejo del DOM. Buena estructura del código y uso correcto de eventos.', 'calificada', '2026-07-12 23:24:48'),
(2, 4, 2, 'Adjunto el documento de normalización.', 'https://github.com/luis-morejon/normalizacion', NULL, 7.00, 'Buen trabajo, revisar la 3FN en la tabla de pedidos.', 'calificada', '2026-07-12 23:24:48'),
(3, 6, 2, 'Implementación con pilas explícitas.', 'https://github.com/luis-morejon/recursividad', NULL, 8.50, 'Muy buena solución, código limpio.', 'calificada', '2026-07-12 23:24:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

CREATE TABLE `evaluaciones` (
  `id_evaluacion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `tipo` enum('examen','quiz') NOT NULL DEFAULT 'examen',
  `temas` text DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `duracion_min` int(11) NOT NULL DEFAULT 60,
  `ponderacion` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nota_minima` decimal(4,2) NOT NULL DEFAULT 7.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones`
--

INSERT INTO `evaluaciones` (`id_evaluacion`, `id_curso`, `titulo`, `tipo`, `temas`, `fecha`, `duracion_min`, `ponderacion`, `nota_minima`) VALUES
(1, 1, 'Parcial 1 — Fundamentos Web', 'examen', 'HTML5 semántico, estructura de documentos, formularios y validación básica con CSS.', '2026-06-12 23:24:48', 90, 30.00, 7.00),
(2, 1, 'Quiz — CSS Layouts', 'quiz', 'Flexbox, Grid y posicionamiento avanzado.', '2026-06-27 23:24:48', 30, 10.00, 7.00),
(3, 1, 'Parcial 2 — JavaScript', 'examen', 'Variables, funciones, DOM, eventos, fetch API y manejo de promesas en JavaScript.', '2026-07-20 23:24:48', 90, 30.00, 7.00),
(4, 2, 'Parcial 1 — SQL Básico', 'examen', 'Consultas SELECT, filtros, joins básicos.', '2026-06-14 23:24:48', 60, 30.00, 7.00),
(5, 2, 'Quiz — Normalización', 'quiz', 'Formas normales 1FN, 2FN, 3FN.', '2026-06-30 23:24:48', 20, 10.00, 7.00),
(6, 3, 'Parcial 2 — Algoritmos', 'examen', 'Recursividad, complejidad algorítmica y estructuras de datos lineales.', '2026-07-22 23:24:48', 75, 30.00, 7.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id_inscripcion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `progreso` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `fecha_inscripcion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id_inscripcion`, `id_curso`, `id_estudiante`, `progreso`, `fecha_inscripcion`) VALUES
(1, 1, 2, 72, '2026-07-12 23:24:48'),
(2, 2, 2, 55, '2026-07-12 23:24:48'),
(3, 3, 2, 88, '2026-07-12 23:24:48'),
(4, 1, 4, 0, '2026-07-14 18:50:25'),
(5, 2, 4, 0, '2026-07-14 18:50:52'),
(6, 3, 4, 0, '2026-07-14 18:50:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id_material` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `tipo` enum('documento','video','pdf','enlace') NOT NULL DEFAULT 'documento',
  `enlace` varchar(255) DEFAULT NULL,
  `fecha_publicacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_material`, `id_curso`, `titulo`, `tipo`, `enlace`, `fecha_publicacion`) VALUES
(1, 1, 'Introducción a HTML5', 'pdf', NULL, '2026-07-12 23:24:48'),
(2, 1, 'Estilos CSS — Video Clase', 'video', NULL, '2026-07-12 23:24:48'),
(3, 1, 'JavaScript Fundamentos', 'documento', NULL, '2026-07-12 23:24:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultados_evaluacion`
--

CREATE TABLE `resultados_evaluacion` (
  `id_resultado` int(11) NOT NULL,
  `id_evaluacion` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `nota` decimal(4,2) DEFAULT NULL,
  `retroalimentacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resultados_evaluacion`
--

INSERT INTO `resultados_evaluacion` (`id_resultado`, `id_evaluacion`, `id_estudiante`, `nota`, `retroalimentacion`) VALUES
(1, 1, 2, 8.00, 'Buen manejo de HTML semántico. Mejorar el uso de selectores CSS y la estructura de formularios.'),
(2, 2, 2, 8.00, 'Muy buen dominio de Flexbox y Grid.'),
(3, 4, 2, 7.50, 'Buen nivel general, reforzar subconsultas.'),
(4, 5, 2, 7.00, 'Correcto, revisar ejemplos de 3FN.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_log`
--

CREATE TABLE `sesiones_log` (
  `id_log` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `correo_usado` varchar(120) NOT NULL,
  `exitoso` tinyint(1) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_origen` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sesiones_log`
--

INSERT INTO `sesiones_log` (`id_log`, `id_usuario`, `correo_usado`, `exitoso`, `fecha_hora`, `ip_origen`) VALUES
(1, NULL, 'ddd@d.c', 0, '2026-07-08 15:18:06', '::1'),
(2, 2, 'estudiante@eduplat.edu', 0, '2026-07-08 15:18:29', '::1'),
(3, 2, 'estudiante@eduplat.edu', 0, '2026-07-08 15:23:38', '::1'),
(4, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-08 15:30:47', '::1'),
(5, 1, 'docente@eduplat.edu', 1, '2026-07-08 15:32:23', '::1'),
(6, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-08 18:27:24', '::1'),
(7, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-08 18:45:58', '::1'),
(8, 1, 'docente@eduplat.edu', 1, '2026-07-08 18:48:03', '::1'),
(9, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-08 19:25:48', '::1'),
(10, NULL, 'luis.mor@jm.com', 0, '2026-07-08 20:24:50', '::1'),
(11, 5, 'ricardo@gmail.com', 1, '2026-07-08 20:26:53', '::1'),
(12, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-11 20:06:02', '::1'),
(13, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-14 18:34:23', '::1'),
(14, 1, 'docente@eduplat.edu', 0, '2026-07-14 18:42:20', '::1'),
(15, 1, 'docente@eduplat.edu', 0, '2026-07-14 18:42:25', '::1'),
(16, 1, 'docente@eduplat.edu', 1, '2026-07-14 18:43:01', '::1'),
(17, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-14 18:43:22', '::1'),
(18, 1, 'docente@eduplat.edu', 1, '2026-07-14 18:47:27', '::1'),
(19, 4, 'jimmyortizp11@gmail.com', 1, '2026-07-14 18:50:15', '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id_tarea` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `instrucciones` text DEFAULT NULL,
  `ponderacion` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fecha_limite` datetime NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id_tarea`, `id_curso`, `titulo`, `instrucciones`, `ponderacion`, `fecha_limite`, `fecha_creacion`) VALUES
(1, 1, 'Práctica HTML/CSS', 'Desarrollar una página web completa usando HTML5 y CSS3 que incluya: encabezado con navegación, sección hero, galería con grid/flexbox y footer. Debe ser responsive y pasar validación W3C.', 10.00, '2026-07-17 23:24:48', '2026-07-12 23:24:48'),
(2, 1, 'JavaScript DOM', 'Manipulación del DOM con JavaScript puro: selección de elementos, eventos y actualización dinámica del contenido.', 10.00, '2026-07-02 23:24:48', '2026-07-12 23:24:48'),
(3, 2, 'Consultas SQL Avanzadas', 'Escribir consultas con JOIN, subconsultas y funciones de agregación sobre el esquema proporcionado.', 15.00, '2026-07-19 23:24:48', '2026-07-12 23:24:48'),
(4, 2, 'Normalización de Tablas', 'Normalizar el esquema entregado hasta 3FN y justificar cada paso.', 15.00, '2026-06-22 23:24:48', '2026-07-12 23:24:48'),
(5, 3, 'Árbol Binario de Búsqueda', 'Implementar un ABB con inserción, búsqueda y recorridos in-order, pre-order y post-order.', 20.00, '2026-07-21 23:24:48', '2026-07-12 23:24:48'),
(6, 3, 'Recursividad y Pilas', 'Resolver 5 problemas de recursividad utilizando pilas explícitas e implícitas.', 20.00, '2026-06-26 23:24:00', '2026-07-12 23:24:48'),
(7, 1, 'Taller con Ajax', 'Subir Taller Realizado en Hora de Clase Haciendo uso de Ajax', 20.00, '2026-07-15 23:59:00', '2026-07-14 18:49:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombres` varchar(80) NOT NULL,
  `apellidos` varchar(80) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('estudiante','docente','admin') NOT NULL DEFAULT 'estudiante',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombres`, `apellidos`, `correo`, `password_hash`, `rol`, `estado`, `fecha_registro`) VALUES
(1, 'Johana', 'Zumba', 'docente@eduplat.edu', '$2b$10$axvIqTUU4OHs9uw2GwyaT.YjjglSNit7ROPw1IenO9NCOVzvS4sh2', 'docente', 1, '2026-07-08 15:13:51'),
(2, 'Luis Diego', 'Morejon Salavarria', 'estudiante@eduplat.edu', '$2b$10$VRI0mE6MDJu9XSDUCZCORu7YE.IvpYStOuLXJQO3QfjgsOMnYylNG', 'estudiante', 1, '2026-07-08 15:13:51'),
(3, 'Admin', 'Sistema', 'admin@eduplat.edu', '$2b$10$x5qUlQBgz3L4bUbtIbHIdeUUFoyXiXDLvIAihs8c/M0ZdbHs70xjC', 'admin', 1, '2026-07-08 15:13:51'),
(4, 'Jimmy Ricardo', 'Ortiz Peña', 'jimmyortizp11@gmail.com', '$2y$10$nB8CEqH8cf4W4fkx9sGIg..9RzSzcO/15W3t1G36O4GKm/xkhCdIq', 'estudiante', 1, '2026-07-08 15:30:38'),
(5, 'Ricardo', 'Perez', 'ricardo@gmail.com', '$2y$10$5i/10PT5NWykyyT4WOu3Fe.3Jm818puiduVuL7zdOzEt.QjUFwJlW', 'estudiante', 1, '2026-07-08 20:26:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `fk_cursos_docente` (`docente_id`);

--
-- Indices de la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id_entrega`),
  ADD UNIQUE KEY `uq_entrega` (`id_tarea`,`id_estudiante`),
  ADD KEY `fk_entrega_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`id_evaluacion`),
  ADD KEY `fk_eval_curso` (`id_curso`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD UNIQUE KEY `uq_inscripcion` (`id_curso`,`id_estudiante`),
  ADD KEY `fk_insc_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `fk_material_curso` (`id_curso`);

--
-- Indices de la tabla `resultados_evaluacion`
--
ALTER TABLE `resultados_evaluacion`
  ADD PRIMARY KEY (`id_resultado`),
  ADD UNIQUE KEY `uq_resultado` (`id_evaluacion`,`id_estudiante`),
  ADD KEY `fk_resultado_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `sesiones_log`
--
ALTER TABLE `sesiones_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `fk_sesiones_usuario` (`id_usuario`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `fk_tarea_curso` (`id_curso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `resultados_evaluacion`
--
ALTER TABLE `resultados_evaluacion`
  MODIFY `id_resultado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sesiones_log`
--
ALTER TABLE `sesiones_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_cursos_docente` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `entregas`
--
ALTER TABLE `entregas`
  ADD CONSTRAINT `fk_entrega_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_entrega_tarea` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`id_tarea`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD CONSTRAINT `fk_eval_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `fk_insc_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_insc_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `fk_material_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resultados_evaluacion`
--
ALTER TABLE `resultados_evaluacion`
  ADD CONSTRAINT `fk_resultado_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_resultado_eval` FOREIGN KEY (`id_evaluacion`) REFERENCES `evaluaciones` (`id_evaluacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesiones_log`
--
ALTER TABLE `sesiones_log`
  ADD CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `fk_tarea_curso` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
