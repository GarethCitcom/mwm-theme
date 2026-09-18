<?php
/**
 * Ink band with a scrolling strip of Quick Maths shorts.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$heading = mwm_field( 'heading', 'Quick Maths' );
$sub     = mwm_field( 'sub', 'Under a minute, straight to the point.' );
$count   = (int) mwm_field( 'count', 8 );
// Prefer the plain Quick Maths shorts here; the Gaming row above covers the Roblox/Minecraft ones.
$shorts = mwm_query_lessons( [ 'format' => 'short', 'theme' => 'none', 'per_page' => $count ] );
if ( count( $shorts ) < $count ) {
	$shorts = array_merge( $shorts, mwm_query_lessons( [ 'format' => 'short', 'exclude' => array_column( $shorts, 'id' ), 'per_page' => $count - count( $shorts ) ] ) );
}
if ( ! $shorts ) {
	return;
}
$strip_id = 'mwm-quick-strip';
?>
<section id="quick-maths" class="mwm-band" aria-labelledby="mwm-quick-title">
	<div class="mwm-band__inner">
		<div class="mwm-band__head">
			<div class="mwm-band__title">
				<img src="<?php echo esc_url( MWM_THEME_URI . '/assets/img/icon-white.svg' ); ?>" alt="" class="mwm-band__icon" width="44" height="44">
				<div>
					<h2 id="mwm-quick-title" class="mwm-h2 mwm-band__h2"><a href="<?php echo esc_url( mwm_page_url( 'quick-maths' ) ); ?>" style="color:inherit"><?php echo esc_html( $heading ); ?></a></h2>
					<p class="mwm-band__sub"><?php echo esc_html( $sub ); ?></p>
				</div>
			</div>
			<div class="mwm-band__arrows">
				<button type="button" class="mwm-band__arrow" aria-label="Previous" aria-controls="<?php echo esc_attr( $strip_id ); ?>" data-mwm-scroll="prev"><?php echo mwm_icon( 'chevron-left', 16, [ 'stroke' => '#FFFFFF' ] ); ?></button>
				<button type="button" class="mwm-band__arrow" aria-label="Next" aria-controls="<?php echo esc_attr( $strip_id ); ?>" data-mwm-scroll="next"><?php echo mwm_icon( 'chevron-right', 16, [ 'stroke' => '#FFFFFF' ] ); ?></button>
			</div>
		</div>
	</div>
	<div id="<?php echo esc_attr( $strip_id ); ?>" class="mwm-strip">
		<?php foreach ( $shorts as $s ) : ?>
			<a href="<?php echo esc_url( $s['youtube_url'] ); ?>" target="_blank" rel="noopener" class="mwm-short mwm-short--band"<?php echo $s['youtube_id'] ? ' data-short="' . esc_attr( $s['youtube_id'] ) . '" data-short-title="' . esc_attr( $s['title'] ) . '"' : ''; ?>>
				<span class="mwm-short__thumb"><?php echo $s['thumb'] ? mwm_thumb_img( $s['thumb'], '', '(max-width: 700px) 45vw, 190px' ) : '<span aria-hidden="true"></span>'; ?></span>
				<span class="mwm-short__title"><?php echo esc_html( $s['title'] ); ?></span>
				<span class="mwm-short__duration"><?php echo esc_html( $s['duration'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
