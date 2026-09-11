/* Quick Maths: filter shorts by level. */
(function () {
	'use strict';
	var root = document.querySelector('[data-quick-maths]');
	if (!root) { return; }
	var seg = root.querySelector('.mwm-seg');
	var cards = root.querySelectorAll('[data-level]');
	var empty = root.querySelector('[data-empty]');
	var count = root.querySelector('[data-count]');
	seg.addEventListener('click', function (e) {
		var tab = e.target.closest('.mwm-seg__tab');
		if (!tab) { return; }
		var level = tab.getAttribute('data-value');
		seg.querySelectorAll('.mwm-seg__tab').forEach(function (t) {
			var on = t === tab;
			t.classList.toggle('is-on', on);
			t.setAttribute('aria-selected', on ? 'true' : 'false');
		});
		var n = 0;
		cards.forEach(function (c) {
			var show = level === 'all' || c.getAttribute('data-level') === level;
			if (show) { n++; c.removeAttribute('hidden'); } else { c.setAttribute('hidden', ''); }
		});
		count.textContent = n + ' shorts · every one opens on YouTube';
		if (n) { empty.setAttribute('hidden', ''); } else { empty.removeAttribute('hidden'); root.querySelector('[data-empty-title]').textContent = 'No shorts for this level yet'; }
	});
})();
