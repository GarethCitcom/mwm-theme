<?php
/**
 * Quick Maths: ink hero, level filter, 9:16 grid of shorts.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'quick-maths' );

$heading = mwm_field( 'heading', 'Quick Maths' );
$sub     = mwm_field( 'sub', 'Under a minute, straight to the point. One skill per short, no filler.' );
$cta     = mwm_field( 'cta_text', 'New shorts most weeks — they land on YouTube first.' );
$shorts  = mwm_query_lessons( [ 'format' => 'short' ] );
$yt      = mwm_youtube_channel_url();
?>
<div class="mwm-qm-hero">
	<div class="mwm-qm-hero__inner">
		<img src="<?php echo esc_url( MWM_THEME_URI . '/assets/img/icon-white.svg' ); ?>" alt="" class="mwm-qm-hero__icon" width="44" height="44">
		<div>
			<h1 class="mwm-qm-hero__h1"><?php echo esc_html( $heading ); ?></h1>
			<p class="mwm-qm-hero__sub"><?php echo esc_html( $sub ); ?></p>
		</div>
	</div>
</div>
<div class="mwm-page" data-quick-maths>
	<?php echo mwm_segmented( [ 'all' => 'All', 'gcse-foundation' => 'GCSE Foundation', 'gcse-higher' => 'GCSE Higher' ], 'all', 'Study level', 'level' ); ?>
	<div class="mwm-count" data-count><?php echo count( $shorts ); ?> shorts · every one opens on YouTube</div>
	<?php if ( $shorts ) : ?>
		<div class="mwm-shorts-grid" data-grid>
			<?php foreach ( $shorts as $s ) { echo mwm_short_card( $s, 'grid' ); } ?>
		</div>
	<?php endif; ?>
	<div class="mwm-empty mwm-empty--28" data-empty<?php echo $shorts ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title" data-empty-title><?php echo $shorts ? 'No shorts for this level yet' : 'Shorts are on their way'; ?></p>
		<p class="mwm-empty__body">They’re being recorded now — new shorts land on YouTube first.</p>
	</div>
	<div class="mwm-cta">
		<p class="mwm-cta__text"><?php echo esc_html( $cta ); ?></p>
		<?php echo mwm_button( $yt, 'Subscribe on YouTube', 'primary', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
	</div>
</div>
