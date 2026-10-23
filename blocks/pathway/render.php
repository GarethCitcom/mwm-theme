<?php
/**
 * Revision: a level's topic overview (/revision/{level}/) or one topic's pathway (/revision/{level}/{topic}/),
 * the topic's subtopics in revision order with their lessons and a tick per subtopic. Exam + papers sidebar.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
mwm_block_script( 'pathway' );

$st         = MWM_Rewrites::pathway_state();
$level      = $st['level'];
$board      = $st['board'];
$level_name = mwm_level_name( $level );
$board_name = mwm_board_name( $board );
$pathway    = $st['topic'] ? mwm_pathway( $level, $st['topic'] ) : null;
$topics     = mwm_pathway_overview( $level );
$exam       = mwm_next_exam( $level, $board );
$tier       = $level === 'a-level' ? 'A-level' : str_replace( 'GCSE ', '', $level_name );
$base       = trailingslashit( mwm_page_url( 'revision' ) );
$level_urls = [];
foreach ( mwm_levels() as $slug => $l ) {
	$level_urls[ $slug ] = mwm_pathway_url( $slug );
}
// Prev / next topic for the footer of a topic page.
$prev = $next = null;
if ( $pathway ) {
	foreach ( $topics as $i => $t ) {
		if ( $t['topic'] === $pathway['topic'] ) {
			$prev = $topics[ $i - 1 ] ?? null;
			$next = $topics[ $i + 1 ] ?? null;
		}
	}
}
$island = [
	'level'    => $level,
	'board'    => $board,
	'base'     => $base,
	'pathways' => array_map( static fn( $t ) => [ 'id' => $t['id'], 'keys' => $t['keys'] ], $topics ),
	'current'  => $pathway ? [ 'id' => $pathway['id'], 'keys' => array_column( array_filter( $pathway['rows'], static fn( $r ) => ! $r['coming_soon'] ), 'key' ) ] : null,
];
?>
<div class="mwm-page" data-pathway-page data-level="<?php echo esc_attr( $level ); ?>" data-base="<?php echo esc_url( $base ); ?>">
	<?php if ( $pathway ) : ?>
		<?php echo mwm_breadcrumb( [ [ 'label' => 'Revision', 'url' => mwm_page_url( 'revision' ) ], [ 'label' => $level_name, 'url' => mwm_pathway_url( $level ) ], [ 'label' => $pathway['topic_name'] ] ] ); ?>
		<h1 class="mwm-h1"><?php echo esc_html( $pathway['topic_name'] ); ?> <span class="mwm-h1__muted">· <?php echo esc_html( $level_name ); ?></span></h1>
		<p class="mwm-pathway__line">
			<?php
			$bits = [ $pathway['total'] . ( $pathway['total'] === 1 ? ' subtopic' : ' subtopics' ) . ' in order' ];
			if ( $pathway['coming_soon'] ) {
				$bits[] = $pathway['coming_soon'] . ' coming soon';
			}
			echo esc_html( implode( ' · ', $bits ) );
			?>
		</p>
	<?php else : ?>
		<?php echo mwm_breadcrumb( [ [ 'label' => 'Revision', 'url' => mwm_page_url( 'revision' ) ], [ 'label' => $level_name ] ] ); ?>
		<h1 class="mwm-h1"><?php echo esc_html( $level_name . ' revision pathways' ); ?></h1>
		<p class="mwm-pathway__line">Pick a topic. Each one lists its subtopics in the order to revise them; tick them off as you go.</p>
		<?php echo mwm_segmented( array_map( static fn( $l ) => $l['name'], mwm_levels() ), $level, 'Level', 'level', $level_urls ); ?>
	<?php endif; ?>

	<div class="mwm-progress-row" data-progress-for="<?php echo esc_attr( $pathway ? $pathway['id'] : 'level' ); ?>">
		<div class="mwm-progress">
			<div class="mwm-progress__labels"><span data-progress-label>0 ticked</span><span data-progress-pct>0%</span></div>
			<div class="mwm-bar"><div class="mwm-bar__fill" data-progress-bar style="width:0%"></div></div>
			<p class="mwm-progress__hint"><?php echo $pathway ? 'Ticks are yours to set; quiz scores are shown separately.' : 'Your ticks across every ' . esc_html( $level_name ) . ' topic.'; ?></p>
		</div>
		<div class="mwm-stat">
			<span class="mwm-stat__label">Quiz average</span>
			<span class="mwm-stat__value" data-quiz-average>—</span>
		</div>
	</div>

	<div class="mwm-cols mwm-cols--48">
		<div class="mwm-cols__main">
			<?php if ( $pathway ) : ?>
				<?php if ( $pathway['rows'] ) : ?>
					<div class="mwm-pw-group" data-pathway="<?php echo esc_attr( $pathway['id'] ); ?>">
						<?php foreach ( $pathway['rows'] as $r ) : ?>
							<div class="mwm-pw-row<?php echo $r['coming_soon'] ? ' mwm-pw-row--soon' : ''; ?>" data-row="<?php echo esc_attr( $r['key'] ); ?>">
								<span class="mwm-pw-row__step"><?php echo esc_html( $r['step'] ); ?></span>
								<div class="mwm-pw-row__main">
									<?php if ( count( $r['lessons'] ) === 1 ) : ?>
										<a href="<?php echo esc_url( $r['lessons'][0]['url'] ); ?>" class="mwm-pw-row__title"><?php echo esc_html( $r['name'] ); ?></a>
									<?php else : ?>
										<span class="mwm-pw-row__title<?php echo $r['coming_soon'] ? ' mwm-pw-row__title--soon' : ''; ?>"><?php echo esc_html( $r['name'] ); ?></span>
									<?php endif; ?>
									<?php if ( $r['note'] ) : ?><span class="mwm-pw-row__note"><?php echo esc_html( $r['note'] ); ?></span><?php endif; ?>
									<?php if ( count( $r['lessons'] ) === 1 && $r['lessons'][0]['quiz_id'] ) : ?>
										<a href="<?php echo esc_url( $r['lessons'][0]['quiz_url'] ); ?>" class="mwm-quiz-result" data-quiz-result="<?php echo (int) $r['lessons'][0]['quiz_id']; ?>" hidden></a>
									<?php endif; ?>
									<?php if ( count( $r['lessons'] ) > 1 ) : ?>
										<ul class="mwm-pw-row__lessons">
											<?php foreach ( $r['lessons'] as $l ) : ?>
												<li><a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['title'] ); ?></a><?php if ( $l['quiz_id'] ) : ?> <a href="<?php echo esc_url( $l['quiz_url'] ); ?>" class="mwm-quiz-result" data-quiz-result="<?php echo (int) $l['quiz_id']; ?>" hidden></a><?php endif; ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
								<?php if ( $r['coming_soon'] ) : ?>
									<span class="mwm-pw-row__soon">Coming soon</span>
								<?php else : ?>
									<span class="mwm-pw-row__icons">
										<?php
										$first = $r['lessons'][0];
										$res   = [
											[ 'video', true, $first['url'] ],
											[ 'worksheet', $r['worksheet'], $first['worksheet_url'] ?: $first['url'] ],
											[ 'quiz', $r['quiz'], $first['quiz_url'] ?: $first['url'] ],
										];
										foreach ( $res as [ $kind, $on, $href ] ) {
											if ( $on && $href ) {
												echo '<a href="' . esc_url( $href ) . '" class="mwm-res" aria-label="' . esc_attr( 'Open ' . $kind ) . '" title="' . esc_attr( $kind . ' available' ) . '">' . mwm_icon( $kind, 16 ) . '</a>';
											} else {
												echo '<span class="mwm-res mwm-res--off" title="' . esc_attr( $kind . ' not available yet' ) . '">' . mwm_icon( $kind, 16 ) . '</span>';
											}
										}
										?>
									</span>
									<button type="button" class="mwm-tick" aria-pressed="false" aria-label="<?php echo esc_attr( 'Mark ' . $r['name'] . ' as done' ); ?>" data-tick="<?php echo esc_attr( $r['key'] ); ?>" data-topic="<?php echo esc_attr( $r['name'] ); ?>"><?php echo mwm_icon( 'tick', 14, [ 'stroke' => '#FFFFFF', 'stroke-width' => '2' ] ); ?></button>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<div class="mwm-empty" style="margin-top:0">
						<p class="mwm-empty__title">The <?php echo esc_html( $level_name . ' ' . $pathway['topic_name'] ); ?> pathway is coming soon</p>
						<p class="mwm-empty__body">Subtopics are being lined up now. Every lesson is already on Learn Maths in the meantime.</p>
						<?php echo mwm_button( mwm_browse_url( $level, $pathway['topic'] ), 'Browse ' . $pathway['topic_name'] . ' lessons', 'secondary', [ 'style' => 'margin-top:20px' ] ); ?>
					</div>
				<?php endif; ?>
				<nav class="mwm-prevnext" aria-label="Other topics">
					<?php if ( $prev ) : ?><a href="<?php echo esc_url( $prev['url'] ); ?>" class="mwm-prevnext__link"><span class="mwm-prevnext__label">Previous topic</span><?php echo esc_html( $prev['topic_name'] ); ?></a><?php else : ?><span></span><?php endif; ?>
					<?php if ( $next ) : ?><a href="<?php echo esc_url( $next['url'] ); ?>" class="mwm-prevnext__link mwm-prevnext__link--next"><span class="mwm-prevnext__label">Next topic</span><?php echo esc_html( $next['topic_name'] ); ?></a><?php endif; ?>
				</nav>
			<?php elseif ( $topics ) : ?>
				<div class="mwm-pw-topics">
					<?php foreach ( $topics as $t ) : ?>
						<a href="<?php echo esc_url( $t['url'] ); ?>" class="mwm-pw-topic" data-pathway-card="<?php echo esc_attr( $t['id'] ); ?>" data-total="<?php echo count( $t['keys'] ); ?>">
							<span class="mwm-pw-topic__icon"><?php echo mwm_topic_icon( $t['icon'], 18 ); ?></span>
							<span class="mwm-pw-topic__name"><?php echo esc_html( $t['topic_name'] ); ?></span>
							<span class="mwm-pw-topic__meta">
								<?php
								if ( ! $t['total'] ) {
									echo 'Coming soon';
								} else {
									echo esc_html( $t['total'] . ( $t['total'] === 1 ? ' subtopic' : ' subtopics' ) . ( $t['coming_soon'] ? ' · ' . $t['coming_soon'] . ' coming soon' : '' ) );
								}
								?>
							</span>
							<span class="mwm-pw-topic__progress"><span class="mwm-bar mwm-bar--12"><span class="mwm-bar__fill" data-card-bar style="width:0%"></span></span><span class="mwm-pw-topic__count" data-card-count></span></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="mwm-empty" style="margin-top:0">
					<p class="mwm-empty__title">The <?php echo esc_html( $level_name ); ?> pathways are coming soon</p>
					<p class="mwm-empty__body"><?php echo esc_html( $level === 'a-level' ? 'Pure, Statistics and Mechanics are being recorded now.' : 'Topics are being lined up now.' ); ?> Every lesson is already on Learn Maths in the meantime.</p>
				</div>
			<?php endif; ?>
		</div>

		<aside class="mwm-cols__side" aria-label="Exam, past papers and predicted papers">
			<div class="mwm-panel">
				<span class="mwm-exam-side__label">Next <?php echo esc_html( $level_name ); ?> exam</span>
				<label class="mwm-select-label mwm-exam-side__board">Exam board
					<select class="mwm-select" data-select="board">
						<?php foreach ( mwm_boards() as $slug => $name ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>"<?php selected( $slug, $board ); ?>><?php echo esc_html( $name ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<?php if ( $exam ) : ?>
					<div class="mwm-exam-side__date"><?php echo esc_html( $exam['full'] ); ?></div>
					<div class="mwm-exam-side__sub"><?php echo esc_html( $exam['session_label'] . ' · ' . $exam['paper'] ); ?></div>
					<div class="mwm-exam-side__note"><?php echo esc_html( mwm_exam_verified_note( $exam ) ); ?></div>
				<?php else : ?>
					<div class="mwm-exam-side__unverified">Exam dates will be added when confirmed.</div>
					<div class="mwm-exam-side__unverified-note">We haven’t verified the <?php echo esc_html( $board_name ); ?> dates yet. Check your own timetable with your school.</div>
				<?php endif; ?>
				<?php echo mwm_arrow_link( add_query_arg( [ 'level' => $level, 'board' => $board ], mwm_page_url( 'calendar' ) ), 'See the exam calendar', 'mwm-arrow--mt16' ); ?>
			</div>
			<div class="mwm-panel">
				<h2 class="mwm-panel__h2">Past papers · <?php echo esc_html( $board_name . ' ' . $tier ); ?></h2>
				<p class="mwm-panel__sub">Every paper and mark scheme as free PDFs, with matching worksheets.</p>
				<?php echo mwm_arrow_link( add_query_arg( [ 'tier' => strtolower( $tier ), 'board' => $board ], mwm_page_url( 'past-papers' ) ), 'Browse past papers', 'mwm-arrow--mt12' ); ?>
			</div>
			<div class="mwm-panel">
				<h2 class="mwm-panel__h2">Predicted papers · <?php echo esc_html( $board_name . ' ' . $tier ); ?></h2>
				<p class="mwm-panel__sub">Melissa’s own papers for the next exam, with worked solutions that explain every step.</p>
				<?php echo mwm_arrow_link( add_query_arg( [ 'level' => $level, 'board' => $board ], mwm_page_url( 'predicted-papers' ) ), 'Try a predicted paper', 'mwm-arrow--mt12' ); ?>
			</div>
		</aside>
	</div>
	<?php echo mwm_json_script( 'mwm-pathway-data', $island ); ?>
</div>
