<?php
/**
 * Debarras38 : point d'entrée du thème.
 *
 * Chaque responsabilité a son fichier dans inc/.
 */

defined( 'ABSPATH' ) || exit;

define( 'D38_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'D38_DIR', get_template_directory() );
define( 'D38_URI', get_template_directory_uri() );

require D38_DIR . '/inc/coordonnees.php';
require D38_DIR . '/inc/setup.php';
require D38_DIR . '/inc/motif.php';
require D38_DIR . '/inc/formulaire.php';
require D38_DIR . '/inc/seo.php';
