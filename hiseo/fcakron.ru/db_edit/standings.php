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

// список всех игр
global $wpdb;
echo '<pre>'; 
$sql = 'SELECT team_1 as team FROM meet WHERE  tourney=3 union SELECT team_2 FROM meet WHERE  tourney=3';
$teams = $wpdb->get_results($sql, 'ARRAY_A');
//var_dump($teams);
$s=[];
foreach ($teams as $team) {
    // echo $team['team'].PHP_EOL;
    $i = $team['team'];
    $s[$i]['victory']= 0;
    $s[$i]['draw']= 0;
    $s[$i]['defeat']= 0;
    $s[$i]['meet']= 0;
    $s[$i]['points']= 0;
}  
// var_dump($s);

$sql = 'SELECT team_1, team_2, goal_1, goal_2 FROM meet WHERE tourney=3 AND completed';
$meets = $wpdb->get_results($sql, 'ARRAY_A');
foreach ($meets as $meet) {
    // В случае равенства очков места Команд в турнирной таблице определяются: 
    // - по результатам игр(ы) между собой (число очков, число побед, разность забитых и 
    // пропущенных мячей, число забитых мячей, число мячей, забитых на чужом поле); 
    // - по наибольшему числу побед во всех Матчах; 
    // - по лучшей разности забитых и пропущенных мячей во всех Матчах; 
    // - по наибольшему числу забитых мячей во всех Матчах; 
    // - по наибольшему числу мячей, забитых на чужих полях во всех Матчах.

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