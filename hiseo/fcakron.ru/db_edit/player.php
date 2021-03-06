<?php
//  ----------------------------------------------------------------------------
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ИГРОКОВ
//  ----------------------------------------------------------------------------

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$code_cry = get_country();    // страна select
$code_pos = get_position();  // позиция select
$code_team = get_team_select();  // команда select

// player_stat();

//  ★★★★ ДОБАВИТЬ ЗАПИСЬ ★★★★
//
//  GET запрос наличие переменной add без значения
//  https://fcakron.ru/wp-admin/admin.php?page=player&add

if (isset($_GET['add'])) { ?>
    <h2>Новый игрок</h2>
    <div class="player_add">
        <div class="add_rec">
            <div class="err">Поля в красной рамке обязательныe.</div>
            <table>
                <tr>
                    <td>Команда:
                        <select name="team" required>
                            <option value="">выбрать название команды</option>
                            <?php foreach ($code_team as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?> (<?= $opt['city'] ?>)</option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>Страна игрока:</div>
                        <select name="country">
                            <option value="">выбрать страну</option>
                            <?php foreach ($code_cry as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?></option>
                            <?php } ?>
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
                    <td>Дата рождения: <input type="date" name="birthday" value=""></td>
                </tr>
                <tr>
                    <td>Рост: <input type="text" name="growing" value="" maxlength="3"> Вес: <input type="text" name="weight" value="" maxlength="3"></td>
                </tr>
                <tr>
                    <td>Номер игрока: <input type="text" name="number" value="" placeholder=""> Капитан: <input type="checkbox" name="capitan" value="0"></td>
                </tr>
                <tr>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        Позиция игрока:
                        <select name="position">
                            <option value="">выбрать позицию</option>
                            <?php foreach ($code_pos as $opt) { ?>
                                <option value="<?= $opt['code'] ?>"><?= $opt['name'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>VC: <input type="text" name="vc" value="" placeholder=""></td>
                </tr>
                <tr>
                    <td>INSTAGRAM: <input type="text" name="instagram" value="" placeholder=""></td>
                </tr>
                <tr>
                    <td>Фотография 1 (PNG - 450х300): </td>
                </tr>
                <tr>
                    <td><input type="file" name="file_1"></td>
                </tr>
                <tr>
                    <td>Фотография 2 (PNG - 450х300): </td>
                </tr>
                <tr>
                    <td><input type="file" name="file_2"></td>
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
                if ($('select[name="team"]').val() == "" ||
                    $('input[name="lastname"]').val() == "" ||
                    $('input[name="name"]').val() == "") {
                    alert("Не заполнены обязательные поля.");
                    return false;
                }
                form_data = new FormData(); // создание формы
                form_data.append('team', $('select[name="team"]').val());
                form_data.append('position', $('select[name="position"]').val());
                form_data.append('country', $('select[name="country"]').val());
                form_data.append('number', $('input[name="number"]').val());
                form_data.append('lastname', $('input[name="lastname"]').val().trim());
                form_data.append('name', $('input[name="name"]').val().trim());
                form_data.append('birthday', $('input[name="birthday"]').val());
                form_data.append('growing', $('input[name="growing"]').val());
                form_data.append('weight', $('input[name="weight"]').val());
                form_data.append('vc', $('input[name="vc"]').val());
                form_data.append('instagram', $('input[name="instagram"]').val());

                if ($('input[name="capitan"]').is(':checked')) {
                    form_data.append('capitan', "1");
                } else {
                    form_data.append('capitan', "0");
                }

                let file_1 = $('input[name="file_1"]');
                if (file_1.val() != '') {
                    file_data_1 = file_1.prop('files')[0];
                    form_data.append('file_1', file_data_1);
                }
                let file_2 = $('input[name="file_2"]');
                if (file_2.val() != '') {
                    file_data_2 = file_2.prop('files')[0];
                    form_data.append('file_2', file_data_2);
                }
                form_data.append('action', 'load_player'); // функция обработки
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
                    document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=player";
                });

            });
        });
    </script>

<?php
    wp_die();
}



//  -------------------------------------------------------- ТАБЛИЦА ИГРОКОВ ----

/*
 * Фильтрация игроков по командам.
 * Если в GET задана команда, то вытаскивать игроком только этой команды
 */

if (isset($_GET['team'])) {          // это для формирования селектора
    $team = $_GET['team'];
} else {
    $team = "1";
}

$players = get_players($team);


$start = microtime(true);
//var_damp(get_pers_post("p", 22));
$j = get_pers_post("p", 22);
var_dump($j);
?>

<script src="https://fcakron.ru/wp-content/themes/fcakron/db_edit/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://fcakron.ru/wp-content/themes/fcakron/db_edit/css/jquery-ui.css">

<h1>Таблица игроков</h1>
<div>
    <button class="btn_add_rec">Добавить игрока</button>
</div>
<hr class="hr_db">

<div>Фильтр по командам
    <select class="team_filter" name="team_filter">
        <?php foreach ($code_team as $opt) { ?>
            <option value="<?= $opt['code'] ?>" <?= ($opt['code'] == $team) ? 'selected' : ''; ?>><?= $opt['name'] ?> (<?= $opt['city'] ?>)</option>
        <?php } ?>
    </select>
</div>


<div class="player_table">
    <?php
    foreach ($players as $rec) {
        $code = $rec['code']; ?>
        <hr class="hr_db">
        <div class="player" data-code="<?= $code ?>">
            <!-- контейнер grid -->
            <div id="main_tab">

                <div>Команда:</div>
                    <select name="team">
                        <?php foreach ($code_team as $opt) { ?>
                            <option value="<?= $opt['code'] ?>" <?php echo ($opt['code'] == $rec['team']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                        <?php } ?>
                    </select>
                <div>Фамилия:</div>
                <div style="display: flex;    justify-content: space-between;">
                        <input type="text" name="lastname" value="<?= $rec['lastname'] ?>">
                        <input type="text" name="lastname_en" value="<?= $rec['lastname_en'] ?>">
                </div>
                <div>5</div>
                <div>6</div>
                <div>7</div>
                <div>8</div>
                <div>9</div>

                <div>Позиция:</div>
                <div>
                    <div class="inline_block">
                        <select class="position" name="position">
                            <option value="" <?php echo ('' == $rec['position']) ? 'selected' : ''; ?>>не указана</option>
                            <?php foreach ($code_pos as $opt) { ?>
                                <option value="<?= $opt['code'] ?>" <?php echo ($opt['code'] == $rec['position']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="inline_block" style="margin-left: 8px;">
                        Номер:
                    </div>
                    <div class="inline_block">
                        <input class="digit_only" type="text" name="number" value="<?= $rec['number'] ?>">
                    </div>
                    <div class="inline_block" style="margin-left: 8px;">
                    Капитан:
                    </div>
                    <div class="inline_block" style="text-align: right;">
                    <input type="checkbox" name="capitan" value="<?= $rec['capitan'] ?>" <?php echo ($rec['capitan'] == 1) ? 'checked' : ''; ?>> 
                    </div>
                </div>
                <div>Имя:</div>
                <div>
                    <div class="inline_block">
                        <input type="text" name="name" value="<?= $rec['name'] ?>">
                    </div>
                    <div class="inline_block">
                        <input type="text" name="name_en" value="<?= $rec['name_en'] ?>">
                    </div>
                </div>
                <div>5</div>
                <div>6</div>
                <div>7</div>
                <div>8</div>
                <div>9</div>

                <div>vk:</div>
                <input type="text" name="vc" value="<?= $rec['vc'] ?>">
                <div>д.р.</div>
                <input type="date" name="birthday" value="<?= $rec['birthday'] ?>">
                <div>5</div>
                <div>6</div>
                <div>7</div>
                <div>8</div>
                <div>9</div>

                <div>instagram:</div>
                <input type="text" name="instagram" value="<?= $rec['instagram'] ?>">
                <div>Рост:</div>
                <div>
                    <div class="inline_block">
                        <input class="digit_only" type="text" name="growing" value="<?= $rec['growing'] ?>">
                    </div>
                    <div class="inline_block">
                        Вес:
                    </div>
                    <div class="inline_block">
                    <input class="digit_only" type="text" name="weight" value="<?= $rec['weight'] ?>">
                                    </div>
                </div>
                <div>5</div>
                <div>6</div>
                <div>7</div>
                <div>8</div>
                <div>9</div>
            </div>

            <hr class=" hr_db">

                        <!-- начало таблицы -->
                        <table>
                            <tr>
                                <td> Команда:</td>
                                <td>
                                    <select name="team">
                                        <?php foreach ($code_team as $opt) { ?>
                                            <option value="<?= $opt['code'] ?>" <?php echo ($opt['code'] == $rec['team']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>Фамилия:</td>
                                <td><input type="text" name="lastname" value="<?= $rec['lastname'] ?>"></td>
                                <td> Первое гражданство: </td>
                                <td>
                                    <select class="country" name="country">
                                        <option value="" <?php echo ('' == $rec['country']) ? 'selected' : ''; ?>>не указано</option>
                                        <?php foreach ($code_cry as $opt) { ?>
                                            <option value="<?= $opt['code'] ?>" <?php echo ($opt['code'] == $rec['country']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>

                                <?php  // если нет картинок на сервере, то и не загружаем
                                    $src = dirname(__FILE__) . '/../images/db/player/a' . $rec['code'] . 's.png';
                                    if (file_exists($src)) { ?>
                                    <td rowspan="3"><img class="img_1 photo" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/player/a<?= $rec['code'] ?>s.png" alt=""></td>
                                <?php
                                    } else { ?>
                                    <td rowspan="3"><img class="img_1 photo" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/player/nofoto.png"></td>
                                <?php
                                    }
                                    $src = dirname(__FILE__) . '/../images/db/player/b' . $rec['code'] . 's.png';
                                    if (file_exists($src)) { ?>
                                    <td rowspan="3"><img class="img_2 photo" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/player/b<?= $rec['code'] ?>s.png" alt=""></td>
                                <?php
                                    } else { ?>
                                    <td rowspan="3"><img class="img_2 photo" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/player/nofoto.png"></td>
                                <?php
                                    } ?>

                                <td><button class="btn_career">Карьера</button></td>

                            </tr>

                            <tr>
                                <!-- 2 -->
                                <td>Позиция:</td>
                                <td>
                                    <select class="position" name="position">
                                        <option value="" <?php echo ('' == $rec['position']) ? 'selected' : ''; ?>>не указана</option>
                                        <?php foreach ($code_pos as $opt) { ?>
                                            <option value="<?= $opt['code'] ?>" <?php echo ($opt['code'] == $rec['position']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>Имя:</td>
                                <td><input type="text" name="name" value="<?= $rec['name'] ?>"></td>
                                <td> Второе гражданство: </td>
                                <td>
                                    <select name="country_2">
                                        <option value="" <?php echo ('' == $rec['country_2']) ? 'selected' : ''; ?>>не указано</option>
                                        <?php foreach ($code_cry as $opt) { ?>
                                            <option value="<?= $opt['code'] ?>" <?php echo ($opt['code'] == $rec['country_2']) ? 'selected' : ''; ?>><?= $opt['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td><a href="https://fcakron.ru/komanda/igrok/?person=<?= $code ?>" target="_blank">Посмотреть игрока на сайте</a></td>
                            </tr>

                            <tr>
                                <!-- 3 -->
                                <td>Номер:</td>
                                <td>
                                    <input class="digit_only" type="text" name="number" value="<?= $rec['number'] ?>">
                                </td>
                                <td>Дата рождения:</td>
                                <td><input type="date" name="birthday" value="<?= $rec['birthday'] ?>"></td>
                                <td>vk:</td>
                                <td><input type="text" name="vc" value="<?= $rec['vc'] ?>"></td>
                            </tr>

                            <tr>
                                <!-- 4 -->
                                <td> Капитан: </td>
                                <td><input type="checkbox" name="capitan" value="<?= $rec['capitan'] ?>" <?php echo ($rec['capitan'] == 1) ? 'checked' : ''; ?>> </td>

                                <td>Рост: <input class="digit_only" type="text" name="growing" value="<?= $rec['growing'] ?>"></td>
                                <td>Вес: <input class="digit_only" type="text" name="weight" value="<?= $rec['weight'] ?>">
                                </td>
                                <td>instagram:</td>
                                <td><input type="text" name="instagram" value="<?= $rec['instagram'] ?>"></td>
                                <td style="text-align:center">
                                    <label for="file_1<?= $code ?>" class="button">Загрузить</label>
                                    <input id="file_1<?= $code ?>" data-num="1" type="file">
                                </td>
                                <td style="text-align:center">
                                    <label for="file_2<?= $code ?>" class="button">Загрузить</label>
                                    <input id="file_2<?= $code ?>" data-num="2" type="file">
                                </td>
                            </tr>
                            <tr>
                            </tr>
                        </table>
                        <!-- конец таблицы -->

                        <?php if ($rec['position'] != 1) { ?>

                            <div class="stat1">
                                <h3>Статистик не вычисляемая (вводить вручную)</h3>

                                <table style="white-space: nowrap;">

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
                                </table>
                            </div>
                        <?php } ?>

                        <div class="stat1">
                            <b>Статистика вычисляемая по событиям встреч (только для чтения)</b>

                            <table>
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
                                    <?php if ($rec['position'] != 1) { ?>
                                    <?php } else { ?>
                                    <?php } ?>
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
                                        <td class="count"><input class="digit_only" type="text" name="save" readonly value="<?= $rec['save'] ?>"></td>
                                    <?php } ?>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                <hr class="hr_db">
                <div>
                    <button class="btn_add_rec">Добавить игрока</button>
                </div>
                </div>

                <!-------------------------------------------------------------------- JQ ---->
                <script>
                    jQuery(function($) {

                        //  ★★★★ кнопка СТАТИСТИКА ★★★★
                        $(document).on('click', '.btn_career', function() {
                            let code = $(this).closest(".player").data("code");
                            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=career&player=" + code;
                        });

                        // ---- АККОРДЕОН --------------------
                        $(".stat1").accordion({
                            collapsible: true,
                            active: false,
                            animate: false
                        });

                        //  ★★★★ ВВОДИТЬ ТОЛЬКО ЦИФРЫ ★★★★
                        $(document).ready(function() {
                            $('.digit_only').on("change keyup input click", function() {
                                if (this.value.match(/[^0-9]/g)) { // g ищет все совпадения, без него – только первое.
                                    this.value = this.value.replace(/[^0-9]/g, '');
                                }
                            });
                        });

                        //  ★★★★ ДОБАВИТЬ ЗАПИСЬ ★★★★
                        $(document).on('click', '.btn_add_rec', function() { // кнопка "добавить игрока"
                            document.location.href = "https://fcakron.ru/wp-admin/admin.php?page=player&add";
                        });

                        //  ★★★★ ФИЛЬТР ★★★★
                        $(".team_filter").change(function() {
                            team = $(this).val();
                            href = "https://fcakron.ru/wp-admin/admin.php?page=player&team=" + team;
                            console.log(href);
                            document.location.href = href;
                        });

                        //  ★★★★ РЕДАКТИРОВАНИЕ ПОЛЕЙ ★★★★
                        // кроме загрузки файлов
                        $(document).on('change', 'input:not([type=file]), select, textarea', function(e) {

                            let table = 'player'; // таблица
                            let name = $(this).attr("name"); // имя поля
                            let code = $(this).closest(".player").data("code"); // код строки


                            let value;
                            if ($(this).attr("type") == 'checkbox') {
                                value = ($(this).prop("checked") ? 1 : 0);
                            } else {
                                value = $(this).val();
                            }
                            if (name == 'vk' || name == 'instagram') {
                                value = value.replace('https://', '');
                                if (value != '') {
                                    value = 'https://' + value;
                                }
                                $(this).val(value);
                            }
                            console.log(name, value);

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
                            }).done(function(data) {});
                            console.log(data);
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