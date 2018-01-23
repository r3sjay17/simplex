<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'simplex');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'v4p->+y}V-r,uXAAam;XM..5:D)CIn3IF)6*=D_6HMDhu_9d]jsYQ6I&I5m.U1r9');
define('SECURE_AUTH_KEY',  'u(WC6h`)w@f,osQH*;ce9ZwOHv|nK@>CbmLqcH]NgqW9!-ZJo3@a!8VKIS6~rmLp');
define('LOGGED_IN_KEY',    'xHt[ +d(KY> .o,$W?_dWk0UOhq2HSAnJbVE+$?{SIN/>AJ~#k~;5fw_CY L53-6');
define('NONCE_KEY',        'VA~!mJ7W]__{Y6ppbgt_=C+2gq]aVr@dL0_2)%@E~A>`]jDeJS$VhQULAzJYtKWF');
define('AUTH_SALT',        'E^ XJf8~W?z%D9c|VZsbH,xhFKqg>qF8z};|)` Vior._B(]aQEIZ1SANf.RnaEy');
define('SECURE_AUTH_SALT', 'F(*H;RR9>[L{pw/&0&Hp`;03y=q<^?0Z)ZczL1nJJ%@]2!s.ITPdfuviB/@2E 9-');
define('LOGGED_IN_SALT',   'U^L)cBjfS<JU.1!8N-I6#Bv! go)hI[S9ffv~7=12-9:-9inF%=8dY7|ZewH$[Oj');
define('NONCE_SALT',       'Nhw$;`AjFU_(q06=a/b, 0A&26dFW)3h`v@![$b6dK|h=lQ={t]?-*IDn`c0Wdr{');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'sphr_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
/*define('WP_ALLOW_MULTISITE', true);
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost');
define('PATH_CURRENT_SITE', '/simplex');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);*/

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
