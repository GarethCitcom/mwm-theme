<?php
/**
 * "Next in your pathway" strip under a lesson.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$id   = get_the_ID();
$card = mwm_lesson_card( $id );
if ( ! $card || ! $card['level_slug'] ) {
	return;
}
$prefs   = mwm_user_prefs();
$pathway = mwm_find_pathway( $card['level_slug'], $prefs['board'] );
if ( ! $pathway ) {
	return;
}
$data = mwm_pathway_data( $pathway );
$rows = [];
foreach ( $data['groups'] as $g ) {
	foreach ( $g['rows'] as $r ) {
		$rows[] = $r;
	}
}
$next  = null;
$found = false;
foreach ( $rows as $r ) {
	if ( $found && ! $r['coming_soon'] ) {
		$next = $r;
		break;
	}
	if ( (int) $r['lesson_id'] === (int) $id ) {
		$found = true;
	}
}
$revision = trailingslashit( mwm_page_url( 'revision' ) ) . $card['level_slug'] . '/' . $prefs['board'] . '/';
if ( $next ) {
	$label = 'Next in your pathway';
	$title = $next['topic'];
	$link  = 'Continue pathway';
} elseif ( $found ) {
	$label = 'You’ve reached the end of the pathway';
	$title = $data['level_name'] . ' revision pathway';
	$link  = 'Review your pathway';
} else {
	$label = 'Keep going with a pathway';
	$title = $data['level_name'] . ' revision pathway';
	$link  = 'Open pathway';
}
?>
<div class="mwm-cta mwm-cta--next">
	<div>
		<span class="mwm-cta__label"><?php echo esc_html( $label ); ?></span>
		<div class="mwm-cta__title"><?php echo esc_html( $title ); ?></div>
	</div>
	<?php echo mwm_arrow_link( $revision, $link ); ?>
</div>
