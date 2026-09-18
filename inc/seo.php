<?php
/**
 * SEO head output: page-specific titles, canonical URLs, meta description, Open Graph / Twitter cards and JSON-LD.
 *
 * The site's "pages" (Learn Maths, Revision, Past papers…) are single WordPress pages that render many URLs via
 * rewrite rules and query strings, so the defaults (one title and one canonical per page) would tell search engines
 * every level, topic and board is the same document. mwm_seo_context() works out what the current URL really is.
 */

defined( 'ABSPATH' ) || exit;

/**
 * What the current request is about: title, description, canonical URL, share image, type, breadcrumbs, video.
 * Cached per request.
 */
function mwm_seo_context(): array {
	static $ctx = null;
	if ( $ctx !== null ) {
		return $ctx;
	}
	$ctx = [ 'title' => '', 'description' => '', 'canonical' => '', 'image' => '', 'type' => 'website', 'crumbs' => [], 'video' => null, 'noindex' => false ];
	if ( ! function_exists( 'mwm_core_active' ) || ! mwm_core_active() ) {
		return $ctx;
	}
	$home  = home_url( '/' );
	$pages = (array) get_option( 'mwm_pages', [] );
	$page  = static fn( string $key ) => (int) ( $pages[ $key ] ?? 0 );
	$id    = get_queried_object_id();
	$logo  = MWM_THEME_URI . '/assets/img/share-card.png';
	$ctx['image'] = file_exists( MWM_THEME_DIR . '/assets/img/share-card.png' ) ? $logo : MWM_THEME_URI . '/assets/img/logo-charcoal.svg';
	$ctx['crumbs'] = [ [ 'Home', $home ] ];

	if ( is_front_page() ) {
		$ctx['description'] = 'Free GCSE and A-level maths lessons from Maths with Melissa: clear video lessons by topic, printable worksheets with answers, past papers with mark schemes, and revision pathways that put it all in order.';
		$ctx['canonical']   = $home;
		$ctx['crumbs']      = [];
		return $ctx;
	}

	if ( is_singular( 'mwm_lesson' ) ) {
		$card = mwm_lesson_card( $id );
		// YouTube descriptions often open with links and hashtags; keep the first real sentence, if there is one.
		$text = wp_strip_all_tags( (string) get_post_field( 'post_content', $id ) );
		$text = preg_replace( '~(?:https?://|www\.)\S+~i', ' ', $text );
		$text = preg_replace( '/#\w+/', ' ', $text );
		$text = trim( preg_replace( '/\s+/', ' ', $text ), " \t\n-–|:•" );
		if ( preg_match( '/^(.{40,}?[.!?])(\s|$)/u', $text, $m ) ) {
			$text = $m[1];
		}
		if ( mb_strlen( $text ) < 40 || ! preg_match( '/[a-z]{3,}/i', $text ) ) {
			$text = '';
		}
		$what = $card['is_short'] ? 'Quick Maths short' : ( $card['is_gaming'] ? 'Gaming & Story maths lesson' : 'maths lesson' );
		$ctx['description'] = $text ? mwm_seo_trim( $text ) : mwm_seo_trim( $card['title'] . ': a free ' . ( $card['level'] ? $card['level'] . ' ' : '' ) . ( $card['topic'] ? $card['topic'] . ' ' : '' ) . $what . ' from Maths with Melissa' . ( $card['has_worksheet'] ? ', with a worksheet to practise on' : '' ) . ( $card['has_quiz'] ? ' and a quick quiz' : '' ) . '.' );
		$ctx['canonical']   = get_permalink( $id );
		$ctx['image']       = $card['thumb'] ?: $ctx['image'];
		$ctx['type']        = 'video.other';
		$ctx['video']       = $card;
		$ctx['crumbs'][]    = [ 'Learn Maths', mwm_page_url( 'browse' ) ];
		if ( $card['level_slug'] ) {
			$ctx['crumbs'][] = [ $card['level'], mwm_browse_url( $card['level_slug'] ) ];
			if ( $card['topic_slug'] ) {
				$ctx['crumbs'][] = [ $card['topic'], mwm_browse_url( $card['level_slug'], $card['topic_slug'] ) ];
			}
		}
		$ctx['crumbs'][] = [ $card['title'], get_permalink( $id ) ];
		return $ctx;
	}

	if ( is_singular( 'mwm_worksheet' ) ) {
		$w    = mwm_worksheet_data( $id );
		$desc = trim( (string) ( $w['description'] ?? '' ) );
		$ctx['title']       = preg_match( '/worksheets?$/i', trim( $w['title'] ) ) ? $w['title'] : $w['title'] . ' worksheet';
		$ctx['description'] = $desc ? mwm_seo_trim( $desc ) : mwm_seo_trim( $w['title'] . ': a free printable ' . ( $w['level'] ? $w['level'] . ' ' : '' ) . ( $w['topic'] ? $w['topic'] . ' ' : '' ) . 'maths worksheet PDF' . ( $w['has_answers'] ? ' with worked answers' : '' ) . ( $w['lesson'] ? ', matched to the video lesson' : '' ) . '.' );
		$ctx['canonical']   = $w['url'];
		$ctx['image']       = $w['thumb'] ?: $ctx['image'];
		$ctx['type']        = 'article';
		$ctx['crumbs'][]    = [ 'Worksheets', mwm_page_url( 'worksheets' ) ];
		$ctx['crumbs'][]    = [ $w['title'], $w['url'] ];
		return $ctx;
	}

	if ( is_post_type_archive( 'mwm_worksheet' ) ) {
		$level = sanitize_key( (string) ( $_GET['level'] ?? '' ) );
		$topic = sanitize_title( (string) ( $_GET['topic'] ?? '' ) );
		$lname = mwm_level_name( $level );
		$tterm = $topic ? get_term_by( 'slug', $topic, 'mwm_topic' ) : null;
		$tname = $tterm ? wp_specialchars_decode( $tterm->name ) : '';
		$ctx['title']       = trim( ( $lname ? $lname . ' ' : '' ) . ( $tname ? $tname . ' ' : '' ) . 'maths worksheets' );
		$ctx['description'] = mwm_seo_trim( 'Free printable ' . ( $lname ? $lname . ' ' : 'GCSE ' ) . ( $tname ? $tname . ' ' : '' ) . 'maths worksheets as PDFs, with worked answers where they exist. Pick a level and topic, then print it or work through it on screen.' );
		$ctx['canonical']   = add_query_arg( array_filter( [ 'level' => $level, 'topic' => $topic ] ), mwm_page_url( 'worksheets' ) );
		$ctx['crumbs'][]    = [ 'Worksheets', mwm_page_url( 'worksheets' ) ];
		return $ctx;
	}

	if ( is_page() && $id === $page( 'browse' ) ) {
		$st    = MWM_Rewrites::browse_state();
		$lname = mwm_level_name( $st['level'] );
		$term  = $st['topic'] ? get_term_by( 'slug', $st['topic'], 'mwm_topic' ) : null;
		$tname = $term ? wp_specialchars_decode( $term->name ) : '';
		$intro = $term ? trim( (string) get_term_meta( $term->term_id, 'intro', true ) ) : '';
		$ctx['title']       = $tname ? "$tname · $lname maths lessons" : "$lname maths lessons";
		$ctx['description'] = mwm_seo_trim( $intro ?: ( $tname
			? "Every $lname $tname video lesson from Maths with Melissa, in order, with worksheets and quizzes where they exist."
			: "Every $lname maths video lesson from Maths with Melissa, organised by topic, with free worksheets and quizzes." ) );
		$ctx['canonical']   = mwm_browse_url( $st['level'], $st['topic'] );
		$ctx['crumbs'][]    = [ 'Learn Maths', mwm_page_url( 'browse' ) ];
		$ctx['crumbs'][]    = [ $lname, mwm_browse_url( $st['level'] ) ];
		if ( $tname ) {
			$ctx['crumbs'][] = [ $tname, mwm_browse_url( $st['level'], $st['topic'] ) ];
		}
		return $ctx;
	}

	if ( is_page() && $id === $page( 'revision' ) ) {
		$st    = MWM_Rewrites::pathway_state();
		$lname = mwm_level_name( $st['level'] );
		$bname = mwm_board_name( $st['board'] );
		$ctx['title']       = "$lname revision pathway · $bname";
		$ctx['description'] = mwm_seo_trim( "A step-by-step $lname maths revision pathway for $bname: every topic in the order to revise it, ticked off as you go, with lessons, worksheets and past papers to match." );
		$ctx['canonical']   = trailingslashit( mwm_page_url( 'revision' ) ) . $st['level'] . '/' . $st['board'] . '/';
		$ctx['crumbs'][]    = [ 'Revision', mwm_page_url( 'revision' ) ];
		$ctx['crumbs'][]    = [ "$lname · $bname", $ctx['canonical'] ];
		return $ctx;
	}

	if ( is_page() && $id === $page( 'past-papers' ) ) {
		$board = sanitize_key( (string) ( $_GET['board'] ?? '' ) );
		$board = isset( mwm_boards()[ $board ] ) ? $board : 'edexcel';
		$bname = mwm_board_name( $board );
		$ctx['title']       = "$bname GCSE maths past papers";
		$ctx['description'] = mwm_seo_trim( "Every $bname GCSE maths past paper as a free PDF with its mark scheme, Foundation and Higher, plus practice worksheets that match what came up." );
		$ctx['canonical']   = add_query_arg( 'board', $board, mwm_page_url( 'past-papers' ) );
		$ctx['crumbs'][]    = [ 'Revision', mwm_page_url( 'revision' ) ];
		$ctx['crumbs'][]    = [ 'Past papers', $ctx['canonical'] ];
		return $ctx;
	}

	if ( is_page() ) {
		$fixed = [
			'quick-maths' => [ 'title' => 'Quick Maths shorts', 'description' => 'Under a minute, straight to the point: one GCSE maths skill per short from Maths with Melissa, filtered by level and ready to play.' ],
			'gaming'      => [ 'title' => 'Gaming & Story Maths', 'description' => 'Real GCSE maths inside Roblox, Minecraft and stories, with every video mapped to a topic so the fun counts towards your revision.' ],
			'calendar'    => [ 'title' => 'GCSE maths exam dates', 'description' => 'Verified GCSE maths exam dates for Edexcel, AQA and OCR, Foundation and Higher, with the next paper counted down.' ],
			'my-learning' => [ 'title' => 'My learning', 'description' => 'Your saved progress across lessons, quizzes and revision pathways.', 'noindex' => true ],
			'privacy'     => [ 'title' => '', 'description' => 'How Maths with Melissa handles your data.' ],
		];
		foreach ( $fixed as $key => $meta ) {
			if ( $id === $page( $key ) ) {
				$ctx['title']       = $meta['title'];
				$ctx['description'] = $meta['description'];
				$ctx['noindex']     = ! empty( $meta['noindex'] );
				break;
			}
		}
		if ( ! $ctx['description'] ) {
			$ctx['description'] = mwm_seo_trim( (string) get_post_field( 'post_excerpt', $id ) ?: wp_strip_all_tags( (string) get_post_field( 'post_content', $id ) ) );
		}
		$ctx['canonical'] = get_permalink( $id );
		$ctx['crumbs'][]  = [ get_the_title( $id ), get_permalink( $id ) ];
		return $ctx;
	}

	if ( is_search() ) {
		$ctx['noindex'] = true;
	}
	return $ctx;
}

/**
 * Trim to a search-snippet length on a word boundary.
 */
function mwm_seo_trim( string $text, int $max = 158 ): string {
	$text = trim( preg_replace( '/\s+/', ' ', wp_specialchars_decode( $text ) ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $max - 1 );
	$sp  = mb_strrpos( $cut, ' ' );
	return rtrim( $sp ? mb_substr( $cut, 0, $sp ) : $cut, ' ,;:-' ) . '…';
}

/**
 * ISO 8601 duration for schema.org (PT9M12S).
 */
function mwm_seo_iso_duration( int $seconds ): string {
	$h = intdiv( $seconds, 3600 );
	$m = intdiv( $seconds % 3600, 60 );
	$s = $seconds % 60;
	return 'PT' . ( $h ? $h . 'H' : '' ) . ( $m ? $m . 'M' : '' ) . ( $s || ! $seconds ? $s . 'S' : '' );
}

/* Page-specific <title>. */
add_filter( 'document_title_parts', static function ( array $parts ): array {
	$ctx = mwm_seo_context();
	if ( $ctx['title'] ) {
		$parts['title'] = $ctx['title'];
	}
	return $parts;
} );

/* Canonical for the multi-URL pages (WordPress would point every level/topic/board at the bare page). */
add_filter( 'get_canonical_url', static function ( $url ) {
	$ctx = mwm_seo_context();
	return $ctx['canonical'] ?: $url;
} );

/* noindex for the user-specific page and search. */
add_filter( 'wp_robots', static function ( array $robots ): array {
	if ( mwm_seo_context()['noindex'] ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
} );

/* Description, share cards, JSON-LD, font preload, archive canonical. */
add_action( 'wp_head', static function () {
	echo '<link rel="preload" href="' . esc_url( MWM_THEME_URI . '/assets/fonts/inter-latin.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	if ( ! has_site_icon() ) { // No favicon means a 404 in the console on every page; the theme's SVG mark does the job.
		echo '<link rel="icon" href="' . esc_url( MWM_THEME_URI . '/assets/img/icon-charcoal.svg' ) . '" type="image/svg+xml">' . "\n";
	}
	if ( is_admin() ) {
		return;
	}
	$ctx = mwm_seo_context();
	if ( $ctx['canonical'] && ! is_singular() ) { // WordPress only prints rel=canonical on singular views.
		echo '<link rel="canonical" href="' . esc_url( $ctx['canonical'] ) . '">' . "\n";
	}
	if ( $ctx['noindex'] ) {
		return;
	}
	$title = wp_get_document_title();
	if ( $ctx['description'] ) {
		echo '<meta name="description" content="' . esc_attr( $ctx['description'] ) . '">' . "\n";
	}
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:locale" content="en_GB">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $ctx['type'] ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $ctx['description'] ) {
		echo '<meta property="og:description" content="' . esc_attr( $ctx['description'] ) . '">' . "\n";
	}
	if ( $ctx['canonical'] ) {
		echo '<meta property="og:url" content="' . esc_url( $ctx['canonical'] ) . '">' . "\n";
	}
	if ( $ctx['image'] ) {
		echo '<meta property="og:image" content="' . esc_url( $ctx['image'] ) . '">' . "\n";
	}
	if ( $ctx['video'] && $ctx['video']['youtube_id'] ) {
		echo '<meta property="og:video" content="' . esc_url( 'https://www.youtube.com/embed/' . $ctx['video']['youtube_id'] ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="' . ( $ctx['image'] ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $ctx['description'] ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $ctx['description'] ) . '">' . "\n";
	}
	if ( $ctx['image'] ) {
		echo '<meta name="twitter:image" content="' . esc_url( $ctx['image'] ) . '">' . "\n";
	}

	$graph = [];
	$org   = [
		'@type'  => 'Organization',
		'@id'    => home_url( '/#organization' ),
		'name'   => get_bloginfo( 'name' ),
		'url'    => home_url( '/' ),
		'logo'   => MWM_THEME_URI . '/assets/img/logo-charcoal.svg',
		'sameAs' => [ mwm_youtube_channel_url() ],
	];
	if ( is_front_page() ) {
		$graph[] = $org;
		$graph[] = [
			'@type'           => 'WebSite',
			'@id'             => home_url( '/#website' ),
			'url'             => home_url( '/' ),
			'name'            => get_bloginfo( 'name' ),
			'publisher'       => [ '@id' => home_url( '/#organization' ) ],
			'potentialAction' => [
				'@type'       => 'SearchAction',
				'target'      => [ '@type' => 'EntryPoint', 'urlTemplate' => home_url( '/?s={search_term_string}' ) ],
				'query-input' => 'required name=search_term_string',
			],
		];
	}
	if ( count( $ctx['crumbs'] ) > 1 ) {
		$items = [];
		foreach ( $ctx['crumbs'] as $i => $c ) {
			$items[] = [ '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1] ];
		}
		$graph[] = [ '@type' => 'BreadcrumbList', 'itemListElement' => $items ];
	}
	if ( $ctx['video'] && $ctx['video']['youtube_id'] ) {
		$v    = $ctx['video'];
		$date = (string) get_post_meta( $v['id'], 'yt_published', true ) ?: $v['published'];
		$vid  = [
			'@type'        => 'VideoObject',
			'name'         => $v['title'],
			'description'  => $ctx['description'],
			'thumbnailUrl' => array_values( array_filter( [ $v['thumb'], mwm_youtube_thumb( $v['youtube_id'], 'hqdefault' ) ] ) ),
			'uploadDate'   => $date ? $date . 'T09:00:00+00:00' : '',
			'embedUrl'     => 'https://www.youtube-nocookie.com/embed/' . $v['youtube_id'],
			'contentUrl'   => $v['youtube_url'],
			'publisher'    => $org,
		];
		if ( $v['seconds'] ) {
			$vid['duration'] = mwm_seo_iso_duration( (int) $v['seconds'] );
		}
		$graph[] = array_filter( $vid );
	}
	if ( $graph ) {
		echo '<script type="application/ld+json">' . wp_json_encode( [ '@context' => 'https://schema.org', '@graph' => $graph ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}, 1 );
