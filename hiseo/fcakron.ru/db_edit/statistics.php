<?php
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
// РЕДАКТИРОВАНИЕ ТАБЛИЦЫ СТАСТИСТИКИ
// https://fcakron.ru/wp-admin/admin.php?page=statistics&meet=15
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$meet = $_GET['meet']; // код матча

// select игрока. выборка игроков только из списка команд участвующих в игре

$sql = "SELECT `code`,`lastname`, (SELECT name FROM team WHERE code = `team`) AS `team_name`
        FROM `player` 
        WHERE `team` IN (
            SELECT `team_1` FROM `meet` WHERE `code` = $meet 
            union 
            SELECT `team_2` FROM `meet` WHERE `code` = $meet)";
global $wpdb;
$code_player = $wpdb->get_results($sql, 'ARRAY_A');
$code_event = get_event();  // select события

// на каком коде события сколько игроков
$player_event = [];
foreach ($code_event as $rec) {
    $player_event[$rec['code']] = $rec['player'];
}

// нужна хоть одна запись, поскольку добавление записей
// делается клонированием последней записи
$res = get_statistics($meet);
if (!$res) {
    global $wpdb;
    $wpdb->insert('statistics', array('meet' => $meet), array('%d'));
}

//
// ★★★★ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ★★★★
//
?>
<style>
    .event0 select[name=player] {
        display: none;
    }

    .event5 select[name=player] {
        display: none;
    }

    .event13 select[name=player] {
        display: none;
    }

    .event14 select[name=player] {
        display: none;
    }

    .event17 select[name=player] {
        display: none;
    }

    .event18 select[name=player] {
        display: none;
    }

    .event3 select[name=player_2] {
        display: block;
    }
    .event6 select[name=player_2] {
        display: block;
    }
    .event9 select[name=player_2] {
        display: block;
    }
</style>

<h1>Таблица статистики</h1>
<h3>(информация пока не используется на сайте)</h3>
<div>
    <button class="btn_add_rec" data-meet="<?= $meet ?>">Добавить событие</button>
</div>

<div class="statistics_table">
    <hr class="hr_db">
    <b>Время, событие, игроки, комметарий</b>
    <?php
    foreach (get_statistics($meet) as $rec) {
        $code = $rec['code'];
        // нет события - нет игроков
        if (isset($rec['event'])) {
            $css = $rec['event'];
        } else {
            $css = 0;
        }
        ?>
        <div class="row <?= 'event' . $css ?>" data-table="statistics" data-code="<?= $code ?>">

            <button class="btn_delete" data-meet="<?= $meet ?>"><img src="http://fcakron.ru/wp-content/themes/fcakron/images/db/delete.png"></button>

            <input class="digit_only" type="text" name="minute" value="<?= $rec['minute'] ?>">
            <select name="event">
                <option value="">Выбрать событие</option>
                <? foreach ($code_event as $opt) { ?>
                    <option value="<?= $opt["code"] ?>" <? echo ($opt["code"] == $rec["event"]) ? "selected" : ""; ?>><?= $opt["name"] ?></option>
                <? } ?>
            </select>
            <select name="player">
                <option value="">Выбрать игрока</option>
                <? foreach ($code_player as $opt) { ?>
                    <option value="<?= $opt["code"] ?>" <? echo ($opt["code"] == $rec["player"]) ? "selected" : ""; ?>><?= $opt["lastname"] ?> (<?= $opt["team_name"] ?>)</option>
                <? } ?>
            </select>
            <select name="player_2">
                <option value="">Выбрать второго игрока</option>
                <? foreach ($code_player as $opt) { ?>
                    <option value="<?= $opt["code"] ?>" <? echo ($opt["code"] == $rec["player_2"]) ? "selected" : ""; ?>><?= $opt["lastname"] ?> (<?= $opt["team_name"] ?>)</option>
                <? } ?>
            </select>
            <input rows="2" cols="80" type="textarea" name="comment" value="<?= $rec["comment"] ?>">
        </div>
    <?php
    } ?>
</div>
<hr class="hr_db">
<div>
    <button class="btn_add_rec" data-meet="<?= $meet ?>">Добавить событие</button>
</div>


<script>
    jQuery(function($) {

        //  ★★★★ УДАЛЕНИЕ СТРОКИ ★★★★
        $(document).on('click', '.btn_delete', function() {
            let row = $(this).parent();
            let code = row.data('code'); // код записи
            let data_lib = {
                action: 'row_delete',
                nonce_code: my_ajax_noncerr,
                table: "statistics",
                code: code,
            };
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: data_lib
            }).done(function(data) {
                row.remove();
            });
        });


        //  ★★★★ СМЕНА ПОЛЕЙ ПО СОБЫТИЮ ★★★★
        $(document).on('change', '[name=event]', function(e) {
            console.log('event');
            // изменяем класс
            let s = 'row event' + $(this).val();
            $(this).parent().attr('class', s);
        });

        //  ★★★★ вводить ТОЛЬКО ЦИФРЫ ★★★★
        $(document).ready(function() {
            $('.digit_only').on("change keyup input click", function() {
                if (this.value.match(/[^0-9]/g)) { // g ищет все совпадения, без него – только первое.
                    this.value = this.value.replace(/[^0-9]/g, '');
                }
            });
        });

        //  ★★★★ кнопка ДОБАВИТЬ запись события ★★★★
        $(document).on('click', '.btn_add_rec', function() {

            let meet = $(this).data("meet"); // код матча прописан в кнопке
            let data_lib = {
                action: 'statistics_add',
                nonce_code: my_ajax_noncerr,
                meet: meet
            };
            let data;
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: data_lib
            }).done(function(new_code) {
                // new_code - код новой строки из базы
                // клонируем последний элемент таблицы
                // меняем код на новый, обнуляем переменные
                let row = $(".statistics_table .row:last-child").clone();
                console.log(row);
                row.data("code", new_code);
                row.find("input,select").val("");
                row.attr('class', 'row event0');
                $(".statistics_table").append(row);
            });

        });

        //  ★★★★ РЕДАКТИРОВАНИЕ ФОРМЫ ★★★★

        $(document).on('change', 'input, select', function(e) {

            let parent = $(this).parent(); // родитель
            let code = parent.data("code"); // код записи
            let name = $(this).attr("name");
            let value = $(this).val();
            console.log(name, code, value);
            //return;
            let data_lib = {
                action: 'data_change',
                nonce_code: my_ajax_noncerr,
                table: "statistics",
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

        //  ★★★★★★★★★★★★★★★★★★★★★★★★ ЗАГРУЗКА ФАЙЛА ★★★★

        $(document).on('change', 'input[type="file"]', function() {

            let file_data = $(this).prop('files')[0];
            let parent = $(this).closest(".trainer");
            let code = parent.data("code"); // код записи
            let img = parent.find('img'); // картинка

            form_data = new FormData();
            form_data.append('key', 'tourney'); // ключ из ассоциативного массива на сервере
            form_data.append('code', code);
            form_data.append('file', file_data);
            form_data.append('action', 'load_file'); // функция обработки 
            form_data.append('nonce_code', my_ajax_noncerr); // ключ

            $.ajax({
                method: "POST",
                cache: false,
                contentType: false,
                processData: false,
                url: ajaxurl,
                data: form_data,
            }).done(function(msg) {
                if (msg != "") {
                    alert(msg);
                } else {
                    // обновление img
                    let src = img.attr('src') + '?t=' + Date.now();
                    img.attr('src', src);
                }
            });
        });
    });
</script>