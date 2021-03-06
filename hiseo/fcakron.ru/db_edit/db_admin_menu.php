<?php

//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰ КВА ▰▰▰▰
//  РЕДАКТИРОВАНИЕ БАЗЫ ДАННЫХ В АДМИНКЕ
//  ▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰▰

add_action('admin_menu', 'my_admin_menu'); // это хук

function my_admin_menu()
{
    add_menu_page('База данных футбольного клуба', 'База данных', 1, 'db_fcakron', 'db_fcakron', 'dashicons-welcome-widgets-menus');
    function db_fcakron()
    {
        require_once(dirname(__FILE__) . '/main.php');
    }
    add_submenu_page('db_fcakron', 'Редактирование турниров', 'Турниры', 1, 'tourney', 'tourney_edit');
    function tourney_edit()
    {
        require_once(dirname(__FILE__) . '/tourney.php');
    }
    add_submenu_page('db_fcakron', 'Редактирование матчей', 'Матчи', 1, 'meet', 'meet_edit');
    function meet_edit()
    {
        require_once(dirname(__FILE__) . '/meet.php');
    }
    add_submenu_page('db_fcakron', 'Редактирование команд', 'Команды', 1, 'team', 'team_edit');
    function team_edit()
    {
        require_once(dirname(__FILE__) . '/team.php');
    }
    add_submenu_page('db_fcakron', 'Редактирование игроков', 'Игроки', 1, 'player', 'player_edit');
    function player_edit()
    {
        require_once(dirname(__FILE__) . '/player.php');
    }
    add_submenu_page('NULL', 'Статистика игроков', 'Статистика игроков', 1, 'player_stat', 'player_statistics');
    function player_statistics()
    {
        require_once(dirname(__FILE__) . '/player_stat.php');
    }
    add_submenu_page('NULL', 'Схема игроков', 'Схема', 1, 'scheme', 'scheme_edit');
    function scheme_edit()
    {
        require_once(dirname(__FILE__) . '/scheme.php');
    }
    add_submenu_page('db_fcakron', 'Редактирование тренеров', 'Тренеры', 1, 'trainer', 'trainer_edit');
    function trainer_edit()
    {
        require_once(dirname(__FILE__) . '/trainer.php');
    }
    add_submenu_page(NULL, 'Редактирование статистики', 'Статистика', 1, 'statistics', 'statistics_edit');
    function statistics_edit()
    {
        require_once(dirname(__FILE__) . '/statistics.php');
    }
    add_submenu_page(NULL, 'Редактирование карьеры', 'Карьера', 1, 'career', 'career_edit');
    function career_edit()
    {
        require_once(dirname(__FILE__) . '/career.php');
    }
    add_submenu_page('db_fcakron', 'Турнирная таблица', 'Турнирная таблица', 1, 'standings', 'standings_edit');
    function standings_edit()
    {
        require_once(dirname(__FILE__) . '/standings.php');
    }
}
?>