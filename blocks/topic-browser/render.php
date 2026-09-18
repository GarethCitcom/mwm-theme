<?php
/**
 * Browse by topic: level switcher + topic chips + live preview panel.
 * Server renders the default level; script.js switches levels and opens previews from the data island.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'topic-browser' );

$heading = mwm_field( 'heading', 'Browse by topic' );
$sub     = mwm_field( 'sub', 'Pick your level, then choose a topic to see every lesson, worksheet and quiz for it.' );
$prefs   = mwm_user_prefs();
$default = $prefs['level'];

// Counts and three sample lessons per topic and level. Cached for a few hours; cleared whenever a lesson changes.
$data = get_transient( 'mwm_topic_browser' );
if ( ! is_array( $data ) ) {
	$data = [];
	foreach ( mwm_levels() as $slug => $level ) {
		$topics = [];
		foreach ( mwm_topics( $slug ) as $t ) {
			$args    = [ 'level' => $slug, 'topic' => $t['slug'], 'format' => 'lesson' ];
			$lessons = mwm_query_lessons( $args + [ 'per_page' => 3 ] );
			$topics[] = [
				'slug'   => $t['slug'],
				'name'   => $t['name'],
				'icon'   => $t['icon'],
				'level'  => $t['level_label'],
				'count'  => count( $lessons ) < 3 ? count( $lessons ) : mwm_count_lessons( $args ),
				'url'    => mwm_browse_url( $slug, $t['slug'] ),
				'lessons'=> array_map( static fn( $c ) => [
					'title' => $c['title'],
					'url'   => $c['url'],
					'thumb' => $c['thumb'],
					'meta'  => trim( $c['level'] . ' · ' . $c['duration'], ' ·' ),
				], $lessons ),
			];
		}
		$data[ $slug ] = [ 'name' => $level['name'], 'topics' => $topics ];
	}
	set_transient( 'mwm_topic_browser', $data, 6 * HOUR_IN_SECONDS );
}
$icons = [];
foreach ( [ 'number', 'algebra', 'ratio', 'geometry', 'probability', 'statistics', 'pure', 'mechanics' ] as $k ) {
	$icons[ $k ] = mwm_topic_icon( $k );
}
?>
<section class="mwm-section" aria-labelledby="mwm-topics-title" data-topic-browser>
	<h2 id="mwm-topics-title" class="mwm-h2"><?php echo esc_html( $heading ); ?></h2>
	<p class="mwm-section__sub mwm-section__sub--12"><?php echo esc_html( $sub ); ?></p>
	<?php
	$opts = [];
	foreach ( mwm_levels() as $slug => $level ) {
		$opts[ $slug ] = $level['name'];
	}
	echo mwm_segmented( $opts, $default, 'Study level', 'level' );
	?>
	<div class="mwm-chips mwm-chips--home" data-topic-chips>
		<?php foreach ( $data[ $default ]['topics'] as $t ) : ?>
			<?php echo mwm_chip( $t['name'], false, [ 'topic' => $t['slug'] ], 'lg', $t['icon'] ? mwm_topic_icon( $t['icon'] ) : '' ); ?>
		<?php endforeach; ?>
	</div>
	<div class="mwm-topic-preview" data-topic-preview hidden></div>
	<?php echo mwm_json_script( 'mwm-topic-browser-data', [ 'levels' => $data, 'icons' => $icons, 'current' => $default ] ); ?>
</section>
