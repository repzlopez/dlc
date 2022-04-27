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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dlcwp' );

/** MySQL database username */
define( 'DB_USER', 'dlcph' );

/** MySQL database password */
define( 'DB_PASSWORD', 'dlc0306@)!)' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ']r9+>+mBKy/vE33%6A~=X CoH]91k#;Sl2/GLMIp!%8{_,vz4#8i=6uG7t<<HpjX' );
define( 'SECURE_AUTH_KEY',  '+d/J:Lz)pKX_HcXfG_<r+SN7<%c#KuzWS2B8oI&LyN9)1K@;B%Jkmc6CY+PB?u}#' );
define( 'LOGGED_IN_KEY',    'jF9Z}s3NE+P)  6LL5oz2Qf~fDAWMxYaiFDe5&Sq|jO#xx{83285Q|`.yma<Ea&M' );
define( 'NONCE_KEY',        '.@t]Ny$^s0#hwW2(4>l7=|~po!cL0OdJAB%m(axS!d=ZhyLij=/i!c;WhHZjo{-a' );
define( 'AUTH_SALT',        'JZ]?r LM[M&=,t>n:Zljk|g)55^hgMI}j!U[DPtp5K0SgLejJaqOiWOl7Iw}w*HD' );
define( 'SECURE_AUTH_SALT', 'IveyK5Zx3ksPi/i$w7|]!SXU!8fHyF%utF>dH!2xV]pcDzJHWKAdkM{/Z7LV.tg*' );
define( 'LOGGED_IN_SALT',   'tJOOUrbD1#+/%nRD2il}Ui=DB 0`RHzye.P9P{g9ACE3C;=eiC(W|<Ahl+(#qlwF' );
define( 'NONCE_SALT',       '{>_z-hCvHpj;.Em7xYVPLXDHn<}N4wDyyN~e&TpE|E`Ehaqk7VAl^`sz,qa&4%R2' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'rd_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
