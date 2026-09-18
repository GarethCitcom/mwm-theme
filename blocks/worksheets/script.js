/* Worksheets: level/topic filters query the REST API; more load on scroll. */
(function () {
	'use strict';
	var root = document.querySelector('[data-worksheets]');
	if (!root || !window.MWMPaged) { return; }
	var seg = root.querySelector('.mwm-seg');
	var level = root.getAttribute('data-level') || 'all';
	var topic = root.getAttribute('data-topic') || '';

	var paged = window.MWMPaged({
		root: root, endpoint: 'worksheets', render: '1', perPage: 24,
		grid: root.querySelector('[data-grid]'), count: root.querySelector('[data-count]'), empty: root.querySelector('[data-empty]'),
		labels: { one: 'worksheet', many: 'worksheets' },
		params: function () { return { level: level === 'all' ? '' : level, topic: topic }; }
	});

	function syncUrl() {
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
		syncUrl(); paged.reload();
	});
	root.addEventListener('click', function (e) {
		var chip = e.target.closest('.mwm-chip[data-topic]');
		if (!chip) { return; }
		topic = chip.getAttribute('data-topic');
		root.querySelectorAll('.mwm-chip[data-topic]').forEach(function (c) { var on = c === chip; c.classList.toggle('is-on', on); c.setAttribute('aria-pressed', on ? 'true' : 'false'); });
		syncUrl(); paged.reload();
	});
})();
