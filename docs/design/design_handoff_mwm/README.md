# Handoff: Maths with Melissa — full site redesign

## Overview
Complete redesign of mathswithmelissa.co.uk — a free UK GCSE & A-level maths learning site built around Kym's YouTube channel. Eleven screens: Home, Browse (topic), Lesson, Revision Pathway, Exam Calendar, Past Papers, Quick Maths, Gaming & Story Maths, Quiz, My Learning, and a front-end Admin (mini-CMS) so Kym can publish without touching wp-admin. Approved by Kym; design direction signed off.

## About the design files
The files in `prototype/` are **design references created in HTML** — interactive prototypes showing intended look and behaviour, NOT production code. Each `*.dc.html` opens directly in a browser (keep `support.js` and `assets/` beside them). The task is to **recreate these designs in the target environment** — a WordPress theme + plugin on the existing site (see "Existing content & migration") — using WP's established patterns. Read the prototypes' inline styles for exact values; read their logic classes (bottom `<script>`) for interaction behaviour.

## Fidelity
**High-fidelity.** Colours, typography, spacing, copy and interactions are final. Recreate pixel-perfectly. The copy in the prototypes is approved UK-English copy — reuse it verbatim.

## Design tokens
Colours (the complete palette — use nothing else):
- `#FFFFFF` page surface / cards
- `#171717` headings, body text, ink band (footer, Quick Maths band) — dark-mode surface
- `#5C5C5C` secondary text, metadata
- `#F9E8EE` soft pink — section tints and hover fills ONLY (never text/icons)
- `#F1CBD8` pink — card borders, dividers, thin-line decorative maths shapes
- `#C2185B` action pink — buttons, links, active nav, focus rings; hover `#A3144C`
- Dark mode (user toggle, persisted in `localStorage['mwm-theme']`): surface `#171717`, card `#211D1F`, tint `#271D22`, border `#4A353D`, muted `#BBAFB5`, links `#F088B0` (hover `#F7ABC8`), action hover `#E0447F`, band `#0D0D0D`
- No green, no orange, no gradients, no textures.

Typography — Inter throughout (400/500/600):
- Hero display 56px / H1 40px / H2 32px / H4 24px / H5 20px — all weight 600, line-height 1.3 (1.4 for H4–H6)
- Lead 20px, body 16px, meta 14px, tags 12px — weight 400, line-height 1.5; nav 16px weight 500
- Mobile: hero → 40px, H1 → 32px, H2 → 24px
- Maths expressions render as text/HTML (sup/sub, minus signs), never images.

Layout:
- 1440 design: 1200px content container (headers cap at 1264px so inner content aligns after 32px padding), 12 cols, 24px gutters
- Section spacing 96px desktop / 56px mobile; page padding 32px / 16px mobile
- Article pages: 8-col main + 4-col sticky sidebar
- Cards: 12px radius, 1px `#F1CBD8` border, **no shadow** (soft shadow on hover only)
- Buttons: primary filled `#C2185B` (white text, 12–16px radius, 48px tall), secondary 1px `#C2185B` outline, tertiary text link. 44px minimum touch targets.
- Responsive breakpoint: 700px. Each prototype's `<style>` block defines CSS vars in `[data-mwm]` with a 700px media override — replicate that pattern.

Components used across pages (keep identical everywhere): header with primary nav + search + theme toggle, level switcher (segmented: GCSE Foundation / GCSE Higher / A-level), video card (thumbnail 16:9 uncropped in 1px pink border, title, level tag, topic tag, duration, worksheet/quiz icons only when present), short card (9:16), topic chips, pathway row, exam panel, availability line, ink footer with white logo.

## Screens
| Screen | Prototype | Purpose / notable behaviour |
|---|---|---|
| Home | `Home.dc.html` | Hero, 3 level cards, "Start with these" grid, topic browser (level switcher + chips filter a live preview row), pathway cards, Gaming row, Quick Maths ink band strip (horizontal scroll + arrows), exam panel, subscribe strip, footer |
| Browse | `Browse.dc.html` | GCSE Higher › Algebra. Subtopic dropdown selector (not pills — deliberately distinct from the main level switcher; scales to 20+ subtopics), filter bar (content type, Has worksheet, Has quiz), active-filter chips, live-filtered card grid, honest empty state |
| Lesson | `Lesson.dc.html` | Breadcrumb, tag row, thumbnail with play (no autoplay/embed until tapped), What you'll learn, related lessons; sticky Practice sidebar (worksheet download, reveal answers, quiz CTA, Save/Mark complete). Missing worksheet → availability line "No worksheet for this lesson yet", never a dead button |
| Pathway | `Pathway.dc.html` | 32 topics under 6 H2 groups, tick controls (user-set, persisted), progress bar, quiz average, prerequisite notes, greyed absent resource icons, one "Coming soon" row, exam panel + past papers sidebar |
| Calendar | `Calendar.dc.html` | May+June 2027 calendars, exam days circled in action pink, shaded revision-plan weeks, week-by-week plan list |
| Past Papers | `PastPapers.dc.html` | Filterable table of paper sets (board/tier/year/season), each row: paper PDF, mark scheme, linked practice worksheet where one exists, availability line otherwise |
| Quick Maths | `QuickMaths.dc.html` | 9:16 shorts grid, topic filter |
| Gaming & Story | `Gaming.dc.html` | Theme tag (Roblox/Minecraft/Story) + real maths topic tag on every card |
| Quiz | `Quiz.dc.html` | Question types: multiple choice, choice-with-images, put-in-order. One question at a time, explanation after answering, score screen with per-question review + Try again |
| My Learning | `MyLearning.dc.html` | Signed-in view: saved lessons, pathway progress, quiz history |
| Admin (mini-CMS) | `Admin.dc.html` | Kym's front-end publishing area — see below |

## Admin / mini-CMS (build as a WP plugin)
Kym finds wp-admin overwhelming — this is a **front-end admin** at e.g. `/studio/`, gated by capability check (her WP user), styled exactly like the site. Never mention the developer anywhere in its UI; the tone tells her she can do everything herself.
- **Add a lesson** — 4-step wizard: paste YouTube URL (fetch title/duration/thumbnail via oEmbed), pick level+topic, upload worksheet/answers PDFs (optional), preview & publish. Skipped uploads produce the availability line automatically.
- **Quiz via ChatGPT** — the wizard shows a copyable prompt; Kym pastes ChatGPT's JSON back (or uploads .json); friendly validator checks it (plain-English errors per question) before attach. Schema: `{"questions":[{"type":"choice"|"order","q","options":[3–5],"correct":n|"correctOrder":[…],"explain","optionImages?":[…]}]}` — identical to what the Quiz page renders. Store as post meta.
- **Manage content** — list of everything she's published (lessons, past papers, exam dates, quizzes) with filter chips, edit and delete (confirm step).
- **Past papers & exam dates** — simple forms; exam dates feed the exam panels and Calendar.
- **Quick Maths / Gaming & Story auto-populate** — WP-Cron job pulls the YouTube playlists (Data API v3, one key, daily) and upserts shorts/gaming videos as posts; Kym never touches these. Playlist IDs are plugin settings.

## Interactions & behaviour (global)
- Light/dark toggle in header; `localStorage['mwm-theme']`; both logo variants supplied in `assets/` (charcoal on white, white on ink — never recoloured)
- Visible focus rings (`2px #C2185B`, offset 2) on ALL interactive elements; WCAG 2.2 AA contrast throughout
- No autoplay, no motion beyond simple hover (background tint / soft shadow / link colour)
- Never a dead button: absent resources show a short grey availability line
- Search in header on every page; mobile ≤700px: icon logo, hamburger menu, stacked layouts, horizontal-scroll strips with partial-card peek

## Existing content & migration (IMPORTANT)
The current site is WordPress built with Oxygen and a cluttered plugin stack — it will NOT be reused. **Start fresh**: a clean WP install with a new block theme and **ACF Pro**, with all custom rendering done as **ACF Blocks**. The only thing to preserve is the content itself — existing lessons already have worksheets attached (e.g. `mathswithmelissa.co.uk/lesson/6968/`).
1. New install: block theme ("mwm-theme") + ACF Pro. Field groups for lessons, past papers, exam dates, pathways, quizzes; register CPTs via ACF or a small core plugin; page sections built as ACF Blocks so layouts stay editable.
2. **Content migration, not platform migration**: export lessons from the old site (WP export/REST or a WP-CLI script run against the old DB) capturing title, YouTube ID, level, topic, worksheet + answers PDFs. Import into the fresh install as the new CPT with ACF fields; copy the PDF/media files across. Nothing from the old theme, Oxygen markup, or plugin tables comes along.
3. Traffic is currently low, so URL continuity is nice-to-have not critical — still add a simple 301 map from old `/lesson/{id}/` URLs to the new slugs where practical.
4. The old site stays live untouched until the new one is ready to switch DNS/hosting.

## Assets
`prototype/assets/` — logo SVGs (charcoal + white, full + icon-only), video thumbnails (16:9 dark), shorts thumbnails (9:16). Thumbnails in production come from YouTube (`maxresdefault`); the supplied ones are Kym's real thumbnail style.

## Screenshots
`screenshots/` — one desktop capture per screen, named to match the prototypes.

## Files
- `prototype/*.dc.html` + `support.js` + `assets/` — open in any browser
- `STARTER-PROMPT.md` — paste into Claude Code to begin
