-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-10-2026 a las 14:49:17
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
-- Base de datos: `app_login`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `intentos_fallidos` int(11) DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_login` datetime DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `cedula`, `email`, `password`, `rol`, `intentos_fallidos`, `bloqueado_hasta`, `ultimo_login`, `creado_en`) VALUES
(1, 'admin', '00000000', 'admin@ajeven.com', '$2y$10$KpFJAxVpk6vAj6UOOUdjfOB0AhZ0B6bWVXvU2C5CysGq5m7uXRiE6', 'admin', 0, NULL, '2026-10-29 09:13:11', '2026-10-18 14:35:07'),
(3, 'AdrianTovar', '22432749', 'adrian.informatico25@gmail.com', '$2y$10$BSmVTjBkmQLpHCQbCHp/Te0IiFmC3.81hzSW0Lb68gi4np4hAzXMW', 'usuario', 0, NULL, '2026-10-29 09:08:58', '2026-10-20 22:19:23'),
(4, 'YandeivisGallardo', '22000724', 'Yandeivis@gmail.com', '$2y$10$MHe0uBj3SSlNj5cdUtDRIelp3CXkrmmWFvp7VN.K1.dVbw/ovE7b2', 'usuario', 0, NULL, NULL, '2026-10-22 20:23:54'),
(5, 'JosueNieves', '30251967', 'Josue@gmail.com', '$2y$10$lYAIZGAH/Kh4CiX0TZpjB.C9EZWmVbp//abMze/6QozXtaAiJSHH.', 'usuario', 3, '2026-10-26 22:58:22', NULL, '2026-10-26 21:23:48'),
(6, 'juanperez', '987654321', 'juan.perez@gmail.com', '$2y$10$eBJtblUTFSkgf2GQ0fkPfu30fN2SDT6f9VgHIvuOCTlsfyyy9T3AG', 'usuario', 0, NULL, NULL, '2026-10-28 03:14:51');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
