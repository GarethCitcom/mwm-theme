<?php
/**
 * Rendering helpers shared by blocks: cards, tags, badges, buttons, links.
 * Every helper returns a string; render.php files echo them.
 */

defined( 'ABSPATH' ) || exit;

function mwm_core_active(): bool {
	return function_exists( 'mwm_lesson_card' );
}

/**
 * Pill tag. Styles: tint (soft pink fill), higher (pink outline), outline (grey outline), band (on ink), action (filled).
 */
function mwm_tag( string $text, string $style = 'outline', string $extra_class = '' ): string {
	return '<span class="mwm-tag mwm-tag--' . esc_attr( $style ) . ( $extra_class ? ' ' . esc_attr( $extra_class ) : '' ) . '">' . esc_html( $text ) . '</span>';
}

function mwm_level_tag( array $card ): string {
	return $card['level'] ? mwm_tag( $card['level'], $card['level_style'] ) : '';
}

/**
 * "Worksheet" / "Quiz" badge with icon (card footers).
 */
function mwm_badge( string $kind ): string {
	$label = $kind === 'quiz' ? 'Quiz' : 'Worksheet';
	return '<span class="mwm-badge"><span class="mwm-badge__icon">' . mwm_icon( $kind, 16 ) . '</span>' . $label . '</span>';
}

function mwm_card_badges( array $card ): string {
	$out = '';
	if ( $card['has_worksheet'] ) {
		$out .= mwm_badge( 'worksheet' );
	}
	if ( $card['has_quiz'] ) {
		$out .= mwm_badge( 'quiz' );
	}
	return $out;
}

/**
 * Arrow link: "Browse all videos →".
 */
function mwm_arrow_link( string $url, string $text, string $class = '', array $attrs = [] ): string {
	$a = '';
	foreach ( $attrs as $k => $v ) {
		$a .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return '<a href="' . esc_url( $url ) . '" class="mwm-arrow' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '"' . $a . '>' . esc_html( $text ) . '<span aria-hidden="true">→</span></a>';
}

function mwm_button( string $url, string $text, string $style = 'primary', array $attrs = [] ): string {
	$a = '';
	foreach ( $attrs as $k => $v ) {
		$a .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return '<a href="' . esc_url( $url ) . '" class="mwm-btn mwm-btn--' . esc_attr( $style ) . '"' . $a . '>' . esc_html( $text ) . '</a>';
}

/**
 * 16:9 thumbnail area. Shows a "thumbnail to follow" placeholder when the lesson has no image yet.
 */
/**
 * A responsive thumbnail <img>. YouTube thumbnails get a srcset of the small sizes (320/480/640 wide) instead of the
 * 1280px "maxres" file, which cut listing pages from ~2.5 MB to a few hundred KB. The first three thumbnails on a
 * page load eagerly with high priority (they are usually the LCP element); the rest lazy-load.
 *
 * @param string $sizes The CSS sizes attribute; defaults to a 3-column card.
 */
function mwm_thumb_img( string $src, string $alt, string $sizes = '(max-width: 700px) calc(100vw - 32px), 380px', array $attrs = [] ): string {
	static $rendered = 0;
	$rendered++;
	$eager  = $rendered <= 3 && ! wp_is_json_request() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST );
	$a      = [ 'alt' => $alt, 'decoding' => 'async' ] + $attrs;
	if ( preg_match( '~i\.ytimg\.com/vi/([\w-]{11})/~', $src, $m ) ) {
		$id            = $m[1];
		$a['src']      = mwm_youtube_thumb( $id, 'hqdefault' );
		// Cards stop at 480w: sddefault is 4:3 with letterbox bars, so on phones it mostly downloads pixels that get cropped.
		$a['srcset']   = mwm_youtube_thumb( $id, 'mqdefault' ) . ' 320w, ' . mwm_youtube_thumb( $id, 'hqdefault' ) . ' 480w' . ( $attrs['maxres'] ?? false ? ', ' . mwm_youtube_thumb( $id, 'sddefault' ) . ' 640w, ' . $src . ' 1280w' : '' );
		$a['sizes']    = $sizes;
		$a['width']    = $a['width'] ?? '480';
		$a['height']   = $a['height'] ?? '270';
	} else {
		$a['src'] = $src;
	}
	unset( $a['maxres'] );
	if ( $eager ) {
		$a['fetchpriority'] = 'high';
	} else {
		$a['loading'] = 'lazy';
	}
	$html = '<img';
	foreach ( $a as $k => $v ) {
		$html .= ' ' . $k . '="' . ( $k === 'src' || $k === 'srcset' ? esc_attr( $v ) : esc_attr( $v ) ) . '"';
	}
	return $html . '>';
}

/**
 * A small (480px) version of a YouTube thumbnail URL, for tiny background-image thumbs.
 */
function mwm_thumb_small( string $url ): string {
	return preg_replace( '~/(maxresdefault|sddefault)\.jpg$~', '/hqdefault.jpg', $url );
}

function mwm_thumb( array $card, bool $link = true, string $class = '' ): string {
	$cls = 'mwm-thumb' . ( $class ? ' ' . esc_attr( $class ) : '' );
	if ( $card['thumb'] ) {
		$inner = mwm_thumb_img( $card['thumb'], $card['alt'] );
		return $link
			? '<a href="' . esc_url( $card['url'] ) . '" class="' . $cls . '" tabindex="-1" aria-hidden="true">' . $inner . '</a>'
			: '<div class="' . $cls . '">' . $inner . '</div>';
	}
	$label = ( $card['theme'] ? strtolower( $card['theme'] ) . ' ' : '' ) . 'thumbnail to follow';
	return '<div class="' . $cls . ' mwm-thumb--placeholder" role="img" aria-label="' . esc_attr( $card['title'] . ' — thumbnail to follow' ) . '"><span>' . esc_html( $label ) . '</span></div>';
}

/**
 * Standard video card (Home "Start with these", Browse grid).
 */
function mwm_video_card( array $card ): string {
	$media = $card['is_short']
		? '<a href="' . esc_url( $card['url'] ) . '" class="mwm-thumb mwm-thumb--short-in-wide" tabindex="-1" aria-hidden="true">' . mwm_thumb_img( $card['thumb'], '' ) . '</a>'
		: mwm_thumb( $card );
	$topic_tag = $card['topic'] ? mwm_tag( $card['topic'], 'outline' ) : '';
	return '<article class="mwm-card mwm-card--video" data-id="' . (int) $card['id'] . '">'
		. $media
		. '<div class="mwm-card__body">'
		. '<div class="mwm-tags">' . mwm_level_tag( $card ) . $topic_tag . '</div>'
		. '<h3 class="mwm-card__title"><a href="' . esc_url( $card['url'] ) . '">' . esc_html( $card['title'] ) . '</a></h3>'
		. '<div class="mwm-card__foot"><span class="mwm-meta">' . esc_html( $card['duration'] ) . '</span><span class="mwm-badges">' . mwm_card_badges( $card ) . '</span></div>'
		. '</div></article>';
}

/**
 * Gaming card: theme tag + topic tag, smaller title, duration/meta at the bottom.
 */
function mwm_gaming_card( array $card, string $meta_style = 'duration' ): string {
	$meta = $meta_style === 'level' ? trim( $card['level'] . ' · ' . $card['duration'], ' ·' ) : $card['duration'];
	return '<article class="mwm-card mwm-card--gaming" data-id="' . (int) $card['id'] . '">'
		. mwm_thumb( $card )
		. '<div class="mwm-card__body mwm-card__body--tight">'
		. '<div class="mwm-tags">' . ( $card['theme'] ? mwm_tag( $card['theme'], 'tint' ) : '' ) . ( $card['topic'] ? mwm_tag( $card['topic'], 'outline' ) : '' ) . '</div>'
		. '<h3 class="mwm-card__title mwm-card__title--sm"><a href="' . esc_url( $card['url'] ) . '">' . esc_html( $card['title'] ) . '</a></h3>'
		. '<span class="mwm-card__meta">' . esc_html( $meta ) . '</span>'
		. '</div></article>';
}

/**
 * Worksheet card (Worksheets archive, related worksheets): lesson thumbnail, level/topic, title, PDF size, answers badge.
 */
function mwm_worksheet_card( array $w ): string {
	$media = $w['thumb']
		? '<a href="' . esc_url( $w['url'] ) . '" class="mwm-thumb" tabindex="-1" aria-hidden="true">' . mwm_thumb_img( $w['thumb'], '' ) . '</a>'
		: '<a href="' . esc_url( $w['url'] ) . '" class="mwm-thumb mwm-thumb--doc" tabindex="-1" aria-hidden="true"><span class="mwm-thumb__doc">' . mwm_icon( 'worksheet', 28 ) . '<span>PDF</span></span></a>';
	return '<article class="mwm-card mwm-card--worksheet" data-id="' . (int) $w['id'] . '" data-level="' . esc_attr( $w['level_slug'] ) . '" data-topic="' . esc_attr( $w['topic_slug'] ) . '">'
		. $media
		. '<div class="mwm-card__body">'
		. '<div class="mwm-tags">' . ( $w['level'] ? mwm_tag( $w['level'], $w['level_style'] ) : '' ) . ( $w['topic'] ? mwm_tag( $w['topic'], 'outline' ) : '' ) . '</div>'
		. '<h3 class="mwm-card__title"><a href="' . esc_url( $w['url'] ) . '">' . esc_html( $w['title'] ) . '</a></h3>'
		. '<div class="mwm-card__foot"><span class="mwm-meta">' . esc_html( $w['pdf']['label'] ?? 'PDF' ) . '</span><span class="mwm-badges">' . ( $w['has_answers'] ? '<span class="mwm-badge"><span class="mwm-badge__icon">' . mwm_icon( 'tick', 16 ) . '</span>Answers</span>' : '' ) . ( $w['lesson_id'] ? '<span class="mwm-badge"><span class="mwm-badge__icon">' . mwm_icon( 'video', 16 ) . '</span>Lesson</span>' : '' ) . '</span></div>'
		. '</div></article>';
}

/**
 * Related-lesson card (Lesson page): title + "GCSE Higher · 9 min".
 */
function mwm_related_card( array $card ): string {
	return '<article class="mwm-card mwm-card--related">'
		. mwm_thumb( $card )
		. '<div class="mwm-card__body mwm-card__body--tight">'
		. '<h3 class="mwm-card__title mwm-card__title--sm mwm-card__title--related"><a href="' . esc_url( $card['url'] ) . '">' . esc_html( $card['title'] ) . '</a></h3>'
		. '<span class="mwm-card__meta mwm-card__meta--auto">' . esc_html( trim( $card['level'] . ' · ' . $card['duration'], ' ·' ) ) . '</span>'
		. '</div></article>';
}

/**
 * 9:16 short card. Variants: 'grid' (Quick Maths page, with topic + duration), 'band' (ink strip), 'row' (Gaming page).
 */
function mwm_short_card( array $card, string $variant = 'grid' ): string {
	// The title is visible text on every variant, so the thumbnail is decorative (an accessible name that differs from the visible text confuses voice control).
	$thumb = '<span class="mwm-short__thumb">' . ( $card['thumb'] ? mwm_thumb_img( $card['thumb'], '', '(max-width: 700px) 45vw, 190px' ) : '<span aria-hidden="true"></span>' ) . '</span>';
	if ( $variant === 'band' ) {
		return '<div class="mwm-short mwm-short--band">' . $thumb . '<span class="mwm-short__title">' . esc_html( $card['title'] ) . '</span><span class="mwm-short__duration">' . esc_html( $card['duration'] ) . '</span></div>';
	}
	$url  = $card['youtube_url'];
	$play = $card['youtube_id'] ? ' data-short="' . esc_attr( $card['youtube_id'] ) . '" data-short-title="' . esc_attr( $card['title'] ) . '"' : '';
	$aria = $card['youtube_id'] ? 'Play: ' . $card['title'] : 'Watch on YouTube: ' . $card['title'];
	if ( $variant === 'row' ) {
		return '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" class="mwm-short mwm-short--row"' . $play . '>' . $thumb . '<span class="mwm-short__title">' . esc_html( $card['title'] ) . '</span><span class="mwm-short__duration">' . esc_html( $card['duration'] ) . '</span></a>';
	}
	return '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" class="mwm-short mwm-short--grid" data-level="' . esc_attr( $card['level_slug'] ) . '"' . $play . '>'
		. $thumb
		. '<span class="mwm-short__title">' . esc_html( $card['title'] ) . '</span>'
		. '<span class="mwm-short__meta">' . ( $card['topic'] ? mwm_tag( $card['topic'], 'outline', 'mwm-tag--sm' ) : '' ) . '<span class="mwm-short__duration">' . esc_html( $card['duration'] ) . '</span></span>'
		. '</a>';
}

/**
 * Segmented level switcher (tablist). $options: [ value => label ], $current: selected value.
 */
function mwm_segmented( array $options, string $current, string $aria_label, string $name = 'level', array $urls = [] ): string {
	$out = '<div role="tablist" aria-label="' . esc_attr( $aria_label ) . '" class="mwm-seg" data-seg="' . esc_attr( $name ) . '">';
	foreach ( $options as $value => $label ) {
		$on  = (string) $value === $current;
		$tag = isset( $urls[ $value ] ) ? 'a' : 'button';
		$out .= '<' . $tag . ' role="tab" ' . ( $tag === 'a' ? 'href="' . esc_url( $urls[ $value ] ) . '"' : 'type="button"' ) . ' aria-selected="' . ( $on ? 'true' : 'false' ) . '" class="mwm-seg__tab' . ( $on ? ' is-on' : '' ) . '" data-value="' . esc_attr( (string) $value ) . '">' . esc_html( $label ) . '</' . $tag . '>';
	}
	return $out . '</div>';
}

/**
 * Toggle chip (aria-pressed). Sizes: lg (44px), md (40px), sm (36px active-filter chip).
 */
function mwm_chip( string $label, bool $on, array $data = [], string $size = 'lg', string $icon = '' ): string {
	$d = '';
	foreach ( $data as $k => $v ) {
		$d .= ' data-' . esc_attr( $k ) . '="' . esc_attr( (string) $v ) . '"';
	}
	return '<button type="button" class="mwm-chip mwm-chip--' . esc_attr( $size ) . ( $on ? ' is-on' : '' ) . '" aria-pressed="' . ( $on ? 'true' : 'false' ) . '"' . $d . '>' . ( $icon ? '<span class="mwm-chip__icon">' . $icon . '</span>' : '' ) . esc_html( $label ) . '</button>';
}

/**
 * Breadcrumb trail. Items: [ ['label'=>..,'url'=>..], ... ]; the last item is the current page.
 */
function mwm_breadcrumb( array $items, string $aria = 'Breadcrumb' ): string {
	$out  = '<nav class="mwm-crumbs">';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $it ) {
		if ( $i > 0 ) {
			$out .= '<span aria-hidden="true" class="mwm-crumbs__sep">›</span>';
		}
		if ( $i === $last ) {
			$out .= '<span class="mwm-crumbs__current" aria-current="page">' . esc_html( $it['label'] ) . '</span>';
		} elseif ( ! empty( $it['url'] ) ) {
			$out .= '<a href="' . esc_url( $it['url'] ) . '">' . esc_html( $it['label'] ) . '</a>';
		} else {
			$out .= '<span>' . esc_html( $it['label'] ) . '</span>';
		}
	}
	return $out . '</nav>';
}

/**
 * Exam panel body shared by Home and the pathway sidebar.
 */
function mwm_exam_verified_note( ?array $exam ): string {
	if ( ! $exam ) {
		return '';
	}
	return 'Verified ' . $exam['verified_label'] . ' · Check your own timetable with your school';
}

/**
 * Progress JSON for the current visitor, printed once for the front-end scripts.
 */
function mwm_progress_bootstrap(): array {
	if ( ! is_user_logged_in() || ! class_exists( 'MWM_Progress' ) ) {
		return [];
	}
	return MWM_Progress::get( get_current_user_id() );
}

/**
 * Print a JSON data island for a block's script.
 */
function mwm_json_script( string $id, $data ): string {
	return '<script type="application/json" id="' . esc_attr( $id ) . '">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>';
}

/**
 * The signed-in user's display name / initial for the header pill.
 */
function mwm_current_user_label(): array {
	$u    = wp_get_current_user();
	$name = $u->first_name ?: ( $u->display_name ?: $u->user_login );
	$name = explode( ' ', trim( $name ) )[0];
	return [ 'name' => $name, 'initial' => strtoupper( mb_substr( $name, 0, 1 ) ) ];
}
