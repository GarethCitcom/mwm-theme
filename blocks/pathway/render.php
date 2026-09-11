<?php
/**
 * Revision pathway: level/board selectors, progress, grouped topic rows with ticks, exam + past papers sidebar.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'pathway' );

$st      = MWM_Rewrites::pathway_state();
$level   = $st['level'];
$board   = $st['board'];
$post    = mwm_find_pathway( $level, $board );
$data    = $post ? mwm_pathway_data( $post ) : null;
$exam    = mwm_next_exam( $level, $board );
$has_any_exam = (bool) mwm_exam_dates( $level, $board );
$level_name = mwm_level_name( $level );
$board_name = mwm_board_name( $board );
$revision   = trailingslashit( mwm_page_url( 'revision' ) );
$total      = $data ? $data['total'] : 0;
$line       = $data
	? ( $data['complete'] ? "$total topics in order" : "$total topics so far · more being added" )
	: 'Coming soon';
$tier = $level === 'a-level' ? 'A-level' : str_replace( 'GCSE ', '', $level_name );
?>
<div class="mwm-page" data-pathway="<?php echo $data ? (int) $data['id'] : 0; ?>" data-total="<?php echo (int) $total; ?>" data-level="<?php echo esc_attr( $level ); ?>" data-board="<?php echo esc_attr( $board ); ?>" data-base="<?php echo esc_url( $revision ); ?>">
	<div class="mwm-selects">
		<label class="mwm-select-label">Level
			<select class="mwm-select" data-select="level">
				<?php foreach ( mwm_levels() as $slug => $l ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $slug, $level ); ?>><?php echo esc_html( $l['name'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label class="mwm-select-label">Exam board
			<select class="mwm-select" data-select="board">
				<?php foreach ( mwm_boards() as $slug => $name ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $slug, $board ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>

	<h1 class="mwm-h1 mwm-pathway__h1"><?php echo esc_html( $level_name . ' revision pathway' ); ?></h1>
	<p class="mwm-pathway__line"><?php echo esc_html( $line ); ?></p>
	<div class="mwm-progress-row">
		<div class="mwm-progress">
			<div class="mwm-progress__labels"><span data-progress-label>0 of <?php echo (int) $total; ?> ticked</span><span data-progress-pct>0%</span></div>
			<div class="mwm-bar"><div class="mwm-bar__fill" data-progress-bar style="width:0%"></div></div>
			<p class="mwm-progress__hint">Ticks are yours to set; quiz scores are shown separately.</p>
		</div>
		<div class="mwm-stat">
			<span class="mwm-stat__label">Quiz average</span>
			<span class="mwm-stat__value" data-quiz-average>—</span>
		</div>
	</div>

	<div class="mwm-cols mwm-cols--48">
		<div class="mwm-cols__main">
			<?php if ( $data && $data['groups'] ) : ?>
				<?php foreach ( $data['groups'] as $g ) : ?>
					<div class="mwm-pw-group">
						<h2 class="mwm-h2 mwm-pw-group__h2"><?php echo esc_html( $g['name'] ); ?></h2>
						<?php foreach ( $g['rows'] as $r ) : ?>
							<div class="mwm-pw-row" data-row="<?php echo esc_attr( $r['key'] ); ?>">
								<span class="mwm-pw-row__step"><?php echo esc_html( $r['step'] ); ?></span>
								<div class="mwm-pw-row__main">
									<?php if ( $r['coming_soon'] ) : ?>
										<span class="mwm-pw-row__title mwm-pw-row__title--soon"><?php echo esc_html( $r['topic'] ); ?></span>
									<?php elseif ( $r['url'] ) : ?>
										<a href="<?php echo esc_url( $r['url'] ); ?>" class="mwm-pw-row__title"><?php echo esc_html( $r['topic'] ); ?></a>
									<?php else : ?>
										<span class="mwm-pw-row__title"><?php echo esc_html( $r['topic'] ); ?></span>
									<?php endif; ?>
									<?php if ( $r['note'] ) : ?><span class="mwm-pw-row__note"><?php echo esc_html( $r['note'] ); ?></span><?php endif; ?>
								</div>
								<?php if ( $r['coming_soon'] ) : ?>
									<span class="mwm-pw-row__soon">Coming soon</span>
								<?php else : ?>
									<span class="mwm-pw-row__icons">
										<?php
										$res = [
											[ 'video', $r['video'], $r['url'] ],
											[ 'worksheet', $r['worksheet'], $r['url'] ],
											[ 'quiz', $r['quiz'], $r['quiz_url'] ?: $r['url'] ],
										];
										foreach ( $res as [ $kind, $on, $href ] ) {
											if ( $on && $href ) {
												echo '<a href="' . esc_url( $href ) . '" class="mwm-res" aria-label="' . esc_attr( 'Open lesson (' . $kind . ' available)' ) . '" title="' . esc_attr( $kind . ' available' ) . '">' . mwm_icon( $kind, 16 ) . '</a>';
											} else {
												echo '<span class="mwm-res mwm-res--off" title="' . esc_attr( $kind . ' not available yet' ) . '">' . mwm_icon( $kind, 16 ) . '</span>';
											}
										}
										?>
									</span>
									<button type="button" class="mwm-tick" aria-pressed="false" aria-label="<?php echo esc_attr( 'Mark ' . $r['topic'] . ' as done' ); ?>" data-tick="<?php echo esc_attr( $r['key'] ); ?>" data-topic="<?php echo esc_attr( $r['topic'] ); ?>"><?php echo mwm_icon( 'tick', 14, [ 'stroke' => '#FFFFFF', 'stroke-width' => '2' ] ); ?></button>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="mwm-empty" style="margin-top:0">
					<p class="mwm-empty__title">The <?php echo esc_html( $level_name ); ?> pathway is coming soon</p>
					<p class="mwm-empty__body"><?php echo $level === 'a-level' ? 'Pure, Statistics and Mechanics are being recorded now. The GCSE pathways are ready to use.' : 'Topics are being lined up now. The other pathways are ready to use.'; ?></p>
				</div>
			<?php endif; ?>
		</div>

		<aside class="mwm-cols__side" aria-label="Exam and past papers">
			<div class="mwm-panel">
				<span class="mwm-exam-side__label">Next exam for this pathway (<?php echo esc_html( $board_name ); ?>)</span>
				<?php if ( $exam ) : ?>
					<div class="mwm-exam-side__date"><?php echo esc_html( $exam['full'] ); ?></div>
					<div class="mwm-exam-side__sub"><?php echo esc_html( $exam['session_label'] . ' · ' . $exam['paper'] ); ?></div>
					<div class="mwm-exam-side__note"><?php echo esc_html( mwm_exam_verified_note( $exam ) ); ?></div>
				<?php else : ?>
					<div class="mwm-exam-side__unverified">Exam dates will be added when confirmed.</div>
					<div class="mwm-exam-side__unverified-note">We haven’t verified the <?php echo esc_html( $board_name ); ?> mapping yet — check your own timetable with your school.</div>
				<?php endif; ?>
				<?php echo mwm_arrow_link( add_query_arg( [ 'level' => $level, 'board' => $board ], mwm_page_url( 'calendar' ) ), 'See the exam calendar', 'mwm-arrow--mt16' ); ?>
			</div>
			<div class="mwm-panel">
				<h2 class="mwm-panel__h2">Past papers · <?php echo esc_html( $board_name . ' ' . $tier ); ?></h2>
				<p class="mwm-panel__sub">Every paper and mark scheme as free PDFs, with matching worksheets.</p>
				<?php echo mwm_arrow_link( add_query_arg( [ 'tier' => strtolower( $tier ), 'board' => $board ], mwm_page_url( 'past-papers' ) ), 'Browse past papers', 'mwm-arrow--mt12' ); ?>
			</div>
		</aside>
	</div>
</div>
