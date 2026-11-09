-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-10-2026 a las 14:49:05
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
-- Base de datos: `app_proyecto_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intereses_prestaciones`
--

CREATE TABLE `intereses_prestaciones` (
  `id` int(11) NOT NULL,
  `recibo_no` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ficha` varchar(20) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `fecha_ingreso` varchar(20) NOT NULL,
  `periodo_reporte` varchar(100) NOT NULL DEFAULT 'DESDE ENERO/2026 HASTA DICIEMBRE/2026',
  `fecha_emision` varchar(50) NOT NULL,
  `usuario_reporte` varchar(50) NOT NULL DEFAULT '',
  `detalles_meses` longtext NOT NULL COMMENT 'Almacena un array JSON con los datos mensuales puros',
  `fecha_visualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `intereses_prestaciones`
--

INSERT INTO `intereses_prestaciones` (`id`, `recibo_no`, `cedula`, `nombre`, `ficha`, `departamento`, `fecha_ingreso`, `periodo_reporte`, `fecha_emision`, `usuario_reporte`, `detalles_meses`, `fecha_visualizacion`) VALUES
(1, '2026-01', '22432749', 'ADRIAN TOVAR', '813', '102 PRODUCCION', '08/11/2007', 'DESDE ENERO/2026 HASTA DICIEMBRE/2026', 'Oct. 15, 2026', '', '[\r\n    {\"mes\": \"ENE-26\", \"rem_dev\": \"5,846.05\", \"alic_bono\": \"649.56\", \"alic_util\": \"2,878.02\", \"sal_int\": \"9,373.63\", \"res_mensual\": \"4,686.75\", \"dias_adic\": \"-\", \"ant_acum\": \"136,606.66\", \"prestamos\": \"0.00\", \"anticipos\": \"26,698.50\", \"saldo_neto\": \"109,908.16\", \"tasa\": \"59.38\", \"interes_mes\": \"0.00\", \"interes_acum\": \"0.00\", \"destacado\": false},\r\n    {\"mes\": \"ENE-26\", \"rem_dev\": \"5,846.05\", \"alic_bono\": \"649.56\", \"alic_util\": \"2,878.02\", \"sal_int\": \"9,373.63\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"136,606.66\", \"prestamos\": \"0.00\", \"anticipos\": \"26,698.50\", \"saldo_neto\": \"109,908.16\", \"tasa\": \"59.38\", \"interes_mes\": \"5,438.60\", \"interes_acum\": \"5,438.60\", \"destacado\": false},\r\n    {\"mes\": \"FEB-26\", \"rem_dev\": \"10,702.12\", \"alic_bono\": \"1,189.12\", \"alic_util\": \"3,567.37\", \"sal_int\": \"15,458.61\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"136,606.66\", \"prestamos\": \"0.00\", \"anticipos\": \"109,098.50\", \"saldo_neto\": \"27,508.16\", \"tasa\": \"59.39\", \"interes_mes\": \"1,361.40\", \"interes_acum\": \"6,800.00\", \"destacado\": false},\r\n    {\"mes\": \"MAR-26\", \"rem_dev\": \"17,940.44\", \"alic_bono\": \"1,993.38\", \"alic_util\": \"5,980.15\", \"sal_int\": \"25,913.97\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"136,606.66\", \"prestamos\": \"0.00\", \"anticipos\": \"109,098.50\", \"saldo_neto\": \"27,508.16\", \"tasa\": \"59.36\", \"interes_mes\": \"1,360.70\", \"interes_acum\": \"8,160.70\", \"destacado\": false},\r\n    {\"mes\": \"ABR-26\", \"rem_dev\": \"14,741.12\", \"alic_bono\": \"1,637.90\", \"alic_util\": \"4,913.71\", \"sal_int\": \"21,292.73\", \"res_mensual\": \"10,646.40\", \"dias_adic\": \"-\", \"ant_acum\": \"147,253.06\", \"prestamos\": \"0.00\", \"anticipos\": \"109,098.50\", \"saldo_neto\": \"38,154.56\", \"tasa\": \"59.29\", \"interes_mes\": \"0.00\", \"interes_acum\": \"8,160.70\", \"destacado\": true},\r\n    {\"mes\": \"ABR-26\", \"rem_dev\": \"14,741.12\", \"alic_bono\": \"1,637.90\", \"alic_util\": \"4,913.71\", \"sal_int\": \"21,292.73\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"147,253.06\", \"prestamos\": \"0.00\", \"anticipos\": \"109,098.50\", \"saldo_neto\": \"38,154.56\", \"tasa\": \"59.29\", \"interes_mes\": \"1,885.20\", \"interes_acum\": \"10,045.90\", \"destacado\": true},\r\n    {\"mes\": \"MAY-26\", \"rem_dev\": \"27,750.38\", \"alic_bono\": \"3,083.38\", \"alic_util\": \"9,250.13\", \"sal_int\": \"40,083.89\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"147,253.06\", \"prestamos\": \"0.00\", \"anticipos\": \"109,098.50\", \"saldo_neto\": \"38,154.56\", \"tasa\": \"59.22\", \"interes_mes\": \"1,883.00\", \"interes_acum\": \"11,928.90\", \"destacado\": false},\r\n    {\"mes\": \"JUN-26\", \"rem_dev\": \"18,023.37\", \"alic_bono\": \"2,002.60\", \"alic_util\": \"6,007.79\", \"sal_int\": \"26,033.76\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"147,253.06\", \"prestamos\": \"0.00\", \"anticipos\": \"109,098.50\", \"saldo_neto\": \"38,154.56\", \"tasa\": \"59.27\", \"interes_mes\": \"1,884.50\", \"interes_acum\": \"13,813.40\", \"destacado\": false},\r\n    {\"mes\": \"JUL-26\", \"rem_dev\": \"21,469.64\", \"alic_bono\": \"2,385.52\", \"alic_util\": \"7,156.55\", \"sal_int\": \"31,011.71\", \"res_mensual\": \"15,505.80\", \"dias_adic\": \"-\", \"ant_acum\": \"162,758.86\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"25,060.36\", \"tasa\": \"59.23\", \"interes_mes\": \"0.00\", \"interes_acum\": \"13,813.40\", \"destacado\": true},\r\n    {\"mes\": \"JUL-26\", \"rem_dev\": \"21,469.64\", \"alic_bono\": \"2,385.52\", \"alic_util\": \"7,156.55\", \"sal_int\": \"31,011.71\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"162,758.86\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"25,060.36\", \"tasa\": \"59.23\", \"interes_mes\": \"1,236.90\", \"interes_acum\": \"15,050.30\", \"destacado\": true},\r\n    {\"mes\": \"AGO-26\", \"rem_dev\": \"18,981.72\", \"alic_bono\": \"2,109.08\", \"alic_util\": \"6,327.24\", \"sal_int\": \"27,418.04\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"162,758.86\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"25,060.36\", \"tasa\": \"59.06\", \"interes_mes\": \"1,233.40\", \"interes_acum\": \"16,283.70\", \"destacado\": false},\r\n    {\"mes\": \"SEP-26\", \"rem_dev\": \"23,350.30\", \"alic_bono\": \"2,594.48\", \"alic_util\": \"7,783.43\", \"sal_int\": \"33,728.21\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"162,758.86\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"25,060.36\", \"tasa\": \"58.95\", \"interes_mes\": \"1,231.10\", \"interes_acum\": \"17,514.80\", \"destacado\": false},\r\n    {\"mes\": \"OCT-26\", \"rem_dev\": \"19,114.33\", \"alic_bono\": \"2,123.81\", \"alic_util\": \"6,371.44\", \"sal_int\": \"27,609.58\", \"res_mensual\": \"13,804.80\", \"dias_adic\": \"-\", \"ant_acum\": \"176,563.66\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"38,865.16\", \"tasa\": \"58.95\", \"interes_mes\": \"0.00\", \"interes_acum\": \"17,514.80\", \"destacado\": true},\r\n    {\"mes\": \"OCT-26\", \"rem_dev\": \"19,114.33\", \"alic_bono\": \"2,123.81\", \"alic_util\": \"6,371.44\", \"sal_int\": \"27,609.58\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"176,563.66\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"38,865.16\", \"tasa\": \"58.95\", \"interes_mes\": \"1,909.20\", \"interes_acum\": \"19,424.00\", \"destacado\": true},\r\n    {\"mes\": \"NOV-26\", \"rem_dev\": \"135,037.17\", \"alic_bono\": \"15,004.13\", \"alic_util\": \"45,012.39\", \"sal_int\": \"195,053.69\", \"res_mensual\": \"195,053.70\", \"dias_adic\": \"30\", \"ant_acum\": \"371,617.36\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"233,918.86\", \"tasa\": \"58.95\", \"interes_mes\": \"0.00\", \"interes_acum\": \"19,424.00\", \"destacado\": true},\r\n    {\"mes\": \"NOV-26\", \"rem_dev\": \"135,037.17\", \"alic_bono\": \"15,004.13\", \"alic_util\": \"45,012.39\", \"sal_int\": \"195,053.69\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"371,617.36\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"233,918.86\", \"tasa\": \"58.95\", \"interes_mes\": \"11,491.30\", \"interes_acum\": \"30,915.30\", \"destacado\": true},\r\n    {\"mes\": \"DIC-26\", \"rem_dev\": \"0.00\", \"alic_bono\": \"684.93\", \"alic_util\": \"2,054.80\", \"sal_int\": \"2,739.73\", \"res_mensual\": \"-\", \"dias_adic\": \"-\", \"ant_acum\": \"371,617.36\", \"prestamos\": \"0.00\", \"anticipos\": \"137,698.50\", \"saldo_neto\": \"233,918.86\", \"tasa\": \"58.95\", \"interes_mes\": \"11,491.30\", \"interes_acum\": \"42,408.60\", \"destacado\": false}\r\n  ]', '2026-10-28 00:22:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recibos`
--

CREATE TABLE `recibos` (
  `id` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `cuadrilla` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `seccion` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `recibo_no` varchar(50) DEFAULT NULL,
  `periodo` varchar(50) DEFAULT NULL,
  `asignaciones_texto` text DEFAULT NULL,
  `asignaciones_cant` varchar(50) DEFAULT NULL,
  `asignaciones_monto` varchar(50) DEFAULT NULL,
  `deducciones_texto` text DEFAULT NULL,
  `deducciones_cant` varchar(50) DEFAULT NULL,
  `deducciones_monto` varchar(50) DEFAULT NULL,
  `saldos_texto` varchar(50) DEFAULT NULL,
  `total_asignaciones` varchar(50) DEFAULT NULL,
  `total_deducciones` varchar(50) DEFAULT NULL,
  `neto_letras` text DEFAULT NULL,
  `neto_pagar` varchar(50) DEFAULT NULL,
  `salarios_devengados` varchar(50) DEFAULT NULL,
  `disponible_prestaciones` varchar(50) DEFAULT NULL,
  `cuenta_bancaria` varchar(50) DEFAULT NULL,
  `fecha_visualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recibos`
--

INSERT INTO `recibos` (`id`, `cedula`, `tipo`, `codigo`, `nombre`, `turno`, `cuadrilla`, `departamento`, `seccion`, `cargo`, `recibo_no`, `periodo`, `asignaciones_texto`, `asignaciones_cant`, `asignaciones_monto`, `deducciones_texto`, `deducciones_cant`, `deducciones_monto`, `saldos_texto`, `total_asignaciones`, `total_deducciones`, `neto_letras`, `neto_pagar`, `salarios_devengados`, `disponible_prestaciones`, `cuenta_bancaria`, `fecha_visualizacion`) VALUES
(1, '22432749', '', 'EMP-184668', 'ADRIAN TOVAR', 'Rotativo', 'Cuadrilla A', 'Producción', 'Envasado', 'Operador de Planta', 'REC-2026-0038', 'Semana 38 (15/09/2026 - 22/09/2026)', 'Sueldo Base\nBono de Producción', '7\n1', '15000.00\n30.00', 'Seguro Social (IVSS)\nAhorro Habitacional', '1', '6\n1.00', '0.00', 'BS15000.00', 'BS6.00', 'SON: QUINCE MIL BOLIVARES EXACTOS', 'BS15000.00', 'BS15000.00', 'BS12850.00', '0102-1234-56-7890123456', '2026-10-28 00:22:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `utilidades`
--

CREATE TABLE `utilidades` (
  `id` int(11) NOT NULL,
  `recibo_no` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `turno` varchar(20) DEFAULT NULL,
  `cuadrilla` varchar(50) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `seccion` varchar(100) DEFAULT NULL,
  `cargo` varchar(150) DEFAULT NULL,
  `periodo` varchar(100) DEFAULT NULL,
  `asignaciones_texto` text DEFAULT NULL,
  `asignaciones_cant` text DEFAULT NULL,
  `asignaciones_monto` text DEFAULT NULL,
  `deducciones_texto` text DEFAULT NULL,
  `deducciones_cant` text DEFAULT NULL,
  `deducciones_monto` text DEFAULT NULL,
  `saldos_texto` text DEFAULT NULL,
  `total_asignaciones` decimal(12,2) DEFAULT 0.00,
  `total_deducciones` decimal(12,2) DEFAULT 0.00,
  `neto_letras` varchar(255) DEFAULT NULL,
  `neto_pagar` decimal(12,2) DEFAULT 0.00,
  `salarios_devengados` decimal(12,2) DEFAULT 0.00,
  `disponible_prestaciones` decimal(12,2) DEFAULT 0.00,
  `cuenta_bancaria` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_visualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `utilidades`
--

INSERT INTO `utilidades` (`id`, `recibo_no`, `cedula`, `codigo`, `nombre`, `turno`, `cuadrilla`, `departamento`, `seccion`, `cargo`, `periodo`, `asignaciones_texto`, `asignaciones_cant`, `asignaciones_monto`, `deducciones_texto`, `deducciones_cant`, `deducciones_monto`, `saldos_texto`, `total_asignaciones`, `total_deducciones`, `neto_letras`, `neto_pagar`, `salarios_devengados`, `disponible_prestaciones`, `cuenta_bancaria`, `created_at`, `fecha_visualizacion`) VALUES
(1, '23', '22432749', 'emp-184668', 'Adrian Tovar', '61', 'GRUPO A 2022', 'PRODUCCION', 'ENVASADO LINEA 8', 'OPERADOR DE PLANTA', 'del 01/01/2026 al 31/12/2026', 'UTILIDAD', '33.33%', '107,550.53', 'RETENCION FAOV\nCUOTA SINDICAL UTILIDADES\nDEDUCCION ANTICIPO DE UTILIDAD\nINCES', '1%\n.8%\n\n.5%', '202.39\n161.91\n87,311.96\n101.19', '607.17', 107550.53, 87777.45, 'Son Diecinueve Mil Setecientos Setenta Y Tres Con 8/100', 19773.08, 12137.23, 47111.81, '0105012111121123456', '2026-10-25 18:35:24', '2026-10-28 00:22:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones_asignaciones`
--

CREATE TABLE `vacaciones_asignaciones` (
  `id` int(11) NOT NULL,
  `liquidacion_id` int(11) NOT NULL,
  `concepto` varchar(150) NOT NULL,
  `dias` decimal(8,2) DEFAULT NULL,
  `monto_dia` decimal(12,2) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vacaciones_asignaciones`
--

INSERT INTO `vacaciones_asignaciones` (`id`, `liquidacion_id`, `concepto`, `dias`, `monto_dia`, `monto`) VALUES
(1, 1, 'DIAS DE VACACIONES Art.219', 36.00, 6056.27, 218025.81),
(2, 1, 'BONO VACACIONAL Art.223', 40.00, 6056.27, 242250.90),
(3, 1, 'HORAS COMPENSADAS', NULL, NULL, 551.91),
(4, 1, 'DIA ADICIONAL POR DOM.LABORADO', 1.00, 1009.47, 1009.47),
(5, 1, 'SALARIO', 3.00, 271.75, 815.25),
(6, 1, 'PROLONGACION NOCT. S/TURNO', NULL, NULL, 868.26),
(7, 1, 'DOMINGO PROMEDIO', 1.00, 4333.69, 4333.69),
(8, 1, 'BONO SALARIAL', NULL, NULL, 34.15),
(9, 1, 'BONO NOCTURNO', 12.00, NULL, 1002.01),
(10, 1, 'RECARGO DOM.TRAB.S/ART.88 RLOT', 12.00, NULL, 2240.11),
(11, 1, 'BONIFICACION ESPECIAL SALARIO*', NULL, NULL, 2356.75),
(12, 1, 'DIAS LIBRES CONVENIDOS', 2.00, 813.95, 1627.90),
(13, 1, 'SABADO PROMEDIO', 1.00, 3571.72, 3571.72),
(14, 1, 'TIEMPO DE VIAJE', 3.00, 70.20, 210.60),
(15, 1, 'TIEMPO DE VIAJE**', 1.00, NULL, 70.20),
(16, 1, 'DIA ADICIONAL S/CLAUSULA 89', 15.00, 6056.27, 90844.08),
(17, 1, 'DESCANSO TRABAJADO DIURNO', 12.00, NULL, 6192.06);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones_deducciones`
--

CREATE TABLE `vacaciones_deducciones` (
  `id` int(11) NOT NULL,
  `liquidacion_id` int(11) NOT NULL,
  `concepto` varchar(150) NOT NULL,
  `dias` decimal(8,2) DEFAULT NULL,
  `monto_dia` decimal(12,2) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vacaciones_deducciones`
--

INSERT INTO `vacaciones_deducciones` (`id`, `liquidacion_id`, `concepto`, `dias`, `monto_dia`, `monto`) VALUES
(2, 1, 'SEGURO SOCIAL OBLIGATORIO', NULL, NULL, 48.00),
(3, 1, 'LRPE', NULL, NULL, 12.00),
(4, 1, 'RETENCION FAOV', NULL, NULL, 5757.69),
(5, 1, 'RETENCION DE I.S.L.R.', NULL, NULL, 172.73),
(6, 1, 'CUOTA SINDICAL', NULL, NULL, 4606.15),
(7, 1, 'MONTEPIO', NULL, NULL, 271.75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones_liquidaciones`
--

CREATE TABLE `vacaciones_liquidaciones` (
  `id` int(11) NOT NULL,
  `recibo_no` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `ficha` varchar(20) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `cargo` varchar(150) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `salario_promedio` decimal(12,2) DEFAULT 0.00,
  `inicio_vacaciones` date DEFAULT NULL,
  `fecha_culminacion` date DEFAULT NULL,
  `fecha_reintegro` date DEFAULT NULL,
  `periodo_laboral` varchar(50) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `vacaciones_a_disfrutar` int(11) DEFAULT 0,
  `total_asignaciones` decimal(12,2) DEFAULT 0.00,
  `total_deducciones` decimal(12,2) DEFAULT 0.00,
  `neto_pagar` decimal(12,2) DEFAULT 0.00,
  `tramitado_fecha` date DEFAULT NULL,
  `elaborado_por` varchar(100) DEFAULT NULL,
  `aprobado_por` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_visualizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vacaciones_liquidaciones`
--

INSERT INTO `vacaciones_liquidaciones` (`id`, `recibo_no`, `cedula`, `ficha`, `apellidos`, `nombres`, `cargo`, `departamento`, `salario_promedio`, `inicio_vacaciones`, `fecha_culminacion`, `fecha_reintegro`, `periodo_laboral`, `fecha_ingreso`, `fecha_vencimiento`, `vacaciones_a_disfrutar`, `total_asignaciones`, `total_deducciones`, `neto_pagar`, `tramitado_fecha`, `elaborado_por`, `aprobado_por`, `created_at`, `fecha_visualizacion`) VALUES
(1, 'VAC-2026-001', '22432749', 'EMP-184668', 'TOVAR', 'ADRIAN', '11121-OPERADOR DE PLANTA', '102-PRODUCCION', 6056.27, '2026-10-05', '2026-11-16', '2026-11-17', '2025-2026', '2006-09-28', '2026-09-28', 30, 575804.87, 10904.55, 564900.32, '2026-09-27', '', '', '2026-10-25 16:23:00', '2026-10-28 00:22:23');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `intereses_prestaciones`
--
ALTER TABLE `intereses_prestaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cedula_recibo` (`cedula`,`recibo_no`);

--
-- Indices de la tabla `recibos`
--
ALTER TABLE `recibos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `utilidades`
--
ALTER TABLE `utilidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_util_recibo_cedula` (`recibo_no`,`cedula`);

--
-- Indices de la tabla `vacaciones_asignaciones`
--
ALTER TABLE `vacaciones_asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_liq_asignaciones` (`liquidacion_id`);

--
-- Indices de la tabla `vacaciones_deducciones`
--
ALTER TABLE `vacaciones_deducciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_liq_deducciones` (`liquidacion_id`);

--
-- Indices de la tabla `vacaciones_liquidaciones`
--
ALTER TABLE `vacaciones_liquidaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_recibo_cedula` (`recibo_no`,`cedula`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `intereses_prestaciones`
--
ALTER TABLE `intereses_prestaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `recibos`
--
ALTER TABLE `recibos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `utilidades`
--
ALTER TABLE `utilidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `vacaciones_asignaciones`
--
ALTER TABLE `vacaciones_asignaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `vacaciones_deducciones`
--
ALTER TABLE `vacaciones_deducciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `vacaciones_liquidaciones`
--
ALTER TABLE `vacaciones_liquidaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `vacaciones_asignaciones`
--
ALTER TABLE `vacaciones_asignaciones`
  ADD CONSTRAINT `fk_liq_asignaciones` FOREIGN KEY (`liquidacion_id`) REFERENCES `vacaciones_liquidaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vacaciones_deducciones`
--
ALTER TABLE `vacaciones_deducciones`
  ADD CONSTRAINT `fk_liq_deducciones` FOREIGN KEY (`liquidacion_id`) REFERENCES `vacaciones_liquidaciones` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
