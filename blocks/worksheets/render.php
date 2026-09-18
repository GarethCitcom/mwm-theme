<?php
/**
 * Worksheets: every worksheet as a grid, filtered by level and topic. First page server-rendered; more on scroll.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'worksheets' );

$per_page = 24;
$levels   = mwm_levels();
$level    = sanitize_key( (string) ( $_GET['level'] ?? 'all' ) );
if ( $level !== 'all' && ! isset( $levels[ $level ] ) ) {
	$level = 'all';
}
$topic  = sanitize_title( (string) ( $_GET['topic'] ?? '' ) );
$topics = mwm_topics();
$result = mwm_query_worksheets_paged( [ 'level' => $level === 'all' ? '' : $level, 'topic' => $topic, 'per_page' => $per_page, 'page' => 1 ] );
$opts   = [ 'all' => 'All' ];
foreach ( $levels as $slug => $l ) {
	$opts[ $slug ] = $l['name'];
}
?>
<div class="mwm-page" data-worksheets data-total="<?php echo (int) $result['total']; ?>" data-pages="<?php echo (int) $result['pages']; ?>" data-page="1" data-level="<?php echo esc_attr( $level ); ?>" data-topic="<?php echo esc_attr( $topic ); ?>">
	<?php echo mwm_breadcrumb( [ [ 'label' => 'Home', 'url' => home_url( '/' ) ], [ 'label' => 'Worksheets' ] ] ); ?>
	<h1 class="mwm-h1">Worksheets</h1>
	<p class="mwm-intro">Every worksheet as a free PDF, with worked answers where they exist. Pick a level and a topic, then print it or work through it on screen.</p>

	<?php echo mwm_segmented( $opts, $level, 'Study level', 'level' ); ?>
	<div class="mwm-chips">
		<?php echo mwm_chip( 'All topics', ! $topic, [ 'topic' => '' ] ); ?>
		<?php foreach ( $topics as $t ) { echo mwm_chip( $t['name'], $topic === $t['slug'], [ 'topic' => $t['slug'] ], 'lg', $t['icon'] ? mwm_topic_icon( $t['icon'] ) : '' ); } ?>
	</div>
	<div class="mwm-count" data-count><?php echo $result['total'] === 1 ? '1 worksheet' : $result['total'] . ' worksheets'; ?></div>

	<div class="mwm-grid3 mwm-grid3--28" data-grid<?php echo $result['items'] ? '' : ' hidden'; ?>>
		<?php foreach ( $result['items'] as $w ) { echo mwm_worksheet_card( $w ); } ?>
	</div>
	<div class="mwm-empty mwm-empty--28" data-empty<?php echo $result['items'] ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title">No worksheets here yet</p>
		<p class="mwm-empty__body">Try another topic or level — new worksheets are added with each lesson.</p>
	</div>
	<div class="mwm-more" data-more<?php echo $result['pages'] > 1 ? '' : ' hidden'; ?>>
		<button type="button" class="mwm-btn mwm-btn--secondary">Show more worksheets</button>
		<span class="mwm-more__status" data-more-status aria-live="polite"></span>
	</div>
	<div class="mwm-sentinel" data-sentinel aria-hidden="true"></div>
</div>
