<?php
/**
 * Register every block in /blocks/<name>/block.json (ACF blocks with PHP render templates).
 */

defined( 'ABSPATH' ) || exit;

function mwm_block_dirs(): array {
	$dirs = glob( MWM_THEME_DIR . '/blocks/*/block.json' ) ?: [];
	return array_map( 'dirname', $dirs );
}

add_action( 'init', static function () {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}
	foreach ( mwm_block_dirs() as $dir ) {
		register_block_type( $dir );
	}
}, 10 );

/**
 * Per-block front-end scripts: blocks/<name>/script.js is enqueued only when the block renders.
 */
function mwm_block_script( string $block_name, array $deps = [ 'mwm-theme' ] ): void {
	$file = MWM_THEME_DIR . '/blocks/' . $block_name . '/script.js';
	if ( ! file_exists( $file ) ) {
		return;
	}
	$handle = 'mwm-block-' . $block_name;
	if ( ! wp_script_is( $handle, 'enqueued' ) ) {
		wp_enqueue_script( $handle, MWM_THEME_URI . '/blocks/' . $block_name . '/script.js', $deps, (string) filemtime( $file ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	}
}

/**
 * Small wrapper so render templates can read block fields with a default.
 */
function mwm_field( string $name, $default = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$v = get_field( $name, $post_id );
	return ( $v === null || $v === '' || $v === false ) ? $default : $v;
}

/**
 * Are we rendering inside the block editor preview?
 */
function mwm_is_preview( array $block = [] ): bool {
	return ! empty( $block['data']['is_preview'] ) || ( function_exists( 'acf_is_block_editor' ) && acf_is_block_editor() ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_GET['context'] ) && $_GET['context'] === 'edit' );
}
