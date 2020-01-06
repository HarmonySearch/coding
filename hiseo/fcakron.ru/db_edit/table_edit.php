<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  РЕДАКТИРОВАНИЯ ТАБЛИЦ БАЗЫ ДАННЫХ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ЗАГРУЗКА ТУРНИРНОЙ ТАБЛИЦЫ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function standings_load_callback()
{

    global $wpdb;

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    $sql = "TRUNCATE TABLE standings;";
    $wpdb->query($sql);
    foreach ($_POST['json'] as $rec) {
        //echo($rec['meet']);
        $data_a = array(
            'tourney' => $rec['tourney'],
            'team_code' => $rec['team_code'],
            'meet' => $rec['meet'],
            'victory' => $rec['victory'],
            'draw' => $rec['draw'],
            'defeat' => $rec['defeat'],
            'points' => $rec['points']
        );
        //var_dump($data_a);
        $format = array('%d', '%d', '%d', '%d', '%d', '%d');
        $result = $wpdb->insert('standings', $data_a, $format);
        if ($result <= 0) {
            wp_die('Ошибка записи в базу данных.');
        }
    };

    die();
}

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  UPDATE FIELD
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function data_change_callback()
{
    /*
     * Получаем 4 параметра
     * Проверяем актуальность
     */

    $tables = ['team', 'player', 'tourney', 'meet', 'trainer', 'player_scheme', 'statistics', 'career']; // список допустымых таблиц

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    $table = clean($_POST['table']);
    if (!in_array($table, $tables)) die('Stop! No table'); // нет такой таблицы
    $code = clean($_POST['code']);
    $field = clean($_POST['name']);
    $value = clean($_POST['value']);

    global $wpdb;

    $result =
        $wpdb->update(
            $table,
            array($field => $value),
            array('code' => intVal($code))
        );
    echo $result;
    wp_die();
}


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  УДАЛЕНИЕ СТРОКИ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function row_delete_callback()
{
    /*
     * Получаем 2 параметра: таблицу и код строки
     */

    $tables = ['team', 'player', 'tourney', 'meet', 'trainer', 'player_scheme', 'statistics', 'career']; // список допустымых таблиц

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    $table = clean($_POST['table']);
    if (!in_array($table, $tables)) die('Stop! No table'); // нет такой таблицы

    $code = clean($_POST['code']);

    $where = array('code' => $code);

    global $wpdb;

    $result = $wpdb->delete($table, $where);

    echo $result;
    // echo $table, $code, $where;

    wp_die();
}



$rule_file = array(
    'meet' => array('type' => 'image/jpeg', 'size' => 500000,  'dir' => '/images/db/meet/', 'ext' => '.jpg'),
    'team' => array('type' => 'image/png', 'size' => 500000,  'dir' => '/images/db/team/', 'ext' => '.png'),
    'tourney' => array('type' => 'image/png', 'size' => 100000, 'dir' => '/images/db/tourney/', 'ext' => '.png'),
    'player_1' => array('type' => 'image/png', 'size' => 1000000, 'dir' => '/images/db/player/a', 'ext' => '.png'),
    'player_2' => array('type' => 'image/png', 'size' => 1000000, 'dir' => '/images/db/player/b', 'ext' => '.png'),
    'trainer' => array('type' => 'image/png', 'size' => 1000000, 'dir' => '/images/db/trainer/', 'ext' => '.png')
);


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ ЗАГРУЗКА ФАЙЛОВ ★★★★
// - безопасность
// - ошибки загрузки на клиент -> сервер
// - проверка типа и размера файла
// - (пока нет) масштабирование фалов + два размера
// - ошибки загрузки файла в каталог
// - возвращаем ошибку или кусок src, если все в норме 

function load_file_callback()
{
    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Ошибка защитного ключа!');
    if (!is_user_logged_in()) die('Пользователь не залогонился!');

    //wp_die('вход');

    if ($_FILES['file']['error'] < 0) {
        wp_die('Ошибка закрузки на сервер файла: ' . $_FILES['file']['error']);
    }

    global $rule_file;
    $param = $rule_file[$_POST['key']];

    if ($_FILES['file']['type'] != $param['type']) {
        wp_die('Нужен тип файла: ' . $param['type']);
    }

    if ($_FILES['file']['size'] > $param['size']) {
        wp_die('Размер файла больше: ' . $param['size']);
    }
    // префикс + каталог + код записи + пасширение
    $pach = get_template_directory() . $param['dir'] . $_POST['code'] . $param['ext'];
    //wp_die($pach);
    $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
    if (!$result)
        echo 'Ошибка загрузки файла в каталог: ', $pach;

    // для png и jpg разный алгоритм
    if ($param['ext'] == '.png') { // png
        $img = imagecreatefrompng($pach); // исходник
        list($width, $height) = getimagesize($pach);
        $sizes = array('s' => 100, 'm' =>  250, 'l' =>  500);
        foreach ($sizes as $key => $size) {
            $newHeight = $size;
            if ($newHeight > $height) { $newHeight = $height; } // увеличивать нельзя
            $newWidth = ($width / $height) * $newHeight;
            $tmp = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($tmp, false); //Отключаем режим сопряжения цветов
            imagesavealpha($tmp, true); //Включаем сохранение альфа канала
            imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $pach = get_template_directory() . $param['dir'] . $_POST['code'] . $key . $param['ext'];
            $write = imagepng($tmp, $pach);
        }
    } else { // jpg
        $img = imagecreatefromjpeg($pach);
        list($width, $height) = getimagesize($pach);

        $sizes = array('s' => 100, 'm' =>  330);
        foreach ($sizes as $key => $size) {
            $newHeight = $size; // 100
            $newWidth = ($width / $height) * $newHeight;
            $tmp = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $pach = get_template_directory() . $param['dir'] . $_POST['code'] . $key . $param['ext'];
            $write = imagejpeg($tmp, $pach); // качество 75%
        }
    }
    wp_die($param['dir'] . $_POST['code'] . 's' . $param['ext']);
}



//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ ТУРНИРА
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_tourney_callback()
{

    global $rule_file;

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    // проверяем файлы если они существуют
    if (isset($_FILES['file'])) {

        if ($_FILES['file']['error'] < 0) {
            wp_die('Ошибка: ' . $_FILES['file']['error']);
        }

        $param = $rule_file['tourney'];

        if ($_FILES['file']['type'] != $param['type']) {
            wp_die('Тип файла не: ' . $param['type']);
        }

        if ($_FILES['file']['size'] > $param['size']) {
            wp_die('Размер файла больше: ' . $param['size']);
        }
    }

    $data_a = array(
        'name' => clean($_POST['name'])
    );

    global $wpdb;
    $result = $wpdb->insert('tourney', $data_a);
    if ($result <= 0) {
        wp_die('Ошибка записи в базу данных.');
    }

    $id = $wpdb->insert_id; // код новой записи

    // грузим файлы если они существуют
    if (isset($_FILES['file'])) {

        $id = $wpdb->insert_id; // код новой записи
        $pach = get_template_directory() . "/images/db/tourney/$id.png";
        $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
        if (!$result)
            wp_die(' Ошибка загрузки файла.');
    }
    wp_die(); // если всё ок
}

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ КОМАНДЫ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  сначала проверка файла на допустимость. 
//  если всё допустимо, по создаём запись и берём код
//  грузим файл в дирректорию
//  если запись создана, выкидывать пользователя в общую таблицу
//  чтобы не запорачиваться с JSON:
//  'бубубу' - ошибка до записи
//  ' бубубу' - ошибка после записи
//  '' - норм

function load_team_callback()
{
    global $rule_file;

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    if (isset($_FILES['file'])) {
        if ($_FILES['file']['error'] < 0) {
            wp_die('Логотип: ошибка ' . $_FILES['file']['error']);
        }

        $param = $rule_file['team'];

        if ($_FILES['file']['type'] != $param['type']) {
            wp_die('Тип файла не: ' . $param['type']);
        }

        if ($_FILES['file']['size'] > $param['size']) {
            wp_die('Размер файла больше: ' . $param['size']);
        }
    }

    $data_a = array(
        'name' => clean($_POST['name']),
        'city' => clean($_POST['city'])
    );

    global $wpdb;
    $result = $wpdb->insert('team', $data_a);
    if ($result <= 0) {
        wp_die('Ошибка записи в базу данных.');
    }

    // грузим файл если есть
    if (isset($_FILES['file'])) {
        $id = $wpdb->insert_id; // код новой записи
        $pach = get_template_directory() . "/images/db/team/" . $id . ".png";
        $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
        if (!$result)
            wp_die(' Ошибка загрузки файла.');
    }
    wp_die(); // если всё ок
}


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ ВСТРЕЧИ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_meet_callback()
{

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    $data_a = array(
        'name' => clean($_POST['name']),
        'city' => clean($_POST['city']),
        'stadium' => clean($_POST['stadium']),
        'date_meet' => clean($_POST['date_meet']),
        'time_meet' => clean($_POST['time_meet']),
        'time_zone' => clean($_POST['time_zone']),
        'tourney' => clean($_POST['tourney']),
        'team_1' => clean($_POST['team_1']),
        'team_2' => clean($_POST['team_2'])
    );

    $free = clean($_POST['free']);
    if (is_numeric($free)) {
        $data_a['free'] = (int) $free;
    }

    $format = array('%s', '%s', '%s', '%s', '%s',  '%s', '%d', '%d', '%d', '%d');

    global $wpdb;

    $result = $wpdb->insert('meet', $data_a, $format);

    if ($result <= 0) {
        wp_die('ошибка записи');
    }
    wp_die(); // если всё ок, то возвращаем ""
}




//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ СТАТИСТИКИ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function statistics_add_callback()
{

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    $data_a = array(
        'meet' => (int) clean($_POST['meet'])
    );
    $format = array('%d');

    global $wpdb;
    $result = $wpdb->insert('statistics', $data_a, $format);

    if ($result <= 0) {
        wp_die('ошибка записи');
    }
    $id = $wpdb->insert_id; // код новой записи

    wp_die($id);
}


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ КАРЬЕРЫ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function career_add_callback()
{

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    $data_a = array(
        'player' => (int) clean($_POST['player'])
    );
    $format = array('%d');

    global $wpdb;
    $result = $wpdb->insert('career', $data_a, $format);

    if ($result <= 0) {
        wp_die('ошибка записи');
    }
    $id = $wpdb->insert_id; // код новой записи

    wp_die($id);
}


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ ИГРОКА
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_player_callback()
{

    global $rule_file;

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    // начинаем с проверки файлов фотографий
    if (isset($_FILES['file_1'])) {
        if ($_FILES['file_1']['error'] < 0) {
            wp_die('Фотография 1: ошибка ' . $_FILES['file_1']['error']);
        }
        $param = $rule_file['player_1'];

        if ($_FILES['file_1']['type'] != $param['type']) {
            wp_die('Тип файла 1 не: ' . $param['type']);
        }

        if ($_FILES['file_1']['size'] > $param['size']) {
            wp_die('Размер файла 1 больше: ' . $param['size']);
        }
    }

    if (isset($_FILES['file_2'])) {
        if ($_FILES['file_2']['error'] < 0) {
            wp_die('Фотография 2: ошибка ' . $_FILES['file_2']['error']);

            $param = $rule_file['player_2'];

            if ($_FILES['file_2']['type'] != $param['type']) {
                wp_die('Тип файла 2 не: ' . $param['type']);
            }

            if ($_FILES['file_2']['size'] > $param['size']) {
                wp_die('Размер файла 2 больше: ' . $param['size']);
            }
        }
    }

    $data_a = array(
        'number' => clean($_POST['number']),
        'lastname' => clean($_POST['lastname']),
        'name' => clean($_POST['name']),
        'growing' => clean($_POST['growing']),
        'weight' => clean($_POST['weight']),
        'vc' => clean($_POST['vc']),
        'instagram' => clean($_POST['instagram'])
    );

    $birthday = clean($_POST['birthday']);
    if (($birthday) == '') {
        $data_a['birthday'] = '1900-01-01';
    } else {
        $data_a['birthday'] = $birthday;
    }

    $team = (int) clean($_POST['team']);
    $data_a['team'] = $team;

    $position = clean($_POST['position']);
    if (is_numeric($position)) {
        $data_a['position'] = (int) $position;
    }

    $country = clean($_POST['country']);
    if (is_numeric($country)) {
        $data_a['country'] = (int) $country;
    }

    $capitan = clean($_POST['capitan']);
    if (is_numeric($capitan)) {
        $data_a['capitan'] = (int) $capitan;
    }


    global $wpdb;
    $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d');
    $result = $wpdb->insert('player', $data_a, $format);
    if ($result <= 0) {
        wp_die('ошибка записи');
    }

    $id = $wpdb->insert_id; // код новой записи

    // грузим файлы если они существуют

    if (isset($_FILES['file_1'])) {
        $pach = get_template_directory() . "/images/db/player/" . $id . "-1.png";
        $result = move_uploaded_file($_FILES['file_1']['tmp_name'], $pach);
        if (!$result)
            wp_die('Фотография 1 - ошибка загрузки.' . $pach);
    }

    if (isset($_FILES['file_2'])) {
        $pach = get_template_directory() . "/images/db/player/" . $id . "-2.png";
        $result = move_uploaded_file($_FILES['file_2']['tmp_name'], $pach);
        if (!$result) {
            wp_die('Фотография 2 - ошибка загрузки.' . $pach);
        }
    }

    wp_die();
}

// ----------------------------------------------------------------------------
// РЕДАКТИРОВАНИЕ СОБЫТИЙ МАТЧА
// 
// получаем переменную для изменения. если событие относится к игроку, то 
// запускаем вычисление соответствующей статистики для данного турнира и для
// данного игрока
// ----------------------------------------------------------------------------

function edit_match_events_callback()
{
    // нам нужны: code, name, value. таблицу мы и так знаем. 
    // сначала записываем информацию в таблицу

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    $code = clean($_POST['code']);
    $field = clean($_POST['field']);
    $value = clean($_POST['value']);

    wp_die(''.$code.$field.$value);
    
    // global $wpdb;

    // $result =
    //     $wpdb->update(
    //         $table,
    //         array($field => $value),
    //         array('code' => intVal($code))
    //     );
    // echo $result;
    // wp_die();


}


//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ СОТРУДНИКА
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_trainer_callback()
{

    global $rule_file;

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

    // проверяем файлы если они существуют
    if (isset($_FILES['file'])) {

        if ($_FILES['file']['error'] < 0) {
            wp_die('Ошибка: ' . $_FILES['file']['error']);
        }

        $param = $rule_file['trainer'];

        if ($_FILES['file']['type'] != $param['type']) {
            wp_die('Тип файла не: ' . $param['type']);
        }

        if ($_FILES['file']['size'] > $param['size']) {
            wp_die('Размер файла больше: ' . $param['size']);
        }
    }

    $data_a = array(
        'lastname' => clean($_POST['lastname']),
        'name' => clean($_POST['name']),
        'position' => clean($_POST['position']),  // должность
        'group' => clean($_POST['group'])  // должность
    );
    $country = clean($_POST['country']);
    if (is_numeric($country)) {
        $data_a['country'] = (int) $country;
    }

    $team = (int) clean($_POST['team']);
    $data_a['team'] = $team;

    global $wpdb;
    $result = $wpdb->insert('trainer', $data_a);
    if ($result <= 0) {
        wp_die('Ошибка записи в базу данных.');
    }

    $id = $wpdb->insert_id; // код новой записи

    // грузим файлы если они существуют
    if (isset($_FILES['file'])) {

        $id = $wpdb->insert_id; // код новой записи
        $pach = get_template_directory() . "/images/db/trainer/$id.png";
        $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
        if (!$result)
            wp_die(' Ошибка загрузки файла.');
    }
    wp_die();
}

?>