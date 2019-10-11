<?php
//  ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ИГРОКОВ
//  ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/functions_db.php');  // функции для работы с базой данных

$gc = get_country();    // код --> страна
$gp = get_positions();  // код --> позиция игрока
$gt = get_team_code();  // код --> команда

//
//  ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■ ДОБАВИТЬ ЗАПИСЬ ■■■■
//
//  GET запрос наличие переменной add без значения
//

if (isset($_GET['add'])) { ?>
    <h2>Добавить нового игрока</h2>
    <div class="attention">Поля в красной рамке обязательные для заполнения.</div>

    <div class="player_form_add">

        <div>Команда</div>
        <select class="team" name="team">
            <option value="">выбрать название команды</option>
            <? foreach ($gt as $o) { ?>
                <option value="<?= $o['code'] ?>"><?= $o['name'] ?></option>
            <? } ?>
        </select>

        <div>Позиция игрока</div>
        <select class="positions" name="positions">
            <option value="">выбрать позицию</option>
            <? foreach ($gp as $o) { ?>
                <option value="<?= $o['code'] ?>"><?= $o['name'] ?></option>
            <? } ?>
        </select>

        <div>Страна</div>
        <select class="country" name="country">
            <option value="">выбрать страну</option>
            <? foreach ($gc as $o) { ?>
                <option value="<?= $o['code'] ?>"><?= $o['name'] ?></option>
            <? } ?>
        </select>

        <div>Номер игрока</div><input class="number" type="text" name="number" value="" placeholder="">
        <div>Капитан</div><input class="capitan" type="checkbox" name="capitan" value="0">
        <div>Фамилия</div><input class="lastname" type="text" name="lastname" value="" placeholder="">
        <div>Имя</div><input class="name" type="text" name="name" value="" placeholder="">

        <div>Дата рождения</div>
        <input class="birthday" type="date" name="birthday" value="" placeholder="">

        <div>Рост</div>
        <input class="growing" type="text" name="growing" value="" placeholder="">

        <div>Вес</div>
        <input class="weight" type="text" name="weight" value="" placeholder="">

        <div>VC</div>
        <input class="vc" type="text" name="vc" value="" placeholder="">

        <div>INSTAGRAM</div>
        <input class="instagram" type="text" name="instagram" value="" placeholder="">

        <div>Фотография 1</div>
        <input class="load_photo" type="file" name="photo">

        <div>Фотография 2</div>
        <input class="load_photo2" type="file" name="photo2">
    </div>
    <div class="err"></div>
    <button class="load_player">Загрузить в базу</button>
    <script>
        jQuery(function($) {
            $(document).on('click', '.load_player', function() {
                $('.err').text(''); // обнуление строки с сообщением об ошибке
                if ($('.team').val() == '' || $('.lastname').val() == '') {
                    alert("Не заполнены обязательные поля !");
                } else {
                    // загрузка формы в базу
                    form_data = new FormData(); // создание формы

                    // файлы отдельно
                    if ($('.load_photo').val() != '') {
                        file_data = $('.load_photo').prop('files')[0], // ссылка на файл
                            form_data.append('file', file_data);
                        console.log('есть файл1');
                    }
                    if ($('.load_photo2').val() != '') {
                        file_data2 = $('.load_photo2').prop('files')[0], // ссылка на файл
                            form_data.append('file2', file_data2);
                        console.log('есть файл2');
                    }
                    if ($('.capitan').is(':checked')) {
                        form_data.append('capitan', "1");
                    } else {
                        form_data.append('capitan', "0");
                    }

                    // грузим всё остальное
                    form_data.append('team', $('.team').val());
                    form_data.append('positions', $('.positions').val());
                    form_data.append('country', $('.country').val());
                    form_data.append('number', $('.number').val());
                    form_data.append('lastname', $('.lastname').val());
                    form_data.append('name', $('.name').val());
                    form_data.append('birthday', $('.birthday').val());
                    form_data.append('weight', $('.weight').val());
                    form_data.append('vc', $('.vc').val());
                    form_data.append('instagram', $('.instagram').val());

                    form_data.append('action', 'load_player'); // функция обработки 
                    form_data.append('nonce_code', my_ajax_noncerr); // ключ

                    $.ajax({
                        method: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        url: ajaxurl,
                        data: form_data,
                    }).done(function(msg) {
                        console.log(msg);
                        if (msg == '') {
                            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=player";
                        } else {
                            $('.err').text(msg);
                        }

                    });
                }

            });
        });
    </script>

<?
    wp_die();
}

//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ РЕДАКТИРОВАНИЕ ТАбЛИЦЫ ▰▰▰▰

/*
 * Фильтрация игроков по командам.
 * Если в GET задана команда, то вытаскивать игроком только этой команды
 */

if (isset($_GET['team'])) {          // это для формирования селектора
    $team = $_GET['team'];
} else {
    $team = "";
}

if ($team != "") {                  // это для формирования запроса
    $players = get_players($_GET['team']);
} else {
    $players = get_players();
}

?>

<div>
    <button class="player_add">Добавить игрока</button>
</div>
<hr class="hr_db">

<div>Фильтр по командам
    <select class="team_filter" name="team">
        <option value="">все команды</option>
        <? foreach ($gt as $o) { ?>
            <option value="<?= $o['code'] ?>" <?= ($o['code'] == $team) ? 'selected' : ''; ?>><?= $o['name'] ?></option>
        <? } ?>
    </select>
</div>

<div class="player_table">
    <?
    foreach ($players as $player) { ?>

        <hr class="hr_db">
        <div class="player" data-code="<?= $player['code'] ?>">
            <input class="lastname" type="text" name="lastname" value="<?= $player['lastname'] ?>">
            <input class="name" type="text" name="name" value="<?= $player['name'] ?>">
            <input class="birthday" type="date" name="birthday" value="<?= $player['birthday'] ?>">
            <div class="number">номер
                <input type="number" name="number" value="<?= $player['number'] ?>">
            </div>
            <input class="growing" type="text" name="growing" value="<?= $player['growing'] ?>">
            <input class="weight" type="text" name="weight" value="<?= $player['weight'] ?>">
            <input class="vc" type="text" name="vc" value="<?= $player['vc'] ?>">
            <input class="instagram" type="text" name="instagram" value="<?= $player['instagram'] ?>">
            <input class="matches_plus" type="number" name="matches_plus" value="<?= $player['matches_plus'] ?>">
            <input class="output_start_plus" type="number" name="output_start_plus" value="<?= $player['output_start_plus'] ?>">
            <input class="output_in_game_plus" type="number" name="output_in_game_plus" value="<?= $player['output_in_game_plus'] ?>">
            <input class="exchange_plus" type="number" name="exchange_plus" value="<?= $player['exchange_plus'] ?>">
            <input class="goal_plus" type="number" name="goal_plus" value="<?= $player['goal_plus'] ?>">
            <input class="pass_plus" type="number" name="pass_plus" value="<?= $player['pass_plus'] ?>">
            <input class="cart_y_plus" type="number" name="cart_y_plus" value="<?= $player['cart_y_plus'] ?>">
            <input class="cart_r_plus" type="number" name="cart_r_plus" value="<?= $player['cart_r_plus'] ?>">
            <input class="save_plus" type="number" name="save_plus" value="<?= $player['save_plus'] ?>">
            <input class="omission_plus" type="number" name="omission_plus" value="<?= $player['omission_plus'] ?>">


            <div class="vc_l">vc соцсеть</div>
            <div class="instagram_l">инстаграмм</div>
            <div class="matches_plus_l">матчи<br>(корректировка)</div>
            <div class="output_start_plus_l">выходы на старте<br>(корректировка)</div>
            <div class="output_in_game_plus_l">выходы на замене<br>(корректировка)</div>
            <div class="exchange_plus_l">замен в ходе матча<br>(корректировка)</div>
            <div class="goal_plus_l">голы<br>(корректировка)</div>
            <div class="pass_plus_l">голевые передачи<br>(корректировка)</div>
            <div class="cart_y_plus_l">жёлтые карточки<br>(корректировка)</div>
            <div class="cart_r_plus_l">красные карточки<br>(корректировка)</div>
            <div class="save_plus_l">сейвы<br>(корректировка)</div>
            <div class="omission_plus_l">пропуски<br>(корректировка)</div>

            <div class="capitan"><input type="checkbox" name="capitan" value="<?= $player['capitan'] ?>" <? echo ($player['capitan'] == 1) ? 'checked' : ''; ?>>Капитан</div>

            <select class="team" name="team">
                <? foreach ($gt as $o) { ?>
                    <option value="<?= $o['code'] ?>" <? echo ($o['code'] == $player['team']) ? 'selected' : ''; ?>><?= $o['name'] ?></option>
                <? } ?>
            </select>

            <select class="positions" name="positions">
                <? foreach ($gp as $o) { ?>
                    <option value="<?= $o['code'] ?>" <? echo ($o['code'] == $player['positions']) ? 'selected' : ''; ?>><?= $o['name'] ?></option>
                <? } ?>
            </select>

            <select class="country" name="country">
                <? foreach ($gc as $o) { ?>
                    <option value="<?= $o['code'] ?>" <? echo ($o['code'] == $player['country']) ? 'selected' : ''; ?>><?= $o['name'] ?></option>
                <? } ?>
            </select>

            <img class="photo" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/player/<?= $player['code'] ?>-1.png" alt="<?= $player['lastname'] ?>">
            <img class="photo2" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/player/<?= $player['code'] ?>-2.png" alt="<?= $player['lastname'] ?>">

            <input class="load_photo" type="file" name="photo">
            <input class="load_photo2" type="file" name="photo2">
        </div>
    <?
    } ?>
    <hr class="hr_db">
</div>

<script>
    jQuery(function($) {

        $(document).on('click', '.player_add', function() { // кнопка "добавить игрока"
            console.log('добавит игрока');
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=player&add";
        });

        $(".team_filter").change(function() {
            team = $(this).val();
            href = "https://fcakron.ru/wp-admin/admin.php?page=player&team=" + team;
            console.log(href);
            document.location.href = href;
        });

        $(".player input[type=text], .player input[type=number],.player input[type=date],.player select").change(function() {

            let table = 'player',
                name = $(this).attr("name"),
                value = $(this).val(),
                code = $(this).closest(".player").data("code");

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

        // загрузка фото

        $(document).on('change', '.load_photo, .load_photo2', function() {

            // подготовка к обновлению фото
            class_input = $(this).attr('class');
            sel_img = '.' + class_input.replace('load_', '');
            img = $(this).siblings(sel_img);
            new_src = img.attr('src') + '?t=' + Date.now();

            file_data = $(this).prop('files')[0]; // объект файл

            if (file_data.type != 'image/png') {
                alert('Тип файла не png');
                return false;
            }

            if (file_data.size > 200000) {
                alert('Фотография не более 200 Кбайт.');
                return false;
            }

            code = $(this).closest(".player").data("code");

            if ($(this).hasClass('load_photo')) {
                path_file = '/images/db/player/' + code + '-1.png';
            } else {
                path_file = '/images/db/player/' + code + '-2.png';
            }
            // console.log(path_file);

            form_data = new FormData(); // создание формы

            form_data.append('path_file', path_file); //
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
                    img.attr('src', new_src); // обновляем фото
                }
            });
        });
    });
</script>