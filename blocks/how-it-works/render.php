<?php
/**
 * Watch a video → Try the worksheet → Check your answers.
 */

defined( 'ABSPATH' ) || exit;

$steps = [
	[ 'title' => mwm_field( 'step1_title', 'Watch a video' ), 'sub' => mwm_field( 'step1_sub', 'Clear explanations, at your pace.' ), 'icon' => mwm_icon( 'play', 18 ), 'class' => '' ],
	[ 'title' => mwm_field( 'step2_title', 'Try the worksheet' ), 'sub' => mwm_field( 'step2_sub', 'Practise what you’ve learned.' ), 'icon' => mwm_icon( 'worksheet', 18, [ 'stroke' => '#FFFFFF' ] ), 'class' => ' mwm-how__step--mid' ],
	[ 'title' => mwm_field( 'step3_title', 'Check your answers' ), 'sub' => mwm_field( 'step3_sub', 'Step-by-step solutions.' ), 'icon' => mwm_icon( 'tick', 18, [ 'stroke' => '#FFFFFF', 'stroke-width' => '1.8' ] ), 'class' => ' mwm-how__step--end' ],
];
?>
<section class="mwm-how" aria-label="How it works">
	<div class="mwm-how__inner">
		<?php foreach ( $steps as $i => $s ) : ?>
			<?php if ( $i > 0 ) : ?><span aria-hidden="true" class="mwm-how__arrow">→</span><?php endif; ?>
			<div class="mwm-how__step<?php echo esc_attr( $s['class'] ); ?>">
				<span class="mwm-how__icon"><?php echo $s['icon']; ?></span>
				<span><span class="mwm-how__title"><?php echo esc_html( $s['title'] ); ?></span><span class="mwm-how__sub"><?php echo esc_html( $s['sub'] ); ?></span></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
