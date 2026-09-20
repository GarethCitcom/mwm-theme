<?php
/**
 * "Find your next lightbulb moment" — a grid of lesson cards.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$heading = mwm_field( 'heading', 'Find your next lightbulb moment' );
$sub     = mwm_field( 'sub', 'Clear examples. One topic at a time.' );
$link    = mwm_field( 'link_label', 'Browse all videos' );
$ids     = (array) mwm_field( 'lessons', [] );
// Picks made in the Studio ("Home page") win over the block's own field.
$home = function_exists( 'mwm_home_settings' ) ? mwm_home_settings() : [];
if ( ! empty( $home['featured'] ) ) {
	$ids = $home['featured'];
}
$count   = (int) mwm_field( 'count', 6 );
$cards   = $ids ? mwm_query_lessons( [ 'include' => array_map( 'intval', $ids ) ] ) : mwm_query_lessons( [ 'format' => 'lesson', 'per_page' => $count ] );
?>
<section class="mwm-section mwm-section--featured" aria-labelledby="mwm-featured-title">
	<div class="mwm-section__head">
		<div>
			<h2 id="mwm-featured-title" class="mwm-h2"><?php echo esc_html( $heading ); ?></h2>
			<p class="mwm-section__sub"><?php echo esc_html( $sub ); ?></p>
		</div>
		<?php echo mwm_arrow_link( mwm_page_url( 'browse' ), $link ); ?>
	</div>
	<?php if ( $cards ) : ?>
		<div class="mwm-grid3">
			<?php foreach ( $cards as $card ) { echo mwm_video_card( $card ); } ?>
		</div>
	<?php else : ?>
		<p class="mwm-avail" style="margin-top:24px">Lessons are on their way — new videos land on YouTube first.</p>
	<?php endif; ?>
</section>
