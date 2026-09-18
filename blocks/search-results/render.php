<?php
/**
 * Search results: lesson cards for the query.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
global $wp_query;
$q     = get_search_query();
$cards = [];
if ( $wp_query && $wp_query->posts ) {
	foreach ( $wp_query->posts as $p ) {
		$c = mwm_lesson_card( $p );
		if ( $c ) {
			$cards[] = $c;
		}
	}
}
$total = $wp_query ? (int) $wp_query->found_posts : count( $cards );
?>
<div class="mwm-page">
	<?php echo mwm_breadcrumb( [ [ 'label' => 'Home', 'url' => home_url( '/' ) ], [ 'label' => 'Search' ] ] ); ?>
	<h1 class="mwm-h1"><?php echo $q ? 'Results for “' . esc_html( $q ) . '”' : 'Search lessons'; ?></h1>
	<p class="mwm-intro"><?php echo $q ? esc_html( $total === 1 ? '1 lesson' : $total . ' lessons' ) . ' matched. Try a topic name, like “fractions” or “vectors”.' : 'Type a topic, like “fractions” or “vectors”, into the search box above.'; ?></p>
	<?php if ( $cards ) : ?>
		<h2 class="screen-reader-text">Results</h2>
		<div class="mwm-grid3 mwm-grid3--32">
			<?php foreach ( $cards as $c ) { echo mwm_video_card( $c ); } ?>
		</div>
		<?php
		$pagination = paginate_links( [ 'type' => 'list', 'prev_text' => '← Previous', 'next_text' => 'Next →' ] );
		if ( $pagination ) {
			echo '<nav aria-label="More results" class="mwm-pagination">' . $pagination . '</nav>';
		}
		?>
	<?php elseif ( $q ) : ?>
		<div class="mwm-empty">
			<p class="mwm-empty__title">Nothing matched “<?php echo esc_html( $q ); ?>”</p>
			<p class="mwm-empty__body">Try a shorter word, or browse by level and topic instead.</p>
			<?php echo mwm_button( mwm_page_url( 'browse' ), 'Browse all lessons', 'secondary', [ 'style' => 'margin-top:20px' ] ); ?>
		</div>
	<?php endif; ?>
</div>
