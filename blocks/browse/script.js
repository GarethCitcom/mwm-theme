/* Learn Maths: instant client-side filtering with the URL kept in sync. */
(function () {
	'use strict';
	var root = document.querySelector('[data-browse]');
	var island = document.getElementById('mwm-browse-data');
	if (!root || !island) { return; }
	var D;
	try { D = JSON.parse(island.textContent); } catch (e) { return; }

	var S = D.state;
	var subExpanded = false;
	var TYPE_LABELS = { all: 'All', lesson: 'Lessons', short: 'Quick Maths', gaming: 'Gaming & Story' };
	var $ = function (sel) { return root.querySelector(sel); };
	function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

	function topic() { return D.topics.filter(function (t) { return t.slug === S.topic; })[0] || null; }
	function subName(slug) {
		var t = topic();
		if (!t) { return slug; }
		var s = t.subtopics.filter(function (x) { return x.slug === slug; })[0];
		return s ? s.name : slug;
	}
	function anyFilter() { return !!S.subtopic || S.type !== 'all' || S.worksheet || S.quiz; }
	function filtered() {
		return D.lessons.filter(function (c) {
			if (S.topic && c.topic_slug !== S.topic) { return false; }
			if (S.subtopic && c.subtopic_slug !== S.subtopic) { return false; }
			if (S.type !== 'all' && c.format !== S.type) { return false; }
			if (S.worksheet && !c.has_worksheet) { return false; }
			if (S.quiz && !c.has_quiz) { return false; }
			return true;
		});
	}

	function tag(text, style) { return '<span class="mwm-tag mwm-tag--' + style + '">' + esc(text) + '</span>'; }
	function badge(kind) { return '<span class="mwm-badge"><span class="mwm-badge__icon">' + D.icons[kind] + '</span>' + (kind === 'quiz' ? 'Quiz' : 'Worksheet') + '</span>'; }
	function card(c) {
		var media;
		if (c.is_short) {
			media = '<a href="' + esc(c.url) + '" class="mwm-thumb mwm-thumb--short-in-wide" tabindex="-1" aria-hidden="true"><img src="' + esc(c.thumb) + '" alt="" loading="lazy"></a>';
		} else if (c.thumb) {
			media = '<a href="' + esc(c.url) + '" class="mwm-thumb" tabindex="-1" aria-hidden="true"><img src="' + esc(c.thumb) + '" alt="' + esc(c.alt) + '" loading="lazy"></a>';
		} else {
			media = '<div class="mwm-thumb mwm-thumb--placeholder" role="img" aria-label="' + esc(c.title + ' — thumbnail to follow') + '"><span>' + (c.theme ? esc(c.theme.toLowerCase()) + ' ' : '') + 'thumbnail to follow</span></div>';
		}
		return '<article class="mwm-card mwm-card--video" data-id="' + c.id + '">' + media +
			'<div class="mwm-card__body"><div class="mwm-tags">' + (c.level ? tag(c.level, c.level_style) : '') + (c.topic ? tag(c.topic, 'outline') : '') + '</div>' +
			'<h3 class="mwm-card__title"><a href="' + esc(c.url) + '">' + esc(c.title) + '</a></h3>' +
			'<div class="mwm-card__foot"><span class="mwm-meta">' + esc(c.duration) + '</span><span class="mwm-badges">' + (c.has_worksheet ? badge('worksheet') : '') + (c.has_quiz ? badge('quiz') : '') + '</span></div></div></article>';
	}

	function render() {
		var t = topic();
		var res = filtered();
		var isALevel = D.level === 'a-level';
		var any = anyFilter();

		// Title & intro
		$('[data-browse-title]').textContent = t ? t.name : D.levelName;
		$('[data-browse-intro]').textContent = t ? (t.intro || ('Every ' + t.name + ' lesson for this level, with worksheets and quizzes where they exist.')) : 'Every lesson for this level, organised by topic. Pick a topic or filter to find exactly what you need.';
		root.querySelectorAll('[data-practise-label]').forEach(function (el) { el.textContent = t ? t.name : D.levelName; });
		var ws = $('[data-practise-ws]');
		if (ws) { ws.setAttribute('href', D.urls.browse.replace(/\/$/, '') + '/' + D.level + '/' + (t ? t.slug + '/' : '') + '?worksheet=1'); }

		// Topic chips
		$('[data-browse-topics]').querySelectorAll('[data-topic]').forEach(function (b) {
			var on = b.getAttribute('data-topic') === S.topic;
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});

		// Subtopics
		var wrap = $('[data-browse-subtopics]');
		if (t && t.subtopics.length) {
			wrap.removeAttribute('hidden');
			$('[data-sub-total]').textContent = t.subtopics.length;
			var visible = subExpanded ? t.subtopics : t.subtopics.slice(0, 8);
			var html = visible.map(function (s) {
				var on = S.subtopic === s.slug;
				return '<button type="button" class="mwm-subchip' + (on ? ' is-on' : '') + '" aria-pressed="' + (on ? 'true' : 'false') + '" data-subtopic="' + esc(s.slug) + '">' + esc(s.name) + '</button>';
			}).join('');
			if (t.subtopics.length > 8) {
				html += '<button type="button" class="mwm-subchip mwm-subchip--more" data-sub-more>' + (subExpanded ? 'Show fewer' : 'Show all ' + t.subtopics.length) + '</button>';
			}
			$('[data-sub-list]').innerHTML = html;
		} else {
			wrap.setAttribute('hidden', '');
		}

		// Type + toggles
		root.querySelectorAll('[data-type]').forEach(function (b) {
			var on = b.getAttribute('data-type') === S.type;
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		root.querySelectorAll('[data-toggle]').forEach(function (b) {
			var on = !!S[b.getAttribute('data-toggle')];
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});

		// Active filter chips
		var active = [];
		if (S.subtopic) { active.push({ label: subName(S.subtopic), key: 'subtopic' }); }
		if (S.type !== 'all') { active.push({ label: TYPE_LABELS[S.type], key: 'type' }); }
		if (S.worksheet) { active.push({ label: 'Has worksheet', key: 'worksheet' }); }
		if (S.quiz) { active.push({ label: 'Has quiz', key: 'quiz' }); }
		var box = $('[data-browse-active]');
		box.querySelectorAll('[data-remove]').forEach(function (b) { b.remove(); });
		var count = $('[data-browse-count]');
		active.reverse().forEach(function (a) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'mwm-chip mwm-chip--sm';
			b.setAttribute('aria-label', 'Remove filter: ' + a.label.toLowerCase());
			b.setAttribute('data-remove', a.key);
			b.innerHTML = esc(a.label) + '<span aria-hidden="true">✕</span>';
			box.insertBefore(b, box.firstChild);
		});
		count.textContent = res.length === 1 ? '1 lesson' : res.length + ' lessons';
		root.querySelectorAll('[data-clear]').forEach(function (b) { if (any) { b.removeAttribute('hidden'); } else { b.setAttribute('hidden', ''); } });

		// Results / empty
		var grid = $('[data-browse-results]');
		var empty = $('[data-browse-empty]');
		if (res.length) {
			grid.innerHTML = res.map(card).join('');
			grid.removeAttribute('hidden');
			empty.setAttribute('hidden', '');
		} else {
			grid.setAttribute('hidden', '');
			empty.removeAttribute('hidden');
			var soon = isALevel && !any;
			$('[data-empty-title]').textContent = soon ? 'A-level lessons are coming soon' : 'No lessons match these filters yet';
			$('[data-empty-body]').textContent = soon ? 'Pure, Statistics and Mechanics are being recorded now. GCSE Foundation and Higher are ready to browse.' : 'Try removing a filter, or pick a different topic.';
			var chips = $('[data-empty-chips]');
			if (t && t.subtopics.length) {
				chips.innerHTML = t.subtopics.slice(0, 3).map(function (s) { return '<button type="button" class="mwm-chip mwm-chip--xs" data-suggest="' + esc(s.slug) + '">' + esc(s.name) + '</button>'; }).join('');
				chips.removeAttribute('hidden');
			} else {
				chips.setAttribute('hidden', '');
			}
		}

		// URL
		var url = D.urls.browse.replace(/\/$/, '') + '/' + D.level + '/' + (S.topic ? S.topic + '/' : '');
		var q = [];
		if (S.subtopic) { q.push('subtopic=' + encodeURIComponent(S.subtopic)); }
		if (S.type !== 'all') { q.push('type=' + S.type); }
		if (S.worksheet) { q.push('worksheet=1'); }
		if (S.quiz) { q.push('quiz=1'); }
		try { history.replaceState(null, '', url + (q.length ? '?' + q.join('&') : '')); } catch (e) {}
	}

	root.addEventListener('click', function (e) {
		var el;
		if ((el = e.target.closest('[data-topic]'))) {
			var slug = el.getAttribute('data-topic');
			S.topic = S.topic === slug ? '' : slug;
			S.subtopic = '';
			subExpanded = false;
		} else if ((el = e.target.closest('[data-subtopic]'))) {
			var s = el.getAttribute('data-subtopic');
			S.subtopic = S.subtopic === s ? '' : s;
		} else if ((el = e.target.closest('[data-sub-more]'))) {
			subExpanded = !subExpanded;
		} else if ((el = e.target.closest('[data-type]'))) {
			S.type = el.getAttribute('data-type');
		} else if ((el = e.target.closest('[data-toggle]'))) {
			var k = el.getAttribute('data-toggle');
			S[k] = !S[k];
		} else if ((el = e.target.closest('[data-remove]'))) {
			var r = el.getAttribute('data-remove');
			if (r === 'type') { S.type = 'all'; } else if (r === 'subtopic') { S.subtopic = ''; } else { S[r] = false; }
		} else if ((el = e.target.closest('[data-clear]'))) {
			S.subtopic = ''; S.type = 'all'; S.worksheet = false; S.quiz = false;
		} else if ((el = e.target.closest('[data-suggest]'))) {
			S.subtopic = el.getAttribute('data-suggest'); S.type = 'all'; S.worksheet = false; S.quiz = false;
		} else {
			return;
		}
		e.preventDefault();
		render();
	});
})();
