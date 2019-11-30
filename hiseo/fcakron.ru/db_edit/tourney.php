<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ТУРНИРОВ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//  ▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=tourney&add

if (isset($_GET['add'])) { ?>
    <h1>Добавить турнир</h1>
    <div class="tourney_add">
        <div class="add_rec">
            <div class="err">Поля в красной рамке обязательныe.</div>
            <table>
                <tr>
                    <td>Название:<input type="text" name="name" value="" maxlength="32" required></td>
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

    <script>
        jQuery(function($) {

            //  ▰▰▰▰ КНОПКА ЗАГРУЗИТЬ В БАЗУ ▰▰▰▰
            $(document).on('click', '.load_rec', function() {

                if ($('input[name="name"]').val() == '') {
                    alert("Не заполнены обязательные поля.");
                    return false;
                }
                form_data = new FormData(); // создание формы
                form_data.append('name', $('input[name="name"]').val());
                let file = $('input[type="file"]');
                if (file.val() != '') {
                    file_data = file.prop('files')[0];
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
                    if (msg != '') {
                        alert(msg);
                        if (msg[0] != ' ') {
                            return;
                        }
                    }
                    document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=tourney";
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
<h1>Таблица турниров</h1>
<h3>(информация используется на сайте)</h3>
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
                    <td>Название: </td>
                    <td><input type="text" name="name" value="<?= $rec['name'] ?>"></td>
                </tr>
                <tr>
                    <?php
                        $src = dirname(__FILE__) . '/../images/db/tourney/' . $rec['code'] . 's.png';
                        if (file_exists($src)) { ?>
                        <td rowspan="2"><img src="https://fcakron.ru/wp-content/themes/fcakron/images/db/tourney/<?= $rec['code'] ?>s.png" alt="">
                        <?php } else { ?>
                        <td rowspan="2"><img src="https://fcakron.ru/wp-content/themes/fcakron/images/db/tourney/nofoto.png"></td>
                    <?php } ?>
                    <td>Логотип: ( PNG не более 100 Кбайт)<br>
                        <label class="button" for="logo<?= $code ?>">Загрузить</label></td>
                </tr>
                <tr>
                    <td><input id="logo<?= $code ?>" type="file"></td>
                </tr>
            </table>
        </div>
    <?php
    } ?>
    <hr class="hr_db">
</div>

<script>
    jQuery(function($) {

        //  ▰▰▰▰ КНОПКА ДОБАВИТЬ ТУРНИР ▰▰▰▰
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=tourney&add";
        });

        //  ▰▰▰▰ РЕДАКТИРОВАНИЕ ФОРМЫ ▰▰▰▰

        $(document).on('change', 'input:not([type=file]), select', function(e) {

            let table = 'tourney';
            let name = $(this).attr("name");
            let code = $(this).closest(".tourney").data("code");
            let value = $(this).val();

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

        //  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ ЗАГРУЗКА ФАЙЛА ▰▰▰▰

        $(document).on('change', 'input[type="file"]', function() {

            let file_data = $(this).prop('files')[0];
            let parent = $(this).closest(".tourney");
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
                if (msg[0] == '/') { // норм должно прилететь типа /images/db/player/1-1.png
                    src = 'https://fcakron.ru/wp-content/themes/fcakron' + msg + '?t=' + Date.now();
                    img.attr('src', src);
                } else {
                    alert(msg);
                }
            });
        });
    });
</script>