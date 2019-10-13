<? 
/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ КВА 2019.09.27
 * Редактирования таблиц базы данных
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */


##  CSS стили для админ-панели
add_action( 'admin_enqueue_scripts', function(){
  wp_enqueue_style( 'db-wp-admin', get_template_directory_uri() .'/db_edit/style.css' );
});

add_action( 'admin_enqueue_scripts', 'myajax_data', 99 ); // событие 'admin_enqueue_scripts'только на админку

function myajax_data(){  //Создает уникальный защитный ключ на короткий промежуток времени	?>
    <script> var my_ajax_noncerr = '<?= wp_create_nonce( 'my_ajax_nonce' ); ?>'</script>
<?   
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
        
	// }
}
/*
 * 
 */
/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Изменение информации в базе данных
 * (универсальная функция)
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */

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

    $tables = ['team', 'player','tourney', 'meet']; // список допустымых таблиц

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

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * Загрузка файлов в формате png
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */
function load_file_callback(){
     
	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // пользователь не залогонился

    // ошибки процесса загрузки
    if ( $_FILES['file']['error'] < 0 ) {
        wp_die('Ошибка: ' . $_FILES['file']['error']);
    }
    
    $pach = get_template_directory().$_POST['path_file'];

    // wp_die($pach);
    $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
    if( ! $result )
        echo 'ошибка загрузки', $pach;
	wp_die();
}



//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  ДОбАВИТЬ ЗАПИСЬ ТУРНИРА
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  Все проверки на уровне клиента. Здесь только запись.

function load_tourney_callback(){

	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // юзверь не залогонин


    if(isset($_FILES['file'])) {
        if ( $_FILES['file']['error'] < 0 ) {
            wp_die ('Логотип: ошибка ' . $_FILES['file']['error']);
        }
    }

    $data_a = array(
        'name' => clean($_POST['name']));

        global $wpdb;
        $result = $wpdb->insert('tourney', $data_a);
        if ($result <= 0) {
            wp_die('ошибка записи');
        }

        // грузим файлы если они существуют
        if(isset($_FILES['file'])) {    
            $id = $wpdb->insert_id; // код новой записи
            $pach = get_template_directory()."/images/db/tourney/$id.png";
            $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
            if(! $result )
                wp_die ("Логотип: ошибка загрузки.");
        }
        wp_die(''); // если всё ок, то возвращаем ""
}

//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  ДОбАВИТЬ ЗАПИСЬ КОМАНДЫ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  Все проверки на уровне клиента. Здесь только запись.

function load_team_callback(){

	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // юзверь не залогонин


    if(isset($_FILES['file'])) {
        if ( $_FILES['file']['error'] < 0 ) {
            wp_die ('Логотип: ошибка ' . $_FILES['file']['error']);
        }
    }

    $data_a = array(
        'name' => clean($_POST['name']),
        'city' => clean($_POST['city']));

        global $wpdb;
        $result = $wpdb->insert('team', $data_a);
        if ($result <= 0) {
            wp_die('ошибка записи');
        }

        // грузим файлы если он существуют
        if(isset($_FILES['file'])) {    
            $id = $wpdb->insert_id; // код новой записи
            $pach = get_template_directory()."/images/db/team/" . $id . ".png";
            $result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
            if(! $result )
                wp_die ('Логотип: ошибка загрузки.');
        }
        wp_die(); // если всё ок
}


//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  ДОБАВИТЬ ЗАПИСЬ ВСТРЕЧИ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

function load_meet_callback(){

	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // юзверь не залогонин

    $data_a = array(
        'name' => clean($_POST['name']),
        'city' => clean($_POST['city']),
        'stadium' => clean($_POST['stadium']),
        'date_meet' => clean($_POST['date_meet']),
        'time_meet' => clean($_POST['time_meet']),
        'tourney' => clean($_POST['tourney']),
        'team_1' => clean($_POST['team_1']),
        'team_2' => clean($_POST['team_2']));
        $format = array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d' );

        global $wpdb;

        $result = $wpdb->insert('meet', $data_a, $format);
        
        if ($result <= 0) {
            wp_die('ошибка записи');
        }
        wp_die(); // если всё ок, то возвращаем ""
}

//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰
//  ДОбАВИТЬ ЗАПИСЬ ИГРОКА
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

function load_player_callback(){
    
	if( ! wp_verify_nonce( $_POST['nonce_code'], 'my_ajax_nonce' ) ) die( 'Stop!'); // Проверяем защитный ключ
	if(! is_user_logged_in()) die( 'Stop! No login'); // юзверь не залогонин

// начинаем с проверки файлов фотографий
    if(isset($_FILES['file'])) {
        if ( $_FILES['file']['error'] < 0 ) {
            wp_die ('Фотография 1: ошибка ' . $_FILES['file']['error']);
        }
    }

    if(isset($_FILES['file2'])) {
        if ( $_FILES['file2']['error'] < 0 ) {
            wp_die ('Фотография 2: ошибка ' . $_FILES['file2']['error']);
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
    
    $positions = clean($_POST['positions']);
    if (is_numeric($positions)) { $data_a['positions'] = (int)$positions; }
    
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
    
    if(isset($_FILES['file'])) {    
        $pach = get_template_directory()."/images/db/player/" . $id . "-1.png";
        echo "Файл $pach";
		$result = move_uploaded_file($_FILES['file']['tmp_name'], $pach);
		if(! $result )
			wp_die ('Фотография 1 - ошибка загрузки.'.$pach);
    }

    if(isset($_FILES['file2'])) {
        $pach = get_template_directory()."/images/db/player/" . $id . "-2.png";
         echo "Файл $pach";
		$result = move_uploaded_file($_FILES['file2']['tmp_name'], $pach);
		if(! $result ) {
			wp_die ('Фотография 2 - ошибка загрузки.'.$pach);
        }
    }

	wp_die(); // если всё ок
}

?>