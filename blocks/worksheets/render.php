<?php
/**
 * Worksheets: every worksheet as a grid, filtered by level and topic.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'worksheets' );

$levels = mwm_levels();
$level  = sanitize_key( (string) ( $_GET['level'] ?? 'all' ) );
if ( $level !== 'all' && ! isset( $levels[ $level ] ) ) {
	$level = 'all';
}
$topic  = sanitize_title( (string) ( $_GET['topic'] ?? '' ) );
$all    = mwm_query_worksheets();
$topics = mwm_topics();
$shown  = array_filter( $all, static fn( $w ) => ( $level === 'all' || $w['level_slug'] === $level ) && ( ! $topic || $w['topic_slug'] === $topic ) );
$opts   = [ 'all' => 'All' ];
foreach ( $levels as $slug => $l ) {
	$opts[ $slug ] = $l['name'];
}
?>
<div class="mwm-page" data-worksheets>
	<?php echo mwm_breadcrumb( [ [ 'label' => 'Home', 'url' => home_url( '/' ) ], [ 'label' => 'Worksheets' ] ] ); ?>
	<h1 class="mwm-h1">Worksheets</h1>
	<p class="mwm-intro">Every worksheet as a free PDF, with worked answers where they exist. Pick a level and a topic, then print it or work through it on screen.</p>

	<?php echo mwm_segmented( $opts, $level, 'Study level', 'level' ); ?>
	<div class="mwm-chips">
		<?php echo mwm_chip( 'All topics', ! $topic, [ 'topic' => '' ] ); ?>
		<?php foreach ( $topics as $t ) { echo mwm_chip( $t['name'], $topic === $t['slug'], [ 'topic' => $t['slug'] ], 'lg', $t['icon'] ? mwm_topic_icon( $t['icon'] ) : '' ); } ?>
	</div>
	<div class="mwm-count" data-count><?php echo count( $shown ) === 1 ? '1 worksheet' : count( $shown ) . ' worksheets'; ?></div>

	<div class="mwm-grid3 mwm-grid3--28" data-grid>
		<?php foreach ( $all as $w ) : ?>
			<div data-level="<?php echo esc_attr( $w['level_slug'] ); ?>" data-topic="<?php echo esc_attr( $w['topic_slug'] ); ?>" style="display:<?php echo in_array( $w, $shown, true ) ? 'contents' : 'none'; ?>"><?php echo mwm_worksheet_card( $w ); ?></div>
		<?php endforeach; ?>
	</div>
	<div class="mwm-empty mwm-empty--28" data-empty<?php echo $shown ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title">No worksheets here yet</p>
		<p class="mwm-empty__body">Try another topic or level — new worksheets are added with each lesson.</p>
	</div>
</div>
