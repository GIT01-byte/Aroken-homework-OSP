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
define('DB_NAME', 'my_wp_db');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', '');

/** Database hostname */
define('DB_HOST', '127.0.0.1');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         'h&~JTW+5wJt.j76Z,.>[N9Fbe,]oydr:a4uTI~d3@1O/<^cSjV;(eqGew2n1QBtc');
define('SECURE_AUTH_KEY',  '!Y `. :_vF;]G}-)_ (*rP>z_Z3 V^d+Xom&=Y8J+)JRpFg<7~g}Xv?EJ`=SV{*0');
define('LOGGED_IN_KEY',    'GgKnWb!ltpZIY|#Dv2JqXR:7y.]nKy3?DB5Em3P:cK)=0gO2J3nBHw)uuf5CI~j-');
define('NONCE_KEY',        'TpeahAvh%N:q>DquGkIx. z}QX/jU})Lw<S< WjuMM6p:k@kJw$nmg9eb*@@z3n5');
define('AUTH_SALT',        '38%5>E.}KVl1J_k(cq7BB6/d?jLx&gs(BP<Ty6aI3_l&HdWqzDTf|e2T9O%/rM!#');
define('SECURE_AUTH_SALT', ')u%`2!L]!Igm]oX+<jO#lKBk-(G+JB:2$9,h $<hXVUf{Yqy`-~ oKE}xjA1:tkq');
define('LOGGED_IN_SALT',   'r#1tq_sI(y6~[jL:S  &|Q@(#4f=(p|7.h_Ou[Ug-4KK[V<]NfKKNH44a{HL!(2J');
define('NONCE_SALT',       '1hoyG_6XRM!sEulY5WDg2E6j$%jJH#:2QMZfn}r.+Z{_SYO^p]?e%*bS`W|u/arH');

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
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

// define('WP_HTTP_BLOCK_EXTERNAL', true);
