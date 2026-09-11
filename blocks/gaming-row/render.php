<?php
/**
 * Latest Gaming & Story Maths videos (4-up grid; horizontal scroll on mobile).
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$heading = mwm_field( 'heading', 'Gaming & Story Maths' );
$sub     = mwm_field( 'sub', 'Maths inside Roblox, Minecraft and stories, each mapped to a real topic.' );
$link    = mwm_field( 'link_label', 'See all' );
$count   = (int) mwm_field( 'count', 4 );
$cards   = mwm_query_lessons( [ 'format' => 'gaming', 'per_page' => $count ] );
if ( ! $cards ) {
	return;
}
?>
<section id="gaming" class="mwm-section" aria-labelledby="mwm-gaming-title">
	<div class="mwm-section__head">
		<div>
			<h2 id="mwm-gaming-title" class="mwm-h2"><?php echo esc_html( $heading ); ?></h2>
			<p class="mwm-section__sub mwm-section__sub--12"><?php echo esc_html( $sub ); ?></p>
		</div>
		<?php echo mwm_arrow_link( mwm_page_url( 'gaming' ), $link ); ?>
	</div>
	<div class="mwm-gaming-grid">
		<?php foreach ( $cards as $card ) { echo mwm_gaming_card( $card ); } ?>
	</div>
</section>
