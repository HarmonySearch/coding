<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ МАТЧЕЙ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/functions_db.php');  // функции для работы с базой данных

$code_team = get_team_code();  // код --> команда для селекторов
$code_tourney = get_tourney_code();  // код --> турнир 


//
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ РЕДАКТИРОВАНИЕ ТАбЛИЦЫ ▰▰▰▰
//
?>

<div>
    <button class="meet_add">Добавить матч</button>
</div>

<div class="meet_table">
    <?
    foreach (get_meet() as $rec) { ?>
        <hr class="hr_db">
        <div class="meet" data-code="<?= $rec['code'] ?>">

            <!-- название матча -->
            <div class="name_lbl">Название матча:</div>
            <input class="name" type="text" name="name" value="<?= $rec['name'] ?>">

            <!-- турнир -->
            <select class="tourney" name="tourney">
                <? foreach ($code_tourney as $opt) { ?>
                    <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['tourney']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                <? } ?>
            </select>

            <!-- первая команда -->
            <select class="team" name="team_1">
                <? foreach ($code_team as $opt) { ?>
                    <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team_1']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                <? } ?>
            </select>

            <!-- голы (в одно поле) -->
            <div class="goal">Голы:
                <input type="number" name="goal_1" value="<?= $rec['goal_1'] ?>">
            </div>

            <!-- вторая команда -->
            <select class="team" name="team_2">
                <? foreach ($code_team as $opt) { ?>
                    <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team_2']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                <? } ?>
            </select>

            <!-- голы (в одно поле) -->
            <div class="goal">Голы:
                <input type="number" name="goal_2" value="<?= $rec['goal_1'] ?>">
            </div>

            <div class="city_lbl">Город:</div>
            <input class="city" type="text" name="city" value="<?= $rec['city'] ?>">

            <div class="stadium_lbl">Стадион:</div>
            <input class="stadium" type="text" name="stadium" value="<?= $rec['stadium'] ?>">


            <input class="date_meet" type="date" name="date_meet" value="<?= $rec['date_meet'] ?>">
            <input class="time_meet" type="time" name="time_meet" value="<?= $rec['time_meet'] ?>">

        </div>
    <?
    } ?>
    <hr class="hr_db">
</div>

<script>
    jQuery(function($) {

        $("input[type=text]").change(function() { // значение поля изменилось

            let table = 'meet';
            let name = $(this).attr("name");
            let value = $(this).val();
            let code = $(this).closest(".team").data("code");

            console.log(table, code, name, value);

            let data_lib = {
                action: 'data_change',
                nonce_code: my_ajax_noncerr,
                table: table,
                code: code,
                name: name,
                value: value
            };

            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: data_lib
            }).done(function(data) {
                console.log(data);
            });
        });
    });
</script>