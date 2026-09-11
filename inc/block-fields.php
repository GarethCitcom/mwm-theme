<?php
/**
 * ACF field groups for the theme's blocks (presentation settings; the data model is in the plugin).
 * Every field defaults to the approved prototype copy, so blocks work with no configuration.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', static function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$loc = static fn( string $block ) => [ [ [ 'param' => 'block', 'operator' => '==', 'value' => 'acf/' . $block ] ] ];
	$text = static fn( string $key, string $name, string $label, string $default, string $type = 'text', array $extra = [] ) => array_merge( [
		'key' => 'field_mwm_blk_' . $key, 'name' => $name, 'label' => $label, 'type' => $type, 'default_value' => $default, 'placeholder' => $default,
	], $extra );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_home_hero',
		'title'    => 'Home hero',
		'location' => $loc( 'home-hero' ),
		'fields'   => [
			$text( 'hero_eyebrow', 'eyebrow', 'Eyebrow', 'Free maths resources' ),
			$text( 'hero_heading', 'heading', 'Heading', 'GCSE & A-Level Maths, Made Clear' ),
			$text( 'hero_lead', 'lead', 'Lead line', 'Watch. Practise. Build your confidence.' ),
			$text( 'hero_sub', 'sub', 'Supporting line', 'Free videos, worksheets and step-by-step answers.' ),
			$text( 'hero_placeholder', 'placeholder', 'Search placeholder', 'What would you like to learn?' ),
			[ 'key' => 'field_mwm_blk_hero_featured', 'name' => 'featured_lesson', 'label' => 'Featured lesson', 'type' => 'post_object', 'post_type' => [ 'mwm_lesson' ], 'return_format' => 'id', 'allow_null' => 1, 'instructions' => 'Leave empty to show the newest lesson with a thumbnail.' ],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_featured',
		'title'    => 'Featured lessons',
		'location' => $loc( 'featured-lessons' ),
		'fields'   => [
			$text( 'feat_heading', 'heading', 'Heading', 'Find your next lightbulb moment' ),
			$text( 'feat_sub', 'sub', 'Sub-heading', 'Clear examples. One topic at a time.' ),
			$text( 'feat_link', 'link_label', 'Link label', 'Browse all videos' ),
			[ 'key' => 'field_mwm_blk_feat_lessons', 'name' => 'lessons', 'label' => 'Lessons', 'type' => 'relationship', 'post_type' => [ 'mwm_lesson' ], 'return_format' => 'id', 'max' => 12, 'instructions' => 'Leave empty to show the six newest lessons.' ],
			[ 'key' => 'field_mwm_blk_feat_count', 'name' => 'count', 'label' => 'How many (when automatic)', 'type' => 'number', 'default_value' => 6, 'min' => 3, 'max' => 12 ],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_how',
		'title'    => 'How it works',
		'location' => $loc( 'how-it-works' ),
		'fields'   => [
			$text( 'how_1t', 'step1_title', 'Step 1 title', 'Watch a video' ),
			$text( 'how_1s', 'step1_sub', 'Step 1 line', 'Clear explanations, at your pace.' ),
			$text( 'how_2t', 'step2_title', 'Step 2 title', 'Try the worksheet' ),
			$text( 'how_2s', 'step2_sub', 'Step 2 line', 'Practise what you’ve learned.' ),
			$text( 'how_3t', 'step3_title', 'Step 3 title', 'Check your answers' ),
			$text( 'how_3s', 'step3_sub', 'Step 3 line', 'Step-by-step solutions.' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_topic_browser',
		'title'    => 'Topic browser',
		'location' => $loc( 'topic-browser' ),
		'fields'   => [
			$text( 'tb_heading', 'heading', 'Heading', 'Browse by topic' ),
			$text( 'tb_sub', 'sub', 'Sub-heading', 'Pick your level, then choose a topic to see every lesson, worksheet and quiz for it.' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_pathway_cards',
		'title'    => 'Pathway cards',
		'location' => $loc( 'pathway-cards' ),
		'fields'   => [
			$text( 'pc_heading', 'heading', 'Heading (plain part)', 'A little revision.' ),
			$text( 'pc_highlight', 'highlight', 'Heading (pink part)', 'A lot more confidence.' ),
			$text( 'pc_sub', 'sub', 'Sub-heading', 'Choose your level and exam board. Follow a clear path.' ),
			$text( 'pc_link', 'link_label', 'Card link label', 'Start pathway' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_gaming_row',
		'title'    => 'Gaming row',
		'location' => $loc( 'gaming-row' ),
		'fields'   => [
			$text( 'gr_heading', 'heading', 'Heading', 'Gaming & Story Maths' ),
			$text( 'gr_sub', 'sub', 'Sub-heading', 'Maths inside Roblox, Minecraft and stories, each mapped to a real topic.' ),
			$text( 'gr_link', 'link_label', 'Link label', 'See all' ),
			[ 'key' => 'field_mwm_blk_gr_count', 'name' => 'count', 'label' => 'How many', 'type' => 'number', 'default_value' => 4, 'min' => 2, 'max' => 8 ],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_qm_band',
		'title'    => 'Quick Maths band',
		'location' => $loc( 'quick-maths-band' ),
		'fields'   => [
			$text( 'qb_heading', 'heading', 'Heading', 'Quick Maths' ),
			$text( 'qb_sub', 'sub', 'Sub-heading', 'Under a minute, straight to the point.' ),
			[ 'key' => 'field_mwm_blk_qb_count', 'name' => 'count', 'label' => 'How many shorts', 'type' => 'number', 'default_value' => 8, 'min' => 4, 'max' => 20 ],
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_subscribe',
		'title'    => 'Subscribe strip',
		'location' => $loc( 'subscribe-strip' ),
		'fields'   => [
			$text( 'sub_text', 'text', 'Text', 'New lessons every week on YouTube.' ),
			$text( 'sub_button', 'button_label', 'Button label', 'Subscribe on YouTube' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_cta_strip',
		'title'    => 'Call-to-action strip',
		'location' => $loc( 'cta-strip' ),
		'fields'   => [
			$text( 'cta_text', 'text', 'Text', 'New shorts most weeks — they land on YouTube first.' ),
			$text( 'cta_button', 'button_label', 'Button label', 'Subscribe on YouTube' ),
			$text( 'cta_url', 'url', 'Button link (blank = YouTube channel)', '', 'url' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_page_header',
		'title'    => 'Page header',
		'location' => $loc( 'page-header' ),
		'fields'   => [
			$text( 'ph_heading', 'heading', 'Heading', '' ),
			$text( 'ph_intro', 'intro', 'Intro', '', 'textarea', [ 'rows' => 3 ] ),
			$text( 'ph_note', 'note', 'Small note under the intro', '', 'textarea', [ 'rows' => 2 ] ),
			$text( 'ph_crumb', 'parent_label', 'Breadcrumb parent label', '' ),
			$text( 'ph_crumb_url', 'parent_url', 'Breadcrumb parent link', '', 'url' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_quick_maths',
		'title'    => 'Quick Maths page',
		'location' => $loc( 'quick-maths' ),
		'fields'   => [
			$text( 'qm_heading', 'heading', 'Heading', 'Quick Maths' ),
			$text( 'qm_sub', 'sub', 'Sub-heading', 'Under a minute, straight to the point. One skill per short, no filler.' ),
			$text( 'qm_cta', 'cta_text', 'Strip text', 'New shorts most weeks — they land on YouTube first.' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_gaming_story',
		'title'    => 'Gaming & Story page',
		'location' => $loc( 'gaming-story' ),
		'fields'   => [
			$text( 'gs_heading', 'heading', 'Heading', 'Gaming & Story Maths' ),
			$text( 'gs_sub', 'sub', 'Intro', 'Real maths inside Roblox, Minecraft and stories — every video mapped to a GCSE topic, so the fun counts towards your revision.', 'textarea', [ 'rows' => 3 ] ),
			$text( 'gs_cta', 'cta_text', 'Strip text', 'More gaming lessons are on the way — subscribe to catch them first.' ),
			$text( 'gs_shorts_heading', 'shorts_heading', 'Shorts heading', 'Gaming shorts in Quick Maths' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_past_papers',
		'title'    => 'Past papers page',
		'location' => $loc( 'past-papers' ),
		'fields'   => [
			$text( 'pp_heading', 'heading', 'Heading', 'Past papers' ),
			$text( 'pp_intro', 'intro', 'Intro', 'Every Edexcel paper, uploaded as free PDFs with mark schemes — plus the practice worksheets that match what came up. AQA and OCR papers will follow once the mapping is verified.', 'textarea', [ 'rows' => 3 ] ),
			$text( 'pp_cta', 'cta_text', 'Strip text', 'Not sure where to start? The revision pathway puts topics in order first.' ),
			$text( 'pp_cta_btn', 'cta_button', 'Strip button', 'Start the pathway' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_calendar',
		'title'    => 'Exam calendar page',
		'location' => $loc( 'exam-calendar' ),
		'fields'   => [
			$text( 'cal_heading', 'heading', 'Heading', 'Exam calendar' ),
			$text( 'cal_intro', 'intro', 'Intro (after the level/board line)', 'Exam days are circled; the shaded weeks are a suggested revision plan that follows your pathway.', 'textarea', [ 'rows' => 2 ] ),
			$text( 'cal_plan_heading', 'plan_heading', 'Plan heading', 'Your revision plan, week by week' ),
			$text( 'cal_plan_sub', 'plan_sub', 'Plan sub-heading', 'A suggestion, not a rule — tick topics off in your pathway as you go.' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_my_learning',
		'title'    => 'My Learning page',
		'location' => $loc( 'my-learning' ),
		'fields'   => [
			$text( 'ml_heading', 'heading', 'Heading', 'My Learning' ),
			$text( 'ml_intro', 'intro', 'Signed-out intro', 'Sign in to keep your progress across devices. Here’s what My Learning does:' ),
		],
	] );

	acf_add_local_field_group( [
		'key'      => 'group_mwm_blk_site_footer',
		'title'    => 'Footer',
		'location' => $loc( 'site-footer' ),
		'fields'   => [
			[ 'key' => 'field_mwm_blk_footer_variant', 'name' => 'variant', 'label' => 'Layout', 'type' => 'radio', 'choices' => [ 'compact' => 'Compact (inner pages)', 'full' => 'Full (home)' ], 'default_value' => 'compact', 'layout' => 'horizontal' ],
			$text( 'footer_blurb', 'blurb', 'Blurb (full layout)', 'Free GCSE and A-level maths lessons, worksheets and revision pathways.' ),
			$text( 'footer_email', 'email', 'Contact email', 'hello@mathswithmelissa.co.uk', 'email' ),
		],
	] );
} );
