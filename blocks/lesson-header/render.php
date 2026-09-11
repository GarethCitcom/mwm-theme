<?php
/**
 * Lesson header: breadcrumb, title, tag row.
 */

defined( 'ABSPATH' ) || exit;

if ( ! mwm_core_active() ) {
	return;
}
$card = mwm_lesson_card( get_the_ID() );
if ( ! $card ) {
	return;
}
$crumbs = [ [ 'label' => 'Learn Maths', 'url' => mwm_page_url( 'browse' ) ] ];
if ( $card['level_slug'] ) {
	$crumbs[] = [ 'label' => $card['level'], 'url' => mwm_browse_url( $card['level_slug'] ) ];
}
if ( $card['topic_slug'] ) {
	$crumbs[] = [ 'label' => $card['topic'], 'url' => mwm_browse_url( $card['level_slug'], $card['topic_slug'] ) ];
}
$crumbs[] = [ 'label' => $card['title'] ];
$mins = $card['seconds'] >= 60 ? max( 1, (int) round( $card['seconds'] / 60 ) ) : 0;
$meta = [];
if ( $mins ) {
	$meta[] = $mins . ( $mins === 1 ? ' min' : ' mins' );
} elseif ( $card['duration'] ) {
	$meta[] = $card['duration'];
}
$meta[] = 'Published ' . $card['published_label'];
?>
<?php echo mwm_breadcrumb( $crumbs ); ?>
<h1 class="mwm-h1"><?php echo esc_html( $card['title'] ); ?></h1>
<div class="mwm-lesson__tags">
	<?php echo mwm_level_tag( $card ); ?>
	<?php if ( $card['topic'] ) { echo mwm_tag( $card['topic'], 'outline' ); } ?>
	<?php if ( $card['theme'] ) { echo mwm_tag( $card['theme'], 'tint' ); } ?>
	<span class="mwm-lesson__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?></span>
</div>
