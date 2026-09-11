<?php
/**
 * Learn Maths: level switcher, topic chips, subtopics, filter bar, live-filtered results.
 * The server renders the state from the URL; script.js takes over for instant filtering.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'browse' );

$state      = MWM_Rewrites::browse_state();
$level      = $state['level'];
$level_name = mwm_level_name( $level );
$levels     = mwm_levels();
$topics     = mwm_topics( $level );
$topic      = null;
foreach ( $topics as $t ) {
	if ( $t['slug'] === $state['topic'] ) {
		$topic = $t;
		$topic['subtopics'] = mwm_subtopics( $t['id'] );
		$topic['intro']     = (string) get_term_meta( $t['id'], 'intro', true );
		break;
	}
}
$state['topic'] = $topic ? $topic['slug'] : '';
foreach ( $topics as &$t ) {
	$t['subtopics'] = mwm_subtopics( $t['id'] );
	$t['intro']     = (string) get_term_meta( $t['id'], 'intro', true );
}
unset( $t );

$all_cards = mwm_query_lessons( [ 'level' => $level ] );
$results   = array_values( array_filter( $all_cards, static function ( $c ) use ( $state ) {
	if ( $state['topic'] && $c['topic_slug'] !== $state['topic'] ) {
		return false;
	}
	if ( $state['subtopic'] && $c['subtopic_slug'] !== $state['subtopic'] ) {
		return false;
	}
	if ( $state['type'] !== 'all' && $c['format'] !== $state['type'] ) {
		return false;
	}
	if ( $state['worksheet'] && ! $c['has_worksheet'] ) {
		return false;
	}
	if ( $state['quiz'] && ! $c['has_quiz'] ) {
		return false;
	}
	return true;
} ) );

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
	'state'      => $state,
	'level'      => $level,
	'levelName'  => $level_name,
	'levels'     => array_map( static fn( $l ) => $l['name'], $levels ),
	'topics'     => $topics,
	'lessons'    => array_map( [ 'MWM_REST', 'public_card' ], $all_cards ),
	'urls'       => [ 'base' => mwm_browse_url( $level ), 'revision' => trailingslashit( mwm_page_url( 'revision' ) ) . $level . '/', 'browse' => mwm_page_url( 'browse' ) ],
	'icons'      => [ 'worksheet' => mwm_icon( 'worksheet', 16 ), 'quiz' => mwm_icon( 'quiz', 16 ) ],
	'suggested'  => $topic ? array_slice( array_map( static fn( $s ) => [ 'slug' => $s['slug'], 'name' => $s['name'] ], $topic['subtopics'] ), 0, 3 ) : [],
];
$sub_visible = $topic ? array_slice( $topic['subtopics'], 0, 8 ) : [];
?>
<div class="mwm-page" data-browse>
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
		<span class="mwm-meta" data-browse-count><?php echo count( $results ) === 1 ? '1 lesson' : count( $results ) . ' lessons'; ?></span>
		<button type="button" class="mwm-clear" data-clear<?php echo $any_filter ? '' : ' hidden'; ?>>Clear all</button>
	</div>

	<div class="mwm-grid3 mwm-grid3--32" data-browse-results<?php echo $results ? '' : ' hidden'; ?>>
		<?php foreach ( $results as $c ) { echo mwm_video_card( $c ); } ?>
	</div>
	<div class="mwm-empty" data-browse-empty<?php echo $results ? ' hidden' : ''; ?>>
		<p class="mwm-empty__title" data-empty-title><?php echo esc_html( $is_alevel && ! $any_filter ? 'A-level lessons are coming soon' : 'No lessons match these filters yet' ); ?></p>
		<p class="mwm-empty__body" data-empty-body><?php echo esc_html( $is_alevel && ! $any_filter ? 'Pure, Statistics and Mechanics are being recorded now. GCSE Foundation and Higher are ready to browse.' : 'Try removing a filter, or pick a different topic.' ); ?></p>
		<div class="mwm-empty__chips" data-empty-chips hidden></div>
		<button type="button" class="mwm-empty__btn" data-clear<?php echo $any_filter ? '' : ' hidden'; ?>>Clear all filters</button>
	</div>

	<div class="mwm-cta mwm-cta--practise">
		<div>
			<h2>Practise <span data-practise-label><?php echo esc_html( $practise ); ?></span></h2>
			<p>Worksheets and a revision pathway matched to this level.</p>
		</div>
		<div class="mwm-cta__links">
			<a href="<?php echo esc_url( add_query_arg( 'worksheet', '1', mwm_browse_url( $level, $state['topic'] ) ) ); ?>" class="mwm-arrow" data-practise-ws><span data-practise-label><?php echo esc_html( $practise ); ?></span>&nbsp;worksheets<span aria-hidden="true">→</span></a>
			<?php echo mwm_arrow_link( trailingslashit( mwm_page_url( 'revision' ) ) . $level . '/', $level_name . ' revision pathway' ); ?>
		</div>
	</div>
	<?php echo mwm_json_script( 'mwm-browse-data', $island ); ?>
</div>
