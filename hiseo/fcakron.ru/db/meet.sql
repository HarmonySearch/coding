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
-- Структура таблицы `meet`
--

CREATE TABLE `meet` (
  `code` int(10) UNSIGNED NOT NULL COMMENT 'уникальный код',
  `name` varchar(32) DEFAULT NULL COMMENT 'название игры',
  `tourney` int(10) UNSIGNED DEFAULT NULL COMMENT 'турнир (код из таблицы tourney)',
  `team_1` int(10) UNSIGNED DEFAULT NULL COMMENT 'команда 1 (код из таблицы team)',
  `goal_1` int(2) NOT NULL DEFAULT '0' COMMENT 'голы 1 команда',
  `team_2` int(10) UNSIGNED DEFAULT NULL COMMENT 'команда 2 (код из таблицы team)',
  `goal_2` int(2) NOT NULL DEFAULT '0' COMMENT 'голы 2 команда',
  `city` varchar(32) DEFAULT '' COMMENT 'город',
  `stadium` varchar(32) DEFAULT '' COMMENT 'название стадиона',
  `date_meet` date DEFAULT NULL COMMENT 'дата проведения игры',
  `time_meet` time DEFAULT NULL COMMENT 'время проведения игры'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='матч';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `meet`
--
ALTER TABLE `meet`
  ADD PRIMARY KEY (`code`),
  ADD KEY `meet_tourney` (`tourney`),
  ADD KEY `meet_team_1` (`team_1`),
  ADD KEY `meet_team_2` (`team_2`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `meet`
--
ALTER TABLE `meet`
  MODIFY `code` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код';
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `meet`
--
ALTER TABLE `meet`
  ADD CONSTRAINT `meet_team_1` FOREIGN KEY (`team_1`) REFERENCES `team` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `meet_team_2` FOREIGN KEY (`team_2`) REFERENCES `team` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `meet_tourney` FOREIGN KEY (`tourney`) REFERENCES `tourney` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
