<?php
/**
 * Exam calendar: month grids with exam days circled, shaded revision-plan weeks, week-by-week plan.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$heading      = mwm_field( 'heading', 'Exam calendar' );
$intro        = mwm_field( 'intro', 'Exam days are circled; the shaded weeks are a suggested revision plan that follows your pathway.' );
$plan_heading = mwm_field( 'plan_heading', 'Your revision plan, week by week' );
$plan_sub     = mwm_field( 'plan_sub', 'A suggestion, not a rule — tick topics off in your pathway as you go.' );

$st         = MWM_Rewrites::pathway_state();
$level      = $st['level'];
$board      = $st['board'];
$level_name = mwm_level_name( $level );
$board_name = mwm_board_name( $board );
$exams      = array_values( array_filter( mwm_exam_dates( $level, $board ), static fn( $e ) => $e['verified'] && $e['date'] ) );
$pathway    = mwm_find_pathway( $level, $board );
$pdata      = $pathway ? mwm_pathway_data( $pathway ) : null;
$plan       = $pdata ? $pdata['plan'] : [];
$revision   = trailingslashit( mwm_page_url( 'revision' ) ) . $level . '/' . $board . '/';
$verified   = $exams ? $exams[0]['verified_label'] : '';
$season     = $exams ? ( $exams[0]['month'] >= 5 && $exams[0]['month'] <= 7 ? 'Summer ' : 'Autumn ' ) . $exams[0]['year'] : '';

// Months to draw: from the earliest plan/exam month to the latest exam month.
$dates = array_merge( array_column( $exams, 'date' ), array_column( $plan, 'week' ) );
$dates = array_values( array_filter( $dates ) );
$months = [];
if ( $dates ) {
	sort( $dates );
	$start = strtotime( gmdate( 'Y-m-01', strtotime( $dates[0] ) ) );
	$end   = strtotime( gmdate( 'Y-m-01', strtotime( end( $dates ) ) ) );
	for ( $m = $start; $m <= $end; $m = strtotime( '+1 month', $m ) ) {
		$months[] = $m;
	}
}
$paper_short = static function ( string $paper ): string {
	return preg_match( '/Paper\s*(\d+)/i', $paper, $mm ) ? 'Paper ' . $mm[1] : $paper;
};
$exam_by_day = [];
foreach ( $exams as $e ) {
	$exam_by_day[ $e['date'] ] = $e;
}
$shade = [];
$plan_label = [];
foreach ( $plan as $w ) {
	if ( ! $w['week'] ) {
		continue;
	}
	$mon = strtotime( $w['week'] );
	for ( $i = 0; $i < 5; $i++ ) {
		$shade[ gmdate( 'Y-m-d', $mon + $i * 86400 ) ] = true;
	}
	$plan_label[ gmdate( 'Y-m-d', $mon ) ] = $w['short'] ?: $w['focus'];
}
?>
<div class="mwm-page">
	<?php echo mwm_breadcrumb( [ [ 'label' => 'Revision', 'url' => mwm_page_url( 'revision' ) ], [ 'label' => $heading ] ] ); ?>
	<h1 class="mwm-h1"><?php echo esc_html( $heading ); ?></h1>
	<p class="mwm-intro"><?php echo esc_html( trim( $level_name . ' · ' . $board_name . ( $season ? ' · ' . $season : '' ) ) . '. ' . $intro ); ?></p>
	<?php if ( $verified ) : ?>
		<p class="mwm-note">Dates come from our verified exam records (checked <?php echo esc_html( $verified ); ?>). Always confirm with your school’s own timetable.</p>
	<?php else : ?>
		<p class="mwm-note">We haven’t verified <?php echo esc_html( $board_name ); ?> dates for <?php echo esc_html( $level_name ); ?> yet — they’ll appear here once checked. Always confirm with your school’s own timetable.</p>
	<?php endif; ?>

	<?php if ( $months ) : ?>
		<div class="mwm-legend">
			<span class="mwm-legend__item"><span class="mwm-legend__exam"><?php echo esc_html( $exams ? $exams[0]['day'] : '13' ); ?></span>Exam paper</span>
			<span class="mwm-legend__item"><span class="mwm-legend__shade"></span>Planned revision</span>
		</div>
		<div class="mwm-months">
			<?php foreach ( $months as $m ) : ?>
				<?php
				$days_in   = (int) gmdate( 't', $m );
				$start_col = ( (int) gmdate( 'N', $m ) ) - 1;
				$ym        = gmdate( 'Y-m', $m );
				?>
				<div class="mwm-month">
					<h2 class="mwm-h3"><?php echo esc_html( gmdate( 'F Y', $m ) ); ?></h2>
					<div class="mwm-month__grid" role="grid" aria-label="<?php echo esc_attr( gmdate( 'F Y', $m ) ); ?>">
						<?php foreach ( [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ] as $d ) : ?><span class="mwm-month__dow"><?php echo esc_html( $d ); ?></span><?php endforeach; ?>
						<?php for ( $i = 0; $i < $start_col; $i++ ) : ?><div class="mwm-day" aria-hidden="true"></div><?php endfor; ?>
						<?php for ( $d = 1; $d <= $days_in; $d++ ) : ?>
							<?php
							$ymd   = sprintf( '%s-%02d', $ym, $d );
							$exam  = $exam_by_day[ $ymd ] ?? null;
							$label = $exam ? $paper_short( $exam['paper'] ) . ' · ' . ( $exam['session'] === 'morning' ? 'am' : 'pm' ) : ( $plan_label[ $ymd ] ?? '' );
							$cls   = 'mwm-day' . ( $exam ? ' mwm-day--exam' : ( isset( $shade[ $ymd ] ) ? ' mwm-day--shaded' : '' ) );
							?>
							<div class="<?php echo esc_attr( $cls ); ?>"<?php echo $exam ? ' aria-label="' . esc_attr( $exam['paper'] . ', ' . $exam['full'] . ', ' . $exam['session_label'] ) . '"' : ''; ?>>
								<span class="mwm-day__num"><?php echo (int) $d; ?></span>
								<?php if ( $label ) : ?><span class="mwm-day__label"><?php echo esc_html( $label ); ?></span><?php endif; ?>
							</div>
						<?php endfor; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( $plan ) : ?>
		<h2 class="mwm-h2 mwm-plan__h2"><?php echo esc_html( $plan_heading ); ?></h2>
		<p class="mwm-note" style="max-width:none"><?php echo esc_html( $plan_sub ); ?></p>
		<div class="mwm-list">
			<?php foreach ( $plan as $w ) : ?>
				<?php
				$mon = $w['week'] ? strtotime( $w['week'] ) : 0;
				$exam_in_week = null;
				foreach ( $exams as $e ) {
					$t = strtotime( $e['date'] );
					if ( $mon && $t >= $mon && $t < $mon + 7 * 86400 ) {
						$exam_in_week = $e;
						break;
					}
				}
				?>
				<div class="mwm-plan__row">
					<span class="mwm-plan__week"><?php echo esc_html( $mon ? mwm_format_date( $w['week'], 'week' ) : '' ); ?></span>
					<span class="mwm-plan__focus"><?php echo esc_html( $w['focus'] ); ?></span>
					<?php if ( $exam_in_week ) : ?>
						<?php echo mwm_tag( $paper_short( $exam_in_week['paper'] ) . ' · ' . gmdate( 'D j', strtotime( $exam_in_week['date'] ) ), 'action' ); ?>
					<?php endif; ?>
					<?php echo mwm_arrow_link( $revision, 'Open pathway', 'mwm-arrow--sm' ); ?>
				</div>
			<?php endforeach; ?>
			<div class="mwm-list__foot">
				<span class="mwm-meta">Practice weeks use the past papers page — every paper and mark scheme as PDFs.</span>
				<?php echo mwm_arrow_link( mwm_page_url( 'past-papers' ), 'Browse past papers', 'mwm-arrow--sm' ); ?>
			</div>
		</div>
	<?php elseif ( ! $months ) : ?>
		<div class="mwm-empty">
			<p class="mwm-empty__title">No dates on the calendar yet</p>
			<p class="mwm-empty__body">Verified exam dates and the suggested revision plan will appear here. The pathway is ready to use in the meantime.</p>
			<?php echo mwm_button( $revision, 'Open the pathway', 'secondary', [ 'style' => 'margin-top:20px' ] ); ?>
		</div>
	<?php endif; ?>
</div>
