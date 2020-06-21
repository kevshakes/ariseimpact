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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '6SiFB9pd5UfHYc/Rk87cGQ5KFy2kwQGUlnuihADFFX8yaXjIuLy0nuDiFd/5cPF375+YHS/O88mytvYRHQZ3ng==');
define('SECURE_AUTH_KEY',  '48NVbZV5m2ARajDIJM1Zxwg17st4dJV2G4WNQtiX8DiS9jWiWVuGiFXvn1F6rGGCLv6qqPkgtojCbS3PhwLZvA==');
define('LOGGED_IN_KEY',    'KUf9/xrDWvwMhlfTq6E4Cx2i5DOhw/AFmXE3F6qzj89Ns7D/FcvGVK10YSHGgGWEVY2ZlpTkFtU5Jj1cBZIy1w==');
define('NONCE_KEY',        'HDOOL0u/KQnuVxUTP7Eg7mGFzA9i8CE0vaLukqScIDHv1YbeoeOVr9M6q63KTQGQPsOEieHVUQ9w7gDElXfk8Q==');
define('AUTH_SALT',        'OGsbvNmf8R4rxNmlyRpSCYVtzvDNyxrZN0sJcKyqitK3iSh1x05t2EosI15hxi00cpqQUNQEw6+qr15ZrzvBiQ==');
define('SECURE_AUTH_SALT', 'XCCvXsTj1rYwXzbir9G9JF0jYmyE/sBlDe6GbSssUL+IeKaup4UW2B2+n6aM6WhJJHrEb3+coyk0pKPaHQYT8g==');
define('LOGGED_IN_SALT',   'kfo8sPTVsZiRfRoXogHUwcZoZDtkjgFwbBNctsTwgyMxrI1rGPlXvzuz2uZOHA5bw5ix8Ep8Cw26N7KrFKUZJg==');
define('NONCE_SALT',       'O35wjJmSn69lQq0Mm2vF+6WT5x1v5gcNibOqnN8q1KvVWo5DWr0SbnabLTgXxX8DXp6QgIMRb0g3+4vYPSSoZw==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
