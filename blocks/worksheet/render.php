<?php
/**
 * Worksheet page: breadcrumb, title, embedded PDF, lesson + answers sidebar, related worksheets.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'worksheet' );

$w = mwm_worksheet_data( get_the_ID() );
if ( ! $w ) {
	return;
}
$lesson  = $w['lesson'];
$content = apply_filters( 'the_content', get_post_field( 'post_content', $w['id'] ) );
$crumbs  = [ [ 'label' => 'Worksheets', 'url' => mwm_page_url( 'worksheets' ) ] ];
if ( $w['level_slug'] ) {
	$crumbs[] = [ 'label' => $w['level'], 'url' => add_query_arg( 'level', $w['level_slug'], mwm_page_url( 'worksheets' ) ) ];
}
if ( $w['topic_slug'] ) {
	$crumbs[] = [ 'label' => $w['topic'], 'url' => add_query_arg( [ 'level' => $w['level_slug'], 'topic' => $w['topic_slug'] ], mwm_page_url( 'worksheets' ) ) ];
}
$crumbs[] = [ 'label' => $w['title'] ];
$related  = [];
if ( $w['topic_slug'] ) {
	$related = mwm_query_worksheets( [ 'topic' => $w['topic_slug'], 'level' => $w['level_slug'], 'exclude' => [ $w['id'] ], 'per_page' => 3 ] );
}
if ( count( $related ) < 3 ) {
	$related = array_merge( $related, mwm_query_worksheets( [ 'level' => $w['level_slug'], 'exclude' => array_merge( [ $w['id'] ], array_column( $related, 'id' ) ), 'per_page' => 3 - count( $related ) ] ) );
}
$quiz = $lesson && $lesson['has_quiz'] ? MWM_Quiz::summary( $lesson['quiz_id'] ) : null;
$pdf  = $w['pdf'];
?>
<article class="mwm-page" data-worksheet="<?php echo (int) $w['id']; ?>">
	<?php echo mwm_breadcrumb( $crumbs ); ?>
	<h1 class="mwm-h1"><?php echo esc_html( $w['title'] ); ?></h1>
	<div class="mwm-lesson__tags">
		<?php if ( $w['level'] ) { echo mwm_tag( $w['level'], $w['level_style'] ); } ?>
		<?php if ( $w['topic'] ) { echo mwm_tag( $w['topic'], 'outline' ); } ?>
		<span class="mwm-lesson__meta"><?php echo esc_html( ( $pdf ? $pdf['label'] . ' · ' : '' ) . 'Published ' . $w['published_label'] ); ?></span>
	</div>

	<div class="mwm-cols">
		<div class="mwm-cols__main">
			<?php if ( $pdf ) : ?>
				<div class="mwm-pdf">
					<object data="<?php echo esc_url( $pdf['url'] ); ?>#view=FitH" type="application/pdf" aria-label="<?php echo esc_attr( $w['title'] . ' (PDF)' ); ?>">
						<div class="mwm-pdf__fallback">
							<p>Your browser can’t show the PDF here — open it in a new tab or download it instead.</p>
							<div class="mwm-actions" style="justify-content:center;margin-top:0">
								<?php echo mwm_button( $pdf['url'], 'Open the worksheet', 'primary', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
								<?php echo mwm_button( $pdf['url'], 'Download', 'secondary', [ 'download' => '' ] ); ?>
							</div>
						</div>
					</object>
				</div>
				<p class="mwm-player__note">Print it, or work through it on screen — the answers are in the panel when you’re ready.</p>
				<div class="mwm-player__links">
					<?php echo mwm_arrow_link( $pdf['url'], 'Open in a new tab', '', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
					<a href="<?php echo esc_url( $pdf['url'] ); ?>" class="mwm-arrow" download>Download<span aria-hidden="true">↓</span></a>
				</div>
			<?php else : ?>
				<p class="mwm-avail">The PDF for this worksheet is on its way.</p>
			<?php endif; ?>

			<?php if ( trim( wp_strip_all_tags( $content ) ) !== '' ) : ?>
				<h2 class="mwm-h2 mwm-lesson__h2">About this worksheet</h2>
				<div class="mwm-lesson__learn"><?php echo $content; ?></div>
			<?php endif; ?>

			<?php if ( $w['has_answers'] ) : ?>
				<div class="mwm-answers-reveal" data-answers-panel hidden>
					<h2 class="mwm-panel__h2">Worked answers</h2>
					<p class="mwm-panel__sub">Every step shown, so you can see exactly where a method goes.</p>
					<?php echo mwm_arrow_link( $w['answers']['url'], 'Open the answers PDF', 'mwm-arrow--mt12', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $related ) : ?>
				<div class="mwm-section__head mwm-lesson__related">
					<h2 class="mwm-h2">More worksheets</h2>
					<?php echo mwm_arrow_link( add_query_arg( array_filter( [ 'level' => $w['level_slug'], 'topic' => $w['topic_slug'] ] ), mwm_page_url( 'worksheets' ) ), $w['topic'] ? 'All ' . $w['topic'] . ' worksheets' : 'All worksheets' ); ?>
				</div>
				<div class="mwm-grid3 mwm-grid3--32">
					<?php foreach ( $related as $r ) { echo mwm_worksheet_card( $r ); } ?>
				</div>
			<?php endif; ?>
		</div>

		<aside class="mwm-cols__side" aria-label="Lesson and answers">
			<div class="mwm-panel">
				<h2 class="mwm-panel__h2">Goes with the lesson</h2>
				<?php if ( $lesson ) : ?>
					<a href="<?php echo esc_url( $lesson['url'] ); ?>" class="mwm-lesson-mini">
						<span class="mwm-lesson-mini__thumb"<?php echo $lesson['thumb'] ? ' style="background-image:url(' . esc_url( mwm_thumb_small( $lesson['thumb'] ) ) . ')"' : ''; ?>></span>
						<span class="mwm-lesson-mini__title"><?php echo esc_html( $lesson['title'] ); ?><span class="mwm-lesson-mini__meta"><?php echo esc_html( trim( $lesson['level'] . ' · ' . $lesson['duration'], ' ·' ) ); ?></span></span>
					</a>
					<?php echo mwm_arrow_link( $lesson['url'], 'Watch the lesson', 'mwm-arrow--mt16' ); ?>
				<?php else : ?>
					<p class="mwm-panel__sub">No video for this worksheet yet — it works on its own.</p>
				<?php endif; ?>
			</div>
			<div class="mwm-panel">
				<h2 class="mwm-panel__h2">Worked answers</h2>
				<?php if ( $w['has_answers'] ) : ?>
					<div data-answers-hidden>
						<button type="button" class="mwm-linkbtn" style="margin-top:8px" data-reveal-answers>Reveal answers</button>
						<p class="mwm-panel__hint">Try the worksheet first — answers appear when you’re ready.</p>
					</div>
					<div data-answers-shown hidden>
						<p class="mwm-panel__hint">Answers revealed below the worksheet, with every step shown.</p>
						<a href="<?php echo esc_url( $w['answers']['url'] ); ?>" class="mwm-arrow mwm-arrow--mt8" download>Download answers<span aria-hidden="true">↓</span></a>
					</div>
				<?php else : ?>
					<p class="mwm-panel__sub">Worked answers for this worksheet are on their way.</p>
				<?php endif; ?>
			</div>
			<?php if ( $quiz ) : ?>
				<div class="mwm-panel">
					<h2 class="mwm-panel__h2">Quiz</h2>
					<p class="mwm-panel__sub"><?php echo esc_html( $quiz['label'] ); ?></p>
					<?php echo mwm_button( $lesson['quiz_url'], 'Take the quiz', 'primary', [ 'style' => 'margin-top:16px' ] ); ?>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</article>
