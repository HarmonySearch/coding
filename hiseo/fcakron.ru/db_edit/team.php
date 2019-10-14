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
                    <td>Название: <input class="name" type="text" name="name" value="" maxlength="32" required></td>
                </tr>
                <tr>
                    <td>Город: <input class="city" type="text" name="city" value="" maxlength="32" required></td>
                </tr>
                <tr>
                    <td>Логотип: ( PNG не более 100 Кбайт)<br>
                        <input class="load_logo" type="file" name="logo">
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

            $(document).on('click', '.load_rec', function() {

                form_data = new FormData(); // создание формы

                if ($('.name').val() == '' ||
                    $('.city').val() == '') {
                    $(".err").text("Не заполнены обязательные поля !");
                    return false;
                }
                form_data.append('name', $('.name').val());
                form_data.append('city', $('.city').val());

                if ($('.load_logo').val() != '') { // файл загружен ?

                    file_data = $('.load_logo').prop('files')[0]; // ссылка на объект файла
                    if (file_data.type != 'image/png') {
                        $(".err").text("Логотп не в формате PNG.");
                        return false;
                    }
                    if (file_data.size > 100000) {
                        $(".err").text("Логотип не более 100 Кбайт.");
                        return false;
                    }
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
                    if (msg == '') {
                        document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=team";
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

$fields = array('name', 'city', 'website');
?>

<div>
    <button class="btn_add_rec">Добавить команду</button>
</div>

<div class="teams_table">
    <?php
    foreach (get_team() as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="team" data-code="<?= $code ?>">

            <table>
                <tr>
                    <td>Название&nbsp;команды: </td>
                    <td><input type="text" name="name" value="<?= $rec['name'] ?>"></td>
                    <td rowspan="2"><img class="logo num<?= $rec['code'] ?>" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/team/<?= $rec['code'] ?>.png" alt="<?= $rec['name'] ?>"></td>
                    <td><div style="width: 250px;">Логотип: ( PNG не более 100 Кбайт)</div></td>
                </tr>
                <tr>
                    <td>Город: </td>
                    <td><input class="city" type="text" name="city" value="<?= $rec['city'] ?>"></td>
                    <td><label class="button" for="logo<?= $code ?>">Загрузить</label>
                        <input class="logo" id="logo<?= $code ?>" type="file" name="logo"></td>
                </tr>
            </table>
        </div>
    <?php
    } ?>
    <hr class="hr_db">
</div>

<script>
    jQuery(function($) {

        //  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=team&add";
        });

        //  ▰▰▰▰ РЕДАКТИРОВАТЬ ЗАПИСЬ ▰▰▰▰
        $("input[type=text]").change(function() { // значение поля изменилось

            let table = 'team',
                name = $(this).attr("name"),
                value = $(this).val(),
                code = $(this).closest(".team").data("code");

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

        // загрузка логотипа

        $(document).on('change', '.team input[name="logo"]', function() {

            file_data = $(this).prop('files')[0]; // ссылка на файл

            if (file_data.type != 'image/png') {
                alert('Тип файла не png');
                return false;
            }

            if (file_data.size > 100000) {
                alert('Логотип не более 100 Кбайт.');
                return false;
            }

            code = $(this).closest(".team").data("code");
            path_file = '/images/db/team/' + code + '.png';
            console.log(path_file);

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
                    $(".err").text(msg);
                } else {
                    // обновление img
                    let img = $(".num" + code);
                    let src = img.attr('src') + '?t=' + Date.now();
                    img.attr('src', src); // обновляем логотип
                }
            });
        });

    });
</script>
<!-- 
Сделать клик на кнопки
       


-->