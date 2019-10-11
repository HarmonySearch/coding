<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  ФУНКЦИИ РАБОТЫ С БАЗОЙ ДАННЫХ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ ТУРНИРЫ ▰▰▰▰

//  ▰▰▰▰ ВЫБОРКА ПО КОДУ 
function get_tourney($code = 0)
{

    global $wpdb;

    $sql = "SELECT * FROM tourney";
    if ($code != 0) {
        $sql .= " WHERE code = $code";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ▰▰▰▰ ВЫБОРКА КОДОВ ДЛЯ СЕЛЕКТОРА 
function get_tourney_code()
{ // коды для селектора

    global $wpdb;

    $sql = "SELECT code, `name` FROM tourney";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};


//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ МАТЧИ ▰▰▰▰

function get_meet($code = 0)
{

    global $wpdb;

    $sql = "SELECT * FROM meet";
    if ($code != 0) {
        $sql .= " WHERE code = $code";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};



//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ КОМАНДЫ ▰▰▰▰

function get_team($code = 0)
{

    global $wpdb;

    $sql = "SELECT * FROM team";
    if ($code != 0) {
        $sql .= " WHERE code = $code";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};


function get_team_code()
{ // коды для селектора

    global $wpdb;

    $sql = "SELECT code, `name` FROM team";
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
 * Таблица player_positions (позиции игроков)
 */
function get_positions()
{

    global $wpdb;

    $sql = "SELECT * FROM player_positions";
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
    return $result;
};

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Выборка игрока по команде
 */
function get_players($team = 0)
{

    global $wpdb;

    $sql = "SELECT * FROM player";
    if ($team != 0) {
        $sql .= " WHERE team = $team";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};
