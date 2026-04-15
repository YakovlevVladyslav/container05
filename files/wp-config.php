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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

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
define( 'AUTH_KEY',         '?-%owJ+u%CsH!Qu7]q,}3$B>;;l,GlPvg=:`Ue5P|pP[Egim?0Cwnbq.%7AM%M<4' );
define( 'SECURE_AUTH_KEY',  'U#HX>&&~_4f?4q^E.n,#5u|b6,yL)3*.X4,]ddf)<9rpYX2Yzzd=}.Dqo)AvuU@0' );
define( 'LOGGED_IN_KEY',    'u`EBs_L%)UL $9S<gsw&r%:@AjsOe%f=pd23TS$Uv(h`+G>N[_EUJE#<do!&q()f' );
define( 'NONCE_KEY',        '0NE=PiCr[RQkE__[&2}O~}9j=cz;A|h8Px+s(ps9N~Je$p[X6{XgV0kh V)){u_o' );
define( 'AUTH_SALT',        '~+K$m8num~bp>H_>s[C_dSS0uBp)!K$:u*35&R~<P67;FX;4?k8OY-c>vMcm[vc ' );
define( 'SECURE_AUTH_SALT', 'orT!V2msTAtCCw}.E+C<s^[00aF$5m~Jp0&{Bp_;?JcVC75w#h{vSE}*`[z4^{r>' );
define( 'LOGGED_IN_SALT',   'f0s&4r!E,%kCmR3mv^VFNy[p.4y-$;})LI^#L1eHY(G7`s.5Q[;}3YLzp1EMrRI6' );
define( 'NONCE_SALT',       'O`{s.4:-T8N48^xMDtc|4PH3Q/yN~9^V#R 2i$h!!MW=LYD?Uy/?$=y1Li1#EeEI' );

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
