<?php
/**
 * Learn Maths: level switcher, topic chips, subtopics, filter bar, results.
 * The server renders the first page from the URL; script.js re-queries the REST API on each filter change
 * and loads further pages on scroll.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'browse' );

$per_page   = 24;
$state      = MWM_Rewrites::browse_state();
$level      = $state['level'];
$level_name = mwm_level_name( $level );
$levels     = mwm_levels();
$topics     = mwm_topics( $level );
$topic      = null;
foreach ( $topics as &$t ) {
	$t['subtopics'] = mwm_subtopics( $t['id'] );
	$t['intro']     = (string) get_term_meta( $t['id'], 'intro', true );
	if ( $t['slug'] === $state['topic'] ) {
		$topic = $t;
	}
}
unset( $t );
$state['topic'] = $topic ? $topic['slug'] : '';

$result = mwm_query_lessons_paged( [
	'level'     => $level,
	'topic'     => $state['topic'],
	'subtopic'  => $state['subtopic'],
	'format'    => $state['type'] !== 'all' ? $state['type'] : '',
	'worksheet' => $state['worksheet'],
	'quiz'      => $state['quiz'],
	'per_page'  => $per_page,
	'page'      => 1,
] );
$results = $result['items'];

$page_title = $topic ? $topic['name'] : $level_name;
$page_intro = $topic
	? ( $topic['intro'] ?: 'Every ' . $topic['name'] . ' lesson for this level, with worksheets and quizzes where they exist.' )
	: 'Every lesson for this level, organised by topic. Pick a topic or filter to find exactly what you need.';
$any_filter = $state['subtopic'] || $state['type'] !== 'all' || $state['worksheet'] || $state['quiz'];
$is_alevel  = $level === 'a-level';
$practise   = $topic ? $topic['name'] : $level_name;

$type_labels = [ 'all' => 'All', 'lesson' => 'Lessons', 'short' => 'Quick Maths', 'gaming' => 'Gaming & Story' ];
$level_urls  = [];
foreach ( $levels as $slug => $l ) {
	$level_urls[ $slug ] = mwm_browse_url( $slug );
}
$island = [
	'state'     => $state,
	'level'     => $level,
	'levelName' => $level_name,
	'topics'    => $topics,
	'urls'      => [ 'browse' => mwm_page_url( 'browse' ) ],
	'perPage'   => $per_page,
];
$sub_visible = $topic ? array_slice( $topic['subtopics'], 0, 8 ) : [];
?>
<div class="mwm-page" data-browse data-total="<?php echo (int) $result['total']; ?>" data-pages="<?php echo (int) $result['pages']; ?>" data-page="1">
	<?php
	$crumbs = [ [ 'label' => 'Home', 'url' => home_url( '/' ) ], [ 'label' => 'Learn Maths', 'url' => mwm_page_url( 'browse' ) ] ];
	if ( $topic ) {
		$crumbs[] = [ 'label' => $level_name, 'url' => mwm_browse_url( $level ) ];
		$crumbs[] = [ 'label' => $topic['name'] ];
	} else {
		$crumbs[] = [ 'label' => $level_name ];
	}
	echo mwm_breadcrumb( $crumbs );
	?>
	<h1 class="mwm-h1" data-browse-title><?php echo esc_html( $page_title ); ?></h1>
	<p class="mwm-intro" data-browse-intro><?php echo esc_html( $page_intro ); ?></p>

	<?php echo mwm_segmented( array_map( static fn( $l ) => $l['name'], $levels ), $level, 'Study level', 'level', $level_urls ); ?>

	<div class="mwm-chips" data-browse-topics>
		<?php foreach ( $topics as $t ) : ?>
			<?php echo mwm_chip( $t['name'], $state['topic'] === $t['slug'], [ 'topic' => $t['slug'] ] ); ?>
		<?php endforeach; ?>
	</div>

	<div class="mwm-subtopics" data-browse-subtopics<?php echo $topic && $topic['subtopics'] ? '' : ' hidden'; ?>>
		<span class="mwm-subtopics__label">Subtopics · <span data-sub-total><?php echo $topic ? count( $topic['subtopics'] ) : 0; ?></span></span>
		<div class="mwm-subtopics__list" data-sub-list>
			<?php foreach ( $sub_visible as $s ) : ?>
				<button type="button" class="mwm-subchip<?php echo $state['subtopic'] === $s['slug'] ? ' is-on' : ''; ?>" aria-pressed="<?php echo $state['subtopic'] === $s['slug'] ? 'true' : 'false'; ?>" data-subtopic="<?php echo esc_attr( $s['slug'] ); ?>"><?php echo esc_html( $s['name'] ); ?></button>
			<?php endforeach; ?>
			<?php if ( $topic && count( $topic['subtopics'] ) > 8 ) : ?>
				<button type="button" class="mwm-subchip mwm-subchip--more" data-sub-more>Show all <?php echo count( $topic['subtopics'] ); ?></button>
			<?php endif; ?>
		</div>
	</div>

	<div class="mwm-filterbar">
		<div role="group" aria-label="Content type" class="mwm-group" data-browse-types>
			<?php foreach ( $type_labels as $key => $label ) : ?>
				<button type="button" class="mwm-group__btn<?php echo $state['type'] === $key ? ' is-on' : ''; ?>" aria-pressed="<?php echo $state['type'] === $key ? 'true' : 'false'; ?>" data-type="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php echo mwm_chip( 'Has worksheet', $state['worksheet'], [ 'toggle' => 'worksheet' ], 'md' ); ?>
		<?php echo mwm_chip( 'Has quiz', $state['quiz'], [ 'toggle' => 'quiz' ], 'md' ); ?>
	</div>

	<div class="mwm-active" data-browse-active>
		<?php
		$active = [];
		if ( $state['subtopic'] ) {
			$name = $state['subtopic'];
			foreach ( $topic['subtopics'] ?? [] as $s ) {
				if ( $s['slug'] === $state['subtopic'] ) {
					$name = $s['name'];
				}
			}
			$active[] = [ 'label' => $name, 'key' => 'subtopic' ];
		}
		if ( $state['type'] !== 'all' ) {
			$active[] = [ 'label' => $type_labels[ $state['type'] ], 'key' => 'type' ];
		}
		if ( $state['worksheet'] ) {
			$active[] = [ 'label' => 'Has worksheet', 'key' => 'worksheet' ];
		}
		if ( $state['quiz'] ) {
			$active[] = [ 'label' => 'Has quiz', 'key' => 'quiz' ];
		}
		foreach ( $active as $a ) {
			echo '<button type="button" class="mwm-chip mwm-chip--sm" aria-label="' . esc_attr( 'Remove filter: ' . lcfirst( $a['label'] ) ) . '" data-remove="' . esc_attr( $a['key'] ) . '">' . esc_html( $a['label'] ) . '<span aria-hidden="true">✕</span></button>';
		}
		?>
		<span class="mwm-meta" data-browse-count><?php echo $result['total'] === 1 ? '1 lesson' : $result['total'] . ' lessons'; ?></span>
		<button type="button" class="mwm-clear" data-clear<?php echo $any_filter ? '' : ' hidden'; ?>>Clear all</button>
	</div>

	<h2 class="screen-reader-text">Lessons</h2>
	<div class="mwm-grid3 mwm-grid3--32" data-grid<?php echo $results ? '' : ' hidden'; ?>>
		<?php foreach ( $results as $c ) { echo mwm_video_card( $c ); } ?>
	</div>
	<div class="mwm-empty" data-browse-empty<?php echo $results ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title" data-empty-title><?php echo esc_html( $is_alevel && ! $any_filter ? 'A-level lessons are coming soon' : 'No lessons match these filters yet' ); ?></p>
		<p class="mwm-empty__body" data-empty-body><?php echo esc_html( $is_alevel && ! $any_filter ? 'Pure, Statistics and Mechanics are being recorded now. GCSE Foundation and Higher are ready to browse.' : 'Try removing a filter, or pick a different topic.' ); ?></p>
		<div class="mwm-empty__chips" data-empty-chips hidden></div>
		<button type="button" class="mwm-empty__btn" data-clear<?php echo $any_filter ? '' : ' hidden'; ?>>Clear all filters</button>
	</div>
	<div class="mwm-more" data-more<?php echo $result['pages'] > 1 ? '' : ' hidden'; ?>>
		<button type="button" class="mwm-btn mwm-btn--secondary">Show more lessons</button>
		<span class="mwm-more__status" data-more-status aria-live="polite"></span>
	</div>
	<div class="mwm-sentinel" data-sentinel aria-hidden="true"></div>

	<div class="mwm-cta mwm-cta--practise">
		<div>
			<h2>Practise <span data-practise-label><?php echo esc_html( $practise ); ?></span></h2>
			<p>Worksheets and a revision pathway matched to this level.</p>
		</div>
		<div class="mwm-cta__links">
			<a href="<?php echo esc_url( add_query_arg( array_filter( [ 'level' => $level, 'topic' => $state['topic'] ] ), mwm_page_url( 'worksheets' ) ) ); ?>" class="mwm-arrow" data-practise-ws><span data-practise-label><?php echo esc_html( $practise ); ?></span>&nbsp;worksheets<span aria-hidden="true">→</span></a>
			<?php echo mwm_arrow_link( trailingslashit( mwm_page_url( 'revision' ) ) . $level . '/', $level_name . ' revision pathway' ); ?>
		</div>
	</div>
	<?php echo mwm_json_script( 'mwm-browse-data', $island ); ?>
</div>
