<?php
/**
 * Maths with Melissa — theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

define( 'MWM_THEME_VERSION', '1.0.0' );
define( 'MWM_THEME_DIR', get_theme_file_path() );
define( 'MWM_THEME_URI', get_theme_file_uri() );

require_once MWM_THEME_DIR . '/inc/icons.php';
require_once MWM_THEME_DIR . '/inc/template-tags.php';
require_once MWM_THEME_DIR . '/inc/blocks.php';
require_once MWM_THEME_DIR . '/inc/block-fields.php';

add_action( 'after_setup_theme', static function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'script', 'style' ] );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/mwm.css' );
	remove_theme_support( 'core-block-patterns' );
	add_image_size( 'mwm-thumb', 1280, 720, true );
} );

/**
 * Front-end assets.
 */
add_action( 'wp_enqueue_scripts', static function () {
	$css = MWM_THEME_DIR . '/assets/css/mwm.css';
	$js  = MWM_THEME_DIR . '/assets/js/mwm.js';
	wp_enqueue_style( 'mwm-theme', MWM_THEME_URI . '/assets/css/mwm.css', [], file_exists( $css ) ? (string) filemtime( $css ) : MWM_THEME_VERSION );
	wp_enqueue_script( 'mwm-theme', MWM_THEME_URI . '/assets/js/mwm.js', [], file_exists( $js ) ? (string) filemtime( $js ) : MWM_THEME_VERSION, [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_localize_script( 'mwm-theme', 'MWM', [
		'rest'     => esc_url_raw( rest_url( 'mwm/v1/' ) ),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
		'signedIn' => is_user_logged_in(),
		'loginUrl' => wp_login_url( function_exists( 'mwm_page_url' ) ? mwm_page_url( 'my-learning' ) : home_url( '/' ) ),
	] );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
} , 20 );

/**
 * Apply the saved theme before first paint (no flash of the wrong mode).
 */
add_action( 'wp_head', static function () {
	echo '<script>(function(){try{var t=localStorage.getItem("mwm-theme");if(t==="dark"){document.documentElement.setAttribute("data-theme","dark");}}catch(e){}})();</script>' . "\n";
	echo '<meta name="theme-color" content="#FFFFFF" media="(prefers-color-scheme: light)"><meta name="theme-color" content="#171717" media="(prefers-color-scheme: dark)">' . "\n";
}, 0 );

/**
 * Tidy the head.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
add_filter( 'emoji_svg_url', '__return_false' );

/**
 * Body classes for page-specific tweaks (e.g. Home's larger mobile H2).
 */
add_filter( 'body_class', static function ( array $classes ): array {
	$classes[] = 'mwm';
	if ( is_front_page() ) {
		$classes[] = 'mwm-home';
	}
	if ( function_exists( 'mwm_page_url' ) ) {
		$pages = (array) get_option( 'mwm_pages', [] );
		foreach ( $pages as $key => $id ) {
			if ( is_page( (int) $id ) ) {
				$classes[] = 'mwm-page-' . $key;
			}
		}
	}
	return $classes;
} );

/**
 * No WordPress toolbar on the site itself for editors (Kym works in the Studio). Administrators keep it.
 */
add_filter( 'show_admin_bar', static fn( $show ) => current_user_can( 'manage_options' ) ? $show : false );

/**
 * Search only lessons.
 */
add_action( 'pre_get_posts', static function ( WP_Query $q ) {
	if ( ! is_admin() && $q->is_main_query() && $q->is_search() ) {
		$q->set( 'post_type', 'mwm_lesson' );
		$q->set( 'posts_per_page', 24 );
	}
} );

/**
 * Block category for the theme's sections.
 */
add_filter( 'block_categories_all', static function ( array $cats ): array {
	array_unshift( $cats, [ 'slug' => 'mwm', 'title' => 'Maths with Melissa', 'icon' => null ] );
	return $cats;
} );

/**
 * Theme-flavoured login screen so "Sign in" from My Learning stays on-brand.
 */
add_action( 'login_enqueue_scripts', static function () {
	$css = MWM_THEME_DIR . '/assets/css/login.css';
	if ( file_exists( $css ) ) {
		wp_enqueue_style( 'mwm-login', MWM_THEME_URI . '/assets/css/login.css', [], (string) filemtime( $css ) );
	}
} );
add_filter( 'login_headerurl', static fn() => home_url( '/' ) );
add_filter( 'login_headertext', static fn() => 'Maths with Melissa' );
