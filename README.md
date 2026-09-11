# Maths with Melissa — block theme

Recreates the approved prototypes in `docs/design/design_handoff_mwm/` as a WordPress block theme. Every page section is an **ACF Block** (`blocks/<name>/block.json` + `render.php`, optional `script.js`). The data model lives in the **MWM Core** plugin; the theme only renders.

## Structure

```
assets/css/mwm.css      all styles — values transcribed from the prototypes' inline styles
assets/css/login.css    branded wp-login screen
assets/js/mwm.js        theme toggle (localStorage mwm-theme), mobile menu, strip arrows, progress store (window.MWMProgress)
assets/fonts/           Inter (variable, latin)
assets/img/             logo + icon SVGs (charcoal / white), quiz diagram
blocks/                 26 ACF blocks (see below)
inc/icons.php           inline SVG icons
inc/template-tags.php   card / tag / chip / breadcrumb renderers
inc/blocks.php          block registration, per-block script enqueueing
inc/block-fields.php    ACF field groups for block settings (all default to the approved copy)
parts/                  header, footer (compact), footer-full (home)
templates/              front-page, page, page-plain, single-mwm_lesson, single-mwm_quiz, search, index, 404
theme.json              palette (7 colours), Inter, 1200/1264 layout
```

## Blocks

Home: `home-hero`, `level-cards`, `featured-lessons`, `how-it-works`, `topic-browser`, `pathway-cards`, `gaming-row`, `quick-maths-band`, `exam-panel`, `subscribe-strip`.
Pages: `browse`, `pathway`, `exam-calendar`, `past-papers`, `quick-maths`, `gaming-story`, `my-learning`, `search-results`, `page-header`, `cta-strip`.
Lesson/quiz templates: `lesson-header`, `lesson-content`, `lesson-next`, `quiz`. Chrome: `site-header`, `site-footer`.

Blocks read the current URL/state on the server (no-JS works) and their `script.js` takes over for instant filtering, ticks, quizzes and the topic preview.

## Design rules baked in

* Palette only: `#FFFFFF #171717 #5C5C5C #F9E8EE #F1CBD8 #C2185B #A3144C` plus the dark-mode set on `html[data-theme="dark"]`. No gradients, no green, no orange.
* Inter 400/500/600. Hero 56 / H1 40 / H2 32 / H4 24 / H5 20; body 16, meta 14, tags 12. Mobile (≤700px): 40 / 32 / 24 (Home H2 28).
* 1200px content, header caps at 1264px, section spacing 96/56, page padding 32/16.
* Cards 12px radius, 1px `#F1CBD8` border, shadow on hover only. 44px touch targets, 2px focus rings.
* Never a dead button: absent resources show a grey availability line.

## Progress (saved / completed / ticks / quiz scores)

Stored in `localStorage['mwm-progress']` for everyone; signed-in visitors also sync to user meta through `mwm/v1/me/progress`, and anything saved while signed out is pushed on first sign-in.
