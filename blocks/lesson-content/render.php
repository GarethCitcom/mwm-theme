<?php
/**
 * Lesson body: video (loads on play), What you'll learn, related lessons, sticky Practice sidebar.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'lesson-content' );

$id   = get_the_ID();
$card = mwm_lesson_card( $id );
if ( ! $card ) {
	return;
}
$content = apply_filters( 'the_content', get_post_field( 'post_content', $id ) );
$related = [];
if ( $card['topic_slug'] ) {
	$related = mwm_query_lessons( [ 'level' => $card['level_slug'], 'topic' => $card['topic_slug'], 'format' => [ 'lesson', 'gaming' ], 'exclude' => [ $id ], 'per_page' => 3 ] );
}
if ( count( $related ) < 3 ) {
	$more = mwm_query_lessons( [ 'level' => $card['level_slug'], 'format' => [ 'lesson', 'gaming' ], 'exclude' => array_merge( [ $id ], array_column( $related, 'id' ) ), 'per_page' => 3 - count( $related ) ] );
	$related = array_merge( $related, $more );
}
$quiz_summary = $card['has_quiz'] ? MWM_Quiz::summary( $card['quiz_id'] ) : null;
$yt           = mwm_youtube_channel_url();
$embed        = $card['youtube_id'] ? 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $card['youtube_id'] ) . '?autoplay=1&rel=0' : '';
$signed_in    = is_user_logged_in();
?>
<div class="mwm-cols" data-lesson="<?php echo (int) $id; ?>">
	<div class="mwm-cols__main">
		<div class="mwm-player<?php echo $card['is_short'] ? ' mwm-player--short' : ''; ?>" data-player data-embed="<?php echo esc_attr( $embed ); ?>">
			<?php if ( $card['thumb'] ) : ?>
				<?php // Phones get the 640px poster (it is the LCP image; the 1280px file is only worth it on large screens).
				echo mwm_thumb_img( $card['thumb'], $card['alt'], '(max-width: 1000px) 70vw, 800px', [ 'width' => '1280', 'height' => '720', 'maxres' => str_contains( $card['thumb'], 'maxresdefault' ) ] ); ?>
			<?php else : ?>
				<span class="mwm-player__poster"></span>
			<?php endif; ?>
			<?php if ( $embed ) : ?>
				<button type="button" class="mwm-player__link" aria-label="<?php echo esc_attr( 'Play video: ' . $card['title'] ); ?>" data-play><span class="mwm-player__btn"><?php echo mwm_icon( 'play', 24 ); ?></span></button>
			<?php else : ?>
				<a href="<?php echo esc_url( $card['youtube_url'] ); ?>" target="_blank" rel="noopener" class="mwm-player__link" aria-label="<?php echo esc_attr( 'Play video: ' . $card['title'] ); ?>"><span class="mwm-player__btn"><?php echo mwm_icon( 'play', 24 ); ?></span></a>
			<?php endif; ?>
		</div>
		<p class="mwm-player__note">The player loads when you press play — nothing autoplays.</p>
		<div class="mwm-player__links">
			<?php echo mwm_arrow_link( $card['youtube_url'], 'Watch on YouTube', '', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
			<a href="<?php echo esc_url( $yt ); ?>" target="_blank" rel="noopener" class="mwm-player__sub">Subscribe</a>
		</div>

		<?php if ( trim( wp_strip_all_tags( $content ) ) !== '' ) : ?>
			<h2 class="mwm-h2 mwm-lesson__h2">What you’ll learn</h2>
			<div class="mwm-lesson__learn"><?php echo $content; ?></div>
		<?php endif; ?>

		<?php if ( $card['has_answers'] ) : ?>
			<div class="mwm-answers-reveal" data-answers-panel hidden>
				<h2 class="mwm-panel__h2">Worked answers</h2>
				<p class="mwm-panel__sub">Every step shown, so you can see exactly where a method goes.</p>
				<?php echo mwm_arrow_link( $card['answers']['url'], 'Open the answers PDF', 'mwm-arrow--mt12', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $related ) : ?>
			<div class="mwm-section__head mwm-lesson__related">
				<h2 class="mwm-h2">Related lessons</h2>
				<?php if ( $card['topic_slug'] ) { echo mwm_arrow_link( mwm_browse_url( $card['level_slug'], $card['topic_slug'] ), 'All ' . $card['topic'] ); } ?>
			</div>
			<div class="mwm-grid3 mwm-grid3--32">
				<?php foreach ( $related as $r ) { echo mwm_related_card( $r ); } ?>
			</div>
		<?php endif; ?>
	</div>

	<aside class="mwm-cols__side" aria-label="Practice">
		<div class="mwm-panel">
			<h2 class="mwm-panel__h2">Practice</h2>
			<div class="mwm-panel__row">
				<?php if ( $card['has_worksheet'] ) : ?>
					<div class="mwm-panel__line"><span class="mwm-panel__label">Worksheet</span><span class="mwm-panel__size"><?php echo esc_html( $card['worksheet']['label'] ); ?></span></div>
					<div class="mwm-panel__links">
						<a href="<?php echo esc_url( $card['worksheet_url'] ); ?>" class="mwm-arrow mwm-arrow--mt8">Open worksheet<span aria-hidden="true">→</span></a>
						<a href="<?php echo esc_url( $card['worksheet']['url'] ); ?>" class="mwm-arrow mwm-arrow--mt8" download>Download<span aria-hidden="true">↓</span></a>
					</div>
				<?php else : ?>
					<p class="mwm-avail">No worksheet for this lesson yet.</p>
				<?php endif; ?>
			</div>
			<div class="mwm-panel__row">
				<div class="mwm-panel__line"><span class="mwm-panel__label">Worked answers</span></div>
				<?php if ( $card['has_answers'] ) : ?>
					<div data-answers-hidden>
						<button type="button" class="mwm-linkbtn" style="margin-top:8px" data-reveal-answers>Reveal answers</button>
						<p class="mwm-panel__hint">Try the worksheet first — answers appear when you’re ready.</p>
					</div>
					<div data-answers-shown hidden>
						<p class="mwm-panel__hint">Answers revealed below the video, with every step shown.</p>
						<a href="<?php echo esc_url( $card['answers']['url'] ); ?>" class="mwm-arrow mwm-arrow--mt8" download>Download answers<span aria-hidden="true">↓</span></a>
					</div>
				<?php else : ?>
					<p class="mwm-panel__hint" style="margin-top:4px">Worked answers for this lesson are on their way.</p>
				<?php endif; ?>
			</div>
		</div>
		<div class="mwm-panel">
			<h2 class="mwm-panel__h2">Quiz</h2>
			<?php if ( $quiz_summary ) : ?>
				<p class="mwm-panel__sub"><?php echo esc_html( $quiz_summary['label'] ); ?></p>
				<?php echo mwm_button( $card['quiz_url'], 'Take the quiz', 'primary', [ 'style' => 'margin-top:16px' ] ); ?>
			<?php else : ?>
				<p class="mwm-panel__sub">No quiz for this lesson yet.</p>
			<?php endif; ?>
		</div>
		<div class="mwm-panel mwm-panel--controls">
			<div class="mwm-controls">
				<button type="button" class="mwm-ctl" aria-pressed="false" data-ctl="saved" data-on="✓ Saved" data-off="Save">Save</button>
				<button type="button" class="mwm-ctl" aria-pressed="false" data-ctl="completed" data-on="✓ Completed" data-off="Mark complete">Mark complete</button>
			</div>
			<?php if ( $signed_in ) : ?>
				<p class="mwm-controls__note mwm-controls__note--ok">✓ Progress saves to your account.</p>
			<?php else : ?>
				<p class="mwm-controls__note"><a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">Sign in</a> to keep your progress across devices.</p>
			<?php endif; ?>
		</div>
	</aside>
</div>
