<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ТУРНИРНАЯ ТАБЛИЦА
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

$standings = get_standings(3);

$code_team = get_team_select();  // команда select
?>
<h1>Турнирная таблица</h1>
<h2>(выводится на сайт)</h2>

<script src="https://fcakron.ru/wp-content/themes/fcakron/db_edit/js/jquery-ui.min.js"></script>
<script>
    jQuery(function($) {

        $(function() {
            $("#sortable").sortable({
                revert: true // плавно
            });
            $("div").disableSelection();
        });
    })
</script>

<div>
    <button class="btn_add_rec" data-meet="<?= $meet ?>">Добавить строку в таблицу</button>
</div>

<div class="row_stand" style="text-align: center;">
    <span></span>
    <span>Команда</span>
    <span>И</span>
    <span>В</span>
    <span>Н</span>
    <span>П</span>
    <span>О</span>
</div>
<div id="sortable" class="standings">
    <?php
    foreach ($standings as $rec) { ?>
        <div class="row_stand">
            <div class="reg_move">
            </div>
            <select name="team_code">
                <option value="">выбрать название команды</option>
                <? foreach ($code_team as $opt) { ?>
                    <option value="<?= $opt['code'] ?>" <? echo ($opt['code'] == $rec['team_code']) ? 'selected' : ''; ?>><?= $opt['name'] ?> - <?= $opt['city'] ?></option>
                <? } ?>
            </select>
            <input class="digit_only" type="text" name="meet" value="<?= $rec['meet'] ?>">
            <input class="digit_only" type="text" name="victory" value="<?= $rec['victory'] ?>">
            <input class="digit_only" type="text" name="draw" value="<?= $rec['draw'] ?>">
            <input class="digit_only" type="text" name="defeat" value="<?= $rec['defeat'] ?>">
            <input class="digit_only" type="text" name="points" value="<?= $rec['points'] ?>">
        </div>
    <?php } ?>
</div>
<div>
    <button class="write_tab" data-meet="<?= $meet ?>">Записать таблицу в базу данных</button>
</div>

<style>
    .standings .reg_move {
        cursor: move;
        display: inline-block;
        background-image: url(http://fcakron.ru/wp-content/themes/fcakron/images/db/movs.png);
        border: 1px solid #ddd;
        width: 28px;
    }

    .row_stand {
        display: grid;
        grid-template-columns: 30px 210px 30px 30px 30px 30px 30px 60px;
        grid-column-gap: 10px;
        margin-bottom: 6px;
    }

    .btn_add_rec {
        margin-bottom: 10px;
    }

    .write_tab {
        cursor: pointer;
    }
</style>
<script>
    jQuery(function($) {

        //  ★★★★ кнопка ДОБАВИТЬ запись в конец таблицы ★★★★
        $(document).on('click', '.btn_add_rec', function() {
            // клонируем последний элемент таблицы
            let div = $("#sortable div:last-child").clone();
            div.find("input,select").val("");
            $("#sortable").append(div);
        });

        //  ★★★★ запись таблицы в БД ★★★★
        $(document).on('click', '.write_tab', function() {
            // клонируем последний элемент таблицы
            // упаковка таблицы в JSON
            var f = [];
            $("#sortable .row_stand").each(function(key, e) {
                // console.log($(this));
                var z = {
                    'tourney': 3,
                    'team_code': $(this).find('[name=team_code]').val(),
                    'meet': $(this).find('[name=meet]').val(),
                    'victory': $(this).find('[name=victory]').val(),
                    'draw': $(this).find('[name=draw]').val(),
                    'defeat': $(this).find('[name=defeat]').val(),
                    'points': $(this).find('[name=points]').val()
                }
                f[key] = z;
            });
            console.log(f);
            //return;
            let data_lib = {
                action: 'standings_load',
                nonce_code: my_ajax_noncerr,
                json: f
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