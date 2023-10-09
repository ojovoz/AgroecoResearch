-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Servidor: 192.168.86.197
-- Tiempo de generación: 28-01-2019 a las 09:48:43
-- Versión del servidor: 5.5.57-0+deb7u1-log
-- Versión de PHP: 5.3.29-1~dotdeb.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `agresearch`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(10) unsigned NOT NULL,
  `activity_name` varchar(100) NOT NULL,
  `activity_category` varchar(30) NOT NULL,
  `activity_periodicity` int(10) unsigned NOT NULL COMMENT 'in days',
  `activity_measurement_units` varchar(30) NOT NULL,
  `activity_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_x_crop_or_treatment`
--

CREATE TABLE IF NOT EXISTS `activity_x_crop_or_treatment` (
  `activity_x_crop_or_treatment_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `crop_id` int(10) unsigned DEFAULT NULL,
  `treatment_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crop`
--

CREATE TABLE IF NOT EXISTS `crop` (
  `crop_id` int(10) unsigned NOT NULL,
  `crop_name` varchar(20) NOT NULL,
  `crop_symbol` varchar(10) NOT NULL,
  `crop_variety_name` varchar(40) NOT NULL,
  `crop_used_for_intercropping` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `field`
--

CREATE TABLE IF NOT EXISTS `field` (
  `field_id` int(10) unsigned NOT NULL,
  `parent_field_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `field_date_created` date NOT NULL,
  `field_date_final` date DEFAULT NULL,
  `field_name` varchar(30) NOT NULL,
  `field_replication_number` int(10) unsigned NOT NULL,
  `field_lat` varchar(30) NOT NULL,
  `field_lng` varchar(30) NOT NULL,
  `field_configuration` text NOT NULL,
  `field_is_active` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fields registered in the ag. research';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `general_observation`
--

CREATE TABLE IF NOT EXISTS `general_observation` (
  `general_observation_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `category` varchar(200) NOT NULL COMMENT 'From list: climatic event, fall army worm, other pest, other observation',
  `date` date NOT NULL,
  `comments` text NOT NULL,
  `image` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `health_report_item`
--

CREATE TABLE IF NOT EXISTS `health_report_item` (
  `health_report_item_id` int(10) unsigned NOT NULL,
  `item` varchar(100) NOT NULL,
  `item_categories` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `input_log`
--

CREATE TABLE IF NOT EXISTS `input_log` (
  `input_log_id` int(10) unsigned NOT NULL,
  `input_log_date` date NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `plots` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `input_age` varchar(50) NOT NULL,
  `input_origin` varchar(200) NOT NULL,
  `input_crop_variety` varchar(100) NOT NULL,
  `input_quantity` int(10) unsigned NOT NULL,
  `input_units` text NOT NULL,
  `input_cost` varchar(100) NOT NULL,
  `input_treatment_material` text NOT NULL,
  `input_treatment_preparation_method` text NOT NULL,
  `input_comments` text NOT NULL,
  `input_picture` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `plots` varchar(200) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `crop_id` int(10) unsigned NOT NULL,
  `sample_number` varchar(200) NOT NULL,
  `treatment_id` int(10) unsigned NOT NULL,
  `measurement_id` int(10) unsigned NOT NULL,
  `activity_id` int(10) unsigned NOT NULL,
  `log_date` date NOT NULL,
  `log_value_number` float NOT NULL,
  `log_value_units` varchar(30) NOT NULL,
  `log_value_text` text NOT NULL,
  `log_number_of_laborers` varchar(100) NOT NULL,
  `log_cost` varchar(100) NOT NULL,
  `log_comments` text NOT NULL,
  `log_picture` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `measurement`
--

CREATE TABLE IF NOT EXISTS `measurement` (
  `measurement_id` int(10) unsigned NOT NULL,
  `measurement_name` varchar(100) NOT NULL,
  `measurement_category` varchar(30) NOT NULL,
  `measurement_subcategory` varchar(40) NOT NULL,
  `measurement_type` int(10) unsigned NOT NULL,
  `measurement_range_min` float NOT NULL,
  `measurement_range_max` float NOT NULL,
  `measurement_units` varchar(30) NOT NULL,
  `measurement_categories` text NOT NULL COMMENT 'List of items for qualitative measurements',
  `measurement_periodicity` int(10) unsigned NOT NULL COMMENT 'in days',
  `measurement_has_sample_number` tinyint(1) NOT NULL DEFAULT '0',
  `measurement_common_complex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 = common, 1 = complex',
  `measurement_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `measurement_x_crop_or_treatment`
--

CREATE TABLE IF NOT EXISTS `measurement_x_crop_or_treatment` (
  `measurement_x_crop_or_treatment_id` int(10) unsigned NOT NULL,
  `measurement_id` int(10) unsigned NOT NULL,
  `crop_id` int(10) unsigned DEFAULT NULL,
  `treatment_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `notification_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned NOT NULL,
  `receiver_id` int(10) NOT NULL,
  `notification_date` date NOT NULL,
  `notification_text` text NOT NULL,
  `notification_sent` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plot`
--

CREATE TABLE IF NOT EXISTS `plot` (
  `plot_id` int(10) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `polt_row` int(11) unsigned NOT NULL,
  `plot_column` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `treatment`
--

CREATE TABLE IF NOT EXISTS `treatment` (
  `treatment_id` int(10) unsigned NOT NULL,
  `treatment_name` varchar(40) NOT NULL,
  `treatment_category` varchar(30) NOT NULL,
  `primary_crop_id` int(11) unsigned DEFAULT NULL,
  `intercropping_crop_id` int(11) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `treatment_color`
--

CREATE TABLE IF NOT EXISTS `treatment_color` (
  `treatment_color_id` int(10) unsigned NOT NULL,
  `treatment_category` varchar(100) NOT NULL,
  `color` varchar(100) NOT NULL,
  `color_hex` varchar(100) NOT NULL,
  `color_code_app` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `user_alias` varchar(10) NOT NULL,
  `user_password` varchar(30) NOT NULL,
  `user_organization` varchar(40) NOT NULL,
  `user_role` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `weather_data`
--

CREATE TABLE IF NOT EXISTS `weather_data` (
  `weather_data_id` int(10) unsigned NOT NULL,
  `field_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `filename` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indices de la tabla `activity_x_crop_or_treatment`
--
ALTER TABLE `activity_x_crop_or_treatment`
  ADD PRIMARY KEY (`activity_x_crop_or_treatment_id`);

--
-- Indices de la tabla `crop`
--
ALTER TABLE `crop`
  ADD PRIMARY KEY (`crop_id`);

--
-- Indices de la tabla `field`
--
ALTER TABLE `field`
  ADD PRIMARY KEY (`field_id`);

--
-- Indices de la tabla `general_observation`
--
ALTER TABLE `general_observation`
  ADD PRIMARY KEY (`general_observation_id`);

--
-- Indices de la tabla `health_report_item`
--
ALTER TABLE `health_report_item`
  ADD PRIMARY KEY (`health_report_item_id`);

--
-- Indices de la tabla `input_log`
--
ALTER TABLE `input_log`
  ADD PRIMARY KEY (`input_log_id`);

--
-- Indices de la tabla `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indices de la tabla `measurement`
--
ALTER TABLE `measurement`
  ADD PRIMARY KEY (`measurement_id`);

--
-- Indices de la tabla `measurement_x_crop_or_treatment`
--
ALTER TABLE `measurement_x_crop_or_treatment`
  ADD PRIMARY KEY (`measurement_x_crop_or_treatment_id`);

--
-- Indices de la tabla `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indices de la tabla `plot`
--
ALTER TABLE `plot`
  ADD PRIMARY KEY (`plot_id`);

--
-- Indices de la tabla `treatment`
--
ALTER TABLE `treatment`
  ADD PRIMARY KEY (`treatment_id`);

--
-- Indices de la tabla `treatment_color`
--
ALTER TABLE `treatment_color`
  ADD PRIMARY KEY (`treatment_color_id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indices de la tabla `weather_data`
--
ALTER TABLE `weather_data`
  ADD PRIMARY KEY (`weather_data_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity`
--
ALTER TABLE `activity`
  MODIFY `activity_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `activity_x_crop_or_treatment`
--
ALTER TABLE `activity_x_crop_or_treatment`
  MODIFY `activity_x_crop_or_treatment_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `crop`
--
ALTER TABLE `crop`
  MODIFY `crop_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `field`
--
ALTER TABLE `field`
  MODIFY `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `general_observation`
--
ALTER TABLE `general_observation`
  MODIFY `general_observation_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `health_report_item`
--
ALTER TABLE `health_report_item`
  MODIFY `health_report_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `input_log`
--
ALTER TABLE `input_log`
  MODIFY `input_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `log`
--
ALTER TABLE `log`
  MODIFY `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `measurement`
--
ALTER TABLE `measurement`
  MODIFY `measurement_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `measurement_x_crop_or_treatment`
--
ALTER TABLE `measurement_x_crop_or_treatment`
  MODIFY `measurement_x_crop_or_treatment_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `plot`
--
ALTER TABLE `plot`
  MODIFY `plot_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `treatment`
--
ALTER TABLE `treatment`
  MODIFY `treatment_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `treatment_color`
--
ALTER TABLE `treatment_color`
  MODIFY `treatment_color_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `weather_data`
--
ALTER TABLE `weather_data`
  MODIFY `weather_data_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
