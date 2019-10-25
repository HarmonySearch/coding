<?php
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  РЕДАКТИРОВАНИЯ ТАБЛИЦ БАЗЫ ДАННЫХ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★



//  CSS стили для админ-панели
add_action( 'admin_enqueue_scripts', function(){
  wp_enqueue_style( 'db-wp-admin', get_template_directory_uri() .'/db_edit/style.css' );
});

add_action( 'admin_enqueue_scripts', 'myajax_data', 99 ); // событие 'admin_enqueue_scripts'только на админку

function myajax_data(){  //Создает уникальный защитный ключ на короткий промежуток времени	?>
    <script> var my_ajax_noncerr = '<?= wp_create_nonce( 'my_ajax_nonce' ); ?>'</script>
<?php   
}
// проверка условия разрешения редактирования информации
if( wp_doing_ajax() ){ 
	// if(current_user_can('edit_user_data')){ 
		add_action('wp_ajax_data_change', 'data_change_callback');
		add_action('wp_ajax_load_file', 'load_file_callback');
		add_action('wp_ajax_load_tourney', 'load_tourney_callback');
		add_action('wp_ajax_load_meet', 'load_meet_callback');
		add_action('wp_ajax_load_team', 'load_team_callback');
		add_action('wp_ajax_load_player', 'load_player_callback');
		add_action('wp_ajax_load_trainer', 'load_trainer_callback');
		add_action('wp_ajax_statistics_add', 'statistics_add_callback');
	// }
}
/*
 * 
 */
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  UPDATE FIELD
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function clean($var = ""){
    $var = trim($var);
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlspecialchars($var);
    return $var;
}

function data_change_callback(){
    /*
     * Получаем 4 параметра
     * Проверяем актуальность
     */

    $tables = ['team', 'player','tourney', 'meet', 'trainer','player_scheme','statistics']; // список допустымых таблиц

	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // юзверь не залогонин

    $table = clean($_POST['table']);
    if (! in_array($table, $tables)) die('Stop! No table'); // нет такой таблицы
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

$rule_file = array(
    'meet' => array('type' => 'image/jpeg', 'size' => 500000,  'dir' => '/images/db/meet/', 'ext' => '.jpg'),
    'team' => array('type' => 'image/png', 'size' => 100000,  'dir' => '/images/db/team/', 'ext' => '.png'),
    'tourney' => array('type' => 'image/png', 'size' => 100000, 'dir' => '/images/db/tourney/', 'ext' => '.png'),
    'player_1' => array('type' => 'image/png', 'size' => 200000, 'dir' => '/images/db/player/', 'ext' => '-1.png'),
    'player_2' => array('type' => 'image/png', 'size' => 200000, 'dir' => '/images/db/player/', 'ext' => '-2.png'),
    'trainer' => array('type' => 'image/png', 'size' => 200000, 'dir' => '/images/db/trainer/', 'ext' => '.png')
);

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ЗАГРУЗКА ФАЙЛОВ
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_file_callback()
{
    global $rule_file;

    if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!'); // Проверяем защитный ключ
    if (!is_user_logged_in()) die('Stop! No login'); // пользователь не залогонился

    //wp_die('вход');

    // ошибки процесса загрузки
    if ($_FILES['file']['error'] < 0) {
        wp_die('Ошибка: ' . $_FILES['file']['error']);
    }

    $param = $rule_file[$_POST['key']];

    if ( $_FILES['file']['type'] != $param['type'] ) {
        wp_die('Тип файла не: '. $param['type']);
    }

    if ( $_FILES['file']['size'] > $param['size'] ) {
        wp_die('Размер файла больше: '. $param['size']);
    }

    $pach = get_template_directory() . $param['dir'] . $_POST['code'] . $param['ext'];
    // wp_die($pach);
    $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
    if( ! $result )
        echo 'ошибка загрузки файла: ', $pach;
	wp_die();
}



//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ ТУРНИРА
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_tourney_callback(){

    global $rule_file;

	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // юзверь не залогонин

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

    // грузим файлы если он существуют
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
        'meet' => (int)clean($_POST['meet'])
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
//  ДОБАВИТЬ ЗАПИСЬ ИГРОКА
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_player_callback(){

    global $rule_file;
    
	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(!is_user_logged_in()) die('Stop! No login'); // юзверь не залогонин

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
        'instagram' => clean($_POST['instagram']));

    $birthday = clean($_POST['birthday']);
    if (($birthday)=='') {
        $data_a['birthday'] = '1900-01-01';
    } else {
        $data_a['birthday'] = $birthday;
    }

    $team = (int)clean($_POST['team']);
    $data_a['team'] = $team;
    
    $position = clean($_POST['position']);
    if (is_numeric($position)) { $data_a['position'] = (int)$position; }
    
    $country = clean($_POST['country']);
    if ( is_numeric ($country)) { $data_a['country'] = (int)$country; }
    
    $capitan = clean($_POST['capitan']);
    if (is_numeric($capitan)) { $data_a['capitan'] = (int)$capitan; }
    

    global $wpdb;
    $format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d' );
    $result = $wpdb->insert('player', $data_a, $format);
    if ($result <= 0) {
        wp_die('ошибка записи');
    }

    $id = $wpdb->insert_id; // код новой записи
    
    // грузим файлы если они существуют
    
    if(isset($_FILES['file_1'])) {
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

//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
//  ДОБАВИТЬ ЗАПИСЬ ТРЕНЕРА
//  ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

function load_trainer_callback(){ 

    global $rule_file;

	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // юзверь не залогонин

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
        'position' => clean($_POST['position'])
    );
    $country = clean($_POST['country']);
    if ( is_numeric ($country)) { $data_a['country'] = (int)$country; }
    
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