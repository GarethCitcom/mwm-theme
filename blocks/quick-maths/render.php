<?php
/**
 * Quick Maths: ink hero, level filter, 9:16 grid of shorts. First page server-rendered; more on scroll.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'quick-maths' );

$per_page = 24;
$heading  = mwm_field( 'heading', 'Quick Maths' );
$sub      = mwm_field( 'sub', 'Under a minute, straight to the point. One skill per short, no filler.' );
$cta      = mwm_field( 'cta_text', 'New shorts most weeks — they land on YouTube first.' );
$level    = sanitize_key( (string) ( $_GET['level'] ?? 'all' ) );
if ( ! in_array( $level, [ 'all', 'gcse-foundation', 'gcse-higher' ], true ) ) {
	$level = 'all';
}
$result = mwm_query_lessons_paged( [ 'format' => 'short', 'level' => $level === 'all' ? '' : $level, 'per_page' => $per_page, 'page' => 1 ] );
$yt     = mwm_youtube_channel_url();
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
<div class="mwm-page" data-quick-maths data-total="<?php echo (int) $result['total']; ?>" data-pages="<?php echo (int) $result['pages']; ?>" data-page="1" data-level="<?php echo esc_attr( $level ); ?>">
	<?php echo mwm_segmented( [ 'all' => 'All', 'gcse-foundation' => 'GCSE Foundation', 'gcse-higher' => 'GCSE Higher' ], $level, 'Study level', 'level' ); ?>
	<div class="mwm-count" data-count><?php echo $result['total'] === 1 ? '1 short' : $result['total'] . ' shorts'; ?> · tap one to play it here</div>
	<div class="mwm-shorts-grid" data-grid<?php echo $result['items'] ? '' : ' hidden'; ?>>
		<?php foreach ( $result['items'] as $s ) { echo mwm_short_card( $s, 'grid' ); } ?>
	</div>
	<div class="mwm-empty mwm-empty--28" data-empty<?php echo $result['items'] ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title"><?php echo $level === 'all' ? 'Shorts are on their way' : 'No shorts for this level yet'; ?></p>
		<p class="mwm-empty__body">They’re being recorded now — new shorts land on YouTube first.</p>
	</div>
	<div class="mwm-more" data-more<?php echo $result['pages'] > 1 ? '' : ' hidden'; ?>>
		<button type="button" class="mwm-btn mwm-btn--secondary">Show more shorts</button>
		<span class="mwm-more__status" data-more-status aria-live="polite"></span>
	</div>
	<div class="mwm-sentinel" data-sentinel aria-hidden="true"></div>
	<div class="mwm-cta">
		<p class="mwm-cta__text"><?php echo esc_html( $cta ); ?></p>
		<?php echo mwm_button( $yt, 'Subscribe on YouTube', 'primary', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
	</div>
</div>
