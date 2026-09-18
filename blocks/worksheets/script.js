/* Worksheets: filter the grid by level and topic, keeping the URL in step. */
(function () {
	'use strict';
	var root = document.querySelector('[data-worksheets]');
	if (!root) { return; }
	var seg = root.querySelector('.mwm-seg');
	var chips = root.querySelectorAll('.mwm-chip[data-topic]');
	var items = root.querySelectorAll('[data-grid] > [data-level]');
	var level = (seg.querySelector('.is-on') || {}).getAttribute ? seg.querySelector('.is-on').getAttribute('data-value') : 'all';
	var topic = (root.querySelector('.mwm-chip.is-on') || { getAttribute: function () { return ''; } }).getAttribute('data-topic') || '';

	function apply() {
		var n = 0;
		items.forEach(function (it) {
			var show = (level === 'all' || it.getAttribute('data-level') === level) && (!topic || it.getAttribute('data-topic') === topic);
			it.style.display = show ? 'contents' : 'none';
			if (show) { n++; }
		});
		root.querySelector('[data-count]').textContent = n === 1 ? '1 worksheet' : n + ' worksheets';
		var empty = root.querySelector('[data-empty]');
		if (n) { empty.setAttribute('hidden', ''); } else { empty.removeAttribute('hidden'); }
		var q = [];
		if (level !== 'all') { q.push('level=' + level); }
		if (topic) { q.push('topic=' + encodeURIComponent(topic)); }
		try { history.replaceState(null, '', location.pathname + (q.length ? '?' + q.join('&') : '')); } catch (e) {}
	}
	seg.addEventListener('click', function (e) {
		var tab = e.target.closest('.mwm-seg__tab');
		if (!tab) { return; }
		level = tab.getAttribute('data-value');
		seg.querySelectorAll('.mwm-seg__tab').forEach(function (t) { var on = t === tab; t.classList.toggle('is-on', on); t.setAttribute('aria-selected', on ? 'true' : 'false'); });
		apply();
	});
	root.addEventListener('click', function (e) {
		var chip = e.target.closest('.mwm-chip[data-topic]');
		if (!chip) { return; }
		topic = chip.getAttribute('data-topic');
		chips.forEach(function (c) { var on = c === chip; c.classList.toggle('is-on', on); c.setAttribute('aria-pressed', on ? 'true' : 'false'); });
		apply();
	});
})();
