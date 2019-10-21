-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Окт 13 2019 г., 13:05
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
-- Структура таблицы `player`
--
CREATE TABLE `trainer` (
  `code` int(10) UNSIGNED NOT NULL COMMENT 'уникальный код',
  `lastname` varchar(32) NOT NULL DEFAULT 'фамилия игрока' COMMENT 'фамилия',
  `name` varchar(32) DEFAULT '' COMMENT 'имя',
  `position` varchar(32) DEFAULT '' COMMENT 'должност',
  `photo` varchar(50) DEFAULT '' COMMENT 'URL фото',
  `country` int(10) UNSIGNED DEFAULT NULL COMMENT 'страна (код из таблицы country)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='игрок';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`code`),
  ADD KEY `lastname` (`lastname`),
  ADD KEY `player_country` (`country`),
  ADD KEY `player_positions` (`positions`),
  ADD KEY `player_team` (`team`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `player`
--
ALTER TABLE `player`
  MODIFY `code` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код';
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `player_country` FOREIGN KEY (`country`) REFERENCES `country` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `player_positions` FOREIGN KEY (`positions`) REFERENCES `player_positions` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `player_team` FOREIGN KEY (`team`) REFERENCES `team` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
