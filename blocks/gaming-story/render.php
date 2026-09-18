<?php
/**
 * Gaming & Story Maths: theme filter, grid, gaming shorts row. First page server-rendered; more on scroll.
 * Until there are lesson-length gaming videos, the grid shows the Roblox/Minecraft/Story shorts instead.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'gaming-story' );

$per_page = 24;
$heading  = mwm_field( 'heading', 'Gaming & Story Maths' );
$sub      = mwm_field( 'sub', 'Real maths inside Roblox, Minecraft and stories — every video mapped to a GCSE topic, so the fun counts towards your revision.' );
$cta      = mwm_field( 'cta_text', 'More gaming lessons are on the way — subscribe to catch them first.' );
$shorts_h = mwm_field( 'shorts_heading', 'Gaming shorts in Quick Maths' );
$themes   = [ 'all' => 'All', 'roblox' => 'Roblox', 'minecraft' => 'Minecraft', 'story' => 'Story' ];
$theme    = sanitize_key( (string) ( $_GET['theme'] ?? 'all' ) );
if ( ! isset( $themes[ $theme ] ) ) {
	$theme = 'all';
}
$theme_q  = $theme === 'all' ? array_keys( array_slice( $themes, 1 ) ) : [ $theme ];
$has_long = (bool) mwm_query_lessons( [ 'format' => 'gaming', 'per_page' => 1 ] );
$format   = $has_long ? 'gaming' : 'short';
$noun     = $has_long ? 'video' : 'short';
$result   = mwm_query_lessons_paged( [ 'format' => $format, 'theme' => $has_long ? ( $theme === 'all' ? '' : $theme ) : $theme_q, 'per_page' => $per_page, 'page' => 1 ] );
$shorts   = $has_long ? array_slice( mwm_query_lessons( [ 'format' => 'short', 'theme' => array_keys( array_slice( $themes, 1 ) ), 'per_page' => 4 ] ), 0, 4 ) : [];
$yt       = mwm_youtube_channel_url();
?>
<div class="mwm-page" data-gaming data-total="<?php echo (int) $result['total']; ?>" data-pages="<?php echo (int) $result['pages']; ?>" data-page="1" data-format="<?php echo esc_attr( $format ); ?>" data-theme="<?php echo esc_attr( $theme ); ?>" data-noun="<?php echo esc_attr( $noun ); ?>">
	<h1 class="mwm-h1 mwm-h1--flush"><?php echo esc_html( $heading ); ?></h1>
	<p class="mwm-intro"><?php echo esc_html( $sub ); ?></p>
	<div class="mwm-chips mwm-chips--32">
		<?php foreach ( $themes as $k => $label ) { echo mwm_chip( $label, $k === $theme, [ 'theme' => $k ] ); } ?>
	</div>
	<div class="mwm-count" data-count><?php echo $result['total'] === 1 ? "1 $noun" : $result['total'] . " {$noun}s"; ?><?php echo $has_long ? '' : ' · tap one to play it here'; ?></div>
	<div class="<?php echo $has_long ? 'mwm-grid3 mwm-grid3--28' : 'mwm-shorts-grid'; ?>" data-grid<?php echo $result['items'] ? '' : ' hidden'; ?>>
		<?php foreach ( $result['items'] as $v ) { echo $has_long ? mwm_gaming_card( $v, 'level' ) : mwm_short_card( $v, 'grid' ); } ?>
	</div>
	<div class="mwm-empty mwm-empty--28" data-empty<?php echo $result['items'] ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title">No <span data-empty-theme><?php echo esc_html( $theme === 'all' ? 'gaming' : $themes[ $theme ] ); ?></span> <?php echo esc_html( $has_long ? 'lessons' : 'shorts' ); ?> yet</p>
		<p class="mwm-empty__body">They’re being recorded now — new videos land on YouTube first.</p>
	</div>
	<div class="mwm-more" data-more<?php echo $result['pages'] > 1 ? '' : ' hidden'; ?>>
		<button type="button" class="mwm-btn mwm-btn--secondary">Show more</button>
		<span class="mwm-more__status" data-more-status aria-live="polite"></span>
	</div>
	<div class="mwm-sentinel" data-sentinel aria-hidden="true"></div>
	<div class="mwm-cta">
		<p class="mwm-cta__text"><?php echo esc_html( $cta ); ?></p>
		<?php echo mwm_button( $yt, 'Subscribe on YouTube', 'primary', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
	</div>
	<?php if ( $shorts ) : ?>
		<div class="mwm-section__head mwm-gaming__shorts-head">
			<h2 class="mwm-h2"><?php echo esc_html( $shorts_h ); ?></h2>
			<?php echo mwm_arrow_link( mwm_page_url( 'quick-maths' ), 'All Quick Maths' ); ?>
		</div>
		<div class="mwm-shorts-grid mwm-shorts-grid--row">
			<?php foreach ( $shorts as $s ) { echo mwm_short_card( $s, 'row' ); } ?>
		</div>
	<?php endif; ?>
</div>
