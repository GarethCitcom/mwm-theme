/* Learn Maths: filters query the REST API (server-side paging); pages load on scroll. */
(function () {
	'use strict';
	var root = document.querySelector('[data-browse]');
	var island = document.getElementById('mwm-browse-data');
	if (!root || !island || !window.MWMPaged) { return; }
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
		var s = t ? t.subtopics.filter(function (x) { return x.slug === slug; })[0] : null;
		return s ? s.name : slug;
	}
	function anyFilter() { return !!S.subtopic || S.type !== 'all' || S.worksheet || S.quiz; }

	var paged = window.MWMPaged({
		root: root,
		endpoint: 'lessons',
		render: 'grid',
		perPage: D.perPage,
		grid: $('[data-grid]'),
		count: $('[data-browse-count]'),
		empty: $('[data-browse-empty]'),
		labels: { one: 'lesson', many: 'lessons' },
		params: function () {
			return { level: D.level, topic: S.topic, subtopic: S.subtopic, format: S.type === 'all' ? '' : S.type, worksheet: S.worksheet, quiz: S.quiz };
		},
		onEmpty: function () {
			var t = topic();
			var soon = D.level === 'a-level' && !anyFilter();
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
	});

	function paintControls() {
		var t = topic();
		var any = anyFilter();
		$('[data-browse-title]').textContent = t ? t.name : D.levelName;
		$('[data-browse-intro]').textContent = t ? (t.intro || ('Every ' + t.name + ' lesson for this level, with worksheets and quizzes where they exist.')) : 'Every lesson for this level, organised by topic. Pick a topic or filter to find exactly what you need.';
		root.querySelectorAll('[data-practise-label]').forEach(function (el) { el.textContent = t ? t.name : D.levelName; });
		var ws = $('[data-practise-ws]');
		if (ws) { ws.setAttribute('href', ws.getAttribute('href').split('?')[0] + '?level=' + D.level + (t ? '&topic=' + t.slug : '')); }

		$('[data-browse-topics]').querySelectorAll('[data-topic]').forEach(function (b) {
			var on = b.getAttribute('data-topic') === S.topic;
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		var wrap = $('[data-browse-subtopics]');
		if (t && t.subtopics.length) {
			wrap.removeAttribute('hidden');
			$('[data-sub-total]').textContent = t.subtopics.length;
			var visible = subExpanded ? t.subtopics : t.subtopics.slice(0, 8);
			var html = visible.map(function (s) {
				var on = S.subtopic === s.slug;
				return '<button type="button" class="mwm-subchip' + (on ? ' is-on' : '') + '" aria-pressed="' + (on ? 'true' : 'false') + '" data-subtopic="' + esc(s.slug) + '">' + esc(s.name) + '</button>';
			}).join('');
			if (t.subtopics.length > 8) { html += '<button type="button" class="mwm-subchip mwm-subchip--more" data-sub-more>' + (subExpanded ? 'Show fewer' : 'Show all ' + t.subtopics.length) + '</button>'; }
			$('[data-sub-list]').innerHTML = html;
		} else {
			wrap.setAttribute('hidden', '');
		}
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
		var active = [];
		if (S.subtopic) { active.push({ label: subName(S.subtopic), key: 'subtopic' }); }
		if (S.type !== 'all') { active.push({ label: TYPE_LABELS[S.type], key: 'type' }); }
		if (S.worksheet) { active.push({ label: 'Has worksheet', key: 'worksheet' }); }
		if (S.quiz) { active.push({ label: 'Has quiz', key: 'quiz' }); }
		var box = $('[data-browse-active]');
		box.querySelectorAll('[data-remove]').forEach(function (b) { b.remove(); });
		active.reverse().forEach(function (a) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'mwm-chip mwm-chip--sm';
			b.setAttribute('aria-label', 'Remove filter: ' + a.label.toLowerCase());
			b.setAttribute('data-remove', a.key);
			b.innerHTML = esc(a.label) + '<span aria-hidden="true">✕</span>';
			box.insertBefore(b, box.firstChild);
		});
		root.querySelectorAll('[data-clear]').forEach(function (b) { if (any) { b.removeAttribute('hidden'); } else { b.setAttribute('hidden', ''); } });

		var url = D.urls.browse.replace(/\/$/, '') + '/' + D.level + '/' + (S.topic ? S.topic + '/' : '');
		var q = [];
		if (S.subtopic) { q.push('subtopic=' + encodeURIComponent(S.subtopic)); }
		if (S.type !== 'all') { q.push('type=' + S.type); }
		if (S.worksheet) { q.push('worksheet=1'); }
		if (S.quiz) { q.push('quiz=1'); }
		try { history.replaceState(null, '', url + (q.length ? '?' + q.join('&') : '')); } catch (e) {}
	}

	root.addEventListener('click', function (e) {
		var el, changed = true;
		if ((el = e.target.closest('[data-topic]'))) {
			var slug = el.getAttribute('data-topic');
			S.topic = S.topic === slug ? '' : slug; S.subtopic = ''; subExpanded = false;
		} else if ((el = e.target.closest('[data-subtopic]'))) {
			var s = el.getAttribute('data-subtopic'); S.subtopic = S.subtopic === s ? '' : s;
		} else if ((el = e.target.closest('[data-sub-more]'))) {
			subExpanded = !subExpanded; changed = false;
		} else if ((el = e.target.closest('[data-type]'))) {
			S.type = el.getAttribute('data-type');
		} else if ((el = e.target.closest('[data-toggle]'))) {
			var k = el.getAttribute('data-toggle'); S[k] = !S[k];
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
		paintControls();
		if (changed) { paged.reload(); }
	});
})();
