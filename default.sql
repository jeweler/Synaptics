-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 30 2013 г., 23:55
-- Версия сервера: 5.5.29
-- Версия PHP: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `sati`
--

-- --------------------------------------------------------

--
-- Структура таблицы `configs`
--

CREATE TABLE IF NOT EXISTS `configs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `param` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `configs`
--

INSERT INTO `configs` (`id`, `param`, `value`) VALUES
(1, 'deflogin', 'root'),
(2, 'defpass', 't08688121')

-- --------------------------------------------------------

--
-- Структура таблицы `tries`
--

CREATE TABLE IF NOT EXISTS `tries` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `time` bigint(20) NOT NULL,
  `try` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;