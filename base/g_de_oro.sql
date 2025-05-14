-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 14-05-2025 a las 06:01:04
-- Versión del servidor: 8.0.41
-- Versión de PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `g_de_oro`
--
DROP DATABASE IF EXISTS `g_de_oro`;
CREATE DATABASE IF NOT EXISTS `g_de_oro` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `g_de_oro`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacenes`
--

DROP TABLE IF EXISTS `almacenes`;
CREATE TABLE `almacenes` (
  `id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `almacenes`
--

INSERT INTO `almacenes` (`id`, `nombre`, `ubicacion`, `fecha_creacion`) VALUES
(1, 'almacen 3', 'al costado de la laguna', '2025-05-13 23:27:46'),
(2, 'almacen 1', 'en la entrada', '2025-05-13 23:27:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `direccion`, `telefono`) VALUES
(2, 'Calle Falsa 123', '55-1234-5678'),
(6, NULL, NULL),
(8, '', ''),
(10, '', ''),
(12, 'Villas', '7751586346');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_venta`
--

DROP TABLE IF EXISTS `detalles_venta`;
CREATE TABLE `detalles_venta` (
  `id` int NOT NULL,
  `venta_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE `empleados` (
  `id` int NOT NULL,
  `rol_empleado` enum('gestor_almacen','ventas','reportes') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `rol_empleado`) VALUES
(3, 'ventas'),
(4, 'gestor_almacen');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `unidad_medida` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio_compra`, `precio_venta`, `stock`, `unidad_medida`) VALUES
(1, 'Arroz Blanco', 'Arroz de grano largo', '15.00', '25.00', 426, 'kg'),
(2, 'Frijol Negro', 'Frijol de la mejor calidad', '12.50', '65.00', 631, 'kg'),
(4, 'arroz', 'jazmin chino ', '15.36', '36.58', 63, 'kg'),
(5, 'Frijol Negro', 'frijol bayo', '23.60', '30.50', 420, 'kg'),
(7, 'cebada', 'cebada', '12.30', '23.50', 210, 'kg'),
(8, 'Frijol Negro', '500 gr', '30.00', '30.00', 3, '50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_vendidos`
--

DROP TABLE IF EXISTS `productos_vendidos`;
CREATE TABLE `productos_vendidos` (
  `id` bigint UNSIGNED DEFAULT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `cantidad` bigint UNSIGNED NOT NULL,
  `id_venta` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `productos_vendidos`
--

INSERT INTO `productos_vendidos` (`id`, `id_producto`, `cantidad`, `id_venta`) VALUES
(0, 1, 100, 8),
(0, 1, 65, 9),
(0, 1, 1, 10),
(0, 1, 98, 14),
(0, 5, 4000, 15),
(NULL, 5, 100, 21),
(NULL, 5, 99, 22),
(NULL, 4, 98, 22),
(NULL, 5, 56, 23),
(NULL, 4, 101, 24),
(NULL, 7, 10, 25),
(NULL, 1, 10, 26),
(NULL, 2, 23, 27),
(NULL, 4, 1, 28),
(NULL, 7, 22, 29),
(NULL, 7, 3, 30),
(NULL, 5, 325, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_almacen`
--

DROP TABLE IF EXISTS `stock_almacen`;
CREATE TABLE `stock_almacen` (
  `id` int NOT NULL,
  `producto_id` int NOT NULL,
  `almacen_id` int NOT NULL,
  `stock` int UNSIGNED NOT NULL DEFAULT '0',
  `ultima_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `stock_almacen`
--

INSERT INTO `stock_almacen` (`id`, `producto_id`, `almacen_id`, `stock`) VALUES
(13, 8, 1, 123),
(14, 8, 2, 0),
(15, 2, 1, 123),
(16, 2, 2, 12),
(17, 7, 1, 300),
(18, 7, 2, 1200),
(25, 1, 1, 112),
(26, 1, 2, 0),
(27, 4, 1, 3),
(28, 4, 2, 123),
(29, 5, 1, 123),
(30, 5, 2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cliente','empleado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'Administrador', 'admin@granos.com', '$2y$10$5uhiXILPsrMtdZPn9qiqdeXz2HeeU8axs65jRNuJeKV5VcApNc.qO', 'admin'),
(2, 'Cliente Uno', 'cliente1@email.com', '$2y$10$hTPuxeJrS5jAhRHRKCedteuaN.Xdw4o5aqY3.5FzHlEC4NBOwHXyu', 'cliente'),
(3, 'Empleado Ventas', 'empleado_ventas@email.com', 'empleado', 'empleado'),
(4, 'Empleado Almacen', 'empleado_almacen@email.com', 'empleado2', 'empleado'),
(5, 'prueba', 'prueba@gmail.com', '$2y$10$hTPuxeJrS5jAhRHRKCedteuaN.Xdw4o5aqY3.5FzHlEC4NBOwHXyu', 'empleado'),
(6, 'cliente2', 'cliente2@gmail.com', '$2y$10$9XNkSpjSAkRZb4v.SgivkeOl03swD9DdlEgSdR1uO3TQZNWLAD4.2', 'cliente'),
(8, 'cliente3', 'cliente3@email.com', '$2y$10$xC8fUAJLbaC6y/UB6.mwNO5qS9QbU5f4N7IFsTbQAb8Z4UnK4pNl6', 'cliente'),
(9, 'Admin2', 'admin@example.com', '$2y$10$5YLeKyXlqNn4d.xX2u3EtOAqdXkrdbUJgk.zIYKszySnqJCUmZd5.', 'admin'),
(10, 'empleado3', 'emleado3@gmail.com', '$2y$10$3D5928ZhittvVm.vMQeBneo8UhWPWXKnSZbFuy5oUH0z.FSxLIEoO', 'empleado'),
(11, 'cliente4', 'cliente4@gmail.com', '$2y$10$z/Q3mbdihZy17VHhcR1Ub.ZWu2LVechsTvgl47DuyH8NOYyc679BO', 'cliente'),
(12, 'Angel Budar', 'angelbudar@gmail.com', '$2y$10$2xSowXlAnsMg8nHAoE5lqeWmuGFeE/0wMLW8QM7VNPkqTv9xdlDay', 'empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `empleado_id` int DEFAULT NULL,
  `fecha_venta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `cliente_id`, `empleado_id`, `fecha_venta`, `total`) VALUES
(8, 2, NULL, '2025-04-21 22:12:02', '5200.00'),
(9, 2, NULL, '2025-04-22 09:05:19', '3380.00'),
(10, 2, NULL, '2025-04-22 10:06:30', '52.00'),
(14, 8, NULL, '2025-04-22 15:03:38', '5096.00'),
(15, 6, NULL, '2025-04-22 15:51:36', '122000.00'),
(16, 6, NULL, '2025-04-24 12:13:07', '1290.00'),
(17, 10, NULL, '2025-04-24 12:16:57', '6184.84'),
(18, 10, NULL, '2025-04-24 12:19:44', '6184.84'),
(19, 10, NULL, '2025-04-24 12:25:07', '6184.84'),
(20, 2, NULL, '2025-04-24 12:25:34', '3050.00'),
(21, 2, NULL, '2025-04-24 12:27:19', '3050.00'),
(22, 10, NULL, '2025-04-24 12:29:45', '6604.34'),
(24, 12, NULL, '2025-04-29 00:35:21', '3694.58'),
(25, 12, NULL, '2025-04-29 00:41:05', '235.00'),
(26, 2, NULL, '2025-04-29 00:52:54', '250.00'),
(27, 2, NULL, '2025-05-14 11:20:35', '1495.00'),
(28, 2, NULL, '2025-05-14 11:21:14', '36.58'),
(29, 2, NULL, '2025-05-14 11:38:27', '517.00'),
(30, 2, NULL, '2025-05-14 11:39:21', '70.50'),
(31, 2, NULL, '2025-05-14 11:39:43', '9912.50');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token` (`token`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `stock_almacen`
--
ALTER TABLE `stock_almacen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_almacen` (`producto_id`,`almacen_id`),
  ADD KEY `almacen_id` (`almacen_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `stock_almacen`
--
ALTER TABLE `stock_almacen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD CONSTRAINT `detalles_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalles_venta_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `stock_almacen`
--
ALTER TABLE `stock_almacen`
  ADD CONSTRAINT `stock_almacen_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_almacen_ibfk_2` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `empleados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
