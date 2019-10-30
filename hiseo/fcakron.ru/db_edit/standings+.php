<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ИГРОВАЯ ТАБЛИЦА
//  https: //fcakron.ru/wp-admin/admin.php?page=standings
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

// В случае равенства очков места Команд в турнирной таблице определяются: 
// 1. по результатам игр(ы) между собой (число очков, число побед, 
//    разность забитых и пропущенных мячей, число забитых мячей, число мячей, 
//    забитых на чужом поле); 
// 2. по наибольшему числу побед во всех Матчах; 
// 3. по лучшей разности забитых и пропущенных мячей во всех Матчах; 
// 4. по наибольшему числу забитых мячей во всех Матчах; 
// 5. по наибольшему числу мячей, забитых на чужих полях во всех Матчах.

$teams_list = array_column(get_team_select2(), 'name', 'code');

// ★★★★ ВСЕ КОМАНДЫ УЧАСТВУЩИЕ В ТУРНИИРЕ ★★★★

$sql = "SELECT team_1 as team FROM meet WHERE  tourney=3  AND completed union SELECT team_2 as team FROM meet WHERE  tourney=3 AND completed";
global $wpdb;
$teams = $wpdb->get_results($sql, 'ARRAY_A');
if (!$teams) {
    die();
};

// выбираем все сыгранные матчи
$sql = 'SELECT team_1, team_2, goal_1, goal_2 FROM meet WHERE tourney=3 AND completed';
$meets = $wpdb->get_results($sql, 'ARRAY_A');

// ★★★★ СТАТИСТИКА ПО ТУРНИРУ ПО КАЖДОЙ КОМАНДЕ ★★★★

// обнуляем общую статистику
$stat_all = [];
foreach ($teams as $team) {
    $i = $team['team'];
    $stat_all[$i]['team'] = $i; // код команды
    $stat_all[$i]['meet'] = 0; // количество игр
    $stat_all[$i]['victory'] = 0; // количество побед
    $stat_all[$i]['draw'] = 0; // количество ничьих
    $stat_all[$i]['defeat'] = 0; // количество поражение
    $stat_all[$i]['points'] = 0; // количество очки
    $stat_all[$i]['goal'] = 0; // количество голов 
    $stat_all[$i]['goal_guest'] = 0; // количество голов на чужом поле
}

foreach ($meets as $meet) {

    // ИГРЫ
    $stat_all[$meet['team_1']]['meet']++;
    $stat_all[$meet['team_2']]['meet']++;

    // ПОБЕДЫ, ПОРАЖЕНИЯ, НИЧЬИ, ОЧКИ 
    if ($meet['goal_1'] > $meet['goal_2']) {
        $stat_all[$meet['team_1']]['victory']++; // 1-ой команде победу
        $stat_all[$meet['team_2']]['defeat']++; // 2-й команде поражение
        $stat_all[$meet['team_1']]['points'] += 2; // 1-й команде 2 очка за победу
    } elseif ($meet['goal_1'] < $meet['goal_2']) {
        $stat_all[$meet['team_2']]['victory']++; // 2-й команде победу
        $stat_all[$meet['team_1']]['defeat']++; // 1-ой команде поражение
        $stat_all[$meet['team_2']]['points'] += 2; // 2-й команде 2 очка за победу
    } else {
        $stat_all[$meet['team_1']]['draw']++; // 1-й команде ничья
        $stat_all[$meet['team_2']]['draw']++; // 2-й команде ничья
        $stat_all[$meet['team_1']]['points']++; // 1-й команде одно очко
        $stat_all[$meet['team_2']]['points']++; // 2-й команде одно очко
    }
    // ★★★★ подсчёт ГОЛОВ ★★★★
    $stat_all[$meet['team_1']]['goal'] += $meet['goal_1']; // 1-й команде суммируем голы
    $stat_all[$meet['team_2']]['goal'] += $meet['goal_2']; // 2-й команде суммируем голы
    $stat_all[$meet['team_2']]['goal_guest'] += $meet['goal_2']; // 2-й команде суммируем голы на чужём поле
    $stat_all[$meet['team_1']]['diff'] += $meet['goal_1'] - $meet['goal_2']; // 1-й команде суммируем разницу голов
    $stat_all[$meet['team_2']]['diff'] += $meet['goal_2'] - $meet['goal_1']; // 2-й команде суммируем разницу голов
}

// ★★★★ СОРТИРОВКА ★★★★

usort($stat_all, "compare");

function compare($v1, $v2)
{   // сравнение общих очков
    if ($v1["points"] > $v2["points"]) return -1;
    if ($v1["points"] < $v2["points"]) return 1;
    if ($v1["points"] == $v2["points"]) {

        // ★★★★ СТАТИСТИКА ПО ИГРАМ МЕЖДУ КОМАНДАМИ ★★★★

        // echo ("равно: " . $v1["team"] . '---' . $v2["team"] . '<br>');
        $t1 = $v1["team"]; // 1-я команда
        $t2 = $v2["team"]; // 2-я команда
        $sql = "SELECT team_1, team_2, goal_1, goal_2 
                FROM meet 
                WHERE ((team_1 = $t1 AND team_2 = $t2) OR (team_2 = $t1 AND team_1 = $t2)) 
                AND tourney=3 AND completed";
        global $wpdb;
        $meets = $wpdb->get_results($sql, 'ARRAY_A');
        // если матчи имеются, то анализируем
        if ($meets) {
            // первая команда - 0, вторая - 1
            $stat_lcl = [];
            $stat_lcl[0]['victory'] = 0; // количество побед
            $stat_lcl[0]['points'] = 0; // количество очки
            $stat_lcl[0]['goal'] = 0; // количество голов 
            $stat_lcl[0]['goal_guest'] = 0; // количество голов на чужом поле
            $stat_lcl[1]['victory'] = 0; // количество побед
            $stat_lcl[1]['points'] = 0; // количество очки
            $stat_lcl[1]['goal'] = 0; // количество голов 
            $stat_lcl[1]['goal_guest'] = 0; // количество голов на чужом поле

            foreach ($meets as $meet) {

                // ПОБЕДЫ, ПОРАЖЕНИЯ, НИЧЬИ, ОЧКИ 
                if ($meet['goal_1'] > $meet['goal_2']) {
                    $stat_lcl[0]['victory']++; // 1-ой команде победу
                    $stat_lcl[0]['points'] += 2; // 1-й команде 2 очка за победу
                } elseif ($meet['goal_1'] < $meet['goal_2']) {
                    $stat_lcl[1]['victory']++; // 2-й команде победу
                    $stat_lcl[1]['points'] += 2; // 2-й команде 2 очка за победу
                } else {
                    $stat_lcl[0]['points']++; // 1-й команде одно очко
                    $stat_lcl[1]['points']++; // 2-й команде одно очко
                }
                // ★★★★ подсчёт ГОЛОВ ★★★★
                $stat_lcl[0]['goal'] += $meet['goal_1']; // 1-й команде суммируем голы
                $stat_lcl[1]['goal'] += $meet['goal_2']; // 2-й команде суммируем голы
                $stat_lcl[1]['goal_guest'] += $meet['goal_2']; // 2-й команде суммируем голы на чужём поле
                $stat_lcl[0]['diff'] += $meet['goal_1'] - $meet['goal_2']; // 1-й команде суммируем разницу голов
                $stat_lcl[1]['diff'] += $meet['goal_2'] - $meet['goal_1']; // 2-й команде суммируем разницу голов
            }
            // ★★★★ СРАВНЕНИЕ ПО ЛОКАЛЬНОЙ ТАБЛИЦЕ ★★★★

            // echo ('СРАВНЕНИЕ ПО ЛОКАЛЬНОЙ ТАБЛИЦЕ<br>');
            // по очкам
            if ($stat_lcl[0]['points'] > $stat_lcl[1]['points']) return -1;
            if ($stat_lcl[0]['points'] < $stat_lcl[1]['points']) return 1;
            // по победам
            if ($stat_lcl[0]['victory'] > $stat_lcl[1]['victory']) return -1;
            if ($stat_lcl[0]['victory'] < $stat_lcl[1]['victory']) return 1;
            // по разности голов
            if ($stat_lcl[0]['diff'] > $stat_lcl[1]['diff']) return -1;
            if ($stat_lcl[0]['diff'] < $stat_lcl[1]['diff']) return 1;
            // по голам
            if ($stat_lcl[0]['goal'] > $stat_lcl[1]['goal']) return -1;
            if ($stat_lcl[0]['goal'] < $stat_lcl[1]['goal']) return 1;
            // по голам на чужём поле
            if ($stat_lcl[0]['goal_guest'] > $stat_lcl[1]['goal_guest']) return -1;
            if ($stat_lcl[0]['goal_guest'] < $stat_lcl[1]['goal_guest']) return 1;
        };
        // ★★★★ СРАВНЕНИЕ ПО ОБЩЕЙ ТАБЛИЦЕ ★★★★
        // echo ('СРАВНЕНИЕ ПО ОБЩЕЙ ТАБЛИЦ<br>');
        // по наибольшему числу побед во всех Матчах; 
        if ($v1["victory"] > $v2["victory"]) return -1;
        if ($v1["victory"] < $v2["victory"]) return 1;
        // по лучшей разности забитых и пропущенных мячей во всех Матчах;         
        if ($v1["diff"] > $v2["diff"]) return -1;
        if ($v1["diff"] < $v2["diff"]) return 1;
        // по наибольшему числу забитых мячей во всех Матчах;      
        if ($v1["goal"] > $v2["goal"]) return -1;
        if ($v1["goal"] < $v2["goal"]) return 1;
        // по наибольшему числу мячей, забитых на чужих полях во всех Матчах.      
        if ($v1["goal_guest"] > $v2["goal_guest"]) return -1;
        if ($v1["goal_guest"] < $v2["goal_guest"]) return 1;

        return 0;
    };
}
?>
<h1>Турнирная таблица</h1>
<h3>(информация не используется на сайте)</h3>
<h3>Таблица вычисляется по таблицам матчей. Что внесено, то и считает.</h3>

<table>
    <tr>
        <th>Позиция</th>
        <th>Команда</th>
        <th>Матчи</th>
        <th>Победы</th>
        <th>Ничьи</th>
        <th>Поражения</th>
        <th>Очки</th>
    </tr>
    <?php
    foreach ($stat_all as $key=>$rec) {
        ?>
        <tr>
            <td><?= $key+1 ?></td>
            <td><?= $teams_list[$rec["team"]] ?></td>
            <td><?= $rec["meet"] ?></td>
            <td><?= $rec["victory"] ?></td>
            <td><?= $rec["draw"] ?></td>
            <td><?= $rec["defeat"] ?></td>
            <td><?= $rec["points"] ?></td>
        </tr>
    <?php
    }
    echo '</table>';

    // ★★★★ ЗАПИСЬ ТУРНИРНОЙ ТАБЛИЦЫ В БД ★★★★
    foreach ($stat_all as $key => $rec) {

        $wpdb->insert(
            'standings_calс',
            array(
                'position' => $key + 1,
                'team_code' => $rec["team"],
                'team_name' => $teams_list[$rec["team"]],
                'meet' => $rec["meet"],
                'victory' => $rec["victory"],
                'draw' => $rec["draw"],
                'defeat' => $rec["defeat"],
                'points' => $rec["points"]
            ),
            array('%d', '%d', '%s', '%d', '%d', '%d', '%d', '%d')
        );
    }
    ?>