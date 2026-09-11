<?php
/**
 * Past papers: filterable list (tier, series, paper) with PDFs, mark schemes and matching worksheets.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'past-papers' );

$heading = mwm_field( 'heading', 'Past papers' );
$intro   = mwm_field( 'intro', 'Every Edexcel paper, uploaded as free PDFs with mark schemes — plus the practice worksheets that match what came up. AQA and OCR papers will follow once the mapping is verified.' );
$cta     = mwm_field( 'cta_text', 'Not sure where to start? The revision pathway puts topics in order first.' );
$cta_btn = mwm_field( 'cta_button', 'Start the pathway' );

$prefs = mwm_user_prefs();
$board = sanitize_key( (string) ( $_GET['board'] ?? $prefs['board'] ) );
if ( ! isset( mwm_boards()[ $board ] ) ) {
	$board = 'edexcel';
}
$tier = sanitize_key( (string) ( $_GET['tier'] ?? ( $prefs['level'] === 'gcse-foundation' ? 'foundation' : 'higher' ) ) );
if ( ! in_array( $tier, [ 'foundation', 'higher' ], true ) ) {
	$tier = 'higher';
}
$series_sel = sanitize_text_field( (string) ( $_GET['series'] ?? 'All' ) );
$paper_sel  = sanitize_text_field( (string) ( $_GET['paper'] ?? 'All' ) );

$posts  = get_posts( [ 'post_type' => 'mwm_past_paper', 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true, 'tax_query' => [ [ 'taxonomy' => 'mwm_board', 'field' => 'slug', 'terms' => $board ] ] ] );
$papers = array_values( array_filter( array_map( 'mwm_past_paper_data', $posts ) ) );
usort( $papers, static fn( $a, $b ) => [ $b['sort'], $a['paper'] ] <=> [ $a['sort'], $b['paper'] ] );
$series = [];
foreach ( $papers as $p ) {
	$series[ $p['series'] ] = true;
}
$series = array_keys( $series );
$papers_public = array_map( static function ( $p ) {
	return [
		'id' => $p['id'], 'tier' => $p['tier'], 'series' => $p['series'], 'paper' => $p['paper'], 'title' => $p['title'], 'meta' => $p['meta'],
		'qp' => $p['qp'] ? [ 'url' => $p['qp']['url'], 'label' => $p['qp']['label'] ] : null,
		'ms' => $p['ms'] ? [ 'url' => $p['ms']['url'], 'label' => $p['ms']['label'] ] : null,
		'worksheets' => $p['worksheets'],
	];
}, $papers );
$icon_dl = mwm_icon( 'download', 14 );
?>
<div class="mwm-page" data-past-papers>
	<?php echo mwm_breadcrumb( [ [ 'label' => 'Revision', 'url' => mwm_page_url( 'revision' ) ], [ 'label' => $heading ] ] ); ?>
	<h1 class="mwm-h1"><?php echo esc_html( $heading ); ?></h1>
	<p class="mwm-intro"><?php echo esc_html( $intro ); ?></p>

	<div class="mwm-filterbar">
		<div role="group" aria-label="Tier" class="mwm-group">
			<?php foreach ( [ 'foundation' => 'Foundation', 'higher' => 'Higher' ] as $k => $label ) : ?>
				<button type="button" class="mwm-group__btn<?php echo $tier === $k ? ' is-on' : ''; ?>" aria-pressed="<?php echo $tier === $k ? 'true' : 'false'; ?>" data-filter="tier" data-value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<div role="group" aria-label="Exam series" class="mwm-group">
			<?php foreach ( array_merge( [ 'All' ], $series ) as $s ) : ?>
				<button type="button" class="mwm-group__btn<?php echo $series_sel === $s ? ' is-on' : ''; ?>" aria-pressed="<?php echo $series_sel === $s ? 'true' : 'false'; ?>" data-filter="series" data-value="<?php echo esc_attr( $s ); ?>"><?php echo esc_html( $s ); ?></button>
			<?php endforeach; ?>
		</div>
		<div role="group" aria-label="Paper" class="mwm-group">
			<?php foreach ( [ 'All' => 'All papers', '1' => 'Paper 1', '2' => 'Paper 2', '3' => 'Paper 3' ] as $k => $label ) : ?>
				<button type="button" class="mwm-group__btn<?php echo $paper_sel === (string) $k ? ' is-on' : ''; ?>" aria-pressed="<?php echo $paper_sel === (string) $k ? 'true' : 'false'; ?>" data-filter="paper" data-value="<?php echo esc_attr( (string) $k ); ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<span class="mwm-filterbar__count" data-count></span>
	</div>

	<div data-groups></div>

	<div class="mwm-cta">
		<p class="mwm-cta__text"><?php echo esc_html( $cta ); ?></p>
		<?php echo mwm_button( trailingslashit( mwm_page_url( 'revision' ) ) . ( $tier === 'foundation' ? 'gcse-foundation' : 'gcse-higher' ) . '/' . $board . '/', $cta_btn ); ?>
	</div>
	<?php echo mwm_json_script( 'mwm-past-papers-data', [ 'papers' => $papers_public, 'series' => $series, 'state' => [ 'tier' => $tier, 'series' => $series_sel, 'paper' => $paper_sel ], 'icon' => $icon_dl, 'board' => mwm_board_name( $board ) ] ); ?>
	<noscript>
		<?php foreach ( $series as $s ) : ?>
			<div class="mwm-pp-group">
				<h2 class="mwm-h2"><?php echo esc_html( $s . ' · ' . ucfirst( $tier ) ); ?></h2>
				<div class="mwm-list mwm-list--pp">
					<?php foreach ( $papers as $p ) : if ( $p['series'] !== $s || $p['tier'] !== $tier ) { continue; } ?>
						<div class="mwm-pp-row">
							<div class="mwm-pp-row__main"><div class="mwm-pp-row__title"><?php echo esc_html( $p['title'] ); ?></div><div class="mwm-pp-row__meta"><?php echo esc_html( $p['meta'] ); ?></div></div>
							<div class="mwm-pp-row__files">
								<?php if ( $p['qp'] ) : ?><a href="<?php echo esc_url( $p['qp']['url'] ); ?>" class="mwm-filebtn" target="_blank" rel="noopener"><?php echo $icon_dl; ?>Question paper<span class="mwm-filebtn__size"><?php echo esc_html( $p['qp']['label'] ); ?></span></a><?php endif; ?>
								<?php if ( $p['ms'] ) : ?><a href="<?php echo esc_url( $p['ms']['url'] ); ?>" class="mwm-filebtn mwm-filebtn--ms" target="_blank" rel="noopener"><?php echo $icon_dl; ?>Mark scheme<span class="mwm-filebtn__size"><?php echo esc_html( $p['ms']['label'] ); ?></span></a><?php else : ?><span class="mwm-pp-row__soon">Mark scheme coming soon</span><?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</noscript>
</div>
