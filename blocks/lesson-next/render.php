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
if ( ! $card || ! mwm_card_levels( $card ) ) {
	return;
}
$prefs = mwm_user_prefs();
// A lesson can suit more than one level. Try the visitor's own level first, then the others, and use the first
// pathway that actually lists this lesson; failing that, the first pathway found.
$first  = mwm_card_level( $card );
$levels = array_values( array_unique( array_merge( [ $first ], mwm_card_levels( $card ) ) ) );
$level  = '';
$data   = null;
$rows   = [];
foreach ( $levels as $try ) {
	$pathway = mwm_find_pathway( $try, $prefs['board'] );
	if ( ! $pathway ) {
		continue;
	}
	$try_data = mwm_pathway_data( $pathway );
	$try_rows = [];
	foreach ( $try_data['groups'] as $g ) {
		foreach ( $g['rows'] as $r ) {
			$try_rows[] = $r;
		}
	}
	$listed = in_array( (int) $id, array_map( static fn( $r ) => (int) $r['lesson_id'], $try_rows ), true );
	if ( ! $data || $listed ) {
		[ $level, $data, $rows ] = [ $try, $try_data, $try_rows ];
	}
	if ( $listed ) {
		break;
	}
}
if ( ! $data ) {
	return;
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
$revision = trailingslashit( mwm_page_url( 'revision' ) ) . $level . '/' . $prefs['board'] . '/';
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
