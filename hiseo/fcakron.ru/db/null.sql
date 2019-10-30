--
-- Описание для таблицы player_positions
--

--
-- Структура таблицы `player_positions`
--

CREATE TABLE `player_positions` (
  code INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  `name` varchar(50) NOT NULL COMMENT 'название позиции игрока в футболе',
  
    PRIMARY KEY (code),
    UNIQUE INDEX name (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='позиция игрока';


--
-- Дамп данных таблицы `player_positions`
--

INSERT INTO `player_positions` (`code`, `name`) VALUES
(1, 'Вратарь'),
(2, 'Защитник'),
(3, 'Полузащитник'),
(4, 'Нападающий');

CREATE TABLE `scheme` (
  `code` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  `name` varchar(50) NOT NULL COMMENT 'название схемы',
  
    PRIMARY KEY (code),
    UNIQUE INDEX name (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='названия схем';


scheme

CREATE TABLE standings(
  `code` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `position` INT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'позиция',
  `team_code` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'код команды',
  `team_name` VARCHAR(30) NOT NULL DEFAULT '' COMMENT 'название команды',
  `meet` INT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'матчи',
  `victory` INT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'победы',
  `draw` INT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ничьи',
  `defeat` INT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'поражения',
  `points` INT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'очки',

  PRIMARY KEY (code)

)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'турнирная таблица';



CREATE TABLE player_scheme (
  `code` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scheme` int(10) UNSIGNED DEFAULT NULL COMMENT 'схема (код из таблицы scheme)',
  `meet` int(10) UNSIGNED DEFAULT NULL COMMENT 'матч (код из таблицы meet)',
  `player_1` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 1',
  `player_2` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 2',
  `player_3` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 3',
  `player_4` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 4',
  `player_5` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 5',
  `player_6` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 6',
  `player_7` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 7',
  `player_8` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 8',
  `player_9` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 9',
  `player_10` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 10',
  `player_11` int(10) UNSIGNED DEFAULT NULL COMMENT 'игрок позиция 11',

  PRIMARY KEY (code),

  CONSTRAINT `pm_scheme` FOREIGN KEY (`scheme`) REFERENCES `scheme` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_meet` FOREIGN KEY (`meet`) REFERENCES `meet` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player1` FOREIGN KEY (`player_1`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player2` FOREIGN KEY (`player_2`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player3` FOREIGN KEY (`player_3`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player4` FOREIGN KEY (`player_4`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player5` FOREIGN KEY (`player_5`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player6` FOREIGN KEY (`player_6`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player7` FOREIGN KEY (`player_7`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player8` FOREIGN KEY (`player_8`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player9` FOREIGN KEY (`player_9`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player10` FOREIGN KEY (`player_10`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pm_player11` FOREIGN KEY (`player_11`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'расстановка игроков на игру';
--
-- Описание для таблицы country
--

DROP TABLE IF EXISTS country;
CREATE TABLE country (
  code INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(30) NOT NULL COMMENT 'Название страны',
  short_name VARCHAR(10) NOT NULL COMMENT 'сокращённое Название страны',
  PRIMARY KEY (code),
  UNIQUE INDEX short_name (short_name)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'страна';


-- 
-- Вывод данных для таблицы country
--
INSERT INTO country VALUES 
  (1, 'Россия', 'rus'),
  (2, 'Бельгия', 'belg'),
  (3, 'Германия', 'ger'),
  (4, 'Португалия', 'port');


--
-- Описание для таблицы тренер 
--
CREATE TABLE `trainer` (
  `code` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  `lastname` varchar(32) NOT NULL DEFAULT 'фамилия игрока' COMMENT 'фамилия',
  `name` varchar(32) DEFAULT '' COMMENT 'имя',
  `position` varchar(32) DEFAULT '' COMMENT 'должность',
  `country` int(10) UNSIGNED DEFAULT NULL COMMENT 'страна (код из таблицы country)',

  PRIMARY KEY (code),

  CONSTRAINT `trainer_country` FOREIGN KEY (`country`) REFERENCES `country` (`code`) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='тренер';



CREATE TABLE player (


  code INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  `lastname` varchar(30) NOT NULL DEFAULT 'фамилия игрока' COMMENT 'фамилия',
  `name` varchar(30) DEFAULT '' COMMENT 'имя',
  `birthday` date DEFAULT '1900-01-01' COMMENT 'дата рождения',
  `photo` varchar(50) DEFAULT '' COMMENT 'URL фото',
  `photo2` varchar(50) DEFAULT '' COMMENT 'URL фото',
  `number_` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'номер',
  `sostav` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'состав (0-основной, 1-вспомогательный)',
  `capitan` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'капитан (0-нет, 1-да)',
  `growing` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'рост',
  `weight` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'вес',
  `matches_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'матчи - корректировка',
  `matches` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'матчи',
  `output_start_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на старте - корректировка ',
  `output_start` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на старте',
  `output_in_game_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на замене- корректировка',
  `output_in_game` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'выходы на замене',
  `replace_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'замен в ходе матча - корректировка',
  `replace_` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'замен в ходе матча',
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
  `drop_plus` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'пропуски - корректировка',
  `drop_` tinyint(3) UNSIGNED DEFAULT '0' COMMENT 'пропуски',
  `country` int(10) UNSIGNED DEFAULT NULL COMMENT 'страна (код из таблицы country)',
  `team` int(10) UNSIGNED DEFAULT NULL COMMENT 'команда (код из таблицы team)',
  `positions` int(10) UNSIGNED DEFAULT NULL COMMENT 'позиция игрока (код из таблицы player_positions)',
  `vc` varchar(50) DEFAULT '' COMMENT 'vc соцсеть',
  `instagram` varchar(50) DEFAULT '' COMMENT 'инстаграмм'

  PRIMARY KEY (code),
  
  INDEX lastname (lastname),
  
  CONSTRAINT `player_country` FOREIGN KEY (`country`) REFERENCES `country` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `player_positions` FOREIGN KEY (`positions`) REFERENCES `player_positions` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `player_team` FOREIGN KEY (team) REFERENCES team(code) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='игрок';

ALTER TABLE `player`
  ADD CONSTRAINT `player_team` FOREIGN KEY (`team`) REFERENCES `team` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;


--
-- Описание для таблицы team (команда)
--
DROP TABLE IF EXISTS team;

CREATE TABLE team (
  code INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  `name` VARCHAR(32) DEFAULT '' COMMENT 'название',
  city VARCHAR(32) DEFAULT '' COMMENT 'город',
  website VARCHAR(32) DEFAULT '' COMMENT 'офсайт',
  
  PRIMARY KEY (code)
)
ENGINE = INNODB CHARACTER SET utf8 COMMENT = 'команда';


-- 
-- Вывод данных для таблицы country
--
INSERT INTO team VALUES 
  (1, 'ФК «Акрон»', 'Тольятти','','');


--
-- Описание для таблицы play (игра)
--
DROP TABLE IF EXISTS meet
;

CREATE TABLE meet (
  code INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  name VARCHAR(32) DEFAULT NULL COMMENT 'название игры',
  tourney INT(10) UNSIGNED DEFAULT NULL COMMENT 'турнир (код из таблицы tourney)',
  team_1 INT(10) UNSIGNED DEFAULT NULL COMMENT 'команда 1 (код из таблицы team)',
  team_2 INT(10) UNSIGNED DEFAULT NULL COMMENT 'команда 2 (код из таблицы team)',
  city VARCHAR(32) DEFAULT '' COMMENT 'город',
  stadium VARCHAR(32) DEFAULT '' COMMENT 'название стадиона',
  date_meet DATE DEFAULT NULL COMMENT 'дата проведения игры',
  time_play TIME DEFAULT NULL COMMENT 'время проведения игры',
  
  PRIMARY KEY (code),
  
  CONSTRAINT `meet_tourney` FOREIGN KEY (tourney) REFERENCES tourney(code) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `meet_team_1` FOREIGN KEY (team_1) REFERENCES team(code) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `meet_team_2` FOREIGN KEY (team_2) REFERENCES team(code) ON DELETE SET NULL ON UPDATE CASCADE
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'матч';


--
-- Описание для таблицы tourney (турнир)
--
DROP TABLE IF EXISTS tourney;

CREATE TABLE tourney (
  code INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  name VARCHAR(32) DEFAULT NULL COMMENT 'название турнира',
  date_start DATE DEFAULT NULL COMMENT 'дата начала турнира',
  date_end DATE DEFAULT NULL COMMENT 'дата окончания турнира',
  
  PRIMARY KEY (code)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'турнир';


--
-- Описание для таблицы событий матча
--
DROP TABLE IF EXISTS event;

CREATE TABLE `protocol` (
  `code` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  `meet` INT(10) UNSIGNED DEFAULT NULL COMMENT 'игра (код из таблицы play)',
  `minute` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'минута',
  `player` INT(10) UNSIGNED DEFAULT NULL COMMENT 'игрок (код из таблицы player)',
  `event` INT(10) UNSIGNED DEFAULT NULL COMMENT 'игра (код из таблицы play)',

  PRIMARY KEY (code),
  
  CONSTRAINT `protocol_player` FOREIGN KEY (`player`) REFERENCES `player` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `protocol_event` FOREIGN KEY (`event`) REFERENCES `event` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `protocol_meet` FOREIGN KEY (`meet`) REFERENCES `meet` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = 'протокол событий';


CREATE TABLE `event` (
  `code` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'уникальный код',
  `name` varchar(50) NOT NULL COMMENT 'название события',
  
    PRIMARY KEY (code),
    UNIQUE INDEX name (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='список событий';


база данных футбольного клуба

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'fcakron.db' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'fcakron.udb' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', '1X6r1Z0r' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

