<?php
define("THEME_PATH", get_template_directory_uri());
/**
 * Twenty Seventeen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */

/**
 * Twenty Seventeen only works in WordPress 4.7 or later.
 */
if (version_compare($GLOBALS['wp_version'], '4.7-alpha', '<')) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function twentyseventeen_setup()
{
	/*
	 * Make theme available for translation.
	 * Translations can be filed at WordPress.org. See: https://translate.wordpress.org/projects/wp-themes/twentyseventeen
	 * If you're building a theme based on Twenty Seventeen, use a find and replace
	 * to change 'twentyseventeen' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('twentyseventeen');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	add_image_size('twentyseventeen-featured-image', 2000, 1200, true);

	add_image_size('twentyseventeen-thumbnail-avatar', 100, 100, true);

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus(
		array(
			'top'    => __('Top Menu', 'twentyseventeen'),
			'social' => __('Social Links Menu', 'twentyseventeen'),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support(
		'post-formats',
		array(
			'aside',
			'image',
			'video',
			'quote',
			'link',
			'gallery',
			'audio',
		)
	);

	// Add theme support for Custom Logo.
	add_theme_support(
		'custom-logo',
		array(
			'width'      => 250,
			'height'     => 250,
			'flex-width' => true,
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
	  */
	add_editor_style(array('css/editor-style.css', twentyseventeen_fonts_url()));

	// Load regular editor styles into the new block-based editor.
	add_theme_support('editor-styles');

	// Load default block styles.
	add_theme_support('wp-block-styles');

	// Add support for responsive embeds.
	add_theme_support('responsive-embeds');

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = array(
		'widgets'     => array(
			// Place three core-defined widgets in the sidebar area.
			'sidebar-1' => array(
				'text_business_info',
				'search',
				'text_about',
			),

			// Add the core-defined business info widget to the footer 1 area.
			'sidebar-2' => array(
				'text_business_info',
			),

			// Put two core-defined widgets in the footer 2 area.
			'sidebar-3' => array(
				'text_about',
				'search',
			),
		),

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts'       => array(
			'home',
			'about'            => array(
				'thumbnail' => '{{image-sandwich}}',
			),
			'contact'          => array(
				'thumbnail' => '{{image-espresso}}',
			),
			'blog'             => array(
				'thumbnail' => '{{image-coffee}}',
			),
			'homepage-section' => array(
				'thumbnail' => '{{image-espresso}}',
			),
		),

		// Create the custom image attachments used as post thumbnails for pages.
		'attachments' => array(
			'image-espresso' => array(
				'post_title' => _x('Espresso', 'Theme starter content', 'twentyseventeen'),
				'file'       => 'images/espresso.jpg', // URL relative to the template directory.
			),
			'image-sandwich' => array(
				'post_title' => _x('Sandwich', 'Theme starter content', 'twentyseventeen'),
				'file'       => 'images/sandwich.jpg',
			),
			'image-coffee'   => array(
				'post_title' => _x('Coffee', 'Theme starter content', 'twentyseventeen'),
				'file'       => 'images/coffee.jpg',
			),
		),

		// Default to a static front page and assign the front and posts pages.
		'options'     => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{home}}',
			'page_for_posts' => '{{blog}}',
		),

		// Set the front page section theme mods to the IDs of the core-registered pages.
		'theme_mods'  => array(
			'panel_1' => '{{homepage-section}}',
			'panel_2' => '{{about}}',
			'panel_3' => '{{blog}}',
			'panel_4' => '{{contact}}',
		),

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus'   => array(
			// Assign a menu to the "top" location.
			'top'    => array(
				'name'  => __('Top Menu', 'twentyseventeen'),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),

			// Assign a menu to the "social" location.
			'social' => array(
				'name'  => __('Social Links Menu', 'twentyseventeen'),
				'items' => array(
					'link_yelp',
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	/**
	 * Filters Twenty Seventeen array of starter content.
	 *
	 * @since Twenty Seventeen 1.1
	 *
	 * @param array $starter_content Array of starter content.
	 */
	$starter_content = apply_filters('twentyseventeen_starter_content', $starter_content);

	add_theme_support('starter-content', $starter_content);
}
add_action('after_setup_theme', 'twentyseventeen_setup');

//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ КВА АДМИНКА ▰▰▰▰
require_once(dirname(__FILE__) . '/db_edit/db_admin_menu.php');
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

/**
 * Register custom fonts.
 */
function twentyseventeen_fonts_url()
{
	$fonts_url = '';

	/*
	 * Translators: If there are characters in your language that are not
	 * supported by Libre Franklin, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$libre_franklin = _x('on', 'Libre Franklin font: on or off', 'twentyseventeen');

	if ('off' !== $libre_franklin) {
		$font_families = array();

		$font_families[] = 'Libre Franklin:300,300i,400,400i,600,600i,800,800i';

		$query_args = array(
			'family' => urlencode(implode('|', $font_families)),
			'subset' => urlencode('latin,latin-ext'),
		);

		$fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
	}

	return esc_url_raw($fonts_url);
}



/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Seventeen 1.0
 */
function twentyseventeen_javascript_detection()
{
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action('wp_head', 'twentyseventeen_javascript_detection', 0);

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function twentyseventeen_pingback_header()
{
	if (is_singular() && pings_open()) {
		printf('<link rel="pingback" href="%s">' . "\n", esc_url(get_bloginfo('pingback_url')));
	}
}
add_action('wp_head', 'twentyseventeen_pingback_header');


/**
 * Enqueues scripts and styles.
 */
function twentyseventeen_scripts()
{
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style('twentyseventeen-fonts', twentyseventeen_fonts_url(), array(), null);

	// Theme stylesheet.
	wp_enqueue_style('twentyseventeen-style', get_stylesheet_uri() . '?v=' . filemtime(get_template_directory() . '/style.css'));

	// Theme block stylesheet.
	wp_enqueue_style('twentyseventeen-block-style', get_theme_file_uri('/css/blocks.css'), array('twentyseventeen-style'), '1.1');

	// Load the dark colorscheme.
	if ('dark' === get_theme_mod('colorscheme', 'light') || is_customize_preview()) {
		wp_enqueue_style('twentyseventeen-colors-dark', get_theme_file_uri('/css/colors-dark.css'), array('twentyseventeen-style'), '1.0');
	}

	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if (is_customize_preview()) {
		wp_enqueue_style('twentyseventeen-ie9', get_theme_file_uri('/css/ie9.css'), array('twentyseventeen-style'), '1.0');
		wp_style_add_data('twentyseventeen-ie9', 'conditional', 'IE 9');
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style('twentyseventeen-ie8', get_theme_file_uri('/css/ie8.css'), array('twentyseventeen-style'), '1.0');
	wp_style_add_data('twentyseventeen-ie8', 'conditional', 'lt IE 9');

	// Load the html5 shiv.
	wp_enqueue_script('html5', get_theme_file_uri('/js/html5.js'), array(), '3.7.3');
	wp_script_add_data('html5', 'conditional', 'lt IE 9');

	wp_enqueue_script('twentyseventeen-skip-link-focus-fix', get_theme_file_uri('/js/skip-link-focus-fix.js'), array(), '1.0', true);

	wp_enqueue_script('main_scripts', get_theme_file_uri('/js/scripts.js'), array(), filemtime(get_template_directory() . '/js/scripts.js'), true);

	$twentyseventeen_l10n = array(
		'quote' => twentyseventeen_get_svg(array('icon' => 'quote-right')),
	);

	if (has_nav_menu('top')) {
		wp_enqueue_script('twentyseventeen-navigation', get_theme_file_uri('/js/navigation.js'), array('jquery'), '1.0', true);
		$twentyseventeen_l10n['expand']   = __('Expand child menu', 'twentyseventeen');
		$twentyseventeen_l10n['collapse'] = __('Collapse child menu', 'twentyseventeen');
		$twentyseventeen_l10n['icon']     = twentyseventeen_get_svg(
			array(
				'icon'     => 'angle-down',
				'fallback' => true,
			)
		);
	}

	wp_enqueue_script('jquery-scrollto', get_theme_file_uri('/js/jquery.scrollTo.js'), array('jquery'), '2.1.2', true);

	wp_localize_script('twentyseventeen-skip-link-focus-fix', 'twentyseventeenScreenReaderText', $twentyseventeen_l10n);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'twentyseventeen_scripts');

/**
 * Enqueues styles for the block-based editor.
 *
 * @since Twenty Seventeen 1.8
 */
function twentyseventeen_block_editor_styles()
{
	// Block styles.
	wp_enqueue_style('twentyseventeen-block-editor-style', get_theme_file_uri('/css/editor-blocks.css'), array(), '1.1');
	// Add custom fonts.
	wp_enqueue_style('twentyseventeen-fonts', twentyseventeen_fonts_url(), array(), null);
}
add_action('enqueue_block_editor_assets', 'twentyseventeen_block_editor_styles');

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function twentyseventeen_content_image_sizes_attr($sizes, $size)
{
	$width = $size[0];

	if (740 <= $width) {
		$sizes = '(max-width: 706px) 89vw, (max-width: 767px) 82vw, 740px';
	}

	if (is_active_sidebar('sidebar-1') || is_archive() || is_search() || is_home() || is_page()) {
		if (!(is_page() && 'one-column' === get_theme_mod('page_options')) && 767 <= $width) {
			$sizes = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
		}
	}

	return $sizes;
}
add_filter('wp_calculate_image_sizes', 'twentyseventeen_content_image_sizes_attr', 10, 2);

/**
 * Filter the `sizes` value in the header image markup.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $html   The HTML image tag markup being filtered.
 * @param object $header The custom header object returned by 'get_custom_header()'.
 * @param array  $attr   Array of the attributes for the image tag.
 * @return string The filtered header image HTML.
 */
function twentyseventeen_header_image_tag($html, $header, $attr)
{
	if (isset($attr['sizes'])) {
		$html = str_replace($attr['sizes'], '100vw', $html);
	}
	return $html;
}
add_filter('get_header_image_tag', 'twentyseventeen_header_image_tag', 10, 3);


/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 */
function twentyseventeen_front_page_template($template)
{
	return is_home() ? '' : $template;
}
add_filter('frontpage_template', 'twentyseventeen_front_page_template');


/**
 * Get unique ID.
 *
 * This is a PHP implementation of Underscore's uniqueId method. A static variable
 * contains an integer that is incremented with each call. This number is returned
 * with the optional prefix. As such the returned value is not universally unique,
 * but it is unique across the life of the PHP process.
 *
 * @since Twenty Seventeen 2.0
 * @see wp_unique_id() Themes requiring WordPress 5.0.3 and greater should use this instead.
 *
 * @staticvar int $id_counter
 *
 * @param string $prefix Prefix for the returned ID.
 * @return string Unique ID.
 */
function twentyseventeen_unique_id($prefix = '')
{
	static $id_counter = 0;
	if (function_exists('wp_unique_id')) {
		return wp_unique_id($prefix);
	}
	return $prefix . (string) ++$id_counter;
}

/**
 * Implement the Custom Header feature.
 */
require get_parent_theme_file_path('/inc/custom-header.php');

/**
 * Custom template tags for this theme.
 */
require get_parent_theme_file_path('/inc/template-tags.php');

/**
 * Additional features to allow styling of the templates.
 */
require get_parent_theme_file_path('/inc/template-functions.php');

/**
 * Customizer additions.
 */
require get_parent_theme_file_path('/inc/customizer.php');

/**
 * SVG icons functions and filters.
 */
require get_parent_theme_file_path('/inc/icon-functions.php');

function orElse($a, $b)
{
	if ($a == null)
		return $b;
	else
		return $a;
}

if (function_exists('acf_add_options_page')) {
	acf_add_options_page(array(
		'page_title' 	=> 'Настройки новостей',
		'menu_title'	=> 'Настройки новостей',
		'menu_slug' 	=> 'news_settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

function clean($var = "")
{
	$var = trim($var);
	$var = stripslashes($var);
	$var = strip_tags($var);
	$var = htmlspecialchars($var);
	return $var;
}

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * AJAX
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ */


add_action('admin_enqueue_scripts', 'myajax_data', 99);
add_action('wp_enqueue_scripts', 'myajax_data', 99);
function myajax_data()
{ ?>
	<script>
		var my_ajax_noncerr = '<?= wp_create_nonce('my_ajax_nonce'); ?>',
			ajax_url = '<?= admin_url('admin-ajax.php') ?>';
	</script>
<?php }

if (wp_doing_ajax()) {
	add_action('wp_ajax_nopriv_news_list', 'news_list_callback');

	function news_list_callback()
	{
		if (!wp_verify_nonce($_POST['nonce_code'], 'my_ajax_nonce')) die('Stop!');
		require_once(dirname(__FILE__) . '/ajax_news.php');
		wp_die();
	}
}

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * TITLE
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ */
add_filter('wpseo_title', 'remove_one_wpseo_title');
add_filter('document_title_parts', 'person_title');
function person_title($title)
{
	global $post;
	if ($post->ID == 1053) {
		$title['title'] = 'ФК “АКРОН” г.Тольятти I';
		$title['site'] = '';
		if (is_numeric($_GET['person'])) {
			$person = get_player($_GET['person']);
			$title['title'] .= ' ' . $person['number'] . ' ' . $person['lastname'] . ' ' . $person['name'] . ($person['capitan'] ? ' (капитан)' : '');
		}
	}
	return $title;
}
function remove_one_wpseo_title($title)
{
	global $post;
	if ($post->ID == 1053)
		return false;
	else
		return $title;
}

/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * WOOCOMMERCE
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */
function mytheme_add_woocommerce_support()
{
	add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'mytheme_add_woocommerce_support');

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 7);
//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
//add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 35 );


/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 * ИЗМЕНЕНИЕ ПАНЕЛИ АДМИНИСТРАТОРА В ПОСТАХ
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */

// Блок связанных персон
add_action('add_meta_boxes', 'mb_linked_items');
function mb_linked_items($post)
{
	add_meta_box(
		'mb_linked_items',
		'Связанные с новостью персоны',
		'mb_linked_items_html',
		'post',
		'side' // Позиция 'normal' || 'side' || 'advanced'
	);
}
function mb_linked_items_html($post)
{
	// . Читаем тренеров - выводим. Читаем игроков выводим
	// читаем все записи по $post.id
	// ds,bhftv
	global $wpdb;
	$sql = "SELECT `binding` FROM `post_pers` WHERE `post` = $post->ID"; // читаем коды тренеров и игроков в формате json
	$code_pers = $wpdb->get_var($sql);
	if (is_null($code_pers)) { // добавляем запись в таблицу
		$wpdb->insert('post_pers', array('post' => $post->ID, 'binding' => '{}'), array('%d', '%s'));
	}
	$pers = json_decode($code_pers);
	$pers_tr = $pers->tr;
	$pers_pl = $pers->pl;
	$code_team = get_team_select(); // команда select
	// тренеры тоже
	// итоговый список загоняем в селект, чтобы потом получить два массива через ффффшшшшш 
	?>

	<label for="list_trainer">Связанные с новостю тренеры</label>
	<div  name="list_trainer[]" id="list_trainer" class="postbox">
		<?php foreach ($pers_tr as $rec) {
				$prs = get_trainer($rec);
				$name = $prs["lastname"] . ' ' . $prs["name"]; ?>
			<div value="<?= $rec ?>" class="pers" data-code="<?= $rec ?>" data-pers="tr"><?= $name ?></div>
		<?php } ?>
	</div>

	<label for="list_player">Связанные с новостю игроки</label>
	<select multiple name="list_player[]" id="list_player" class="postbox">
		<?php foreach ($pers_pl as $rec) {
				$prs = get_player($rec);
				$name = $prs["lastname"] . ' ' . $prs["name"]; ?>
			<option selected value="<?= $rec ?>" class="pers" data-code="<?= $rec ?>" data-pers="tr"><?= $name ?></option>
		<?php } ?>
	</select>

	<label for="team_id">Команды или тренерский состав</label>
	<select id="group_pers">
		<option>выбрать группу персонала</option>
		<option value="-1">Тренерский состав</option>
		<?php foreach ($code_team as $opt) { ?>
			<option value="<?= $opt['code'] ?>"><?= $opt['name'] ?> (<?= $opt['city'] ?>)</option>
		<?php } ?>
	</select>
	<div id="list_pers">
		<option>пустой список</option>
	</div>
	<!-------------------------------------------------------------------- JQ ---->
	<script>
		jQuery(function($) {

			// выбираем команду
			$(document).on('change', '#group_pers', function(e) {

				form_data = new FormData();
				form_data.append('team', $(this).val());

				form_data.append('action', 'group_pers'); // функция обработки 
				form_data.append('nonce_code', my_ajax_noncerr); // ключ

				$.ajax({
					method: "POST",
					cache: false,
					contentType: false,
					processData: false,
					url: ajaxurl,
					data: form_data,
				}).done(function(msg) {
					$('#list_pers option').remove();
					JSON.parse(msg).forEach(function(p) {
						$('#list_pers').append('<option data-code="'+p['code']+'" data-tab="'+p['tab']+'">' + p['lastname'] + ' ' + p['name'] + '</option>');
					});
				});
			});

			// выбираем персону
			$(document).on('click', '#list_pers option', function(e) {
				console.log('ткнули на игрока');
				let tab = $(this).data("tab");
				let code = $(this).data("code");
				let text = $(this).text();
				// let code = $(this).val();
				console.log(tab,text,code);
				if (tab == 'tr') {
					$('#list_trainer').append('<div value="'+ code +'">' + text + '</div>');
				} else {
					$('#list_player').append('<div value="'+ code +'">' + text + '</div>');
				}
				$(this).remove();
			});
		});
	</script>
<?php
}

// Сохранение
function save_linked_items($post_id)
{
	// Проверка наличия переменной player_id в POST
	ob_start();
	var_dump($_POST);
	$result = ob_get_clean();
	$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/AAA.txt', "w");
	fwrite($fp, $result . '---');
	fclose($fp);

	$tr_s = '';
	if (array_key_exists('list_trainer', $_POST)) {
		$tr_s = join(",", $_POST['list_trainer']);
	}
	$pl_s = '';
	if (array_key_exists('list_player', $_POST)) {
		$pl_s = join(",", $_POST['list_player']);
	}
	// {"tr":[1,2],"pl":[1,2]}
	$s = '{"tr":[' . $tr_s . '],"pl":[' . $pl_s . ']}';

	global $wpdb;
	$wpdb->update('post_pers', array('binding' => $s), array('post' => $_POST['ID']), array('%s'));
	//echo('---------------------'.$wpdb->last_error);
}
add_action('save_post', 'save_linked_items');


/* ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★ КВА 2019.09.27
 * Секция редактирования таблиц базы данных
 * ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
 */
require_once(dirname(__FILE__) . '/db_edit/functions_db.php');

// =======================================
$mounthlib = [' января', ' февраля', ' марта', ' апреля', ' мая', ' июня', ' июля', ' августа', ' сентября', ' октября', ' ноября', ' декабря'];
define('TICKET_LIB', ['', 'вход свободный', 'купить билет', 'билеты в кассе']);
define("THEME_URL", get_template_directory_uri());
define("WEB_ID", get_current_blog_id());
