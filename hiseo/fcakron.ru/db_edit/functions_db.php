<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ФУНКЦИИ РАБОТЫ С БАЗОЙ ДАННЫХ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ТУРНИРЫ ★★★★

//  ★★★★ ВЫБОРКА ПО КОДУ 
function get_tourney($code = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM tourney";
    if ($code != 0) {
        $sql .= " WHERE code = $code";
        $result = $wpdb->get_results($sql);
    } else {
        $result = $wpdb->get_results($sql, 'ARRAY_A');
    }
    return $result;
};

//  ★★★★ ВЫБОРКА КОДОВ ДЛЯ СЕЛЕКТОРА 
function get_tourney_code()
{
    global $wpdb;

    $sql = "SELECT code, `name` FROM tourney";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ МАТЧИ ★★★★
// NOT exclude
function get_meet_all()
{
    global $wpdb;

    $sql = "SELECT * FROM meet ORDER BY date_meet DESC";
    $result = $wpdb->get_results($sql, 'ARRAY_A'); 
    return $result;
};



function get_meet_select()
{
    global $wpdb;

    $sql = "SELECT `code`, `name` FROM `meet` ORDER BY `date_meet` DESC";
    $result = $wpdb->get_results($sql, 'ARRAY_A'); 
    return $result;
};


function get_meet($code = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM meet";
    if ($code != 0) {
        $sql .= " WHERE code = $code ";
    } else {
        $sql .= " WHERE NOT exclude ";
    }
    $sql .= " ORDER BY date_meet DESC";
    $result = $wpdb->get_results($sql, 'ARRAY_A'); 
    return count($result) == 1 ? $result[0] : $result;
};

function get_near_meet()
{
    global $wpdb;

    $sql = "SELECT * FROM meet WHERE completed = 0 AND NOT exclude ORDER BY date_meet ASC LIMIT 1";
    $result = $wpdb->get_results($sql, 'ARRAY_A'); 
    return count($result) ? $result[0] : false;
}

function get_last_meet()
{
    global $wpdb;

    $sql = "SELECT * FROM meet WHERE completed = 1 AND NOT exclude ORDER BY date_meet DESC LIMIT 1";
    $result = $wpdb->get_results($sql, 'ARRAY_A'); 
    return count($result) ? $result[0] : false;
}

// Является ли Акрон победителем в матче. -1/0/1. false - игра не закончилась или ее нет.
function check_winner($meet_id){
	$match = get_meet($meet_id);
	$akron_team = $match['team_1'] == '1' ? 'goal_1' : 'goal_2';
	if($match['completed'])
		return ( intVal($match[$akron_team]) <=> intVal($match[$akron_team == 'goal_1' ? 'goal_2' : 'goal_1']) );
	return false;
}


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ КОМАНДЫ ★★★★

function get_team($code = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM team";
    if ($code != 0) {
        $sql .= " WHERE code = $code";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return count($result) == 1 ? $result[0] : $result;
};


function get_team_code()
{ // коды для селектора

    global $wpdb;

    $sql = "SELECT code, `name` FROM team";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

function get_team_select()
{ // коды для селектора

    global $wpdb;

    $sql = "SELECT `code`, `name`, `city` FROM team ORDER BY `name` ASC";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

function get_team_select2()
{ // коды для селектора

    global $wpdb;

    $sql = "SELECT `code`, CONCAT (`name`, ' ' ,`city`) as `name` FROM team";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};
/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Таблица country (страна)
 */
function get_country()
{

    global $wpdb;

    $sql = "SELECT * FROM country";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Таблица схема расстановки игроков
 */
function get_scheme()
{

    global $wpdb;

    $sql = "SELECT * FROM scheme";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};



/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Таблица player_positions (позиции игроков)
 */
function get_position()
{
    global $wpdb;

    $sql = "SELECT * FROM player_positions";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ИГРОКИ ★★★★

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * для select (АКРОН)
 */
function get_player_select()
{ 
    global $wpdb;

    $sql = "SELECT `code`, `lastname`, `name` FROM player";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};


/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Выборка игрока по коду
 */
function get_player($code = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM player";
    if ($code != 0) {
        $sql .= " WHERE code = $code";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
	return count($result) ? $result[0] : false;
};

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Выборка игрока по команде
 */
function get_players($team = 0, $role = 0)
{

    global $wpdb;

    $sql = "SELECT * FROM player";
    if ($team != 0 && $role == 0) {
        $sql .= " WHERE team = $team";
    }
    if ($team == 0 && $role != 0) {
        $sql .= " WHERE position = $role";
    }
    if ($team != 0 && $role != 0) {
        $sql .= " WHERE team = $team AND position = $role";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ТРЕНЕРЫ ★★★★


function get_trainer()
{

    global $wpdb;

    $sql = "SELECT * FROM trainer";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ СТАТИСТИКА ★★★★


function get_statistics($meet = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM `statistics` WHERE `meet` = $meet ORDER BY minute ASC";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};


function get_event()
{

    global $wpdb;

    $sql = "SELECT * FROM `event`";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ СХЕМЫ ★★★★


function get_player_scheme($meet = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM `player_scheme` WHERE meet = $meet";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};
