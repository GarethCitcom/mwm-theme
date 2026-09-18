<?php
/**
 * Gaming & Story Maths: theme filter, video grid, gaming shorts row.
 * Until there are lesson-length gaming videos, the grid shows the Roblox/Minecraft/Story shorts instead.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'gaming-story' );

$heading  = mwm_field( 'heading', 'Gaming & Story Maths' );
$sub      = mwm_field( 'sub', 'Real maths inside Roblox, Minecraft and stories — every video mapped to a GCSE topic, so the fun counts towards your revision.' );
$cta      = mwm_field( 'cta_text', 'More gaming lessons are on the way — subscribe to catch them first.' );
$shorts_h = mwm_field( 'shorts_heading', 'Gaming shorts in Quick Maths' );
$themes   = [ 'all' => 'All', 'roblox' => 'Roblox', 'minecraft' => 'Minecraft', 'story' => 'Story' ];
$videos   = mwm_query_lessons( [ 'format' => 'gaming' ] );
$themed   = mwm_query_lessons( [ 'format' => 'short', 'theme' => array_keys( array_slice( $themes, 1 ) ) ] );
$fallback = ! $videos && $themed; // no long-form gaming lessons yet: show the themed shorts in the main grid
$items    = $fallback ? $themed : $videos;
$noun     = $fallback ? 'short' : 'video';
$shorts   = $fallback ? [] : array_slice( $themed ?: mwm_query_lessons( [ 'format' => 'short', 'per_page' => 4 ] ), 0, 4 );
$yt       = mwm_youtube_channel_url();
?>
<div class="mwm-page" data-gaming data-noun="<?php echo esc_attr( $noun ); ?>">
	<h1 class="mwm-h1 mwm-h1--flush"><?php echo esc_html( $heading ); ?></h1>
	<p class="mwm-intro"><?php echo esc_html( $sub ); ?></p>
	<div class="mwm-chips mwm-chips--32">
		<?php foreach ( $themes as $k => $label ) { echo mwm_chip( $label, $k === 'all', [ 'theme' => $k ] ); } ?>
	</div>
	<div class="mwm-count" data-count><?php echo count( $items ) === 1 ? "1 $noun" : count( $items ) . " {$noun}s"; ?><?php echo $fallback ? ' · tap one to play it here' : ''; ?></div>
	<?php if ( $fallback ) : ?>
		<div class="mwm-shorts-grid" data-grid>
			<?php foreach ( $items as $s ) : ?>
				<div data-theme="<?php echo esc_attr( $s['theme_slug'] ); ?>" style="display:contents"><?php echo mwm_short_card( $s, 'grid' ); ?></div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="mwm-grid3 mwm-grid3--28" data-grid>
			<?php foreach ( $items as $v ) : ?>
				<div data-theme="<?php echo esc_attr( $v['theme_slug'] ); ?>" style="display:contents"><?php echo mwm_gaming_card( $v, 'level' ); ?></div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<div class="mwm-empty mwm-empty--28" data-empty<?php echo $items ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title">No <span data-empty-theme><?php echo $items ? 'Roblox' : 'gaming'; ?></span> <?php echo esc_html( $noun === 'short' ? 'shorts' : 'lessons' ); ?> yet</p>
		<p class="mwm-empty__body">They’re being recorded now — new videos land on YouTube first.</p>
	</div>
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
