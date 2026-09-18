<?php
/**
 * My Learning: signed-out feature list, or the visitor's pathway progress, quiz scores and saved lessons.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$heading = mwm_field( 'heading', 'My Learning' );
$intro   = mwm_field( 'intro', 'Sign in to keep your progress across devices. Here’s what My Learning does:' );
$signed  = is_user_logged_in();
$revision = mwm_page_url( 'revision' );

if ( ! $signed ) :
	$login    = wp_login_url( get_permalink() );
	$register = get_option( 'users_can_register' ) ? wp_registration_url() : '';
	?>
	<div class="mwm-page mwm-page--narrow">
		<h1 class="mwm-h1 mwm-h1--flush"><?php echo esc_html( $heading ); ?></h1>
		<p class="mwm-intro"><?php echo esc_html( $intro ); ?></p>
		<div class="mwm-features">
			<div class="mwm-features__row"><span class="mwm-features__icon"><?php echo mwm_icon( 'bookmark', 16 ); ?></span><span>Save lessons to watch later</span></div>
			<div class="mwm-features__row"><span class="mwm-features__icon"><?php echo mwm_icon( 'tick', 16 ); ?></span><span>Tick off revision pathway topics across your devices</span></div>
			<div class="mwm-features__row"><span class="mwm-features__icon"><?php echo mwm_icon( 'bars', 16 ); ?></span><span>Keep your quiz scores and see where to focus</span></div>
		</div>
		<div class="mwm-actions">
			<?php echo mwm_button( $login, 'Sign in' ); ?>
			<?php echo mwm_button( $revision, 'Start revising', 'secondary' ); ?>
			<?php if ( $register ) : ?><a href="<?php echo esc_url( $register ); ?>" class="mwm-arrow">Create an account<span aria-hidden="true">→</span></a><?php endif; ?>
		</div>
		<p class="mwm-note" style="margin-top:16px">Not signed in? Saves, ticks and scores still work — they stay in this browser until you sign in.</p>
	</div>
	<?php
	return;
endif;

$user     = mwm_current_user_label();
$progress = MWM_Progress::get( get_current_user_id() );
$prefs    = mwm_user_prefs();
$level    = $prefs['level'];
$board    = $prefs['board'];
$pathway  = mwm_find_pathway( $level, $board );
$pdata    = $pathway ? mwm_pathway_data( $pathway ) : null;
$ticked   = $pdata ? (array) ( $progress['ticks'][ (string) $pdata['id'] ] ?? [] ) : [];
$rows     = [];
if ( $pdata ) {
	foreach ( $pdata['groups'] as $g ) {
		foreach ( $g['rows'] as $r ) {
			$rows[] = $r;
		}
	}
}
$total  = count( $rows );
$count  = 0;
$next   = null;
foreach ( $rows as $r ) {
	if ( in_array( $r['key'], $ticked, true ) ) {
		$count++;
	} elseif ( ! $next && ! $r['coming_soon'] ) {
		$next = $r;
	}
}
$pct = $total ? (int) round( $count / $total * 100 ) : 0;
$avg = MWM_Progress::quiz_average( $progress );
$quiz_rows = [];
foreach ( (array) $progress['quizzes'] as $qid => $res ) {
	$q = get_post( (int) $qid );
	if ( ! $q || $q->post_status !== 'publish' ) {
		continue;
	}
	$lid = (int) get_post_meta( $q->ID, 'lesson', true );
	$quiz_rows[] = [ 'title' => $lid ? mwm_title( $lid ) : mwm_title( $q->ID ), 'url' => get_permalink( $q ), 'score' => (int) $res['score'], 'total' => (int) $res['total'], 'at' => $res['at'] ?? '' ];
}
usort( $quiz_rows, static fn( $a, $b ) => strcmp( $b['at'], $a['at'] ) );
$saved = [];
foreach ( array_reverse( (array) $progress['saved'] ) as $lid ) {
	$c = mwm_lesson_card( (int) $lid );
	if ( $c ) {
		$saved[] = $c;
	}
}
?>
<div class="mwm-page mwm-page--wide-top">
	<div class="mwm-ml__head">
		<div class="mwm-ml__user">
			<span class="mwm-avatar mwm-avatar--lg" aria-hidden="true"><?php echo esc_html( $user['initial'] ); ?></span>
			<div>
				<h1 class="mwm-h1 mwm-h1--flush">Hi <?php echo esc_html( $user['name'] ); ?></h1>
				<p class="mwm-ml__sub"><?php echo esc_html( mwm_level_name( $level ) . ' · ' . mwm_board_name( $board ) ); ?></p>
			</div>
		</div>
		<div class="mwm-actions" style="margin-top:0">
			<?php if ( class_exists( 'MWM_Studio' ) && current_user_can( MWM_Activator::CAP ) ) : ?>
				<a href="<?php echo esc_url( MWM_Studio::url() ); ?>" class="mwm-btn mwm-btn--primary mwm-btn--sm">Open the Studio</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="mwm-btn mwm-btn--quiet">Sign out</a>
		</div>
	</div>
	<div class="mwm-ml-grid">
		<div class="mwm-panel">
			<h2 class="mwm-panel__h2">Your pathway</h2>
			<p class="mwm-panel__sub"><?php echo esc_html( mwm_level_name( $level ) . ' revision pathway' ); ?></p>
			<?php if ( $pdata ) : ?>
				<div class="mwm-progress__labels mwm-ml__labels"><span><?php echo (int) $count; ?> of <?php echo (int) $total; ?> ticked</span><span><?php echo (int) $pct; ?>%</span></div>
				<div class="mwm-bar"><div class="mwm-bar__fill" style="width:<?php echo (int) $pct; ?>%"></div></div>
				<p class="mwm-ml__next"><?php echo $next ? 'Next up: ' . esc_html( $next['topic'] ) : 'Every topic ticked — brilliant.'; ?></p>
				<?php echo mwm_arrow_link( trailingslashit( $revision ) . $level . '/' . $board . '/', 'Continue pathway', 'mwm-arrow--mt16' ); ?>
			<?php else : ?>
				<p class="mwm-ml__empty">This pathway is coming soon.</p>
				<?php echo mwm_arrow_link( $revision, 'See the pathways', 'mwm-arrow--mt16' ); ?>
			<?php endif; ?>
		</div>
		<div class="mwm-panel">
			<h2 class="mwm-panel__h2">Quiz scores</h2>
			<?php if ( $quiz_rows ) : ?>
				<?php foreach ( $quiz_rows as $qr ) : ?>
					<div class="mwm-ml__quizrow"><a href="<?php echo esc_url( $qr['url'] ); ?>"><?php echo esc_html( $qr['title'] ); ?></a><span class="mwm-ml__score"><?php echo (int) $qr['score']; ?> / <?php echo (int) $qr['total']; ?></span></div>
				<?php endforeach; ?>
				<p class="mwm-ml__avg">Average <?php echo (int) $avg; ?>% · Quiz scores are separate from your pathway ticks.</p>
			<?php else : ?>
				<p class="mwm-ml__empty">No quizzes yet — lessons with a quiz show one under Practice.</p>
				<?php echo mwm_arrow_link( add_query_arg( 'quiz', '1', mwm_browse_url( $level ) ), 'Find a lesson with a quiz', 'mwm-arrow--mt16' ); ?>
			<?php endif; ?>
		</div>
		<div class="mwm-panel">
			<h2 class="mwm-panel__h2">Saved lessons</h2>
			<?php if ( $saved ) : ?>
				<?php foreach ( $saved as $c ) : ?>
					<a href="<?php echo esc_url( $c['url'] ); ?>" class="mwm-ml__saved"><span class="mwm-ml__thumb"<?php echo $c['thumb'] ? ' style="background-image:url(' . esc_url( mwm_thumb_small( $c['thumb'] ) ) . ')"' : ''; ?>></span><span class="mwm-ml__savedtitle"><?php echo esc_html( $c['title'] ); ?></span></a>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="mwm-ml__empty">Nothing saved yet — press Save on any lesson to keep it here.</p>
			<?php endif; ?>
			<?php echo mwm_arrow_link( mwm_page_url( 'browse' ), 'Find more lessons', 'mwm-arrow--mt8' ); ?>
		</div>
	</div>
</div>
