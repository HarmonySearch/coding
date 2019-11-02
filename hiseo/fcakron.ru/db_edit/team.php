<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ КОМАНД
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/functions_db.php');  // функции для работы с базой данных


//  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=team&add


if (isset($_GET['add'])) { ?>

    <h2>Добавить команду</h2>
    <div class="team_add">

        <div class="add_rec">
            <table>
                <tr>
                    <td>Название: <input type="text" name="name" value="" maxlength="32" required></td>
                </tr>
                <tr>
                    <td>Город: <input type="text" name="city" value="" maxlength="32" required></td>
                </tr>
                <tr>
                    <td>Логотип: ( PNG не более 100 Кбайт)<br>
                        <input type="file">
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <hr class="hr_db">

    <button class="load_rec">Загрузить в базу</button>
    <div class="err">Поля в красной рамке обязательны для заполнения.</div>

    <script>
        jQuery(function($) {

        //  ▰▰▰▰ КНОПКА ЗАГРУЗИТЬ В БАЗУ ▰▰▰▰
        $(document).on('click', '.load_rec', function() {

                if ($('input[name="name"]').val() == '' ||
                    $('input[name="city"]').val() == '') {
                    alert("Не заполнены обязательные поля !");
                    return false;
                }
                
                form_data = new FormData(); // создание формы
                form_data.append('name', $('input[name="name"]').val());
                form_data.append('city', $('input[name="city"]').val());
                let file = $('input[type="file"]');
                if (file.val() != '') {
                    file_data = file.prop('files')[0];
                    form_data.append('file', file_data);
                }
                form_data.append('action', 'load_team'); // функция обработки 
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
                    document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=team";
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

$fields = array('name', 'city', 'website');


global $wpdb;
$sql = "SELECT * FROM team ORDER BY name ASC";
$teams = $wpdb->get_results($sql, 'ARRAY_A');

?>

<h1>Таблица команд</h1>
<h3>(информация используется на сайте)</h3>
<div><button class="btn_add_rec">Добавить команду</button></div>

<div class="teams_table">
    <?php
    foreach ($teams as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="root_table" data-table="team" data-code="<?= $code ?>">

            <table>
                <tr>
                    <td>Название&nbsp;команды: </td>
                    <td><input type="text" name="name" value="<?= $rec['name'] ?>"></td>
                    <td rowspan="2"><img src="https://fcakron.ru/wp-content/themes/fcakron/images/db/team/<?= $rec['code'] ?>.png" alt="<?= $rec['name'] ?>"></td>
                    <td><div style="width: 250px;">Логотип: ( PNG не более 100 Кбайт)</div></td>
                </tr>
                <tr>
                    <td>Город: </td>
                    <td><input type="text" name="city" value="<?= $rec['city'] ?>"></td>
                    <td><label class="button" for="logo<?= $code ?>">Загрузить</label>
                        <input id="logo<?= $code ?>" type="file" name="logo"></td>
                </tr>
            </table>
        </div>
    <?php
    } ?>
    <hr class="hr_db">
</div>
<div><button class="btn_add_rec">Добавить команду</button></div>

<script>
    jQuery(function($) {

        //  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=team&add";
        });

        //  ▰▰▰▰ РЕДАКТИРОВАТЬ ЗАПИСЬ ▰▰▰▰
        $("input[type=text]").change(function() { // значение поля изменилось

            let patern = $(this).closest(".root_table"); // корневой предок
            let table = patern.data("table"); // у него прописан код
            let code = patern.data("code"); // и название таблицы для правки
            let name = $(this).attr("name");
            let value = $(this).val();

            console.log(table, code, name, value);
            // return;
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
            let parent = $(this).closest(".root_table"); // корневой предок
            let table = parent.data("table"); // у него прописана таблица
            let code = parent.data("code"); // и код
            let img = parent.find('img');

            form_data = new FormData(); // создание формы
            form_data.append('key', table); // ключ из ассоциативного массива на сервере
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