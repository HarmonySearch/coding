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

CREATE TABLE `player` (
  `code` int(10) UNSIGNED NOT NULL COMMENT 'уникальный код',
  `lastname` varchar(30) NOT NULL DEFAULT 'фамилия игрока' COMMENT 'фамилия',
  `name` varchar(30) DEFAULT '' COMMENT 'имя',
  `birthday` date DEFAULT '1900-01-01' COMMENT 'дата рождения',
  `photo` varchar(50) DEFAULT '' COMMENT 'URL фото',
  `photo2` varchar(50) DEFAULT '' COMMENT 'URL фото',
  `number` varchar(3) NOT NULL DEFAULT '' COMMENT 'номер',
  `sostav` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'состав (0-основной, 1-вспомогательный)',
  `capitan` tinyint(1) UNSIGNED DEFAULT '0' COMMENT 'капитан (0-нет, 1-да)',
  `growing` varchar(3) NOT NULL DEFAULT '' COMMENT 'рост',
  `weight` varchar(3) NOT NULL DEFAULT '' COMMENT 'вес',
  `matches_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'матчи - корректировка',
  `matches` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'матчи',
  `output_start_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на старте - корректировка ',
  `output_start` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на старте',
  `output_in_game_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на замене- корректировка',
  `output_in_game` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на замене',
  `exchange_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'замен в ходе матча - корректировка',
  `exchange` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'замен в ходе матча',
  `goal_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'голы - корректировка',
  `goal` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'Голы',
  `pass_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'голевые передачи - корректировка',
  `pass` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'голевые передачи',
  `cart_y_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'жёлтые карточки - корректировка',
  `cart_y` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'жёлтые карточки',
  `cart_r_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'красные карточки - корректировка',
  `cart_r` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'красные карточки',
  `save_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'сейвы - корректировка',
  `save` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'сейвы',
  `omission_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'пропуски - корректировка',
  `omission` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'пропуски',
  `country` int(10) UNSIGNED DEFAULT NULL COMMENT 'страна (код из таблицы country)',
  `team` int(10) UNSIGNED DEFAULT NULL COMMENT 'команда (код из таблицы team)',
  `positions` int(10) UNSIGNED DEFAULT NULL COMMENT 'позиция игрока (код из таблицы player_positions)',
  `vc` varchar(50) DEFAULT '' COMMENT 'vc соцсеть',
  `instagram` varchar(50) DEFAULT '' COMMENT 'инстаграмм'
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
