<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ СТАСТИСТИКИ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_player = get_player_select();  // select игроки АКРОН
$code_event = get_event();  // select события

// $code_team = get_team_select();  // команда select



//  ★★★★ ДОБАВИТЬ ЗАПИСЬ ★★★★
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=statistics&add&meet=10

if (isset($_GET['add'])) {
    $code_meet = $_GET['meet'];
    $meet = get_meet($code_meet); ?>

    <h1>Добавить статистику</h1>
    <h2>Матч: <?= $meet['name'] ?></h2>

    <div class="statistics_add">
        <div class="add_rec" data-meet="<?= $code_meet ?>">

            <table>
                <tr>
                    <td>Минута: <input type="text" name="minute" value="" required></td>
                </tr>
                <tr>
                    <td>Игрок:
                        <select name="player">
                            <option value="">выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                </tr>
                <tr>
                    <td>Событие:
                        <select name="event">
                            <option value="">выбрать событие</option>
                            <? foreach ($code_event as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
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

                if ($('input[name="lastname"]').val() == '' ||
                    $('input[name="name"]').val() == '') {
                    alert("Не заполнены обязательные поля.");
                    return false;
                }
                form_data = new FormData(); // создание формы
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
                    document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=statistics";
                });
            });
        });
    </script>
<?php
    wp_die();
}


//
// ★★★★ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ★★★★
//
?>
<h1>Таблица статистики</h1>
<h3>(информация не используется на сайте)</h3>
<div>
    <button class="btn_add_rec">Добавить событие</button>
</div>

<div class="statistics_table">
    <?php
    foreach (get_statistics() as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="root_table" data-table="statistics" data-code="<?= $code ?>">

            <table>
                <tr>
                    <td>Пусто: </td>
                </tr>

            </table>


        </div>
    <?php
    } ?>
    <hr class="hr_db">
</div>
<div>
    <button class="btn_add_rec">Добавить событие</button>
</div>

<script>
    jQuery(function($) {

        //  ★★★★ КНОПКА ДОБАВИТЬ ТРЕНЕРА ★★★★
        $(document).on('click', '.btn_add_rec', function() { // кнопка добавления записи
            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=statistics&add";
        });

        //  ★★★★ РЕДАКТИРОВАНИЕ ФОРМЫ ★★★★

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