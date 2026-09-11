<?php
/**
 * "New lessons every week on YouTube." strip.
 */

defined( 'ABSPATH' ) || exit;

$text   = mwm_field( 'text', 'New lessons every week on YouTube.' );
$button = mwm_field( 'button_label', 'Subscribe on YouTube' );
$yt     = mwm_core_active() ? mwm_youtube_channel_url() : 'https://www.youtube.com/@mathswithmelissa';
?>
<section class="mwm-subscribe" aria-label="Subscribe">
	<div class="mwm-subscribe__inner">
		<p class="mwm-subscribe__text"><?php echo esc_html( $text ); ?></p>
		<?php echo mwm_button( $yt, $button, 'primary', [ 'target' => '_blank', 'rel' => 'noopener' ] ); ?>
	</div>
</section>
