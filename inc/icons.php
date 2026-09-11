<?php
/**
 * Inline SVG icons, transcribed from the prototypes.
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param string $name  Icon key.
 * @param int    $size  Rendered size in px.
 * @param array  $attrs Extra attributes (e.g. ['stroke' => '#FFFFFF']).
 */
function mwm_icon( string $name, int $size = 16, array $attrs = [] ): string {
	$stroke = $attrs['stroke'] ?? 'currentColor';
	$w      = $attrs['stroke-width'] ?? '1.5';
	$base   = 'fill="none" stroke="' . esc_attr( $stroke ) . '" stroke-width="' . esc_attr( $w ) . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';
	$s      = 'width="' . $size . '" height="' . $size . '"';
	switch ( $name ) {
		case 'search':
			return "<svg $s viewBox=\"0 0 16 16\" $base><circle cx=\"7\" cy=\"7\" r=\"4.6\"></circle><line x1=\"10.4\" y1=\"10.4\" x2=\"13.6\" y2=\"13.6\"></line></svg>";
		case 'moon':
			return "<svg $s viewBox=\"0 0 18 18\" $base><path d=\"M14.5 10.8A6 6 0 0 1 7.2 3.5a6 6 0 1 0 7.3 7.3z\"></path></svg>";
		case 'sun':
			return "<svg $s viewBox=\"0 0 18 18\" $base><circle cx=\"9\" cy=\"9\" r=\"3.6\"></circle><line x1=\"9\" y1=\"1.5\" x2=\"9\" y2=\"3.5\"></line><line x1=\"9\" y1=\"14.5\" x2=\"9\" y2=\"16.5\"></line><line x1=\"1.5\" y1=\"9\" x2=\"3.5\" y2=\"9\"></line><line x1=\"14.5\" y1=\"9\" x2=\"16.5\" y2=\"9\"></line><line x1=\"3.7\" y1=\"3.7\" x2=\"5.1\" y2=\"5.1\"></line><line x1=\"12.9\" y1=\"12.9\" x2=\"14.3\" y2=\"14.3\"></line><line x1=\"3.7\" y1=\"14.3\" x2=\"5.1\" y2=\"12.9\"></line><line x1=\"12.9\" y1=\"5.1\" x2=\"14.3\" y2=\"3.7\"></line></svg>";
		case 'menu':
			return "<svg $s viewBox=\"0 0 20 20\" $base><line x1=\"3\" y1=\"5.5\" x2=\"17\" y2=\"5.5\"></line><line x1=\"3\" y1=\"10\" x2=\"17\" y2=\"10\"></line><line x1=\"3\" y1=\"14.5\" x2=\"17\" y2=\"14.5\"></line></svg>";
		case 'close':
			return "<svg $s viewBox=\"0 0 20 20\" $base><line x1=\"5\" y1=\"5\" x2=\"15\" y2=\"15\"></line><line x1=\"15\" y1=\"5\" x2=\"5\" y2=\"15\"></line></svg>";
		case 'play':
			return "<svg $s viewBox=\"0 0 20 20\" fill=\"" . esc_attr( $attrs['fill'] ?? '#FFFFFF' ) . "\" aria-hidden=\"true\" focusable=\"false\"><path d=\"M7 4.5l9 5.5-9 5.5z\"></path></svg>";
		case 'worksheet':
			return "<svg $s viewBox=\"0 0 16 16\" $base><rect x=\"3\" y=\"2\" width=\"10\" height=\"12\" rx=\"1.5\"></rect><path d=\"M5.5 6h5M5.5 9h5\"></path></svg>";
		case 'quiz':
			return "<svg $s viewBox=\"0 0 16 16\" $base><circle cx=\"8\" cy=\"8\" r=\"6\"></circle><path d=\"M5.4 8l1.9 1.9 3.4-3.7\"></path></svg>";
		case 'video':
			return "<svg $s viewBox=\"0 0 16 16\" $base><rect x=\"1.5\" y=\"3\" width=\"13\" height=\"10\" rx=\"2\"></rect><path d=\"M6.5 6l3.5 2-3.5 2z\"></path></svg>";
		case 'tick':
			return "<svg $s viewBox=\"0 0 16 16\" $base><path d=\"M3.5 8.5l3 3 6-7\"></path></svg>";
		case 'download':
			return "<svg $s viewBox=\"0 0 16 16\" $base><path d=\"M8 2v8M4.5 6.5L8 10l3.5-3.5\"></path><line x1=\"3\" y1=\"13\" x2=\"13\" y2=\"13\"></line></svg>";
		case 'chevron-left':
			return "<svg $s viewBox=\"0 0 16 16\" $base><path d=\"M10 3L5 8l5 5\"></path></svg>";
		case 'chevron-right':
			return "<svg $s viewBox=\"0 0 16 16\" $base><path d=\"M6 3l5 5-5 5\"></path></svg>";
		case 'bookmark':
			return "<svg $s viewBox=\"0 0 16 16\" $base><path d=\"M4 2h8v12l-4-3-4 3z\"></path></svg>";
		case 'bars':
			return "<svg $s viewBox=\"0 0 16 16\" $base><line x1=\"3\" y1=\"13\" x2=\"3\" y2=\"9\"></line><line x1=\"8\" y1=\"13\" x2=\"8\" y2=\"4\"></line><line x1=\"13\" y1=\"13\" x2=\"13\" y2=\"7\"></line></svg>";
		case 'calendar':
			return "<svg $s viewBox=\"0 0 16 16\" $base><rect x=\"2\" y=\"3\" width=\"12\" height=\"11\" rx=\"2\"></rect><line x1=\"2\" y1=\"6.5\" x2=\"14\" y2=\"6.5\"></line><line x1=\"5.5\" y1=\"1.5\" x2=\"5.5\" y2=\"4\"></line><line x1=\"10.5\" y1=\"1.5\" x2=\"10.5\" y2=\"4\"></line></svg>";
		// Topic chip icons (14px in the prototype).
		case 'topic-number':
			return "<svg $s viewBox=\"0 0 14 14\" $base><path d=\"M7 2v10M2 7h10\"></path></svg>";
		case 'topic-algebra':
			return "<svg $s viewBox=\"0 0 14 14\" $base><path d=\"M3 3l8 8M11 3l-8 8\"></path></svg>";
		case 'topic-ratio':
			return "<svg $s viewBox=\"0 0 14 14\" $base><circle cx=\"4.5\" cy=\"7\" r=\"2.8\"></circle><circle cx=\"9.5\" cy=\"7\" r=\"2.8\"></circle></svg>";
		case 'topic-geometry':
			return "<svg $s viewBox=\"0 0 14 14\" $base><path d=\"M7 2.5L12 11.5H2z\"></path></svg>";
		case 'topic-probability':
			return "<svg $s viewBox=\"0 0 14 14\" $base><rect x=\"2.5\" y=\"2.5\" width=\"9\" height=\"9\" rx=\"2\"></rect><circle cx=\"5.5\" cy=\"5.5\" r=\"0.6\" fill=\"currentColor\"></circle><circle cx=\"8.5\" cy=\"8.5\" r=\"0.6\" fill=\"currentColor\"></circle></svg>";
		case 'topic-statistics':
			return "<svg $s viewBox=\"0 0 14 14\" $base><path d=\"M3 11.5V8M7 11.5V4M11 11.5V6\"></path></svg>";
		case 'topic-pure':
			return "<svg $s viewBox=\"0 0 14 14\" $base><path d=\"M2.5 7c1.5-3 3-3 4.5 0s3 3 4.5 0\"></path></svg>";
		case 'topic-mechanics':
			return "<svg $s viewBox=\"0 0 14 14\" $base><path d=\"M3 11l8-8M7 3h4v4\"></path></svg>";
	}
	return '';
}

function mwm_topic_icon( string $key, int $size = 14 ): string {
	return $key ? mwm_icon( 'topic-' . $key, $size ) : '';
}
