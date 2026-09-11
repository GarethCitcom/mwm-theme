<?php
/**
 * Quiz: one question at a time, explanation after answering, score screen.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'quiz' );

$quiz_id   = get_the_ID();
$questions = MWM_Quiz::questions( $quiz_id );
$lesson_id = (int) get_post_meta( $quiz_id, 'lesson', true );
$lesson    = $lesson_id ? mwm_lesson_card( $lesson_id ) : null;
$title     = $lesson ? $lesson['title'] : preg_replace( '/\s+—\s+quiz$/u', '', get_the_title( $quiz_id ) );
$revision  = trailingslashit( mwm_page_url( 'revision' ) ) . ( $lesson && $lesson['level_slug'] ? $lesson['level_slug'] . '/' : '' );
$signed_in = is_user_logged_in();
$n         = count( $questions );
?>
<div class="mwm-quiz" data-quiz="<?php echo (int) $quiz_id; ?>">
	<div class="mwm-eyebrow">Quiz · <?php echo esc_html( $title ); ?></div>
	<?php if ( ! $questions ) : ?>
		<h1 class="mwm-quiz__h1" style="margin-top:20px">This quiz is being written</h1>
		<p class="mwm-quiz__hint">Check back soon — in the meantime the lesson and worksheet are ready.</p>
		<?php if ( $lesson ) { echo mwm_button( $lesson['url'], 'Back to the lesson', 'primary', [ 'style' => 'margin-top:20px' ] ); } ?>
	<?php else : ?>
		<div data-quiz-app>
			<div class="mwm-quiz__head">
				<h1 class="mwm-quiz__h1">Question 1 of <?php echo (int) $n; ?></h1>
				<span class="mwm-meta">0 correct so far</span>
			</div>
			<div class="mwm-bar mwm-bar--12"><div class="mwm-bar__fill" style="width:0%"></div></div>
			<noscript><p class="mwm-quiz__hint">The quiz needs JavaScript to run. The lesson and worksheet work without it.</p></noscript>
		</div>
		<?php echo mwm_json_script( 'mwm-quiz-data', [
			'id'        => $quiz_id,
			'questions' => $questions,
			'lessonUrl' => $lesson ? $lesson['url'] : home_url( '/' ),
			'pathwayUrl'=> $revision,
			'signedIn'  => $signed_in,
			'loginUrl'  => wp_login_url( get_permalink() ),
			'myLearning'=> mwm_page_url( 'my-learning' ),
		] ); ?>
	<?php endif; ?>
</div>
