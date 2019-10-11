<?php
/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Редактирование базы данных команд
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once( dirname( __FILE__  ) . '/functions_db.php' );  // функции для работы с базой данных



/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Редактирование команд
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */



$fields = array('name','city','website');
?>
<button class="team_add">Добавить команду</button>

<div class="teams_table">
<?
foreach(get_team() as $team){ ?>
    <div class="team" data-code="<?= $team['code'] ?>">

        <div class="name_l">Название команды</div>
        <input class="name" type ="text" name="name" value="<?= $team['name'] ?>" >
        
        <div class="city_l">Город</div>
        <input class="city" type ="text" name="city" value="<?= $team['city'] ?>" >
        
        <img class="logo" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/team/<?= $team['code'] ?>.png" alt="<?= $team['name'] ?>">
        <input class="load_logo" type="file" name="photo">

    </div> 
    <hr><? 
} ?>
</div>

<script>
jQuery(function($){
    
    $( "input[type=text]" ).change(function() { // значение поля изменилось

        let table = 'team',
            name = $(this).attr("name"), 
            value = $(this).val(),
            code = $(this).closest(".team").data("code");
            
        console.log(table, code, name, value);

        let data_lib = {
            action: 'data_change',
            nonce_code : my_ajax_noncerr,
            table: table,
            code: code,
            name: name,
            value: value
        };
        
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: data_lib
        }).done(function( data ) {
            console.log(data);
        });
    });
    
    // загрузка логотипа
    
    $(document).on('change', '.load_logo', function(){

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
        }).done(function( msg ) {
            if (msg!="") {
                alert(msg);
            } else{
               console.log("обновить страницу, если фото не получится");
            }
        });
    });

});
</script>
<!-- 
Сделать клик на кнопки
       


-->
