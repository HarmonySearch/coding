-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Окт 13 2019 г., 12:59
-- Версия сервера: 5.7.25
-- Версия PHP: 7.0.33-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `fcakronbd1`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tourney`
--

CREATE TABLE `tourney` (
  `code` int(10) UNSIGNED NOT NULL COMMENT 'уникальный код',
  `name` varchar(50) DEFAULT '' COMMENT 'название турнира',
  `date_start` date DEFAULT NULL COMMENT 'дата начала турнира',
  `date_end` date DEFAULT NULL COMMENT 'дата окончания турнира'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='турнир';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `tourney`
--
ALTER TABLE `tourney`
  ADD PRIMARY KEY (`code`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `tourney`
--
ALTER TABLE `tourney`
  MODIFY `code` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
