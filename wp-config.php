<?php
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

 // ** START ENVIRONMENT SETUP
 // ** Inspiration: https://gist.github.com/ifamily/1703e43490b50e943923
 // Define Environments
 $environments = array(
     'development' => 'localhost',
     // 'staging' => '',
     'production' => 'rue.uk',
 );
 // Get Server name
 $server_name = $_SERVER['SERVER_NAME'];

 foreach($environments AS $key => $env){
     if(strstr($server_name, $env)){
         define('ENVIRONMENT', $key);
         break;
     }
}

// If no environment is set default to production
if(!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
// Define different DB connection details depending on environment
switch(ENVIRONMENT){
  case 'development':
    define('WP_DEBUG', false);
    break;
  case 'staging':
    define('WP_DEBUG', false);
    break;
  case 'production':
    define('WP_DEBUG', false);
    break;
}
// ** END ENVIRONMENT SETUP


$db = parse_url($_ENV["DATABASE_URL"] ? $_ENV["DATABASE_URL"] : "postgres://rturner@localhost:5432/wp_rue");
// $db = parse_url($_ENV["DATABASE_URL"] ? $_ENV["DATABASE_URL"] : "postgres://rturner@localhost:5432/wp_rue");
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', trim($db["path"],"/"));
/** MySQL database username */
define('DB_USER', $db["user"]);
/** MySQL database password */
define('DB_PASSWORD', $db["pass"]);
/** MySQL hostname */
define('DB_HOST', $db["host"]);
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
define('AUTH_KEY',         getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY'));
define('NONCE_KEY',        getenv('NONCE_KEY'));
define('AUTH_SALT',        getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT'));
define('NONCE_SALT',       getenv('NONCE_SALT'));
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
// define('WP_DEBUG', false);

define('FORCE_SSL_ADMIN', false);
// in some setups HTTP_X_FORWARDED_PROTO might contain
// a comma-separated list e.g. http,https
// so check for https existence
if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
       $_SERVER['HTTPS']='on';

/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
