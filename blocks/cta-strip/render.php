<?php
/**
 * Tinted strip with a line of text and a button.
 */

defined( 'ABSPATH' ) || exit;

$text   = mwm_field( 'text', 'New shorts most weeks — they land on YouTube first.' );
$button = mwm_field( 'button_label', 'Subscribe on YouTube' );
$url    = mwm_field( 'url', '' ) ?: ( mwm_core_active() ? mwm_youtube_channel_url() : 'https://www.youtube.com/@mathswithmelissa' );
$ext    = str_contains( $url, 'youtube.com' );
?>
<div class="mwm-cta">
	<p class="mwm-cta__text"><?php echo esc_html( $text ); ?></p>
	<?php echo mwm_button( $url, $button, 'primary', $ext ? [ 'target' => '_blank', 'rel' => 'noopener' ] : [] ); ?>
</div>
