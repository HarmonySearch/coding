<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ТУРНИРОВ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/functions_db.php');  // функции для работы с базой данных


//  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=tourney&add

if (isset($_GET['add'])) { ?>

    <h2>Добавить турнир</h2>
    <div class="tourney_add">

        <div class="add_rec">
            <table>
                <tr>
                    <td>Название:<input class="name" type="text" name="name" value="" maxlength="32" required></td>
                </tr>
                <tr>
                    <td>Логотип: ( PNG не более 100 Кбайт)<br>
                        <input class="load_logo" type="file" name="photo2">
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

                if ($('.name').val() == '') {
                    $(".err").text("Не заполнены обязательные поля !");
                    return false;
                }
                form_data.append('name', $('.name').val());

                if ($('#logo').val() != '') { // файл загружен ?

                    file_data = $('.load_logo').prop('files')[0]; // ссылка на объект файла
                    if (file_data.type != 'image/png') {
                        $(".err").text("Файл не в формате PNG.");
                        return false;
                    }
                    if (file_data.size > 100000) {
                        $(".err").text("Логотип не более 100 Кбайт.");
                        return false;
                    }
                    form_data.append('file', file_data);
                }

                form_data.append('action', 'load_tourney'); // функция обработки 
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
                        document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=tourney";
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
// ▰▰▰▰ РЕДАКТИРОВАНИЕ ТАбЛИЦЫ ▰▰▰▰
//
?>

<div>
    <button class="btn_add_rec">Добавить турнир</button>
</div>

<div class="tourney_table">
    <?php
    foreach (get_tourney() as $rec) { 
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="tourney" data-code="<?= $code ?>">

            <table>
                <tr>
                    <td>Название: <input class="name" type="text" name="name" value="<?= $rec['name'] ?>"></td>
                </tr>
                <tr>
                    <td><img class="logo num<?= $rec['code'] ?>" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/tourney/<?= $rec['code'] ?>.png" alt="<?= $rec['name'] ?>"></td>
                </tr>
                <tr>
                    <td>Логотип: ( PNG не более 100 Кбайт)<br>
                        <label class="button" for="logo<?= $code ?>">Загрузить</label>
                    <td><input class="logo" id="logo<?= $code ?>" type="file" name="photo"></td>
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
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=tourney&add";
        });

        //  ▰▰▰▰ РЕДАКТИРОВАТЬ ЗАПИСЬ ▰▰▰▰
        $("input[type=text]").change(function() { // значение поля изменилось

            let table = 'tourney';
            let name = $(this).attr("name");
            let value = $(this).val();
            let code = $(this).closest(".tourney").data("code");

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

        //  ▰▰▰▰ ЗАГРУЗКА ФАЙЛА ▰▰▰▰
        $(document).on('change', 'input[type="file"]', function() {

            form_data = new FormData(); // создание формы

            file_data = $(this).prop('files')[0]; // ссылка на объект файла

            if (file_data.type != 'image/png') {
                $(".err").text("Не заполнены обязательные поля !");
                return false;
            }
            if (file_data.size > 100000) {
                $(".err").text("Логотип не более 100 Кбайт.");
                return false;
            }
            form_data.append('file', file_data);

            let code = $(this).closest(".tourney").data("code");
            console.log(code);
            let path_file = '/images/db/tourney/' + code + '.png';
            form_data.append('path_file', path_file);

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
                    let img = $(".num" + code);
                    let src = img.attr('src') + '?t=' + Date.now();
                    img.attr('src', src); // обновляем логотип
                }
            });
        });

    });
</script>