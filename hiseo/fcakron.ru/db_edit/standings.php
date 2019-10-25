<?php
// 1. список всех команд участвывающих в матчах турнира
// SELECT team_1 FROM meet union SELECT team_2 FROM meet (только уникальные) массив кодов команд
// дальше 
// обнуляем массив
// forean список команд
// f[команда][игра]=0;
// f[команда][победа]=0;
// f[команда][ничия]=0;
// f[команда][проигрыш]=0;

global $wpdb;
echo '<pre>'; 
// список всех команд игравших в турнире. нужен чтобв подготовить таблицу для сортировки
$sql = 'SELECT team_1 as team FROM meet WHERE  tourney=3 union SELECT team_2 FROM meet WHERE  tourney=3';
$teams = $wpdb->get_results($sql, 'ARRAY_A');
//var_dump($teams);
// обнуляем таблицу
$s=[];
foreach ($teams as $team) {
    // echo $team['team'].PHP_EOL;
    $i = $team['team'];
    $s[$i]['victory']= 0; // победы
    $s[$i]['draw']= 0; // ничьи
    $s[$i]['defeat']= 0; // поражение
    $s[$i]['meet']= 0; // количество игр
    $s[$i]['diff']= 0; // разность забитых и пропущеных
    $s[$i]['goal']= 0; 
    $s[$i]['goal_guest']= 0;
}  
// var_dump($s);
// Перебираем все сыгранные матчи
$sql = 'SELECT team_1, team_2, goal_1, goal_2 FROM meet WHERE tourney=3 AND completed';
$meets = $wpdb->get_results($sql, 'ARRAY_A');
foreach ($meets as $meet) {
    // Берём команду хозяина
    // Суммирум игры победы проигрыши ничьи


    // добавим командам по игре
    $s[$meet['team_1']]['meet']++;
    $s[$meet['team_2']]['meet']++;
    // первая команда выиграла. вторая проиграла
    if ($meet['goal_1'] > $meet['goal_2']) {
        $s[$meet['team_1']]['victory']++;
        $s[$meet['team_1']]['points'] = $s[$meet['team_1']]['points'] + 2;
        $s[$meet['team_2']]['defeat']++;
    }
    if ($meet['goal_1'] == $meet['goal_2']) {
        $s[$meet['team_1']]['draw']++;
        $s[$meet['team_1']]['points']++;
        $s[$meet['team_2']]['draw']++;
        $s[$meet['team_2']]['points']++;
    }
var_dump($s);
}
usort ($s, 'grade_sort');
// отслотировать по очкам
echo '--------------------------------------------------------------';
var_dump($s);
//echo '</pre>'; 
// foreach ($s as $row) {

// }

// Функция сортировки по оценке: сортировка по УБЫВАНИЮ.
function grade_sort($x, $y) {
    if ($x['points'] < $y['points']) {
        return true;
    } else if ($x['points'] > $y['points']) {
        return false;
    } else {
        return 0;
    }
}
?>