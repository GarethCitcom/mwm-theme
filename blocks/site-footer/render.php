<?php
/**
 * Ink footer. Full layout (home) or compact (inner pages).
 */

defined( 'ABSPATH' ) || exit;

$variant = mwm_field( 'variant', 'compact' );
$blurb   = mwm_field( 'blurb', 'Free GCSE and A-level maths lessons, worksheets and revision pathways.' );
$email   = mwm_field( 'email', 'hello@mathswithmelissa.co.uk' );
$core    = mwm_core_active();
$url     = static fn( string $key ) => $core ? mwm_page_url( $key ) : home_url( '/' );
$yt      = $core ? mwm_youtube_channel_url() : 'https://www.youtube.com/@mathswithmelissa';
$logo    = MWM_THEME_URI . '/assets/img/logo-white.svg';
$year    = wp_date( 'Y' );
?>
<div class="mwm-footer mwm-footer--<?php echo esc_attr( $variant ); ?>">
	<div class="mwm-footer__inner">
		<?php if ( $variant === 'full' ) : ?>
			<div class="mwm-footer__top">
				<div>
					<img src="<?php echo esc_url( $logo ); ?>" alt="Maths with Melissa" class="mwm-footer__logo" width="113" height="56">
					<p class="mwm-footer__blurb"><?php echo esc_html( $blurb ); ?></p>
				</div>
				<div class="mwm-footer__cols">
					<div class="mwm-footer__col">
						<span class="mwm-footer__heading">Learn</span>
						<a href="<?php echo esc_url( $url( 'browse' ) ); ?>" class="mwm-footer__link">Learn Maths</a>
						<a href="<?php echo esc_url( $url( 'revision' ) ); ?>" class="mwm-footer__link">Revision</a>
						<a href="<?php echo esc_url( $url( 'quick-maths' ) ); ?>" class="mwm-footer__link">Quick Maths</a>
						<a href="<?php echo esc_url( $url( 'gaming' ) ); ?>" class="mwm-footer__link">Gaming &amp; Story Maths</a>
					</div>
					<div class="mwm-footer__col">
						<span class="mwm-footer__heading">Resources</span>
						<a href="<?php echo esc_url( add_query_arg( 'worksheet', '1', $url( 'browse' ) ) ); ?>" class="mwm-footer__link">Worksheets</a>
						<a href="<?php echo esc_url( $url( 'past-papers' ) ); ?>" class="mwm-footer__link">Past papers</a>
						<a href="<?php echo esc_url( $url( 'my-learning' ) ); ?>" class="mwm-footer__link">My Learning</a>
					</div>
					<div class="mwm-footer__col mwm-footer__col--narrow">
						<span class="mwm-footer__heading">Stay in touch</span>
						<a href="<?php echo esc_url( $yt ); ?>" target="_blank" rel="noopener" class="mwm-footer__link">Subscribe on YouTube</a>
						<p class="mwm-footer__note">Spotted a mistake?<br><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="mwm-footer__top">
				<img src="<?php echo esc_url( $logo ); ?>" alt="Maths with Melissa" class="mwm-footer__logo" width="97" height="48">
				<div class="mwm-footer__links">
					<a href="<?php echo esc_url( $url( 'browse' ) ); ?>" class="mwm-footer__link">Learn Maths</a>
					<a href="<?php echo esc_url( $url( 'revision' ) ); ?>" class="mwm-footer__link">Revision</a>
					<a href="<?php echo esc_url( $url( 'quick-maths' ) ); ?>" class="mwm-footer__link">Quick Maths</a>
					<a href="<?php echo esc_url( $url( 'gaming' ) ); ?>" class="mwm-footer__link">Gaming &amp; Story Maths</a>
					<a href="<?php echo esc_url( $yt ); ?>" target="_blank" rel="noopener" class="mwm-footer__link">Subscribe on YouTube</a>
				</div>
			</div>
		<?php endif; ?>
		<div class="mwm-footer__bottom">
			<span class="mwm-footer__copy">© <?php echo esc_html( $year ); ?> Maths with Melissa</span>
			<a href="<?php echo esc_url( $url( 'privacy' ) ); ?>" class="mwm-footer__privacy">Privacy</a>
		</div>
	</div>
</div>
