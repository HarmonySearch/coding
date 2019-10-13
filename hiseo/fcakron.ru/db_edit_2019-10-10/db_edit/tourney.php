<?php
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  РЕДАКТИРОВАНИЕ ТАБЛИЦЫ ТУРНИРОВ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once( dirname( __FILE__  ) . '/functions_db.php' );  // функции для работы с базой данных

//
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ ДОБАВИТЬ ЗАПИСЬ ▰▰▰▰
//
//  GET запрос наличие переменной add без значения
//

 


//
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ РЕДАКТИРОВАНИЕ ТАбЛИЦЫ ▰▰▰▰
//
?>

<div>
    <button class="tourney_add">Добавить турнир</button>
</div>

<div class="tourney_table">
    <?
    foreach(get_tourney() as $rec){ ?>
        <hr class="hr_db">
        <div class="tourney" data-code="<?= $rec['code'] ?>">

            <div class="name_lbl">Название турнира:</div>
            <input class="name" type ="text" name="name" value="<?= $rec['name'] ?>" >
            
            <img class="logo" src="https://fcakron.ru/wp-content/themes/fcakron/images/db/tourney/<?= $rec['code'] ?>.png" alt="<?= $rec['name'] ?>">
            <input class="load_logo" type="file" name="photo">

        </div> 
        <? 
    } ?>
    <hr class="hr_db">
</div>

<script>
jQuery(function($){

    //  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ РЕДАКТИРОВАНИЕ ПОЛЕЙ ▰▰▰▰
    
    $( "input[type=text]" ).change(function() { // значение поля изменилось

        let table = 'tourney';
        let name = $(this).attr("name");
        let value = $(this).val();
        let code = $(this).closest(".tourney").data("code");

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
    
    //  ■■■■■■■■■■■■■■■■■■■■■■■■ ЗАГРУЗКА ФАЙЛА ■■■■
    
    $(document).on('change', '.load_logo', function(){

        file_data = $(this).prop('files')[0]; // объект - файл

        if (file_data.type != 'image/png') {
            alert('Тип файла не png');
            return false;
        }

        if (file_data.size > 100000) {
            alert('Логотип не более 100 Кбайт.');
            return false;
        }

        code = $(this).closest(".tourney").data("code");
        path_file = '/images/db/tourney/' + code + '.png';
        
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
                // обновление img
                let img = $('.logo');
                let src = img.attr('src') + '?t=' + Date.now();
                img.attr('src',src);  // обновляем фото
            }
        });
    });

});
</script>
<!-- 
Сделать клик на кнопки
       


-->
