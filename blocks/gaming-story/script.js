/* Gaming & Story: filter by theme chip. */
(function () {
	'use strict';
	var root = document.querySelector('[data-gaming]');
	if (!root) { return; }
	var chips = root.querySelectorAll('[data-theme].mwm-chip');
	var items = root.querySelectorAll('[data-grid] > [data-theme]');
	root.addEventListener('click', function (e) {
		var chip = e.target.closest('.mwm-chip[data-theme]');
		if (!chip) { return; }
		var theme = chip.getAttribute('data-theme');
		chips.forEach(function (c) {
			var on = c === chip;
			c.classList.toggle('is-on', on);
			c.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		var n = 0;
		items.forEach(function (it) {
			var show = theme === 'all' || it.getAttribute('data-theme') === theme;
			if (show) { n++; it.style.display = 'contents'; } else { it.style.display = 'none'; }
		});
		root.querySelector('[data-count]').textContent = n === 1 ? '1 video' : n + ' videos';
		var empty = root.querySelector('[data-empty]');
		if (n) { empty.setAttribute('hidden', ''); } else { empty.removeAttribute('hidden'); root.querySelector('[data-empty-theme]').textContent = chip.textContent.trim(); }
	});
})();
