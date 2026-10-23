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
// A lesson can suit more than one level: try the visitor's own level first, then the others, and use the first
// pathway that lists this lesson.
$first  = mwm_card_level( $card );
$levels = array_values( array_unique( array_merge( [ $first ], mwm_card_levels( $card ) ) ) );
$hit    = null;
foreach ( $levels as $try ) {
	$r = mwm_next_in_pathway( $id, $try );
	if ( $r && ( ! $hit || $r['found'] ) ) {
		$hit = $r;
	}
	if ( $hit && $hit['found'] ) {
		break;
	}
}
if ( ! $hit ) {
	return;
}
$pw       = $hit['pathway'];
$revision = $pw['url'];
if ( $hit['next'] ) {
	$label = 'Next in your pathway';
	$title = $hit['next']['name'];
	$link  = 'Continue pathway';
} elseif ( $hit['found'] ) {
	$label = 'You’ve reached the end of this topic';
	$title = $pw['topic_name'] . ' · ' . $pw['level_name'];
	$link  = 'Review the pathway';
} else {
	$label = 'Keep going with a pathway';
	$title = $pw['topic_name'] . ' · ' . $pw['level_name'];
	$link  = 'Open the pathway';
}
?>
<div class="mwm-cta mwm-cta--next">
	<div>
		<span class="mwm-cta__label"><?php echo esc_html( $label ); ?></span>
		<div class="mwm-cta__title"><?php echo esc_html( $title ); ?></div>
	</div>
	<?php echo mwm_arrow_link( $revision, $link ); ?>
</div>
