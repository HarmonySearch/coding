<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  РЕДАКТИРОВАНИЕ СХЕМЫ ИГРОКОВ
//  https://fcakron.ru/wp-admin/admin.php?page=scheme&meet=<код матча>
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_scheme = get_scheme();
$code_player = get_player_select();

// проверка на наличия данной схемы
// если нет то создаём
// ставим по умолчанию схему 1 и открываем для редактирования

if (!isset($_GET['meet'])) {
    die('нет матча');
}
$meet = $_GET['meet'];
$scheme = get_player_scheme($meet);

// если записи нет, то создаём
if (!$scheme) {
    global $wpdb;

    $wpdb->insert(
        'player_scheme',
        array('scheme' => 1, 'meet' => $meet),
        array('%d', '%d')
    );
}

//
// ★★★★ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ★★★★
//
?>
<h1>Схема расстановки игроков</h1>
<h3>(информация не используется на сайте)</h3>

<div>
    <?php
    foreach (get_player_scheme($meet) as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">

        <div class="root_table" data-table="player_scheme" data-code="<?= $code ?>">

            <table>
                <tr>
                    <td style="width: 75px;">Матч: </td>
                    <td style="width: 180px;">
                        <?php
                            $res = get_meet($meet);
                            echo $res['name'];
                            ?>
                    </td>
                </tr>
                <tr>
                    <td>Схема: </td>
                    <td>
                        <select name="scheme">
                            <? foreach ($code_scheme as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['scheme']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td>Капитан: </td>
                </tr>
                <tr>
                    <td>Позиция 1: </td>
                    <td>
                        <select class="player" name="player_1">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_1']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="1" <?= ($rec['capitan'] == '1') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 2: </td>
                    <td>
                        <select class="player" name="player_2">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_2']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="2" <?= ($rec['capitan'] == '2') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 3: </td>
                    <td>
                        <select name="player_3">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_3']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="3" <?= ($rec['capitan'] == '3') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 4: </td>
                    <td>
                        <select name="player_4">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_4']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="4" <?= ($rec['capitan'] == '4') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 5: </td>
                    <td>
                        <select name="player_5">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_5']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="5" <?= ($rec['capitan'] == '5') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 6: </td>
                    <td>
                        <select name="player_6">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_6']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="6" <?= ($rec['capitan'] == '6') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 7: </td>
                    <td>
                        <select name="player_7">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_7']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="7" <?= ($rec['capitan'] == '7') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 8: </td>
                    <td>
                        <select name="player_8">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_8']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="8" <?= ($rec['capitan'] == '8') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 9: </td>
                    <td>
                        <select name="player_9">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_9']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="9" <?= ($rec['capitan'] == '9') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 10: </td>
                    <td>
                        <select name="player_10">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_10']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="10" <?= ($rec['capitan'] == '10') ? 'checked' : ''; ?>></td>
                </tr>
                <tr>
                    <td>Позиция 11: </td>
                    <td>
                        <select name="player_11">
                            <option value="">Выбрать игрока</option>
                            <? foreach ($code_player as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_11']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                            <? } ?>
                        </select>
                    </td>
                    <td><input type="radio" name="capitan" value="11" <?= ($rec['capitan'] == '11') ? 'checked' : ''; ?>></td>
                </tr>
            </table>

            <div class="scheme">
                <div class="item"><b>1</b> </div>
                <div class="item"><b>2</b> </div>
                <div class="item"><b>3</b> </div>
                <div class="item"><b>4</b> </div>
                <div class="item"><b>5</b> </div>
                <div class="item"><b>6</b> </div>
                <div class="item"><b>7</b> </div>
                <div class="item"><b>8</b> </div>
                <div class="item"><b>9</b> </div>
                <div class="item"><b>10</b> </div>
                <div class="item"><b>11</b> </div>
            </div>

        </div>
    <?php
    } ?>
    <hr class="hr_db">
</div>

<script>
    jQuery(function($) {

        //  ★★★★ ОТРАЗИТЬ ВСЕХ ИГРОКОВ В ССОТВЕТСТВИИ СО СХЕМАМИ ★★★★
        $(".scheme").each(function() {
            let parent = $(this).closest(".root_table");
            let scheme = parent.find('[name=scheme]'); // grid
            $(this).attr('class', 'scheme s' + $(scheme).val());
            //console.log(list);
        });

        //  ★★★★ поле СХЕМА  ★★★★
        $(document).on('change', '[name=scheme]', function() { // изменение схемы
            console.log($(this).val());
            let parent = $(this).closest(".root_table");
            let scheme = parent.find('.scheme'); // grid
            $(scheme).attr('class', 'scheme s' + $(this).val());
        });

        //  ★★★★ поле КАПИТАН обновить ★★★★
        // $(document).on('change', 'input', function(e) {

        //     console.log($(this).prop("tagName"));

        // });

        //  ★★★★ ОБНОВИТЬ ПОЛЯ ★★★★
        $(document).on('change', 'input, select', function(e) {

            let patern = $(this).closest(".root_table"); // корневой предок
            let table = patern.data("table"); // у него прописан название таблицы для правки
            let code = patern.data("code"); // и код записи
            let name = $(this).attr("name"); // имя поля
            let value = $(this).val(); // значение поля
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
    });
</script>