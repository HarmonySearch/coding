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
$sql = 'SELECT team_1 FROM meet WHILE tourney=1 union SELECT team_2 FROM meet WHILE tourney=1';
$teams = $wpdb->get_results($sql, 'ARRAY_A');
s=[];
foreach ($teams as $team) {
    s[$team]['victory']= 0;
    s[$team]['draw']= 0;
    s[$team]['defeat']= 0;
    s[$team]['meet']= 0;
    s[$team]['points']= 0;
}  
$sql = 'SELECT team_1, team_2, goal_1, goal_2 FROM meet WHILE tourney=1 AND completed';
$meets = $wpdb->get_results($sql, 'ARRAY_A');
foreach ($meets as $meet) {
    // добавим командам по игре
    s[$meet['team_1']]['meet']++;
    s[$meet['team_2']]['meet']++;
    // первая команда выиграла. вторая проиграла
    if ($meet['goal_1'] > $meet['goal_2']) {
        s[$meet['team_1']]['victory']++;
        s[$meet['team_1']]['points'] = s[$meet['team_1']]['points'] + 2;
        s[$meet['team_2']]['defeat']++;
    }
    if ($meet['goal_1'] == $meet['goal_2']) {
        s[$meet['team_1']]['draw']++;
        s[$meet['team_1']]['points']++;
        s[$meet['team_2']]['draw']++;
        s[$meet['team_2']]['points']++;
    }
var_dump(s);
}
?>