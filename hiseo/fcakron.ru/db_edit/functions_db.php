<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ФУНКЦИИ РАБОТЫ С БАЗОЙ ДАННЫХ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ТУРНИРЫ ★★★★

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


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ МАТЧИ ★★★★
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
function check_winner($meet_id)
{
    $match = get_meet($meet_id);
    $akron_team = $match['team_1'] == '1' ? 'goal_1' : 'goal_2';
    if ($match['completed'])
        return (intVal($match[$akron_team]) <=> intVal($match[$akron_team == 'goal_1' ? 'goal_2' : 'goal_1']));
    return false;
}


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ КОМАНДЫ ★★★★

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


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ИГРОКИ ★★★★


//  ★★★★ АССИСТЕНТЫ ★★★★
// больше всего голевых передач
function get_assistant()
{
    global $wpdb;

    $sql = "SELECT `code`, `number`, `name`, `lastname`, `pass` FROM player ORDER BY pass DESC LIMIT 5";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ★★★★ ГОЛ+ПАС ★★★★
// больше всего голевых передач и голов по сумме
function get_goal_pass()
{
    global $wpdb;

    $sql = "SELECT `code`, `number`, `name`, `lastname`, `goal`, `pass`, (`goal` + `pass`) as goal_pass  
            FROM player ORDER BY (goal + pass) DESC LIMIT 5";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

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


function get_trainer($code = 0)
{

    global $wpdb;
	if($code){
		$sql = "SELECT * FROM `trainer` WHERE `code` = $code";
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		return $result[0];
	}else{
		$sql = "SELECT * FROM trainer";
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ПЕРСОНАЛ ПО КОДУ ★★★★

function get_personal($group = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM `trainer` WHERE `group` = $group";
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

//  ★★★★★★★★ РАСЧЁТ СТАТИСТИКИ ИГРОКОВ ★★★★
function player_stat()
{
    global $wpdb;
    // все схемы игроков в матчах
    $sql = "SELECT * FROM `player_scheme`";
    $schemes = $wpdb->get_results($sql, 'ARRAY_N');


    // все игроки акрона
    $sql = "SELECT * FROM `player` WHERE `team` = 1";
    $players = $wpdb->get_results($sql, 'ARRAY_A');
    foreach ($players as $player) {
        $code = $player["code"];

        // ★★★★ ИГР СЫГРАНО
        // нужно перебирать все поля не запасных игроков в player_scheme
        // поскольку одного игрока два раза не записать, то достаточно найти его код 
        $m = 0;
        // echo (count($schemes));
        // echo '<br>';
        foreach ($schemes as $scheme) {
            for ($i = 4; $i <= 14; $i++) {
                if ($code == $scheme[$i]) $m++;
            }
        }
        // проверить сколько раз в играх он выходил на замену
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player_2` = " . $code . ") AND (`event` = 6);";
        // игр сыграно = выходы на замену + заявлено в матче + корректировка
        $count = $wpdb->get_var($sql) + $m;
        $wpdb->update('player', array('matches' => $count), array('code' => $code), array('%d'));

        // ★★★★ ГОЛОВ ЗАБИЛ
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 3);";
        $count = $wpdb->get_var($sql);
        $wpdb->update('player', array('goal' => $count), array('code' => $code), array('%d'));

        // ★★★★ ГОЛОВ С ПЕНАЛЬТИ
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 9);";
        $count = $wpdb->get_var($sql);
        $wpdb->update('player', array('penalty' => $count), array('code' => $code), array('%d'));

        // ★★★★ желтая карточка
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 2);";
        $count = $wpdb->get_var($sql);
        $wpdb->update('player', array('cart_yellow' => $count), array('code' => $code), array('%d'));

        // ★★★★ красная карточка
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 1);";
        $count = $wpdb->get_var($sql);
        $wpdb->update('player', array('cart_red' => $count), array('code' => $code), array('%d'));

        // ★★★★ голевая передача
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 4);";
        $count = $wpdb->get_var($sql);
        $wpdb->update('player', array('pass' => $count), array('code' => $code), array('%d'));

        //  ★★★★ СЕЙВ
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 19);";
        $count = $wpdb->get_var($sql);
        $wpdb->update('player', array('save' => $count), array('code' => $code), array('%d'));

        // ★★★★ ПРОПУЩЕНИЕ ГОЛЫ
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player_2` = " . $code . ") AND (`event` = 3);";
        $count = $wpdb->get_var($sql);
        $wpdb->update('player', array('goal_overlook' => $count), array('code' => $code), array('%d'));

        // ★★★★ СУХИЕ МАТЧИ
        // это вратарь?
        if ($player["position"] == 1) {
            $shutout = 0;
            $sql = "SELECT * FROM `meet` WHERE `completed` = 1"; // все сыгранные матчи
            $meets = $wpdb->get_results($sql, 'ARRAY_A');
            foreach ($meets as $meet) { // по каждой встрече
                //echo $meet["name"];

                $sql = "SELECT * FROM `player_scheme` WHERE `meet` = ".$meet['code'];  // берём схему игроков
                $player_scheme = $wpdb->get_results($sql, 'ARRAY_N');
                $main = false;
                foreach ($player_scheme as $scheme) { // он основной игрок?
                    for ($i = 4; $i <= 14; $i++) {
                        if ($code == $scheme[$i]) {
                            $main = true;
                        }
                    }
                }
                if ($main) {
                    if ($meet['team_1'] != 1) { // сколько нам забили голов
                        $goal = $meet['goal_1'];
                    } else {
                        $goal = $meet['goal_2'];
                    }
                    if ($goal) $shutout++; // если счет всухую
                }
            }
            $wpdb->update('player', array('shutout' => $shutout), array('code' => $code), array('%d'));
        }

        // ★★★★ МИНУТЫ НА ПОЛЕ
        $sql = "SELECT * FROM `meet` WHERE completed;"; // все сыгранные матчи
        $meets = $wpdb->get_results($sql, 'ARRAY_A');
        $time = 0;
        // если в основном. если его заменили, то берём время замены. если нет то берём время конца матча
        // перебираем встречи
        foreach ($meets as $meet) { // по каждой встрече
            $meet_code = $meet['code'];

            $sql = "SELECT `minute` FROM `statistics` WHERE `meet` = $meet_code AND `event` = 20";  // событие окончание матча
            $time_end = $wpdb->get_var($sql);
            if (is_null($time_end)) $time_end = 90; // если не прописано, то считаем 90 минут два тайма

            $sql = "SELECT * FROM `player_scheme` WHERE `meet` = ".$meet['code'];  // берём схему игроков
            $meet_schemes = $wpdb->get_results($sql, 'ARRAY_N');
            $main = false;
            foreach ($meet_schemes as $scheme) { // он в основной игрок?
                for ($i = 4; $i <= 14; $i++) {
                    if ($code == $scheme[$i]) {
                        $main = true;
                    }
                }
            }
            if ($main) {
                // echo $main.'<br>';
                $sql = "SELECT `minute` FROM `statistics` WHERE `meet` = ".$meet['code']." AND `event` = 6 AND `player` = $code";  // игрок был заменён?
                $minute = $wpdb->get_var($sql);
                if (!is_null($minute)) { // была замена
                    $time += $minute; // играл до минуты замены
                } else {
                    $time += $time_end; // играл до конца игры
                }
                // echo $t.'<br>';
            }
            $sub = false;
            foreach ($meet_schemes as $scheme) { // он в запасной игрок?
                for ($i = 15; $i <= 24; $i++) {
                    if ($code == $scheme[$i]) {
                        $sub = true;
                    }
                }
            }
            if ($sub) {
                $sql = "SELECT `minute` FROM `statistics` WHERE `meet` = ".$meet['code']." and `event` = 6 and `player_2` = $code";  // был заменён игроком?
                $minute = $wpdb->get_var($sql);
                if (!is_null($minute)) {
                    $sql = "SELECT `minute` FROM `statistics` WHERE `meet` = ".$meet['code']." and `event` = 20";  // окончание матча
                    $time_end = $wpdb->get_var($sql);
                    if (!is_null($time_end)) {
                        $time += $time_end - $minute;
                    } else {
                        $time += 90 - $minute;
                    }
                }
            }
        }
        $wpdb->update('player', array('minute' => $time), array('code' => $code), array('%d'));
    }
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ СХЕМЫ ★★★★


function get_player_scheme($meet = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM `player_scheme` WHERE meet = $meet";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ КАРЬЕРА ★★★★

function get_player_career($player = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM `career` WHERE player = $player";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ТУРНИРНАЯ ТАБЛИЦА ★★★★

function get_standings($tourney = 0)
{
    global $wpdb;

    $sql = "SELECT * FROM standings  WHERE tourney = $tourney";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ КАРЬЕРА ★★★★

function get_group()
{
    global $wpdb;

    $sql = "SELECT * FROM `group`";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

