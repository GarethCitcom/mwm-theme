<?php
/**
 * Predicted papers: Melissa's own papers for the coming exam, filtered by board and level, grouped by exam year.
 * Simpler than past papers on purpose: no series or paper-number filters; each paper carries its own name.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'predicted-papers' );

$prefs = mwm_user_prefs();
$board = sanitize_key( (string) ( $_GET['board'] ?? $prefs['board'] ) );
if ( ! isset( mwm_boards()[ $board ] ) ) {
	$board = 'edexcel';
}
$level = sanitize_key( (string) ( $_GET['level'] ?? $prefs['level'] ) );
if ( ! isset( mwm_levels()[ $level ] ) ) {
	$level = 'gcse-higher';
}
$board_name = mwm_board_name( $board );

$heading    = mwm_field( 'heading', 'Predicted papers' );
// "{board}" is swapped for the selected exam board, so one line of copy works for all three.
$intro      = str_replace( '{board}', $board_name, mwm_field( 'intro', 'Melissa’s own predicted papers for the next {board} exams, written in the style of the real thing, with worked solutions that explain every step. Switch board to see AQA, Edexcel or OCR.' ) );
$disclaimer = str_replace( '{board}', $board_name, mwm_field( 'disclaimer', 'Original independent practice material written by Maths with Melissa. Not an official {board} paper and not endorsed by {board}. The worked solutions are not an official mark scheme; other valid methods are fine.' ) );
$cta        = mwm_field( 'cta_text', 'Want the real thing too? Every past paper and mark scheme is a click away.' );
$cta_btn    = mwm_field( 'cta_button', 'Browse past papers' );

$board_urls = [];
foreach ( mwm_boards() as $slug => $name ) {
	$board_urls[ $slug ] = add_query_arg( [ 'board' => $slug, 'level' => $level ], mwm_page_url( 'predicted-papers' ) );
}

$posts  = get_posts( [ 'post_type' => 'mwm_predicted_paper', 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true, 'tax_query' => [ [ 'taxonomy' => 'mwm_board', 'field' => 'slug', 'terms' => $board ] ] ] );
$papers = array_values( array_filter( array_map( 'mwm_predicted_paper_data', $posts ) ) );
// Newest exam year first, then by name, so "Paper 1" sits above "Paper 2".
usort( $papers, static fn( $a, $b ) => [ $b['year'], $a['title'] ] <=> [ $a['year'], $b['title'] ] );
$levels = [];
foreach ( mwm_levels() as $slug => $l ) {
	$levels[ $slug ] = $l['name'];
}
$papers_public = array_map( static function ( $p ) {
	return [
		'id' => $p['id'], 'level' => $p['level'], 'year' => $p['year'], 'group' => $p['group'], 'title' => $p['title'], 'meta' => $p['meta'],
		'qp'  => $p['qp'] ? [ 'url' => $p['qp']['url'], 'label' => $p['qp']['label'] ] : null,
		'sol' => $p['solutions'] ? [ 'url' => $p['solutions']['url'], 'label' => $p['solutions']['label'] ] : null,
		'worksheets' => $p['worksheets'],
	];
}, $papers );
$icon_dl = mwm_icon( 'download', 14 );
$tier    = $level === 'gcse-foundation' ? 'foundation' : 'higher';
?>
<div class="mwm-page" data-predicted-papers>
	<div class="mwm-page-head">
		<div class="mwm-page-head__main">
			<?php echo mwm_breadcrumb( [ [ 'label' => 'Revision', 'url' => mwm_page_url( 'revision' ) ], [ 'label' => $heading ] ] ); ?>
			<h1 class="mwm-h1"><?php echo esc_html( $heading ); ?></h1>
			<p class="mwm-intro"><?php echo esc_html( $intro ); ?></p>
			<p class="mwm-disclaimer"><?php echo esc_html( $disclaimer ); ?></p>
		</div>
		<?php echo mwm_papers_switch( 'predicted', $board, $level ); ?>
	</div>

	<?php echo mwm_segmented( mwm_boards(), $board, 'Exam board', 'board', $board_urls ); ?>

	<div class="mwm-filterbar">
		<div role="group" aria-label="Level" class="mwm-group">
			<?php foreach ( $levels as $k => $label ) : ?>
				<button type="button" class="mwm-group__btn<?php echo $level === $k ? ' is-on' : ''; ?>" aria-pressed="<?php echo $level === $k ? 'true' : 'false'; ?>" data-filter="level" data-value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<span class="mwm-filterbar__count" data-count></span>
	</div>

	<div data-groups></div>

	<div class="mwm-cta">
		<p class="mwm-cta__text"><?php echo esc_html( $cta ); ?></p>
		<?php echo mwm_button( add_query_arg( [ 'board' => $board, 'tier' => $tier ], mwm_page_url( 'past-papers' ) ), $cta_btn ); ?>
	</div>
	<?php echo mwm_json_script( 'mwm-predicted-papers-data', [ 'papers' => $papers_public, 'levels' => $levels, 'state' => [ 'level' => $level ], 'icon' => $icon_dl, 'board' => $board_name, 'boardSlug' => $board ] ); ?>
	<noscript>
		<?php
		$groups = [];
		foreach ( $papers as $p ) {
			if ( $p['level'] === $level ) {
				$groups[ $p['group'] ][] = $p;
			}
		}
		foreach ( $groups as $group => $rows ) : ?>
			<div class="mwm-pp-group">
				<h2 class="mwm-h2"><?php echo esc_html( $group ); ?></h2>
				<div class="mwm-list mwm-list--pp">
					<?php foreach ( $rows as $p ) : ?>
						<div class="mwm-pp-row">
							<div class="mwm-pp-row__main"><div class="mwm-pp-row__title"><?php echo esc_html( $p['title'] ); ?></div><div class="mwm-pp-row__meta"><?php echo esc_html( $p['meta'] ); ?></div></div>
							<div class="mwm-pp-row__files">
								<?php if ( $p['qp'] ) : ?><a href="<?php echo esc_url( $p['qp']['url'] ); ?>" class="mwm-filebtn" target="_blank" rel="noopener"><?php echo $icon_dl; ?>Question paper<span class="mwm-filebtn__size"><?php echo esc_html( $p['qp']['label'] ); ?></span></a><?php endif; ?>
								<?php if ( $p['solutions'] ) : ?><a href="<?php echo esc_url( $p['solutions']['url'] ); ?>" class="mwm-filebtn mwm-filebtn--ms" target="_blank" rel="noopener"><?php echo $icon_dl; ?>Worked solutions<span class="mwm-filebtn__size"><?php echo esc_html( $p['solutions']['label'] ); ?></span></a><?php else : ?><span class="mwm-pp-row__soon">Worked solutions coming soon</span><?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</noscript>
</div>
