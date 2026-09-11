<?php
/**
 * Home hero: eyebrow, headline, lead, search box and the featured lesson card.
 */

defined( 'ABSPATH' ) || exit;

$eyebrow     = mwm_field( 'eyebrow', 'Free maths resources' );
$heading     = mwm_field( 'heading', 'GCSE & A-Level Maths, Made Clear' );
$lead        = mwm_field( 'lead', 'Watch. Practise. Build your confidence.' );
$sub         = mwm_field( 'sub', 'Free videos, worksheets and step-by-step answers.' );
$placeholder = mwm_field( 'placeholder', 'What would you like to learn?' );
$featured_id = (int) mwm_field( 'featured_lesson', 0 );

$card = null;
if ( mwm_core_active() ) {
	if ( $featured_id ) {
		$card = mwm_lesson_card( $featured_id );
	}
	if ( ! $card ) {
		foreach ( mwm_query_lessons( [ 'format' => 'lesson', 'per_page' => 12 ] ) as $c ) {
			if ( $c['thumb'] ) {
				$card = $c;
				break;
			}
		}
	}
}
?>
<section class="mwm-hero" aria-labelledby="mwm-hero-title">
	<div class="mwm-hero__inner">
		<div class="mwm-hero__copy">
			<div class="mwm-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<h1 id="mwm-hero-title" class="mwm-hero__title"><?php echo esc_html( $heading ); ?></h1>
			<p class="mwm-hero__lead"><?php echo esc_html( $lead ); ?></p>
			<p class="mwm-hero__sub"><?php echo esc_html( $sub ); ?></p>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mwm-hero__search">
				<input type="search" name="s" class="mwm-hero__input" aria-label="Search lessons" placeholder="<?php echo esc_attr( $placeholder ); ?>">
				<input type="hidden" name="post_type" value="mwm_lesson">
				<button type="submit" class="mwm-hero__go" aria-label="Search"><?php echo mwm_icon( 'search', 18, [ 'stroke' => '#FFFFFF' ] ); ?></button>
			</form>
		</div>
		<?php if ( $card ) : ?>
			<div class="mwm-hero__media">
				<div class="mwm-hero__card">
					<div class="mwm-hero__frame">
						<?php echo mwm_thumb( $card, false ); ?>
						<a href="<?php echo esc_url( $card['url'] ); ?>" class="mwm-play" aria-label="<?php echo esc_attr( 'Play: ' . $card['title'] ); ?>"><?php echo mwm_icon( 'play', 20 ); ?></a>
					</div>
					<div class="mwm-hero__cardfoot">
						<a href="<?php echo esc_url( $card['url'] ); ?>" class="mwm-hero__cardtitle"><?php echo esc_html( $card['title'] ); ?></a>
						<span class="mwm-hero__cardmeta"><?php echo esc_html( trim( $card['level'] . ' · ' . $card['duration'], ' ·' ) ); ?></span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
