<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ МАТЧЕЙ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/functions_db.php');  // функции для работы с базой данных

$code_team = get_team_select();  // код --> команда (для select)
$code_tourney = get_tourney_code();  // код --> турнир (для select)




//  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=meet&add

if (isset($_GET['add'])) { ?>

    <h2>Новый матч</h2>
    <div class="meet_add">

        <div class="add_rec">
            <table>
                <tr>
                    <td>Название:<input class="name" type="text" name="name" value="" maxlength="32" required></td>
                </tr>
                <tr>
                    <td>Турнир:
                        <select class="tourney" name="tourney" required>
                            <option value="">Выбрать турнир</option>
                            <? foreach ($code_tourney as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Команда 1:
                        <select class="team_1" name="team_1" required>
                            <option value="">Выбрать команду</option>
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Команда 2:
                        <select class="team_2" name="team_2" required>
                            <option value="">Выбрать команду</option>
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Город: <input class="city" type="text" name="city" value="" maxlength="32"></td>
                </tr>
                <tr>
                    <td>Стадион:<input class="stadium" type="text" name="stadium" value="" maxlength="32"></td>
                </tr>

                <tr>
                    <td> Дата встречи: <input class="date_meet" type="date" name="date_meet" value=""></td>
                </tr>
                <tr>
                    <td>Время: <input class="time_meet" type="time" name="time_meet" value=""></td>
                </tr>
                <tr>
                    <td>Окончен: <input type="checkbox" name="completed" value="0"></td>
                </tr>
            </table>
        </div>
    </div>
    <hr class="hr_db">

    <button class="load_rec">Загрузить в базу</button>
    <div class="err">Поля в красной рамке обязательны для заполнения.</div>

    <script>
        jQuery(function($) {

            $(document).on('click', '.load_rec', function() {

                form_data = new FormData(); // создание формы

                if ($('.name').val() == "" ||
                    $('.tourney').val() == "" ||
                    $('.team_1').val() == "" ||
                    $('.team_2').val() == "") {
                    $(".err").text("Не заполнены обязательные поля !");
                    return false;
                }
                form_data.append('name', $('.name').val());
                form_data.append('tourney', $('.tourney').val());
                form_data.append('team_1', $('.team_1').val());
                form_data.append('team_2', $('.team_2').val());
                form_data.append('city', $('.city').val());
                form_data.append('stadium', $('.stadium').val());
                form_data.append('date_meet', $('.date_meet').val());
                form_data.append('time_meet', $('.time_meet').val());
                form_data.append('completed', $('input[name="completed"]').val());

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
                    if (msg == '') {
                        document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=meet";
                    } else {
                        $('.err').text(msg);
                    }
                });
            });
        });
    </script>
<?php
    wp_die();
}



//
//  ▰▰▰▰ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ▰▰▰▰
//
?>

<div>
    <button class="btn_add_rec">Добавить матч</button>
</div>

<div class="meet_table">
    <?php
    foreach (get_meet() as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="meet" data-code="<?= $rec['code'] ?>">


            <table>
                <tr>
                    <th>Матч</th>
                    <th>Место проведения</th>
                    <th>Команда 1</th>
                    <th>Команда 2</th>
                </tr>
                <tr>
                    <td><input class="name" type="text" name="name" value="<?= $rec['name'] ?>"></td>
                    <td>Город: <input class="city" type="text" name="city" value="<?= $rec['city'] ?>"></td>
                    <td>
                        <select class="team" name="team_1">
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team_1']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>

                    </td>
                    <td>
                        <select class="team" name="team_2">
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team_2']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td>
                        <select class="tourney" name="tourney">
                            <? foreach ($code_tourney as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['tourney']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>

                    </td>
                    <td>Стадион: <input class="stadium" type="text" name="stadium" value="<?= $rec['stadium'] ?>"></td>
                    <td>Голы:
                        <input type="number" name="goal_1" value="<?= $rec['goal_1'] ?>"></td>
                    <td>Голы:
                        <input type="number" name="goal_2" value="<?= $rec['goal_2'] ?>"></td>
                </tr>
                <tr>
                    <td>Дата: <input class="date_meet" type="date" name="date_meet" value="<?= $rec['date_meet'] ?>"></td>
                    <td>Время: <input class="time_meet" type="time" name="time_meet" value="<?= $rec['time_meet'] ?>"></td>
                    <td>Закончен: <input type="checkbox" name="completed" value="<?= $rec['completed'] ?>" <? echo ($rec['completed'] == 1) ? 'checked' : ''; ?>></td>
                    <td></td>
                </tr>
            </table>
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

        //  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=meet&add";
        });

        //  ▰▰▰▰ РЕДАКТИРОВАТЬ ЗАПИСЬ ▰▰▰▰
        $("input, select").change(function() { // значение поля изменилось

            let table = 'meet';
            let name = $(this).attr("name");
            let code = $(this).closest(".meet").data("code");
            let value;
            if ( $(this).attr("type") == 'checkbox' ) {
                value = ( $(this).prop("checked")? 1 : 0 );
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
                console.log(data);
            });
        });
    });
</script>