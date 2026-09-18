/* Gaming & Story: theme filter queries the REST API; more load on scroll. */
(function () {
	'use strict';
	var root = document.querySelector('[data-gaming]');
	if (!root || !window.MWMPaged) { return; }
	var format = root.getAttribute('data-format') || 'short';
	var noun = root.getAttribute('data-noun') || 'short';
	var theme = root.getAttribute('data-theme') || 'all';
	var chips = root.querySelectorAll('.mwm-chip[data-theme]');
	var names = { all: 'gaming', roblox: 'Roblox', minecraft: 'Minecraft', story: 'Story' };

	var paged = window.MWMPaged({
		root: root, endpoint: 'lessons', render: format === 'gaming' ? 'gaming' : 'short', perPage: 24,
		grid: root.querySelector('[data-grid]'), count: root.querySelector('[data-count]'), empty: root.querySelector('[data-empty]'),
		labels: { one: noun, many: noun + 's', suffix: format === 'short' ? ' · tap one to play it here' : '' },
		params: function () {
			return { format: format, theme: theme === 'all' ? (format === 'short' ? 'roblox,minecraft,story' : '') : theme };
		},
		onEmpty: function () { root.querySelector('[data-empty-theme]').textContent = names[theme] || theme; }
	});
	root.addEventListener('click', function (e) {
		var chip = e.target.closest('.mwm-chip[data-theme]');
		if (!chip) { return; }
		theme = chip.getAttribute('data-theme');
		chips.forEach(function (c) { var on = c === chip; c.classList.toggle('is-on', on); c.setAttribute('aria-pressed', on ? 'true' : 'false'); });
		try { history.replaceState(null, '', location.pathname + (theme === 'all' ? '' : '?theme=' + theme)); } catch (err) {}
		paged.reload();
	});
})();
