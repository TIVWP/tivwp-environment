<?php
/**
 * Plugin Name: TIVWP Environment
 * Plugin URI: https://github.com/TIVWP/tivwp-environment
 * Description: Setup WordPress environment (a must-use plugin)
 * Version: 14.07.18
 * Author: tivnet
 * Author URI: https://profiles.wordpress.org/tivnet
 * License: GPL2
 * *
 * This file should go to the mu-plugins folder
 * *
 * Copyright 2014 Gregory Karpinsky (http://www.tiv.net/)
 */

/**
 * Exit if accessed directly
 */
defined( 'ABSPATH' ) || exit;

/**
 * For "Skeleton" folder structure, when WP_CONTENT_DIR and WP_CONTENT_URL are redefined:
 * Allow using standard WP themes without copying them to the custom "content" folder.
 * @link https://github.com/markjaquith/WordPress-Skeleton/blob/master/content/mu-plugins/register-theme-directory.php
 */
register_theme_directory( ABSPATH . 'wp-content/themes/' );

/**
 * Do not create revisions of the posts
 * @link http://codex.wordpress.org/Revision_Management
 */
if ( ! defined( 'WP_POST_REVISIONS' ) ) {
	define( 'WP_POST_REVISIONS', false );
}

/**
 * Disable file editing in admin
 * @link http://codex.wordpress.org/Hardening_WordPress
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/**
 * Disable autosave
 */
add_action( 'wp_print_scripts', function () {
	wp_dequeue_script( 'autosave' );
	wp_deregister_script( 'autosave' );
} );

/**
 * Remove meta name="generator" from header
 * @see wp_generator()
 */
remove_filter( 'wp_head', 'wp_generator' );

/**
 * Remove link rel="wlwmanifest" from header
 * @see wlwmanifest_link()
 */
remove_action( 'wp_head', 'wlwmanifest_link' );

/**
 * Remove link rel="EditURI" from header
 * @see rsd_link()
 */
remove_action( 'wp_head', 'rsd_link' );

/**
 * Remove link rel='prev', rel='next'
 * @see  adjacent_posts_rel_link_wp_head()
 */
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

/**
 * Remove link rel="alternate" type="application/rss+xml" for posts and comments feeds
 * @see feed_links()
 * @see feed_links_extra()
 */
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

/**
 * Remove link rel='shortlink' and 'Link:' HTTP header
 * @see wp_shortlink_wp_head()
 * @see wp_shortlink_header()
 */
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );


/**
 * Remove X-Pingback HTTP header
 */
add_filter( 'wp_headers', function ( $_ ) {
	unset( $_['X-Pingback'] );
	return $_;
} );

/**
 * Add custom excerpt metabox to pages
 */
add_action( 'admin_menu', function () {
		add_meta_box(
			'postexcerpt',
			__( 'Excerpt' ),
			'post_excerpt_meta_box',
			'page',
			'normal',
			'core'
		);
	}
);

/**
 * Class TIVWP_Debug
 * Useful methods for debug, in case you have no xdebug handy :-)
 */
class TIVWP_Debug {

	/**
	 * @param     $var
	 * @param int $trace_level
	 */
	static function print_var( $var, $trace_level = 0 ) {

		$dbt = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
		if ( isset( $dbt[$trace_level] ) ) {
			echo basename( $dbt[$trace_level]['file'] ) . ' : ' . $dbt[$trace_level]['line'] . "\n";
		}
		if ( ! empty( $var ) ) {
			print_r( $var );
		}
		else {
			var_dump( $var );
		}
		echo "\n";
	}

	/**
	 * @param      $var
	 * @param bool $die
	 */
	static function print_var_html( $var, $die = false ) {
		//        echo '<xmp style="overflow:scroll;">';
		echo '<xmp style="overflow:visible;">';
		self::print_var( $var, 1 );
		echo '</xmp>';

		if ( $die ) {
			die;
		}
	}

	/**
	 * @param $var
	 */
	static function tivwp_error_log_dump( $var ) {
		ob_start();
		self::print_var( $var, 1 );
		error_log( ob_get_clean() );
	}

	/**
	 * @param int $flag
	 */
	static function tivwp_error_log_trace( $flag = DEBUG_BACKTRACE_IGNORE_ARGS ) {
		error_log( print_r( debug_backtrace( $flag ), true ) );
	}

} // class

# --- EOF
