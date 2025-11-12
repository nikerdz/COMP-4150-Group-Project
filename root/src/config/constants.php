<?php
// Root directory
define('ROOT_PATH', realpath(__DIR__ . '/../../') . '/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('SOURCE_PATH', ROOT_PATH . 'src/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('LAYOUT_PATH', ASSETS_PATH . 'layout/');

// Base URL for project
define('BASE_URL', '/COMP-4150-Group-Project/root/');

// ASSETS
define('ASSETS_URL', BASE_URL . 'assets/');         // URL to assets folder
define('IMG_URL', ASSETS_URL . 'img/');             // URL to image files
define('LAYOUT_URL', ASSETS_URL . 'layout/');       // URL to layout files

// ASSETS - Styles
define('CSS_URL', ASSETS_URL . 'styles/');          // URL to styles folder
define('STYLE_URL', CSS_URL . 'style.css');         // URL to main style script
define('TEMPLATES_URL', CSS_URL . 'templates/');    // URL to template style scripts

// PUBLIC
define('PUBLIC_URL', BASE_URL . 'public/');         // URL to public folder
define('USER_URL', PUBLIC_URL . 'user/');           // URL to user html/php files
define('ADMIN_URL', PUBLIC_URL . 'admin/');         // URL to admin html/php files
define('CLUB_URL', PUBLIC_URL . 'club/');           // URL to club html/php files
define('EVENT_URL', PUBLIC_URL . 'event/');         // URL to event html/php files

// SOURCE
define('SOURCE_URL', BASE_URL . 'src/');            // URL to source folder
define('CONFIG_URL', SOURCE_URL . 'config/');       // URL to config folder

// SOURCE - Scripts
define('SCRIPTS_URL', SOURCE_URL . '/scripts');     // URL to scripts folder
define('JS_URL', SCRIPTS_URL . 'js/');              // URL to JS scripts folder
define('PHP_URL', SCRIPTS_URL . 'php/');            // URL to PHP scripts folder
define('SQL_URL', SCRIPTS_URL . 'sql/');            // URL to SQL scripts folder

?>