# Starter prompt for Claude Code

Paste everything below the line into Claude Code, run from the repo/site root (ideally a staging copy of the live WordPress install).

---

I'm rebuilding mathswithmelissa.co.uk to a new, approved design — as a **fresh WordPress build** (the old site is Oxygen-based with a cluttered plugin stack and will be abandoned; only its content gets migrated). Stack: clean WP install, a new block theme, **ACF Pro with ACF Blocks** for all custom fields and page sections. The design handoff package is in `design_handoff_mwm/` — read `README.md` fully first. It contains 11 interactive HTML prototypes in `prototype/` (open them in a browser; inline styles carry the exact values, the `<script>` at the bottom of each carries the interaction logic) plus `screenshots/`.

**What to build**
1. A new WordPress **block theme** ("mwm-theme") recreating the prototype screens pixel-perfectly, with page sections implemented as **ACF Blocks**: Home, Browse, Lesson, Revision Pathway, Exam Calendar, Past Papers, Quick Maths, Gaming & Story Maths, Quiz, My Learning. Inter font, the exact 7-colour palette, light/dark toggle (localStorage `mwm-theme`), 1200px container, 700px breakpoint, WCAG 2.2 AA, visible focus rings, 44px touch targets, no gradients/green/orange.
2. A companion **plugin** ("mwm-core") owning the data model: CPTs registered in code, **ACF Pro field groups** (registered via PHP/JSON, not click-configured) for lessons, past papers, exam dates, pathways, quizzes; the quiz JSON schema in the README; REST endpoints the theme needs; a WP-Cron job that syncs the Quick Maths and Gaming & Story YouTube playlists daily via the YouTube Data API (playlist IDs as settings).
3. A **front-end admin** at `/studio/` (capability-gated) recreating `Admin.dc.html`: the 4-step add-a-lesson wizard (YouTube oEmbed fetch, level/topic, optional PDF uploads, preview/publish), the ChatGPT quiz paste-and-validate flow, manage/edit/delete lists, past-paper and exam-date forms. Plain, kind UK-English microcopy exactly as prototyped; never a dead button — absent resources show availability lines.

**Critical constraint — existing content**
This is a fresh install: nothing from the old Oxygen theme, its plugins, or its database structure carries over. But the old site's lessons (with attached worksheet PDFs, e.g. `/lesson/6968/`) must be migrated as content. Write a one-off import script (WP-CLI, reading the old site's REST API or a DB export I'll provide) that pulls each lesson's title, YouTube ID, level, topic, and worksheet/answers PDFs, creates the new CPT entries with ACF fields populated, and sideloads the media. Also generate a 301 redirect map from old lesson URLs to new slugs. Ask me for the old-site export/credentials when you reach that step.

**Process**
Work in this order: (1) fresh install scaffold — theme, plugin, ACF Pro field groups and CPTs, (2) theme templates + ACF Blocks against the prototypes, (3) front-end admin, (4) content import script + redirect map, (5) cron playlist sync. Ask me before any destructive operation. Compare each finished template against the matching screenshot and prototype before moving on.
