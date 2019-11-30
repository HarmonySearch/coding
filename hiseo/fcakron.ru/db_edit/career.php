<?php
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
// РЕДАКТИРОВАНИЕ КАРЬЕРЫ ИГРОКА
// https://fcakron.ru/wp-admin/admin.php?page=career&player=1
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$player = $_GET['player']; // код игрока

// select игрока. выборка игроков только из списка команд участвующих в игре

// $sql = "SELECT `code`,`lastname`, (SELECT name FROM team WHERE code = `team`) AS `team_name`
//         FROM `player` 
//         WHERE `team` IN (
//             SELECT `team_1` FROM `meet` WHERE `code` = $meet 
//             union 
//             SELECT `team_2` FROM `meet` WHERE `code` = $meet)";
// global $wpdb;
// $code_player = $wpdb->get_results($sql, 'ARRAY_A');
// $code_event = get_event();  // select события

// // на каком коде события сколько игроков
// $player_event = [];
// foreach ($code_event as $rec) {
//     $player_event[$rec['code']] = $rec['player'];
// }

// нужна хоть одна запись, поскольку добавление записей
// делается клонированием последней записи
$res = get_player_career($player);
if (!$res) {
    global $wpdb;
    $wpdb->insert('career', array('player' => $player), array('%d'));
}

//
// ★★★★ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ★★★★
//
?>

<h1>Таблица карьера игрока</h1>
<h3>(информация пока не используется на сайте)</h3>
<div>
    <button class="btn_add_rec" data-player="<?= $player ?>">Добавить</button>
</div>

<div class="career_table">
    <hr class="hr_db">
    <b>Годы, команда, матчи, голы</b>
    <?php
    foreach (get_player_career($player) as $rec) {
        $code = $rec['code'];
        ?>
        <div class="row <?= 'event' . $css ?>" data-table="career" data-code="<?= $code ?>">
            <button class="btn_delete" data-meet="<?= $meet ?>"><img src="http://fcakron.ru/wp-content/themes/fcakron/images/db/delete.png"></button>
            <input type="text" name="year" value="<?= $rec['year'] ?>">
            <input type="text" name="team" value="<?= $rec['team'] ?>">
            <input class="digit_only" type="text" name="match" value="<?= $rec['match'] ?>">
            <input class="digit_only" type="text" name="goal" value="<?= $rec['goal'] ?>">
        </div>
    <?php
    } ?>
</div>
<hr class="hr_db">
<div>
    <button class="btn_add_rec" data-player="<?= $player ?>">Добавить</button>
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
                table: "career",
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


        //  ★★★★ кнопка ДОБАВИТЬ ★★★★
        $(document).on('click', '.btn_add_rec', function() {

            let player = $(this).data("player"); // код матча прописан в кнопке
            let data_lib = {
                action: 'career_add',
                nonce_code: my_ajax_noncerr,
                player: player
            };
            let data;
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: data_lib
            }).done(function(new_code) {
                console.log(new_code);
                // new_code - код новой строки из базы
                // клонируем последний элемент таблицы
                // меняем код на новый, обнуляем переменные
                let row = $(".career_table .row:last-child").clone();
                console.log(row);
                row.data("code", new_code); // изменить код новой строки
                row.find("textarea").val(""); // очистить поля
                $(".career_table").append(row);
            });

        });

        //  ★★★★ РЕДАКТИРОВАНИЕ ФОРМЫ ★★★★

        $(document).on('change', 'textarea', function(e) {

            let parent = $(this).parent(); // родитель
            let code = parent.data("code"); // код записи
            let name = $(this).attr("name");
            let value = $(this).val();
            console.log(name, code, value);
            //return;
            let data_lib = {
                action: 'data_change',
                nonce_code: my_ajax_noncerr,
                table: "career",
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