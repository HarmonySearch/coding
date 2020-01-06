<?php

//  CSS стили для админ-панели
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('db-wp-admin', get_template_directory_uri() . '/db_edit/style.css');
});

// События для AJAX
if (wp_doing_ajax() && is_admin()) {
    require_once(dirname(__FILE__) . '/table_edit.php');
    // функции для обработки переданнjuj AJAX запроса
    add_action('wp_ajax_data_change', 'data_change_callback');
    add_action('wp_ajax_row_delete', 'row_delete_callback');
    add_action('wp_ajax_load_file', 'load_file_callback');
    add_action('wp_ajax_load_tourney', 'load_tourney_callback');
    add_action('wp_ajax_load_meet', 'load_meet_callback');
    add_action('wp_ajax_load_team', 'load_team_callback');
    add_action('wp_ajax_load_player', 'load_player_callback');
    add_action('wp_ajax_load_trainer', 'load_trainer_callback');
    add_action('wp_ajax_statistics_add', 'statistics_add_callback');
    add_action('wp_ajax_career_add', 'career_add_callback');
    add_action('wp_ajax_standings_load', 'standings_load_callback');
    add_action('wp_ajax_edit_match_events', 'edit_match_events_callback');
}

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

function get_last_meet($limit = 1)
{
    global $wpdb;

    $sql = "SELECT * FROM meet WHERE completed = 1 AND NOT exclude ORDER BY date_meet DESC LIMIT $limit";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return count($result) == 1 ? $result[0] : $result;
}

//  ★★★★ БУДУЩИЕ МАТЧИ ★★★★

function get_new_match($limit = 1)
// если время матча прошло
{
    global $wpdb;

    $sql = "SELECT * FROM meet WHERE UNIX_TIMESTAMP(CONCAT(date_meet, ' ', time_meet)) + time_zone * 3600 - UNIX_TIMESTAMP(UTC_TIMESTAMP()) > 0 ORDER BY date_meet ASC LIMIT $limit";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return count($result) == 1 ? $result[0] : $result;
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

function get_ticket()
{

    global $wpdb;

    $sql = "SELECT * FROM ticket";
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
    if ($code) {
        $sql = "SELECT * FROM `trainer` WHERE `code` = $code";
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result[0];
    } else {
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


function get_statistics($meet = 0, $period = 0)
{
    global $wpdb;

    if ($period == 0) {
        $sql = "SELECT * FROM `statistics` WHERE `meet` = $meet ORDER BY minute ASC";
    } elseif ($period == 1) {
        $sql = "SELECT * FROM `statistics` WHERE (`meet` = $meet) AND (minute < 46) ORDER BY minute ASC";
    } elseif ($period == 2) {
        $sql = "SELECT * FROM `statistics` WHERE (`meet` = $meet) AND (minute > 45) AND (minute <= 90) ORDER BY minute ASC";
    } elseif ($period == 3) {
        $sql = "SELECT * FROM `statistics` WHERE (`meet` = $meet) AND (minute > 90) ORDER BY minute ASC";
    }
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ СОБЫТИЯ ★★★★

function get_event()
{

    global $wpdb;

    $sql = "SELECT * FROM `event`";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ РАСЧЁТ СТАТИСТИКИ ИГРОКОВ ★★★★
//  статистика должна подсчитываться по турнирам, по не работает

function player_stat($tourney = 0)
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


        // ---- ИГР СЫГРАНО ------------------------------------------------------------
        // перебирать все поля стартового состава в player_scheme. есть, занчит выходил
        $match_count = 0;
        // echo (count($schemes));
        // echo '<br>';
        foreach ($schemes as $scheme) {
            for ($i = 4; $i <= 14; $i++) {
                if ($code == $scheme[$i]) {
                    $match_count++;
                    // echo 'заявлен<br>';
                    break;
                }
            }
        }
        // сколько раз в играх он выходил на замену
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player_2` = " . $code . ") AND (`event` = 6);";
        $count = $wpdb->get_var($sql);
        // echo 'стартовый '. $match_count . 'на замене '. $count . '<br>';
        $match_count += $count;
        $wpdb->update('player', array('matches' => $match_count), array('code' => $code), array('%d'));


        // ---- ГОЛОВ ЗАБИЛ ------------------------------------------------------------
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


        // ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
        // ★★★★ ДЛЯ ВРАТАРЕЙ ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ НАЧАЛО ★★★★

        if ($player["position"] == 1) {

            // ★★★★ ГОЛОВ ПРОПУЩЕНО ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
            // если сухая, то дальше. если нет замены то пишем dhtvz 0 код1 код1  если был замена то запоминаем время замены Ч код1 код2
            // смотреть по статистике когда (массив минут) были забиты голы (запрос на все голы, если они были не нами забиты) гол.
            // перебираем массив минут. если <= Ч код1 счет++, если больше >= Ч код2 счет++.
            // если код == код1, добавляем бублики 1 если код == код2 добавляем бублики 2
            $goal_count = 0; // сброс счетчика голов
            $sql = "SELECT * FROM `meet` WHERE `completed` = 1"; // все сыгранные матчи
            $meets = $wpdb->get_results($sql, 'ARRAY_A');
            // echo (count($meets));
            foreach ($meets as $meet) { // по каждому матчу
                if ($meet['team_1'] == 1) { // сколько нам забили
                    $goal = $meet['goal_2'];
                } else {
                    $goal = $meet['goal_1'];
                };

                // echo ('<br>' . $meet['name'] . '<br>');
                // echo ('Нам забили' . $goal . '<br>');

                if ($goal == 0) continue; // в сухую следующий матч

                // были голы. считаем голы отдельно по каждому вратарю
                $goal1 = 0;
                $goal2 = 0;
                // берём схему игроков
                $sql = "SELECT * FROM `player_scheme` WHERE `meet` = " . $meet['code'];
                $player_scheme = $wpdb->get_results($sql, 'ARRAY_N')[0];

                // проверяем замену вратаря
                $sql = "SELECT `minute`, `player`, `player_2` FROM `statistics` WHERE `meet` = " . $meet['code'] . " AND `event` = 6 AND `player` = " . $code; // 6 замена
                $result = $wpdb->get_results($sql, 'ARRAY_A');
                if ($result) { // да были
                    $t = (int)result['minute']; // на какой минуте
                    $g1 = result['player']; // кто первый голкипер
                    $g2 = result['player_2']; // кто второй голкипер
                } else { // нет не были
                    $t = 0; // любое время без разницы
                    $g1 = $player_scheme[4]; // оба голкипера из схемы 4-это вратарь
                    $g2 = $player_scheme[4];
                }

                // echo ('замена на минуте:' . $t .' игрок А '. $g1 .' игрок Б ' . $g2  . '<br>');

                // в событиях не указано команда забившая гол. берём все голы и пенальти. Ищем код игрока забившего гол в схеме своих игроков.
                // если не найден, то гол нам.
                $sql = "SELECT `minute`, `player` FROM `statistics` WHERE `meet` = " . $meet['code'] . " AND (`event` = 3 OR `event` = 9)"; // 3 гол или гол с пенальти 9
                $goals = $wpdb->get_results($sql, 'ARRAY_A');
                // echo('всего голов: '. count($goals).'<br>');
                // echo($sql.'---------------'.count($player_scheme).'<br>');
                if ($player_scheme) { // есть ли  схема игроков
                     foreach ($goals as $goal) { // по каждому голу
                        // echo('минута: '. $goal['minute'].' игрок: '. $goal['player'].'<br>');
                        $find = 0;
                        for ($i = 4; $i <= 24; $i++) { // ищем среди своих игроков
                            if ($goal['player'] == $player_scheme[$i]) {
                                $find = 1;
                            }
                        }
                        if ($find) { // найден?
                            continue; // да. забил наш игрок
                        } else { // нет забили нам
                            if ((int)$goal['minute'] <= $t) { // до замены голы или после замены
                                $goal1++;
                            } else {
                                $goal2++;
                            }
                        }
                    }
                    // echo($goal1.'+'.$goal2.'<br>');
                    if ($code == $g1) {
                        $goal_count += $goal1;
                    } // суммируем голы
                    if ($code == $g2) {
                        $goal_count += $goal2;
                    }
                }
            }
            $wpdb->update('player', array('goal_allow' => $goal_count), array('code' => $code), array('%d'));

            // ★★★★ СУХИЕ МАТЧИ ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
            // это вратарь?
            $shutout = 0;
            $sql = "SELECT * FROM `meet` WHERE `completed` = 1"; // все сыгранные матчи
            $meets = $wpdb->get_results($sql, 'ARRAY_A');
            foreach ($meets as $meet) { // по каждой встрече
                //echo $meet["name"];

                $sql = "SELECT * FROM `player_scheme` WHERE `meet` = " . $meet['code'];  // берём схему игроков
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
        // ★★★★ ДЛЯ ВРАТАРЕЙ ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ КОНЕЦ ★★★★
        // ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★


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

            $sql = "SELECT * FROM `player_scheme` WHERE `meet` = " . $meet['code'];  // берём схему игроков
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
                $sql = "SELECT `minute` FROM `statistics` WHERE `meet` = " . $meet['code'] . " AND `event` = 6 AND `player` = $code";  // игрок был заменён?
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
                $sql = "SELECT `minute` FROM `statistics` WHERE `meet` = " . $meet['code'] . " and `event` = 6 and `player_2` = $code";  // был заменён игроком?
                $minute = $wpdb->get_var($sql);
                if (!is_null($minute)) {
                    $sql = "SELECT `minute` FROM `statistics` WHERE `meet` = " . $meet['code'] . " and `event` = 20";  // окончание матча
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

//  ---------------------------------------------------- ГРУППА КОМАНДЫ ----
function get_team_group()
{
    $sql = "SELECT * FROM `team_group`";

    global $wpdb;
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};
