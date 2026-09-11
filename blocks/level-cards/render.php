<?php
/**
 * Three level cards: GCSE Foundation, GCSE Higher, A-level.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
?>
<section class="mwm-levels" aria-label="Choose your level">
	<div class="mwm-levels__grid">
		<?php foreach ( mwm_levels() as $slug => $level ) : ?>
			<div class="mwm-level">
				<div>
					<div class="mwm-level__num"><?php echo esc_html( $level['number'] ); ?></div>
					<h3 class="mwm-level__title"><?php echo esc_html( $level['name'] ); ?></h3>
					<p class="mwm-level__blurb"><?php echo esc_html( $level['blurb'] ); ?></p>
				</div>
				<a href="<?php echo esc_url( mwm_browse_url( $slug ) ); ?>" class="mwm-level__go" aria-label="<?php echo esc_attr( 'Browse ' . $level['name'] ); ?>"><span aria-hidden="true">→</span></a>
			</div>
		<?php endforeach; ?>
	</div>
</section>
