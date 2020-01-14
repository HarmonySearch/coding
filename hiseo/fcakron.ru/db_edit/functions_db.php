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

//  ---- ТУРНИРЫ ----------------------------------------------------------------

//  ---- ВЫБОРКА ПО КОДУ ----
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

//  ---- ВЫБОРКА КОДОВ ДЛЯ select ----
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
//  ---- СТАТИСТИКА ИГРОКОВ ----------------------------------------------------

function get_player_stat($player=1, $tourney = 3)
{
    global $wpdb;

    $sql = "SELECT * 
            FROM `player_stat`
            WHERE `player` = $player AND `tourney` = $tourney";
    $result = $wpdb->get_results($sql, 'ARRAY_A');
    return $result;
};

function get_players_stat($team = 1, $tourney = 3)
{
    global $wpdb;

    $sql = "SELECT ps.player as id, ps.* 
            FROM `player_stat` ps
            INNER JOIN `player` p
                ON ps.player = p.code 
                    WHERE p.team = $team AND ps.tourney = $tourney";
    $result = $wpdb->get_results($sql, 'OBJECT_K');
    return $result;
};

//  --------------------------------------------------------------------------------
//  РАСЧЁТ СТАТИСТИКИ ИГРОКОВ (в разработке)
//  должна записываться в новую таблицу player_stat
//
//  --------------------------------------------------------------------------------

// $tourney = 3 (первый турнир), $team = 1 (команда акрон)
function player_stat($tourney = 3, $team = 1)
{
    global $wpdb;

    // все схемы игроков в матчах турнира
    $sql = "SELECT * 
            FROM `player_scheme` ps 
            INNER JOIN `meet` m 
                ON ps.meet = m.code 
                    WHERE m.tourney = $tourney";
    // $sql = "SELECT * FROM `player_scheme`";
    $schemes = $wpdb->get_results($sql, 'ARRAY_N');


    // все игроки команды
    $sql = "SELECT * FROM `player` WHERE `team` = $team "; //+
    $players = $wpdb->get_results($sql, 'ARRAY_A');

    // расчет статистики по каждому игроку команды
    foreach ($players as $player) {
        $code_player = $player["code"]; //+
        $code = $player["code"];

        // нужен запись игрока в статитике. если записи нет, то создаем её и запрашиваем код новой записи 
        $code_stat =  $wpdb->get_var("SELECT `code` FROM `player_stat` WHERE `player` = $code_player AND `tourney` = $tourney");
        if (!$code_stat) {
            $wpdb->insert('player_stat', array('player' => $code_player, 'tourney' => $tourney), array('%d', '%d'));
            $code_stat = $wpdb->insert_id;    
        }

        // ---- ВЫХОДЫ НА СТАРТЕ ------------------------
        // сколько раз завявлен в стартовом составе

        // перебирать всех игроков стартового состава в player_scheme
        // 4-14 поля заявленные игроки
        $output_start = 0; // счетчик выходов
        foreach ($schemes as $scheme) {
            for ($i = 4; $i <= 14; $i++) {
                if ($code == $scheme[$i]) {
                    $output_start++;
                    break;
                }
            }
        }
        $wpdb->update('player', array('output_start' => $output_start), array('code' => $code_player), array('%d')); // в таблицу игрока
        $wpdb->update('player_stat', array('output_start' => $output_start), array('code' => $code_stat), array('%d')); // в таблицу статистики

        // ---- ЗАМЕНЫ В ХОДЕ МАТЧА ------------------------
        // сколько раз заменяли игрока (событие 6 - игрок первый)

        $sql = "SELECT count(event) 
                FROM `statistics` s 
                INNER JOIN `meet` m 
                    ON s.meet = m.code 
                        WHERE m.tourney = $tourney AND s.player = $code_player AND s.event = 6";
        $output_in_game = $wpdb->get_var($sql);
        $wpdb->update('player', array('exchange' => $output_in_game), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('exchange' => $output_in_game), array('code' => $code_stat), array('%d'));

        // ---- ВЫХОДЫ НА ЗАМЕНЕ ------------------------
        // сколько раз выходил на замену  (событие 6 - игрок второй)

        // $sql = "SELECT count(event) 
        //         FROM statistics 
        //         WHERE (`player_2` = " . $code . ") AND (`event` = 6);";
        $sql = "SELECT count(event) 
                FROM `statistics` s 
                INNER JOIN `meet` m 
                    ON s.meet = m.code 
                        WHERE m.tourney = $tourney AND s.player_2 = $code_player AND s.event = 6";
        $output_in_game = $wpdb->get_var($sql);
        $wpdb->update('player', array('output_in_game' => $output_in_game), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('output_in_game' => $output_in_game), array('code' => $code_stat), array('%d'));

        // ---- ИГР СЫГРАНО -----------------------------
        // выходы на старте + выходы на замену

        $matches = $output_start + $output_in_game;
        $wpdb->update('player', array('matches' => $matches), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('matches' => $matches), array('code' => $code_stat), array('%d'));

        // ---- ГОЛЫ С ПЕНАЛЬТИ -------------------------

        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 9);";
        $penalty = $wpdb->get_var($sql);
        $wpdb->update('player', array('penalty' => $penalty), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('penalty' => $penalty), array('code' => $code_stat), array('%d'));

        // ---- ГОЛОВ ЗАБИЛ -----------------------------
        // голы обычные + голы с пенальти

        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 3);";
        $goal = $wpdb->get_var($sql) + $penalty;
        $wpdb->update('player', array('goal' => $goal), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('goal' => $goal), array('code' => $code_stat), array('%d'));


        // ---- ЖЁЛТЫЕ КАРТОЧКИ -----------------------------
        
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 2);";
        $cart_yellow = $wpdb->get_var($sql);
        $wpdb->update('player', array('cart_yellow' => $cart_yellow), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('cart_yellow' => $cart_yellow), array('code' => $code_stat), array('%d'));

        // ---- КРАСНЫЕ КАРТОЧКИ -----------------------------
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 1);";
        $cart_red = $wpdb->get_var($sql);
        $wpdb->update('player', array('cart_red' => $cart_red), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('cart_red' => $cart_red), array('code' => $code_stat), array('%d'));

        // ---- ГОЛЕВЫЕ ПЕРЕДАЧИ -----------------------------
        
        $sql = "SELECT count(event) 
                FROM statistics 
                WHERE (`player` = " . $code . ") AND (`event` = 4);";
        $pass = $wpdb->get_var($sql);
        $wpdb->update('player', array('pass' => $pass), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('pass' => $pass), array('code' => $code_stat), array('%d'));

        // ---- СЕЙВЫ -----------------------------------------
        
        // $sql = "SELECT count(event) 
        //         FROM statistics 
        //         WHERE (`player` = " . $code . ") AND (`event` = 19);";
        // $save = $wpdb->get_var($sql);
        // $wpdb->update('player', array('save' => $save), array('code' => $code_player), array('%d'));
        // $wpdb->update('player_stat', array('save' => $save), array('code' => $code_stat), array('%d'));

        // ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
        // ★★★★ ДЛЯ ВРАТАРЕЙ ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ НАЧАЛО ★★★★

        if ($player["position"] == 1) {

            // ---- СУХИЕ МАТЧИ -----------------------------------------
            // берём сухие матчи. проверяем игрока на наличие в схеме матча

            $shutout = 0; // счетчик сухих матчей
            $sql = "SELECT * FROM `meet` WHERE `completed` = 1 AND `team_1` = $team AND goal_2 = 0 UNION 
            SELECT * FROM `meet` WHERE `completed` = 1 AND `team_2` = $team AND goal_1 = 0" ; // все сухие матчи
            $meets = $wpdb->get_results($sql, 'ARRAY_A');
            foreach ($meets as $meet) { // по каждой встрече

                $sql = "SELECT * FROM `player_scheme` WHERE `meet` = " . $meet['code'];  // берём схему игроков
                $player_scheme = $wpdb->get_results($sql, 'ARRAY_N');
                foreach ($player_scheme as $scheme) {
                    for ($i = 4; $i <= 14; $i++) {
                        if ($code == $scheme[$i]) {
                            $shutout++;
                        }
                    }
                }
            }
            $wpdb->update('player', array('shutout' => $shutout), array('code' => $code_player), array('%d'));
            $wpdb->update('player_stat', array('shutout' => $shutout), array('code' => $code_stat), array('%d'));

            // ---- ГОЛОВ ПРОПУЩЕНО -----------------------------------------

            // если сухая, то дальше. если нет замены то пишем dhtvz 0 код1 код1  если был замена то запоминаем время замены Ч код1 код2
            // смотреть по статистике когда (массив минут) были забиты голы (запрос на все голы, если они были не нами забиты) гол.
            // перебираем массив минут. если <= Ч код1 счет++, если больше >= Ч код2 счет++.
            // если код == код1, добавляем бублики 1 если код == код2 добавляем бублики 2
            $goal_count = 0; // сброс счетчика голов
            $sql = "SELECT * FROM `meet` WHERE `completed` = 1"; // все сыгранные матчи
            $meets = $wpdb->get_results($sql, 'ARRAY_A');
            foreach ($meets as $meet) { // по каждому матчу
                if ($meet['team_1'] == 1) { // сколько нам забили
                    $goal = $meet['goal_2'];
                } else {
                    $goal = $meet['goal_1'];
                };

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


                // в событиях не указано команда забившая гол. берём все голы и пенальти. Ищем код игрока забившего гол в схеме своих игроков.
                // если не найден, то гол нам.
                $sql = "SELECT `minute`, `player` FROM `statistics` WHERE `meet` = " . $meet['code'] . " AND (`event` = 3 OR `event` = 9)"; // 3 гол или гол с пенальти 9
                $goals = $wpdb->get_results($sql, 'ARRAY_A');
                if ($player_scheme) { // есть ли  схема игроков
                     foreach ($goals as $goal) { // по каждому голу
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
                    if ($code == $g1) {
                        $goal_count += $goal1;
                    } // суммируем голы
                    if ($code == $g2) {
                        $goal_count += $goal2;
                    }
                }
            }
//            echo("Пропущено голов $goal_count<br>");
            $wpdb->update('player', array('goal_overlook' => $goal_count), array('code' => $code_player), array('%d'));
            $wpdb->update('player_stat', array('goal_overlook' => $goal_count), array('code' => $code_stat), array('%d'));

        } // БЛОГ ДЛЯ ВРАТАРЕЙ ЗАКОНЧИЛИ




        // ---- МИНУТЫ НА ПОЛЕ ----------------------------------------------------

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
        $wpdb->update('player', array('minute' => $time), array('code' => $code_player), array('%d'));
        $wpdb->update('player_stat', array('minute' => $time), array('code' => $code_stat), array('%d'));
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
