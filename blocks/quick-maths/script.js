/* Quick Maths: level filter queries the REST API; more shorts load on scroll. */
(function () {
	'use strict';
	var root = document.querySelector('[data-quick-maths]');
	if (!root || !window.MWMPaged) { return; }
	var seg = root.querySelector('.mwm-seg');
	var level = root.getAttribute('data-level') || 'all';
	var paged = window.MWMPaged({
		root: root, endpoint: 'lessons', render: 'short', perPage: 24,
		grid: root.querySelector('[data-grid]'), count: root.querySelector('[data-count]'), empty: root.querySelector('[data-empty]'),
		labels: { one: 'short', many: 'shorts', suffix: ' · tap one to play it here' },
		params: function () { return { format: 'short', level: level === 'all' ? '' : level }; },
		onEmpty: function () { root.querySelector('.mwm-empty__title').textContent = level === 'all' ? 'Shorts are on their way' : 'No shorts for this level yet'; }
	});
	seg.addEventListener('click', function (e) {
		var tab = e.target.closest('.mwm-seg__tab');
		if (!tab) { return; }
		level = tab.getAttribute('data-value');
		seg.querySelectorAll('.mwm-seg__tab').forEach(function (t) { var on = t === tab; t.classList.toggle('is-on', on); t.setAttribute('aria-selected', on ? 'true' : 'false'); });
		try { history.replaceState(null, '', location.pathname + (level === 'all' ? '' : '?level=' + level)); } catch (err) {}
		paged.reload();
	});
})();
