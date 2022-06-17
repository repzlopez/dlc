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
define( 'DB_NAME', 'diamondl_wp' );

/** MySQL database username */
define( 'DB_USER', 'diamondl_wp' );

/** MySQL database password */
define( 'DB_PASSWORD', 'SD349!hT-p' );

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
define( 'AUTH_KEY',         'qxyzccqo7uptwd8ylyfw6qta6cwwdmoyy4mz3t0v4imu8eh8oxvrotwzwjxhzukw' );
define( 'SECURE_AUTH_KEY',  'zyevsety3j0808hl5udtcawbisbmrhgkjbf45x3tsfhzosybwea1gl3t4kvqbaug' );
define( 'LOGGED_IN_KEY',    '08qcyx3a3lq6dunidfcqb5jit39n2d44tfwxeaqfrrzenr6em2f7nculvvg5ka54' );
define( 'NONCE_KEY',        'kxw5to2uzn1gwasf47zzjijtpygzhjabqvki7x1sqmjfe45ck23ymrwdy85pos6f' );
define( 'AUTH_SALT',        'ewxufwulhpoka0xsnldhvyyu8kx8uunnnwadmgeuxqe45zav6rkemms41oqko4a6' );
define( 'SECURE_AUTH_SALT', 'gjnzhbbtc5flch36lpm2v25zmtxalgnlwd7fhexn6wwkhgqr7dbmvjgmalw6k0tj' );
define( 'LOGGED_IN_SALT',   'vpnk403gaf49idqpkdnamq4opzkje1gd2ozqqdg6l8rsuoqd2z1v4vbwxl1ostws' );
define( 'NONCE_SALT',       '0aid3ensaimyql00cx0sts6etrbldsvdq4lgpdczqeftdodjjuk9wdratcmzeez3' );

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

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', false );

// Disable display of errors and warnings
define( 'WP_DEBUG_DISPLAY', true );
@ini_set( 'display_errors', 0 );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
