<?php
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
// РЕДАКТИРОВАНИЕ ТАБЛИЦЫ СТАСТИСТИКИ
// https://fcakron.ru/wp-admin/admin.php?page=statistics&meet=15
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_player = get_player_select();  // select игроки АКРОН
$code_event = get_event();  // select события

$meet = $_GET['meet'];

// нужна хоть одна запись, поскольку добавление записей
// делается клонированием последней записи
$res = get_statistics($meet);
if (!$res) {
    global $wpdb;
    $wpdb->insert('statistics', array('meet' => $meet), array('%d'));
}

//
// ★★★★ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ★★★★
//
?>
<h1>Таблица статистики</h1>
<h3>(информация пока не используется на сайте)</h3>
<div>
    <button class="btn_add_rec" data-meet="<?= $meet ?>">Добавить событие</button>
</div>

<div class="statistics_table">
    <hr class="hr_db">
    <table>
        <tr>
            <th width="50px">Время</th>
            <th>Событие</th>
            <th>Игрок</th>
            <th>Комметарий</th>
        </tr>
        <?php
        foreach (get_statistics($meet) as $rec) {
            $code = $rec['code']; ?>
            <tr class="row" data-table="statistics" data-code="<?= $code ?>">
                <td><input class="digit_only" type="text" name="minute" value="<?= $rec['minute'] ?>"></td>
                <td>
                    <select name="event">
                        <option value="">Выбрать событие</option>
                        <? foreach ($code_event as $opt) { ?>
                            <option value="<?= $opt["code"] ?>" <? echo ($opt["code"] == $rec["event"]) ? "selected" : ""; ?>><?= $opt["name"] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td>
                    <select name="player">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($code_player as $opt) { ?>
                            <option value="<?= $opt["code"] ?>" <? echo ($opt["code"] == $rec["player"]) ? "selected" : ""; ?>><?= $opt["lastname"] ?> <?= $opt["name"] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input rows="2" cols="80" type="textarea" name="comment" value="<?= $rec["comment"] ?>"></td>
            </tr>
        <?php
        } ?>
    </table>
    <hr class="hr_db">
</div>
<div>
    <button class="btn_add_rec" data-meet="<?= $meet ?>">Добавить событие</button>
</div>


<script>
    jQuery(function($) {

        //  ★★★★ вводить ТОЛЬКО ЦИФРЫ ★★★★
        $(document).ready(function() {
            $('.digit_only').on("change keyup input click", function() {
                if (this.value.match(/[^0-9]/g)) { // g ищет все совпадения, без него – только первое.
                    this.value = this.value.replace(/[^0-9]/g, '');
                }
            });
        });

        //  ★★★★ кнопка ДОБАВИТЬ запись события ★★★★
        $(document).on('click', '.btn_add_rec', function() {

            let meet = $(this).data("meet"); // код прописан в кнопке
            let data_lib = {
                action: 'statistics_add',
                nonce_code: my_ajax_noncerr,
                meet: meet
            };
            let data;
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                data: data_lib
            }).done(function(data) {
                console.log('--' + data);
                // клонируем последний элемент таблицы
                let tr = $("table tr:last-child").clone();
                console.log('+++++' + data);
                tr.data("code", data);
                console.log(tr.data("code"));
                tr.find("input,select").val("");
                console.log(tr);
                $("table").append(tr);
            });

        });

        //  ★★★★ РЕДАКТИРОВАНИЕ ФОРМЫ ★★★★

        $(document).on('change', 'input, select', function(e) {

            let parent = $(this).closest("tr"); // корневой предок
            let code = parent.data("code"); // код записи
            let name = $(this).attr("name");
            let value = $(this).val();
            console.log(name, code, value);
            //return;
            let data_lib = {
                action: 'data_change',
                nonce_code: my_ajax_noncerr,
                table: "statistics",
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