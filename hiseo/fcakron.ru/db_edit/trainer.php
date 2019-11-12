<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ТУРНИРОВ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_cry = get_country();    // страна select
$code_team = get_team_select();  // команда select

//  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=trainer&add

if (isset($_GET['add'])) { ?>
    <h1>Добавить тренера</h1>
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
<h1>Таблица тренерского состава</h1>
<h3>добавилось поле "Команда". Теперь тренерский состав можно вводить для каждой команды</h3>
<div>
    <button class="btn_add_rec">Добавить тренера</button>
</div>

<div class="trainer_table">
    <?php
    foreach (get_trainer() as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="root_table" data-table="trainer" data-code="<?= $code ?>">

            <table>
            <tr>
            <td>Команда: </td>
                    <td>
                        <select name="team">
                            <? foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    </tr>
                <tr>
                    <td>Фамилия: </td>
                    <td><input type="text" name="lastname" value="<?= $rec['lastname'] ?>"></td>
                    <td rowspan="3"><img src="https://fcakron.ru/wp-content/themes/fcakron/images/db/trainer/<?= $rec['code'] ?>.png" alt="<?= $rec['name'] ?>"></td>
                </tr>

                <tr>
                    <td>Имя: </td>
                    <td><input type="text" name="name" value="<?= $rec['name'] ?>"></td>
                </tr>

                <tr>
                    <td>Должность: </td>
                    <td><input type="text" name="position" value="<?= $rec['position'] ?>"></td>
                </tr>
                <tr>
                    <td>Страна: </td>
                    <td>
                        <select class="country" name="country">
                            <? foreach ($code_cry as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['country']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td style="text-align:center">
                        <label class="button" for="logo<?= $code ?>">Загрузить фото</label>
                        <input id="logo<?= $code ?>" type="file">
                    </td>
                </tr>
            </table>


        </div>
    <?php
    } ?>
    <hr class="hr_db">
</div>

<script>
    jQuery(function($) {

        //  ▰▰▰▰ КНОПКА ДОБАВИТЬ ТРЕНЕРА ▰▰▰▰
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=trainer&add";
        });

        //  ▰▰▰▰ РЕДАКТИРОВАНИЕ ФОРМЫ ▰▰▰▰

        $(document).on('change', 'input:not([type=file]), select', function(e) {

            let patern = $(this).closest(".root_table"); // корневой предок
            let table = patern.data("table"); // у него прописан код
            let code = patern.data("code"); // и название таблицы для правки
            let name = $(this).attr("name");
            let value = $(this).val();
            console.log(table, code, value);
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