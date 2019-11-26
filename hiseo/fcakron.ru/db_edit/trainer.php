<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ТУРНИРОВ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_cry = get_country();    // страна select
$code_team = get_team_select();  // команда select
$group = get_group();

//  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=trainer&add

if (isset($_GET['add'])) { ?>
    <h1>Добавить сотрудника</h1>
    <div class="trainer_add">
        <div class="add_rec">
            <div class="err">Поля в красной рамке обязательныe.</div>
            <table>
                <tr>
                    <td>Команда:
                        <select name="team" required>
                            <option value="">выбрать название команды</option>
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?> (<?= $opt['city'] ?>)</option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Группа:
                        <select name="group" required>
                            <option value="">выбрать группу</option>
                            <? foreach ($group as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>Фамилия: <input type="text" name="lastname" value="" required></td>
                </tr>
                <tr>
                    <td>Имя
                        <input type="text" name="name" value="" required></td>
                </tr>
                <tr>
                    <td>Должность
                        <input type="text" name="position" value=""></td>
                </tr>
                <tr>
                    <td>
                        <div>Страна тренера:</div>
                        <select name="country">
                            <option value="">выбрать страну</option>
                            <? foreach ($code_cry as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Фотография (PNG - 450х300): </td>
                </tr>
                <tr>
                    <td><input type="file"></td>
                </tr>
            </table>
        </div>
    </div>
    <hr class="hr_db">

    <button class="load_rec">Загрузить в базу</button>

    <script>
        jQuery(function($) {

            //  ▰▰▰▰ КНОПКА ЗАГРУЗИТЬ В БАЗУ ▰▰▰▰
            $(document).on('click', '.load_rec', function() {

                if ($('input[name="lastname"]').val() == '' ||
                    $('input[name="name"]').val() == '') {
                    alert("Не заполнены обязательные поля.");
                    return false;
                }
                form_data = new FormData(); // создание формы
                form_data.append('team', $('select[name="team"]').val());
                form_data.append('group', $('select[name="group"]').val());
                form_data.append('lastname', $('input[name="lastname"]').val());
                form_data.append('name', $('input[name="name"]').val());
                form_data.append('position', $('input[name="position"]').val());
                form_data.append('country', $('select[name="country"]').val());
                let file = $('input[type="file"]');
                if (file.val() != '') {
                    file_data = file.prop('files')[0];
                    form_data.append('file', file_data);
                }
                form_data.append('action', 'load_trainer'); // функция обработки 
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
                    document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=trainer";
                });
            });
        });
    </script>
<?php
    wp_die();
}


//
// ▰▰▰▰ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ▰▰▰▰
//


?>

<script src="https://fcakron.ru/wp-content/themes/fcakron/db_edit/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://fcakron.ru/wp-content/themes/fcakron/db_edit/css/jquery-ui.css">

<h1>Администрация, тренеры и т.д.</h1>
<h3>(для тренеров ставить галочку Тренерский состав)</h3>
<div>
    <button class="btn_add_rec">Добавить сотрудника</button>
</div>

<div class="trainer_table">
    <?php
    foreach (get_trainer() as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="root" data-table="trainer" data-code="<?= $code ?>">

            <table>
                <tr>
                    <!-- 1 -->
                    <td>Команда: </td>
                    <td>
                        <select name="team">
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td>Фамилия: </td>
                    <td><input type="text" name="lastname" value="<?= $rec['lastname'] ?>"></td>
                    <td>Инстаграмм:</td>
                    <td><input type="text" name="instagram" value="<?= $rec['instagram'] ?>"></td>
                    <td rowspan="3"><img src="https://fcakron.ru/wp-content/themes/fcakron/images/db/trainer/<?= $rec['code'] ?>.png" alt="<?= $rec['name'] ?>"></td>
                </tr>

                <tr>
                    <!-- 2 -->
                    <td>Группа: </td>
                    <td>
                        <select name="group">
                            <option value="">выбрать группу</option>
                            <? foreach (get_group() as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['group']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td>Имя: </td>
                    <td><input type="text" name="name" value="<?= $rec['name'] ?>"></td>
                    <td>Фейсбук:</td>
                    <td><input type="text" name="facebook" value="<?= $rec['facebook'] ?>"></td>
                </tr>

                <tr>
                    <!-- 3 -->
                    <td>Должность: </td>
                    <td><input type="text" name="position" value="<?= $rec['position'] ?>"></td>
                    <td>Дата рождения:</td>
                    <td><input type="date" name="birthday" value="<?= $rec['birthday'] ?>"></td>
                    <td>В контакте:</td>
                    <td><input type="text" name="vc" value="<?= $rec['vc'] ?>"></td>
                </tr>

                <tr>
                    <!-- 4 -->
                    <td>Контакты:</td>
                    <td><input type="text" name="contacts" value="<?= $rec['contacts'] ?>"></td>
                    <td>Гражданство: </td>
                    <td>
                        <select class="country" name="country">
                            <? foreach ($code_cry as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['country']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td style="text-align:center">
                        <label class="button" for="logo<?= $code ?>">Загрузить фото</label>
                        <input id="logo<?= $code ?>" type="file">
                    </td>
                </tr>
            </table>
            <div class="stat1">
                <h3>Информации о карьере, образовании, достижениях</h3>

                <table style="white-space: nowrap;">

                    <tr>
                        <td class="count">Карьера:</td>
                        <td class="count"><textarea rows="3" cols="45" name="career"><?= $rec['career'] ?></textarea></td>
                        <td class="count">Образование:</td>
                        <td class="count"><textarea rows="3" cols="45" name="education"><?= $rec['education'] ?></textarea></td>
                        <td class="count">Достижения:</td>
                        <td class="count"><textarea rows="3" cols="45" name="progress"><?= $rec['progress'] ?></textarea></td>
                    </tr>
                </table>
            </div>


        </div>
    <?php
    } ?>
    <hr class="hr_db">
    <div>
    <button class="btn_add_rec">Добавить сотрудника</button>
</div>

</div>

<script>
    jQuery(function($) {

        //  ★★★★ АКОРДЕОН ★★★★
        $(function() {
            $(".stat1").accordion({
                collapsible: true,
                active: false,
                animate: false
            });
        });

        //  ▰▰▰▰ КНОПКА ДОБАВИТЬ ТРЕНЕРА ▰▰▰▰
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=trainer&add";
        });

        //  ▰▰▰▰ РЕДАКТИРОВАНИЕ ФОРМЫ ▰▰▰▰

        $(document).on('change', 'input:not([type=file]), select, textarea', function(e) {

            let patern = $(this).closest(".root"); // корневой предок
            let table = patern.data("table"); // у него прописан код
            let code = patern.data("code"); // и название таблицы для правки
            let name = $(this).attr("name");
            let value = $(this).val();
            console.log(table, code, name, value);
            //return;
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

        //  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ ЗАГРУЗКА ФАЙЛА ▰▰▰▰

        $(document).on('change', 'input[type="file"]', function() {

            let file_data = $(this).prop('files')[0];
            let parent = $(this).closest(".root");
            let code = parent.data("code"); // код записи
            let img = parent.find('img'); // картинка
            console.log(code);
            form_data = new FormData();
            form_data.append('key', 'trainer'); // ключ из ассоциативного массива на сервере
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