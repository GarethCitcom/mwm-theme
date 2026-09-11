/* Topic browser: switch level, filter chips, open a live preview of a topic's lessons. */
(function () {
	'use strict';
	var section = document.querySelector('[data-topic-browser]');
	var island = document.getElementById('mwm-topic-browser-data');
	if (!section || !island) { return; }
	var data;
	try { data = JSON.parse(island.textContent); } catch (e) { return; }

	var chips = section.querySelector('[data-topic-chips]');
	var preview = section.querySelector('[data-topic-preview]');
	var seg = section.querySelector('.mwm-seg');
	var level = data.current;
	var topic = null;

	function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

	function renderChips() {
		var topics = (data.levels[level] || { topics: [] }).topics;
		chips.innerHTML = topics.map(function (t) {
			var icon = t.icon && data.icons[t.icon] ? '<span class="mwm-chip__icon">' + data.icons[t.icon] + '</span>' : '';
			return '<button type="button" class="mwm-chip mwm-chip--lg' + (topic === t.slug ? ' is-on' : '') + '" aria-pressed="' + (topic === t.slug ? 'true' : 'false') + '" data-topic="' + esc(t.slug) + '">' + icon + esc(t.name) + '</button>';
		}).join('');
	}

	function renderPreview() {
		var topics = (data.levels[level] || { topics: [] }).topics;
		var t = topics.filter(function (x) { return x.slug === topic; })[0];
		if (!t) { preview.setAttribute('hidden', ''); preview.innerHTML = ''; return; }
		var rows = t.lessons.map(function (l) {
			return '<a href="' + esc(l.url) + '" class="mwm-topic-preview__row">' +
				'<span class="mwm-topic-preview__thumb" role="img" aria-label=""' + (l.thumb ? ' style="background-image:url(' + esc(l.thumb) + ')"' : '') + '></span>' +
				'<span class="mwm-topic-preview__rowtitle">' + esc(l.title) + '</span>' +
				'<span class="mwm-topic-preview__rowmeta">' + esc(l.meta) + '</span></a>';
		}).join('');
		if (!t.lessons.length) {
			rows = '<p class="mwm-topic-preview__empty">Lesson previews for this topic are on their way — the topic page lists everything available now.</p>';
		}
		preview.innerHTML = '<div class="mwm-topic-preview__head">' +
			'<div class="mwm-topic-preview__title"><span class="mwm-topic-preview__name">' + esc(t.name) + '</span><span class="mwm-meta">' + t.count + (t.count === 1 ? ' lesson' : ' lessons') + ' · ' + esc(t.level) + '</span></div>' +
			'<div class="mwm-topic-preview__actions"><a href="' + esc(t.url) + '" class="mwm-arrow">See all ' + esc(t.name) + ' lessons<span aria-hidden="true">→</span></a>' +
			'<button type="button" class="mwm-topic-preview__close" aria-label="Close topic preview" data-close>✕</button></div></div>' + rows;
		preview.removeAttribute('hidden');
	}

	seg.addEventListener('click', function (e) {
		var tab = e.target.closest('.mwm-seg__tab');
		if (!tab) { return; }
		level = tab.getAttribute('data-value');
		topic = null;
		seg.querySelectorAll('.mwm-seg__tab').forEach(function (t) {
			var on = t === tab;
			t.classList.toggle('is-on', on);
			t.setAttribute('aria-selected', on ? 'true' : 'false');
		});
		renderChips();
		renderPreview();
		if (window.MWMProgress) { window.MWMProgress.setPrefs({ level: level }); }
	});
	chips.addEventListener('click', function (e) {
		var chip = e.target.closest('[data-topic]');
		if (!chip) { return; }
		var slug = chip.getAttribute('data-topic');
		topic = topic === slug ? null : slug;
		renderChips();
		renderPreview();
	});
	preview.addEventListener('click', function (e) {
		if (e.target.closest('[data-close]')) { topic = null; renderChips(); renderPreview(); }
	});
})();
