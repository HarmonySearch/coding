<?php
//  ---- ТАБЛИЦА СТАТИСТИКИ ИГРОКОВ --------------------------------------------

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_cry = get_country();    // страна select
$code_pos = get_position();  // позиция select
$code_team = get_team_select();  // команда select


// player_stat();

// фильтруем по команде и турниру

// по-умолчанию команда АКРОН
if (isset($_GET['team'])) {
    $team = $_GET['team'];
} else {
    $team = "1";
}
// если не задан турнир, то по-умолчанию 3-й
if (isset($_GET['tourney'])) {
    $tourney = $_GET['tourney'];
} else {
    $tourney = "3";
}

global $wpdb;

$sql = "SELECT ps.* , p.name, p.lastname, p.position
        FROM `player_stat` ps
        INNER JOIN `player` p
            ON ps.player = p.code 
                WHERE ps.tourney = $tourney AND p.team = $team";
// echo $sql;
$players_stat = $wpdb->get_results($sql, 'ARRAY_A');


$tourneys = get_tourney_code(); // список турнипов
$start = microtime(true);
?>
<h1>Таблица статистики игроков</h1>

<hr class="hr_db">

<div>Команда:
    <select id="team_filter">
        <? foreach ($code_team as $opt) { ?>
            <option value="<?= $opt['code'] ?>" <?= ($opt['code'] == $team) ? 'selected' : ''; ?>><?= $opt['name'] ?> (<?= $opt['city'] ?>)</option>
        <? } ?>
    </select>
</div>

<div>Турнир:
    <select id="tourney_filter">
        <? foreach ($tourneys as $opt) { ?>
            <option value="<?= $opt['code'] ?>" <?= ($opt['code'] == $tourney) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
        <? } ?>
    </select>
</div>

<div id="main_table" data-table="player_stat">
    <?php
    foreach ($players_stat as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="player" data-code="<?= $code ?>">
            <table>
                <tr>
                    <!-- 1 -->
                    <td><b><?= $rec['name'] . ' ' . $rec['lastname'] ?></b></td>
                </tr>

                <?php if ($rec['position'] != 1) { ?>
                    <tr>
                        <!-- 2 -->
                        <td class="count">Точность передач:</td>
                        <td class="count"><input class="digit_only" type="text" name="accuracy" value="<?= $rec['accuracy'] ?>"></td>
                        <td class="count">Успешные отборы:</td>
                        <td><input class="digit_only" type="text" name="take_away" value="<?= $rec['take_away'] ?>"></td>
                        <td class="count">Успешные обводки:</td>
                        <td><input class="digit_only" type="text" name="stroke" value="<?= $rec['stroke'] ?>"></td>
                        <td class="count">Выигранные единоборства:</td>
                        <td><input class="digit_only" type="text" name="combat" value="<?= $rec['combat'] ?>"></td>
                        <td class="count">InstatIndex ср.: </td>
                        <td><input class="digit_only" type="text" name="instat" value="<?= $rec['instat'] ?>"></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td class="count">Сыгранные матчи: </td>
                    <td class="count"><input class="digit_only" type="text" name="matches" readonly value="<?= $rec['matches'] ?>"></td>
                    <td class="count">Выходы на старте: </td>
                    <td class="count"><input class="digit_only" type="text" name="output_start" readonly value="<?= $rec['output_start'] ?>"></td>
                    <td class="count">Выходы на замене: </td>
                    <td class="count"><input class="digit_only" type="text" name="output_in_game" readonly value="<?= $rec['output_in_game'] ?>"></td>
                    <td class="count">Минуты на поле: </td>
                    <td class="count"><input class="digit_only" type="text" name="minute" readonly value="<?= $rec['minute'] ?>"></td>
                    <td class="count">Желтые карточки: </td>
                    <td class="count"><input class="digit_only" type="text" name="cart_yellow" readonly value="<?= $rec['cart_yellow'] ?>"></td>
                    <td class="count">Красные карточки: </td>
                    <td class="count"><input class="digit_only" type="text" name="cart_red" readonly value="<?= $rec['cart_red'] ?>"></td>
                </tr>
                <tr>
                    <?php if ($rec['position'] != 1) { ?>
                        <td class="count">Голевые передачи: </td>
                        <td class="count"><input class="digit_only" type="text" name="pass" readonly value="<?= $rec['pass'] ?>"></td>
                        <td class="count">Голы: </td>
                        <td class="count"><input class="digit_only" type="text" name="goal" readonly value="<?= $rec['goal'] ?>"></td>
                        <td class="count">Голы с пенальти: </td>
                        <td class="count"><input class="digit_only" type="text" name="penalty" readonly value="<?= $rec['penalty'] ?>"></td>
                    <?php } else { ?>
                        <td class="count">Сухие матчи: </td>
                        <td class="count"><input class="digit_only" type="text" name="shutout" readonly value="<?= $rec['shutout'] ?>"></td>
                        <td class="count">Пропущенные голы : </td>
                        <td class="count"><input class="digit_only" type="text" name="goal_allow" readonly value="<?= $rec['goal_allow'] ?>"></td>
                        <td class="count">Сейвы: </td>
                        <td><input class="digit_only" type="text" name="save" value="<?= $rec['save'] ?>"></td>

                    <?php } ?>
                </tr>
            </table>
        </div>
    <?php } ?>
    <hr class="hr_db">
    <div>
        <button class="btn_add_rec">Добавить игрока</button>
    </div>
</div>

<script>
// ---- SCRIPT ------------------------------------------------------------------------------------

    jQuery(function($) {

        //  ★★★★ ВВОДИТЬ ТОЛЬКО ЦИФРЫ ★★★★
        $(document).ready(function() {
            $('.digit_only').on("change keyup input click", function() {
                if (this.value.match(/[^0-9]/g)) { // g ищет все совпадения, без него – только первое.
                    this.value = this.value.replace(/[^0-9]/g, '');
                }
            });
        });



        //  ---- ФИЛЬТР КОМАНД ----------------------------
        $(document).on('change', '#team_filter, #tourney_filter', function() {

            let team = $('#team_filter').val();
            let tourney = $('#tourney_filter').val();

            href = "https://fcakron.ru/wp-admin/admin.php?page=player_stat&team=" + team + "&tourney=" + tourney;
            console.log(href);
            document.location.href = href;
        });


        //  ---- РЕДАКТИРОВАНИЕ ПОЛЕЙ ----------------------------
        // кроме загрузки файлов
        $(document).on('change', 'input:not([type=file]), select, textarea', function(e) {

            let table = $('#main_table').data("table");  // таблица
            let name = $(this).attr("name"); // имя поля
            let code = $(this).closest(".player").data("code"); // код строки
            let value = $(this).val();
            console.log(table,name,code,value);

            let data = {
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
                data: data
            }).done(function(msg) {
                console.log(msg);
            });
        });

        //  ★★★★★★★★★★★★★★★★★★★★★★★★ ЗАГРУЗКА ФОТОГРАФИЙ ★★★★
        // нужны: код записи, номер фото (первое или второе),
        // два фото 
        // ссылки на файл для загрузки, на img для обновления после загрузки 

        $(document).on('change', 'input[type="file"]', function() {

            let parent = $(this).closest(".player");
            let code = parent.data("code"); // код записи
            let num = $(this).data("num"); // номер фото
            let file_data = $(this).prop('files')[0]; // ссылки на файл
            let img = parent.find('.img_' + num); // ссылка на картинку

            form_data = new FormData();
            form_data.append('key', 'player_' + num); // ключ из ассоциативного массива на сервере
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
                    console.log('+++++++++++++' + src);
                    img.attr('src', src);
                } else {
                    alert(msg);
                }
            });
        });
    });
</script>