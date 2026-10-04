<?php
/**
 * Site header: logo, primary nav, search, theme toggle, My Learning.
 */

defined( 'ABSPATH' ) || exit;

$core   = mwm_core_active();
$pages  = (array) get_option( 'mwm_pages', [] );
$url    = static fn( string $key ) => $core ? mwm_page_url( $key ) : home_url( '/' );
$is     = static fn( string $key ) => ! empty( $pages[ $key ] ) && is_page( (int) $pages[ $key ] );

$current = '';
$is_ws   = is_singular( 'mwm_worksheet' ) || is_post_type_archive( 'mwm_worksheet' );
if ( $is( 'browse' ) || is_singular( 'mwm_lesson' ) || is_search() || $is_ws ) {
	$current = 'browse';
} elseif ( $is( 'revision' ) || $is( 'calendar' ) || $is( 'past-papers' ) || $is( 'predicted-papers' ) ) {
	$current = 'revision';
} elseif ( $is( 'quick-maths' ) ) {
	$current = 'quick-maths';
} elseif ( $is( 'gaming' ) ) {
	$current = 'gaming';
}

$nav = [
	'browse'      => 'Learn Maths',
	'revision'    => 'Revision',
	'quick-maths' => 'Quick Maths',
	'gaming'      => 'Gaming & Story Maths',
];
// Dropdown entries: [ url, title, one-line description, is this the current page ].
$menus = [
	'browse'   => [
		[ $url( 'browse' ), 'Lessons', 'Videos by level and topic', ! $is_ws && $current === 'browse' ],
		[ $url( 'worksheets' ), 'Worksheets', 'Free PDFs, with answers', $is_ws ],
	],
	'revision' => [
		[ $url( 'revision' ), 'Revision pathway', 'Every topic in the order to revise it', $is( 'revision' ) ],
		[ $url( 'calendar' ), 'Exam calendar', 'Verified dates and a week-by-week plan', $is( 'calendar' ) ],
		[ $url( 'past-papers' ), 'Past papers', 'Real papers with mark schemes', $is( 'past-papers' ) ],
		[ $url( 'predicted-papers' ), 'Predicted papers', 'Melissa’s papers for the next exam', $is( 'predicted-papers' ) ],
	],
];
$signed_in = is_user_logged_in();
$user      = $signed_in ? mwm_current_user_label() : null;
$studio    = $signed_in && class_exists( 'MWM_Studio' ) && current_user_can( MWM_Activator::CAP ) ? MWM_Studio::url() : '';
$login     = wp_login_url( $url( 'my-learning' ) );
?>
<div class="mwm-header">
	<div class="mwm-header__inner">
		<div class="mwm-header__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Maths with Melissa home" class="mwm-logo">
				<img class="mwm-logo__full is-light" src="<?php echo esc_url( MWM_THEME_URI . '/assets/img/logo-charcoal.svg' ); ?>" alt="Maths with Melissa" width="111" height="44">
				<img class="mwm-logo__icon is-light" src="<?php echo esc_url( MWM_THEME_URI . '/assets/img/icon-charcoal.svg' ); ?>" alt="Maths with Melissa" width="36" height="36">
				<img class="mwm-logo__full is-dark" src="<?php echo esc_url( MWM_THEME_URI . '/assets/img/logo-white.svg' ); ?>" alt="Maths with Melissa" width="89" height="44">
				<img class="mwm-logo__icon is-dark" src="<?php echo esc_url( MWM_THEME_URI . '/assets/img/icon-white.svg' ); ?>" alt="Maths with Melissa" width="36" height="36">
			</a>
			<nav aria-label="Primary" class="mwm-nav">
				<?php foreach ( $nav as $key => $label ) : ?>
					<?php if ( isset( $menus[ $key ] ) ) : ?>
						<div class="mwm-nav__group" data-mwm-dropdown>
							<button type="button" class="mwm-nav__link mwm-nav__toggle<?php echo $current === $key ? ' is-current' : ''; ?>" aria-expanded="false" aria-controls="mwm-<?php echo esc_attr( $key ); ?>-menu"<?php echo $current === $key ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?><span class="mwm-nav__chevron" aria-hidden="true"><?php echo mwm_icon( 'chevron-right', 12 ); ?></span></button>
							<div id="mwm-<?php echo esc_attr( $key ); ?>-menu" class="mwm-nav__menu">
								<?php foreach ( $menus[ $key ] as $item ) : ?>
									<a href="<?php echo esc_url( $item[0] ); ?>" class="mwm-nav__item<?php echo $item[3] ? ' is-current' : ''; ?>"><span class="mwm-nav__item-title"><?php echo esc_html( $item[1] ); ?></span><span class="mwm-nav__item-sub"><?php echo esc_html( $item[2] ); ?></span></a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( $url( $key ) ); ?>" class="mwm-nav__link<?php echo $current === $key ? ' is-current' : ''; ?>"<?php echo $current === $key ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>
		</div>
		<div class="mwm-header__tools">
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mwm-search">
				<span class="mwm-search__icon"><?php echo mwm_icon( 'search', 16 ); ?></span>
				<input type="search" name="s" class="mwm-search__input" aria-label="Search lessons" placeholder="Search a topic" value="<?php echo is_search() ? esc_attr( get_search_query() ) : ''; ?>">
				<input type="hidden" name="post_type" value="mwm_lesson">
			</form>
			<button type="button" class="mwm-iconbtn mwm-iconbtn--mobile" aria-label="Search" aria-expanded="false" aria-controls="mwm-mobile-search" data-mwm-search-toggle><?php echo mwm_icon( 'search', 20 ); ?></button>
			<button type="button" class="mwm-theme-toggle" aria-label="Switch between light and dark mode" data-mwm-theme-toggle>
				<span class="mwm-theme-toggle__moon"><?php echo mwm_icon( 'moon', 18 ); ?></span>
				<span class="mwm-theme-toggle__sun"><?php echo mwm_icon( 'sun', 18 ); ?></span>
			</button>
			<?php if ( $studio ) : ?>
				<a href="<?php echo esc_url( $studio ); ?>" class="mwm-header__viewsite mwm-header__studio">Studio<span aria-hidden="true">→</span></a>
			<?php endif; ?>
			<?php if ( $signed_in ) : ?>
				<a href="<?php echo esc_url( $url( 'my-learning' ) ); ?>" class="mwm-userpill" aria-label="<?php echo esc_attr( 'My Learning, signed in as ' . $user['name'] ); ?>"><span class="mwm-avatar" aria-hidden="true"><?php echo esc_html( $user['initial'] ); ?></span><?php echo esc_html( $user['name'] ); ?></a>
			<?php else : ?>
				<a href="<?php echo esc_url( $url( 'my-learning' ) ); ?>" class="mwm-header__cta">My Learning</a>
			<?php endif; ?>
			<button type="button" class="mwm-iconbtn mwm-iconbtn--mobile mwm-iconbtn--menu" aria-label="Menu" aria-expanded="false" aria-controls="mwm-mobile-nav" data-mwm-menu><?php echo mwm_icon( 'menu', 20 ); ?></button>
		</div>
	</div>
	<div id="mwm-mobile-search" class="mwm-mobile-search" hidden>
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mwm-search">
			<span class="mwm-search__icon"><?php echo mwm_icon( 'search', 16 ); ?></span>
			<input type="search" name="s" class="mwm-search__input" aria-label="Search lessons" placeholder="Search a topic">
			<input type="hidden" name="post_type" value="mwm_lesson">
		</form>
	</div>
	<nav id="mwm-mobile-nav" aria-label="Mobile" class="mwm-mobile-nav" hidden>
		<?php foreach ( $nav as $key => $label ) : ?>
			<?php if ( isset( $menus[ $key ] ) ) : ?>
				<span class="mwm-mobile-nav__heading"><?php echo esc_html( $label ); ?></span>
				<?php foreach ( $menus[ $key ] as $item ) : ?>
					<a href="<?php echo esc_url( $item[0] ); ?>" class="is-sub"><?php echo esc_html( $item[1] ); ?></a>
				<?php endforeach; ?>
			<?php else : ?>
				<a href="<?php echo esc_url( $url( $key ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
		<a href="<?php echo esc_url( $url( 'my-learning' ) ); ?>" class="is-cta">My Learning</a>
		<?php if ( $studio ) : ?><a href="<?php echo esc_url( $studio ); ?>" class="is-cta">Studio</a><?php endif; ?>
	</nav>
</div>
<?php if ( $signed_in && $core ) : ?>
	<?php echo mwm_json_script( 'mwm-progress-data', mwm_progress_bootstrap() ); ?>
	<script>window.MWM = window.MWM || {}; window.MWM.userId = <?php echo (int) get_current_user_id(); ?>;</script>
<?php endif; ?>
