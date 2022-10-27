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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'jikexingkong');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '88758870a');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '=>?eH%cs]&5X~GaL$CK@I}p$<{)]?hLQN3,]4>t%<::0M+}ej_:hJ{GZ4;rE6_$T');
define('SECURE_AUTH_KEY',  'MB?O>2rMyC0{anWvBYnhwu*mz7s&0g9R)GI7kT:j;[484c)&A9wY_[Jxa|y>[K8+');
define('LOGGED_IN_KEY',    'n&KGOq(oGN1b,M%?dBt|xc;5kUT1UpIow4Ot#YNBe_[zD=>1PT-vH-s^H|z?8Ai{');
define('NONCE_KEY',        'VeO+;Ck!-+}Msm}|<1>+U2%u4NdY+:d1L`F_`-?M 6r8;wh4o[O-J,s)peH-@jCe');
define('AUTH_SALT',        'jM2hQ%JGD`oBY%z(X7UdT{3ZRy*S>p?k=VS^rQ!7K8hrJo|J+*?L@V1@Cp3z:qfC');
define('SECURE_AUTH_SALT', '<8S_#<.5`Q}P>=bEfqGT)lm&mY)u{oudsMdAP2~5qjf|EX*a>{&$/=.sYQwW&@M=');
define('LOGGED_IN_SALT',   'STma#^c=kdsvibj5D!zshVBm*m|Mn3/!YmOYahR%7u%ar+)NGf$KF3QEU8)H~z%d');
define('NONCE_SALT',       'Vwq}zmJry;;&W:i{{6v<d0B9>OpH%xfedJ#7UOE%A_NrS&Vq~6e/XS}a$qZ9|eOi');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
