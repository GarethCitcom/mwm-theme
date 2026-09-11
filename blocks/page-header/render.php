<?php
/**
 * Breadcrumb + heading + intro for simple pages (e.g. Privacy).
 */

defined( 'ABSPATH' ) || exit;

$heading = mwm_field( 'heading', get_the_title() );
$intro   = mwm_field( 'intro', '' );
$note    = mwm_field( 'note', '' );
$parent  = mwm_field( 'parent_label', '' );
$purl    = mwm_field( 'parent_url', '' );
$crumbs  = [ [ 'label' => 'Home', 'url' => home_url( '/' ) ] ];
if ( $parent ) {
	$crumbs[] = [ 'label' => $parent, 'url' => $purl ];
}
$crumbs[] = [ 'label' => $heading ];
?>
<div class="mwm-page mwm-page--content">
	<?php echo mwm_breadcrumb( $crumbs ); ?>
	<h1 class="mwm-h1"><?php echo esc_html( $heading ); ?></h1>
	<?php if ( $intro ) : ?><p class="mwm-intro"><?php echo esc_html( $intro ); ?></p><?php endif; ?>
	<?php if ( $note ) : ?><p class="mwm-note"><?php echo esc_html( $note ); ?></p><?php endif; ?>
</div>
