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
define('DB_NAME', 'aedin_wp1');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'J30rIxIZdfX6tk3xUUu9hJEOKXq2XdkPVmXkpQCFBmKhEbfbblZ9LjpKa6TWqjco');
define('SECURE_AUTH_KEY',  '91ZmgHdbeDiGhCMbK7btDz16dl0mfIAB5CzbFsr0cxllgWdgmoAU73sWhd9OyCdt');
define('LOGGED_IN_KEY',    'x2woLcMfo7XrV3ZuK6kJeMN4tclAjwjF9m54EXkWtsvWGSulnJ6t9BrIGbknmkdY');
define('NONCE_KEY',        'Pjw6NjsQ9hFV38YaPS30f1660RuPGt8zCY41j4CtQlsJqEZxujjpYxjOj5q5vx7v');
define('AUTH_SALT',        'hwdAU4WpcUOpPduDETQq3cUnTWZfa2DcEZucuMxRl4XXbbE3Py6LPrqnbFQFBDB1');
define('SECURE_AUTH_SALT', 'qqaHmq129bikqsBQjYaqXeKlywfsrx8lHz04yOhzH9kHQuDXCfHfkn4uzDCzczOh');
define('LOGGED_IN_SALT',   'aiNlSknqN6irxpcVcLMJP9PrdbGHgm1KO05P7r8UEqEqfr0akUmf7p4Ioc17djxp');
define('NONCE_SALT',       'RLkjT2Pz4jcE3HMee9f5e0mnraek4YQIC9BmTjuQYzvY5NfVz1G8tukzUyzwIS9G');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
