<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ МАТЧЕЙ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_team = get_team_select();  // код --> команда (для select)
$code_tourney = get_tourney_code();  // код --> турнир (для select)
$code_ticket = get_ticket();

//  ★★★★ ДОБАВЛЕНИЕ ЗАПИСИ ★★★★
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=meet&add

if (isset($_GET['add'])) { ?>
    <h2>Новый матч</h2>
    <div class="meet_add">
        <div class="add_rec">
            <div class="err">Поля в красной рамке обязательныe.</div>
            <table>
                <tr>
                    <td>Название:<input type="text" name="name" value="" maxlength="32" required></td>
                </tr>
                <tr>
                    <td>Турнир:
                        <select name="tourney" required>
                            <option value="">Выбрать турнир</option>
                            <? foreach ($code_tourney as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Команда 1:
                        <select name="team_1" required>
                            <option value="">Выбрать команду</option>
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Команда 2:
                        <select name="team_2" required>
                            <option value="">Выбрать команду</option>
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Город: <input type="text" name="city" value="" maxlength="32"></td>
                </tr>
                <tr>
                    <td>Стадион:<input type="text" name="stadium" value="" maxlength="32"></td>
                </tr>

                <tr>
                    <td> Дата встречи: <input type="date" name="date_meet" value=""></td>
                </tr>
                <tr>
                    <td>Время: <input type="time" name="time_meet" value=""></td>
                </tr>
                <tr>
                    <td>Временная зона: <input type="number" name="time_zone" value="4"></td>
                </tr>
                <tr>
                    <td>Вход свободный: <input type="checkbox" name="free" value="0" checked></td>
                </tr>
            </table>
        </div>
    </div>
    <hr class="hr_db">

    <button class="load_rec">Загрузить в базу</button>

    <script>
        jQuery(function($) {

            //  ★★★★ КНОПКА ЗАГРУЗИТЬ В БАЗУ ★★★★
            $(document).on('click', '.load_rec', function() {

                if ($('input[name="name"]').val() == "" ||
                    $('select[name="tourney"]').val() == "" ||
                    $('select[name="team_1"]').val() == "" ||
                    $('select[name="team_2"]').val() == "") {
                    alert("Не заполнены обязательные поля.");
                    return false;
                }
                form_data = new FormData(); // создание формы
                form_data.append('name', $('input[name="name"]').val());
                form_data.append('tourney', $('select[name="tourney"]').val());
                form_data.append('team_1', $('select[name="team_1"]').val());
                form_data.append('team_2', $('select[name="team_2"]').val());
                form_data.append('city', $('input[name="city"]').val());
                form_data.append('stadium', $('input[name="stadium"]').val());
                form_data.append('date_meet', $('input[name="date_meet"]').val());
                form_data.append('time_meet', $('input[name="time_meet"]').val());
                if ($('input[name="free"]').is(':checked')) {
                    form_data.append('free', "1");
                } else {
                    form_data.append('free', "0");
                }
                form_data.append('action', 'load_meet'); // функция обработки 
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
                    if (msg != '') {
                        alert(msg);
                        if (msg[0] != ' ') {
                            return;
                        }
                    }
                    document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=meet";
                });
            });
        });
    </script>
<?php
    wp_die();
}



//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ★★★★

?>
<script src="https://fcakron.ru/wp-content/themes/fcakron/db_edit/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://fcakron.ru/wp-content/themes/fcakron/db_edit/css/jquery-ui.css">

<h1>Таблица матчей</h1>
<h3>(информация используется на сайте)</h3>
<div>
    <button class="btn_add_rec">Добавить матч</button>
</div>

<div class="meet_table matches_table">
    <?php
    foreach (get_meet_all() as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="meet" data-code="<?= $rec['code'] ?>">

            <table>
                <tr>
                    <!-- 1 -->
                    <td>Турнир</td>
                    <td>
                        <select name="tourney">
                            <? foreach ($code_tourney as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['tourney']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td>Команда 1</td>
                    <td>
                        <select name="team_1">
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team_1']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td>Голы</td>
                    <td><input type="number" name="goal_1" value="<?= $rec['goal_1'] ?>"></td>
                    <td><input type="checkbox" name="completed" value="<?= $rec['completed'] ?>" <? echo ($rec['completed'] == 1) ? 'checked' : ''; ?>>Матч окончен</td>
                    <td><input type="checkbox" name="display_statistics" value="<?= $rec['display_statistics'] ?>" <? echo ($rec['display_statistics'] == 1) ? 'checked' : ''; ?>>Показать статистику</td>
                    <td> <button class="btn_schema">Схема игроков</button> </td>
                </tr>
                <tr>
                    <!-- 2 -->
                    <td>Матч</td>
                    <td><input type="text" name="name" value="<?= $rec['name'] ?>"></td>
                    <td>Команда 2</td>
                    <td>
                        <select name="team_2">
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team_2']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td>Голы</td>
                    <td><input type="number" name="goal_2" value="<?= $rec['goal_2'] ?>"></td>
                    <td><input type="checkbox" name="exclude" value="<?= $rec['exclude'] ?>" <? echo ($rec['exclude'] == 1) ? 'checked' : ''; ?>>Скрыть матч</td>
                    <td></td>
                    <td><button class="btn_statistics">События</button></td>
                </tr>
            </table>
            <div class="div_accordion">
                <div class="accordion" style="grid-column-start: 1;">
                    <b>Время и место</b>
                    <table>
                        <tr>
                            <td> Дата: </td>
                            <td> <input type="date" name="date_meet" value="<?= $rec['date_meet'] ?>"> </td>
                        </tr>
                        <tr>
                            <td>Город:</td>
                            <td><input type="text" name="city" value="<?= $rec['city'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Стадион:</td>
                            <td> <input type="text" name="stadium" value="<?= $rec['stadium'] ?>"></td>
                        </tr>
                        <tr>
                            <td> Время:</td>
                            <td> <input type="time" name="time_meet" value="<?= $rec['time_meet'] ?>">
                                Зона: <input type="number" name="time_zone" value="<?= $rec['time_zone'] ?>">
                            </td>
                        <tr>
                        <td>Билет:</td>
                            <td>
                                <select name="ticket">
                                    <option value="" <? echo ('' == $rec['ticket']) ? 'selected' : ''; ?>>выбрать</option>
                                    <? foreach ($code_ticket as $opt) { ?>
                                        <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['ticket']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                                    <? } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="accordion" style="grid-column-start: 2;">
                    <b>Cтатистика</b>
                    <table>
                        <tr>
                            <td></td>
                            <td>1 ком.</td>
                            <td>2 ком.</td>
                            <td></td>
                            <td>1 ком.</td>
                            <td>2 ком.</td>
                            <td></td>
                            <td>1 ком.</td>
                            <td>2 ком.</td>
                            <td></td>
                            <td>1 ком.</td>
                            <td>2 ком.</td>
                        </tr>
                        <tr>
                            <td>% владения мячом:</td>
                            <td><input class="digit_only" type="text" name="ball_poss_1" value="<?= $rec['ball_poss_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="ball_poss_2" value="<?= $rec['ball_poss_2'] ?>"></td>
                            <td>Офсайды:</td>
                            <td><input class="digit_only" type="text" name="offside_1" value="<?= $rec['offside_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="offside_2" value="<?= $rec['offside_2'] ?>"></td>
                            <td>Угловые:</td>
                            <td><input class="digit_only" type="text" name="corner_1" value="<?= $rec['corner_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="corner_2" value="<?= $rec['corner_2'] ?>"></td>
                            <td>Голевые моменты:</td>
                            <td><input class="digit_only" type="text" name="goal_moment_1" value="<?= $rec['goal_moment_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="goal_moment_2" value="<?= $rec['goal_moment_2'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Удары по воротам:</td>
                            <td><input class="digit_only" type="text" name="kick_goal_1" value="<?= $rec['kick_goal_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="kick_goal_2" value="<?= $rec['kick_goal_2'] ?>"></td>
                            <td>Удары в створ:</td>
                            <td><input class="digit_only" type="text" name="kick_target_1" value="<?= $rec['kick_target_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="kick_target_2" value="<?= $rec['kick_target_2'] ?>"></td>
                            <td>Штанги:</td>
                            <td><input class="digit_only" type="text" name="goalpost_1" value="<?= $rec['goalpost_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="goalpost_2" value="<?= $rec['goalpost_2'] ?>"></td>
                            <td>Фолы:</td>
                            <td><input class="digit_only" type="text" name="foul_1" value="<?= $rec['foul_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="foul_2" value="<?= $rec['foul_2'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Предупреждения:</td>
                            <td><input class="digit_only" type="text" name="warning_1" value="<?= $rec['warning_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="warning_2" value="<?= $rec['warning_2'] ?>"></td>
                            <td>Удаления:</td>
                            <td><input class="digit_only" type="text" name="sending_off_1" value="<?= $rec['sending_off_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="sending_off_2" value="<?= $rec['sending_off_2'] ?>"></td>
                            <td>Успешные передачи:</td>
                            <td><input class="digit_only" type="text" name="success_pass_1" value="<?= $rec['success_pass_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="success_pass_2" value="<?= $rec['success_pass_2'] ?>"></td>
                            <td>Выигранные единоборства:</td>
                            <td><input class="digit_only" type="text" name="combat_1" value="<?= $rec['combat_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="combat_2" value="<?= $rec['combat_2'] ?>"></td>
                        </tr>
                        <tr>
                            <td>Быстрые атаки (% успешных):</td>
                            <td><input class="digit_only" type="text" name="fast_attacks_1" value="<?= $rec['fast_attacks_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="fast_attacks_2" value="<?= $rec['fast_attacks_2'] ?>"></td>
                            <td>Обводки (% успешных) :</td>
                            <td><input class="digit_only" type="text" name="stroke_1" value="<?= $rec['stroke_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="stroke_2" value="<?= $rec['stroke_2'] ?>"></td>
                            <td>Отборы (% успешных) :</td>
                            <td><input class="digit_only" type="text" name="take_away_1" value="<?= $rec['take_away_1'] ?>"></td>
                            <td><input class="digit_only" type="text" name="take_away_2" value="<?= $rec['take_away_2'] ?>"></td>
                        </tr>
                    </table>
                </div>
                <div class="accordion" style="grid-column-start: 3;">
                    <b>Афиша</b>
                    <table>
                        <tr>
                            <td>
                                <label class="button" for="poster<?= $code ?>">Загрузить афишу</label>
                                <input class="poster" id="poster<?= $code ?>" type="file">
                            </td>
                            <?php
                                $src = dirname(__FILE__) . '/../images/db/meet/' . $rec['code'] . 's.jpg';
                                if (file_exists($src)) { ?>
                                <td rowspan="2"><img src="https://fcakron.ru/wp-content/themes/fcakron/images/db/meet/<?= $rec['code'] ?>s.jpg" alt="">
                                <?php } else { ?>
                                <td rowspan="2"><img src="https://fcakron.ru/wp-content/themes/fcakron/images/db/meet/nofoto.png"></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td>
                                Использовать афишу: <input type="checkbox" name="poster" value="<?= $rec['poster'] ?>" <? echo ($rec['poster'] == 1) ? 'checked' : ''; ?>>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?php
    } ?>
    <hr class="hr_db">
</div>
<div>
    <button class="btn_add_rec">Добавить матч</button>
</div>

<script>
    jQuery(function($) {

        //  ★★★★ АККОРДЕОН ★★★★
        $(function() {
            $(".accordion").accordion({
                collapsible: true,
                active: false,
                animate: false
            });
        });

        //  ★★★★ кнопка ДОБАВИТЬ МАТЧ ★★★★
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=meet&add";
        });

        //  ★★★★ кнопка СХЕМА ИГРОКОВ ★★★★
        $(document).on('click', '.btn_schema', function() {
            let code = $(this).closest(".meet").data("code");
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=scheme&meet=" + code;
        });

        //  ★★★★ кнопка СТАТИСТИКА ★★★★
        $(document).on('click', '.btn_statistics', function() {
            let code = $(this).closest(".meet").data("code");
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=statistics&meet=" + code;
        });

        //  ★★★★ РЕДАКТИРОВАНИЕ ФОРМЫ ★★★★

        $(document).on('change', 'input:not([type=file]), select', function(e) {

            let table = 'meet';
            let name = $(this).attr("name");
            let code = $(this).closest(".meet").data("code");
            let value;
            if ($(this).attr("type") == 'checkbox') {
                value = ($(this).prop("checked") ? 1 : 0);
            } else {
                value = $(this).val();
            }

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
                // console.log(data);
            });
        });

        //  ★★★★★★★★★★★★★★★★★★★★★★★★ ЗАГРУЗКА ФАЙЛА ★★★★

        $(document).on('change', 'input[type="file"]', function(e) {

            let file_data = $(this).prop('files')[0];
            let parent = $(this).closest(".meet");
            let code = parent.data("code");
            let img = parent.find('img');
            // let poster = parent.find('input[name="poster"]');

            form_data = new FormData();
            form_data.append('key', 'meet'); // ключ из ассоциативного массива на сервере
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
                if (msg[0] == '/') { // норм должно прилететь типа /images/db/meet/1.png
                    src = 'https://fcakron.ru/wp-content/themes/fcakron' + msg + '?t=' + Date.now();
                    img.attr('src', src);
                } else {
                    alert(msg);
                }


            });
        });
    });
</script>