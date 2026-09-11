<?php
/**
 * Next verified exam for the visitor's level and board.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$prefs = mwm_user_prefs();
$exam  = mwm_next_exam( $prefs['level'], $prefs['board'] );
$label = 'Next exam for ' . mwm_level_name( $prefs['level'] ) . ' (' . mwm_board_name( $prefs['board'] ) . ')';
$cal   = mwm_page_url( 'calendar' );
?>
<section class="mwm-section" aria-label="Next exam">
	<div class="mwm-exam">
		<?php if ( $exam ) : ?>
			<div class="mwm-exam__date">
				<div class="mwm-exam__day"><?php echo esc_html( $exam['dow_day_month'] ); ?></div>
				<div class="mwm-exam__year"><?php echo esc_html( $exam['year'] . ' · ' . $exam['session'] ); ?></div>
			</div>
			<div class="mwm-exam__body">
				<div class="mwm-exam__label"><?php echo esc_html( $label ); ?></div>
				<div class="mwm-exam__paper"><?php echo esc_html( $exam['paper'] ); ?></div>
				<div class="mwm-exam__mobdate"><?php echo esc_html( $exam['dow_day_month'] . ' ' . $exam['year'] . ' · ' . $exam['session'] ); ?></div>
				<div class="mwm-exam__note"><?php echo esc_html( mwm_exam_verified_note( $exam ) ); ?></div>
				<?php echo mwm_arrow_link( $cal, 'See the exam calendar', 'mwm-arrow--mt16' ); ?>
			</div>
		<?php else : ?>
			<div class="mwm-exam__body">
				<div class="mwm-exam__label"><?php echo esc_html( $label ); ?></div>
				<div class="mwm-exam__unverified">Exam dates will be added when confirmed.</div>
				<div class="mwm-exam__note">We haven’t verified the <?php echo esc_html( mwm_board_name( $prefs['board'] ) ); ?> dates yet — check your own timetable with your school.</div>
				<?php echo mwm_arrow_link( $cal, 'See the exam calendar', 'mwm-arrow--mt16' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
