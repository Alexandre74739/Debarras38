<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'IRNyZAa?^qy(60:/P%@N6qeq+Y4CE:e~AWAx(KD)jYe#-%~:WSBv%WI{5KgQ*^8I' );
define( 'SECURE_AUTH_KEY',   'F.OQV5sDz7Qk8cuTWZU8,qjPX4(`7VY_nylH_a-KRY$=Ny%F[S)$9_#;s7qN.N82' );
define( 'LOGGED_IN_KEY',     'M}P|`ekY#.n}d++?#lz2Utu!;wh|p&6tHaN$KGO.hSK{e1bq[NR;5!Xd3SDhS|2Y' );
define( 'NONCE_KEY',         'M=H{x-:DsNQcumgCV;8dkDRT~{0qJ66|#/ZcX V,@OIJR]4GKq@kK;~%Vuiw9`r6' );
define( 'AUTH_SALT',         'ryhnbTafc[Rvjffd0+wr=Wba8MxJ#.!bf]pYzw|@9~L4~3zB=^*l&6Kv@xr&#+[)' );
define( 'SECURE_AUTH_SALT',  'Sd2j8e.j|,fY.<;+!]7;a}+O><F3XMB&ML:D!^wt%1ko367&.W0yPFUshXn.iqOV' );
define( 'LOGGED_IN_SALT',    'gk6uoUX734^I,iU$~sl78|DC9wOY{3q`/bf/32+F:ngqc1bq#!)T3~m,YyafA$Du' );
define( 'NONCE_SALT',        '*<[imORWEIO?BxEExi=]&RkQ;Ui]u};QD#nIY+lbQnzKO]T=qp.PLcY0J;VO.Hxq' );
define( 'WP_CACHE_KEY_SALT', 'DS~v]4+uS)zy*feC,XmB~>V[FWY WzJ4:;g?KXBZd5iKd5cjX6tO*@w%+I.QAj[#' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
