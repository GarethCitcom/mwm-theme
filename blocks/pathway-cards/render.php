<?php
/**
 * "A little revision. A lot more confidence." — one pathway card per level.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$heading   = mwm_field( 'heading', 'A little revision.' );
$highlight = mwm_field( 'highlight', 'A lot more confidence.' );
$sub       = mwm_field( 'sub', 'Choose your level and exam board. Follow a clear path.' );
$link      = mwm_field( 'link_label', 'Start pathway' );
$revision  = trailingslashit( mwm_page_url( 'revision' ) );
?>
<section class="mwm-section--tint" aria-labelledby="mwm-pathways-title">
	<div class="mwm-section__inner">
		<h2 id="mwm-pathways-title" class="mwm-h2 mwm-pathways__h2"><?php echo esc_html( $heading ); ?> <span><?php echo esc_html( $highlight ); ?></span></h2>
		<p class="mwm-section__sub mwm-section__sub--12"><?php echo esc_html( $sub ); ?></p>
		<div class="mwm-pathways__grid">
			<?php foreach ( mwm_levels() as $slug => $level ) : ?>
				<?php
				$p    = mwm_find_pathway( $slug );
				$d    = $p ? mwm_pathway_data( $p ) : null;
				$line = $d && $d['total']
					? sprintf( '%d topics · %d with worksheets · %d with quizzes', $d['total'], $d['with_worksheets'], $d['with_quizzes'] )
					: 'Coming soon — topics are being recorded now.';
				?>
				<div class="mwm-pathway-card">
					<h3><?php echo esc_html( $level['name'] . ' pathway' ); ?></h3>
					<p><?php echo esc_html( $line ); ?></p>
					<?php echo mwm_arrow_link( $revision . $slug . '/', $link, 'mwm-arrow--mt20' ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
