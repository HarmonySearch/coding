<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  РЕДАКТИРОВАНИЕ СХЕМЫ ИГРОКОВ
//  https://fcakron.ru/wp-admin/admin.php?page=scheme&meet=<код матча>
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $wpdb;

if (!isset($_GET['meet'])) {
    die('нет матча');
}
$meet = $_GET['meet'];

$code_scheme = get_scheme();

// игроки АКРОН
$sql = "SELECT `code`, `lastname`, `name` FROM player WHERE team = 1";
$players_akron = $wpdb->get_results($sql, 'ARRAY_A');

// команда противника
$sql = "SELECT `team_1` FROM meet WHERE code = $meet";
$code_team = $wpdb->get_var($sql);
if ($code_team == 1) {
    $sql = "SELECT `team_2` FROM meet WHERE code = $meet";
    $code_team = $wpdb->get_var($sql);
}
$sql = "SELECT `name`, `city` FROM team WHERE code = $code_team";
$team = $wpdb->get_results($sql, 'ARRAY_A');
var_dump($team);
// игроки противника
$sql = "SELECT `code`, `lastname`, `name` FROM player WHERE team = $code_team";
$players = $wpdb->get_results($sql, 'ARRAY_A');


// проверка на наличия данной схемы
// если нет то создаём
// ставим по умолчанию схему 1 и открываем для редактирования

$scheme = get_player_scheme($meet);

// если записи нет, то создаём
if (!$scheme) {

    $wpdb->insert(
        'player_scheme',
        array('scheme' => 1, 'meet' => $meet),
        array('%d', '%d')
    );
}

//
// ★★★★ РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ★★★★
//
$res = get_meet($meet);

?>
<h1>Матч: <?= $res['name'] ?></h1>
<h3>(составы команд, схема расстановки игроков)</h3>

<div class="line-up">
    <?php
    $rec = get_player_scheme($meet)[0];
    $code = $rec['code']; ?>
    <hr class="hr_db">

    <div class="root_table" data-table="player_scheme" data-code="<?= $code ?>">

    <table style="align-self: center;">
            <tr>
                <td>Позиция 1: </td>
                <td>
                    <select class="player" name="player_1">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_1']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="1" <?= ($rec['capitan'] == '1') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_1" <?= ($rec['best'] == $rec['player_1']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 2: </td>
                <td>
                    <select class="player" name="player_2">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_2']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="2" <?= ($rec['capitan'] == '2') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_2" <?= ($rec['best'] == $rec['player_2']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 3: </td>
                <td>
                    <select name="player_3">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_3']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="3" <?= ($rec['capitan'] == '3') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_3" <?= ($rec['best'] == $rec['player_3']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 4: </td>
                <td>
                    <select name="player_4">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_4']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="4" <?= ($rec['capitan'] == '4') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_4" <?= ($rec['best'] == $rec['player_4']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 5: </td>
                <td>
                    <select name="player_5">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_5']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="5" <?= ($rec['capitan'] == '5') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_5" <?= ($rec['best'] == $rec['player_5']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 6: </td>
                <td>
                    <select name="player_6">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_6']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="6" <?= ($rec['capitan'] == '6') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_6" <?= ($rec['best'] == $rec['player_6']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 7: </td>
                <td>
                    <select name="player_7">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_7']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="7" <?= ($rec['capitan'] == '7') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_7" <?= ($rec['best'] == $rec['player_7']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 8: </td>
                <td>
                    <select name="player_8">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_8']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="8" <?= ($rec['capitan'] == '8') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_8" <?= ($rec['best'] == $rec['player_8']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 9: </td>
                <td>
                    <select name="player_9">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_9']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="9" <?= ($rec['capitan'] == '9') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_9" <?= ($rec['best'] == $rec['player_9']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 10: </td>
                <td>
                    <select name="player_10">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_10']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="10" <?= ($rec['capitan'] == '10') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_10" <?= ($rec['best'] == $rec['player_10']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Позиция 11: </td>
                <td>
                    <select name="player_11">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player_11']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="radio" name="capitan" value="11" <?= ($rec['capitan'] == '11') ? 'checked' : ''; ?>></td>
                <td><input type="checkbox" name="best" value="player_11" <?= ($rec['best'] == $rec['player_11']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td>Кап.</td>
                <td>Луч.</td>
            </tr>

        </table>

        <div>

            <div style="text-align: center;">
                <b>СХЕМА:</b>
                <select name="scheme">
                    <? foreach ($code_scheme as $opt) { ?>
                        <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['scheme']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                    <? } ?>
                </select>
            </div>
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


        <table style="align-self: center;">
            <tr>
                <td>Запасной игрок 1: </td>
                <td>
                    <select class="player" name="substitute_1">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_1']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_1" <?= ($rec['best'] == $rec['substitute_1']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 2: </td>
                <td>
                    <select class="player" name="substitute_2">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_2']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_2" <?= ($rec['best'] == $rec['substitute_2']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 3: </td>
                <td>
                    <select class="player" name="substitute_3">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_3']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_3" <?= ($rec['best'] == $rec['substitute_3']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 4: </td>
                <td>
                    <select class="player" name="substitute_4">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_4']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_4" <?= ($rec['best'] == $rec['substitute_4']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 5: </td>
                <td>
                    <select class="player" name="substitute_5">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_5']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_5" <?= ($rec['best'] == $rec['substitute_5']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 6: </td>
                <td>
                    <select class="player" name="substitute_6">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_6']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_6" <?= ($rec['best'] == $rec['substitute_6']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 7: </td>
                <td>
                    <select class="player" name="substitute_7">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_7']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_7" <?= ($rec['best'] == $rec['substitute_7']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 8: </td>
                <td>
                    <select class="player" name="substitute_8">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_8']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_8" <?= ($rec['best'] == $rec['substitute_8']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 9: </td>
                <td>
                    <select class="player" name="substitute_9">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_9']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_9" <?= ($rec['best'] == $rec['substitute_9']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td>Запасной игрок 10: </td>
                <td>
                    <select class="player" name="substitute_10">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players_akron as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute_10']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
                <td><input type="checkbox" name="best" value="substitute_10" <?= ($rec['best'] == $rec['substitute_10']) ? 'checked' : ''; ?>></td>
            </tr>
            <tr>
                <td> </td>
                <td> </td>
                <td>Луч.</td>
            </tr>
        </table>



    </div>

    <!-- ИГРОКИ ПРОТИВНИКА -->
    <h2>Игроки команды противника: <?= $team[0]['name'] . " (" . $team[0]['city'] . " )" ?></h2>
    <div class="root_table" data-table="player_scheme" data-code="<?= $code ?>">

        <table>
            <tr>
                <td>Позиция 1: </td>
                <td>
                    <select class="player" name="player2_1">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_1']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 2: </td>
                <td>
                    <select class="player" name="player2_2">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_2']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 3: </td>
                <td>
                    <select name="player2_3">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_3']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 4: </td>
                <td>
                    <select name="player2_4">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_4']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 5: </td>
                <td>
                    <select name="player2_5">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_5']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 6: </td>
                <td>
                    <select name="player2_6">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_6']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 7: </td>
                <td>
                    <select name="player2_7">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_7']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 8: </td>
                <td>
                    <select name="player2_8">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_8']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 9: </td>
                <td>
                    <select name="player2_9">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_9']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 10: </td>
                <td>
                    <select name="player2_10">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_10']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Позиция 11: </td>
                <td>
                    <select name="player2_11">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['player2_11']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td>Запасной игрок 1: </td>
                <td>
                    <select class="player" name="substitute2_1">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_1']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 2: </td>
                <td>
                    <select class="player" name="substitute2_2">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_2']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 3: </td>
                <td>
                    <select class="player" name="substitute2_3">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_3']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 4: </td>
                <td>
                    <select class="player" name="substitute2_4">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_4']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 5: </td>
                <td>
                    <select class="player" name="substitute2_5">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_5']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 6: </td>
                <td>
                    <select class="player" name="substitute2_6">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_6']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 7: </td>
                <td>
                    <select class="player" name="substitute2_7">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_7']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 8: </td>
                <td>
                    <select class="player" name="substitute2_8">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_8']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 9: </td>
                <td>
                    <select class="player" name="substitute2_9">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_9']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Запасной игрок 10: </td>
                <td>
                    <select class="player" name="substitute2_10">
                        <option value="">Выбрать игрока</option>
                        <? foreach ($players as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['substitute2_10']) ? 'selected' : ''; ?>><?= $opt['lastname'] ?> <?= $opt['name'] ?></option>
                        <? } ?>
                    </select>
                </td>
            </tr>
        </table> -->



    </div>

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

        //  ★★★★ РЕДАКТИРОВАНИЕ ПОЛЕЙ ★★★★
        $(document).on('change', 'input, select', function(e) {

            let patern = $(this).closest(".root_table"); // корневой предок
            let table = patern.data("table"); // у него прописан название таблицы для правки
            let code = patern.data("code"); // и код записи
            let name = $(this).attr("name"); // имя поля
            let value = $(this).val(); // значение поля
            console.log(table, code, name, value);

            if (name=='best') {
                value = $('[name='+value+']').val(); // код игрока
                console.log(value);
            }

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