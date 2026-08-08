<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'pest_control_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'a#!JE<3;RzC%gc$Jkb2l*|nF%kLx LL[Oy=:mM8{$]D=165U{>$K8i*)23>g`Y^X' );
define( 'SECURE_AUTH_KEY',  'N.Fb-$WS+=cafE<KLc7C4P|G;5NG9^_Xukd[92!|Hd_dEdIeo{bqY1WHLH#H#z,S' );
define( 'LOGGED_IN_KEY',    'k;$&D>a<&&[CPmO9:MU9g}0QDOxZY8,lIqOC`%c9O.iNb[ $StU8D51:J~;C,l&F' );
define( 'NONCE_KEY',        'mq^[Dp`q[6ljncnVD{X<zKz*#_ys6Yvq>q8wKXS,r<FZZ+sMK{a8N}HAyQ*xoKf7' );
define( 'AUTH_SALT',        'c9j~MTsy/&+:9xeYC6itnP$oMfplP/*?m]}n&Dp^O2:+%?9@0yQNjXp0+{}s2%7_' );
define( 'SECURE_AUTH_SALT', '1^mO,gk9pZCe0pN]H%j(3.L&evqNsZXibF 6%`@B;IkQWuqQ ^_#3|h_!XU;F}++' );
define( 'LOGGED_IN_SALT',   '>Qgf|HY7w*y&uK#.xp[z@Ob^EM=|Km@IKR&2nX}a&T.!qRlt0D(oi}E_%P8eo*8|' );
define( 'NONCE_SALT',       'Or% f$GG?d6:;WIs+a5MYx?^rsV `[At?_?H94Bq*TF d,`NhTPOF7&^o)Px|r 6' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
