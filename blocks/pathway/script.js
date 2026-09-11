/* Revision pathway: ticks (persisted), progress bar, quiz average, level/board switching. */
(function () {
	'use strict';
	var root = document.querySelector('[data-pathway]');
	if (!root) { return; }
	var pathwayId = root.getAttribute('data-pathway');
	var base = root.getAttribute('data-base');
	var ticks = root.querySelectorAll('[data-tick]');
	var total = Number(root.getAttribute('data-total')) || ticks.length;

	function paint() {
		var P = window.MWMProgress;
		if (!P) { return; }
		var count = 0;
		ticks.forEach(function (b) {
			var on = P.hasTick(pathwayId, b.getAttribute('data-tick'));
			if (on) { count++; }
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
			b.setAttribute('aria-label', (on ? 'Unmark ' : 'Mark ') + b.getAttribute('data-topic') + ' as done');
		});
		var pct = total ? Math.round(count / total * 100) : 0;
		root.querySelector('[data-progress-label]').textContent = count + ' of ' + total + ' ticked';
		root.querySelector('[data-progress-pct]').textContent = pct + '%';
		root.querySelector('[data-progress-bar]').style.width = pct + '%';
		var avg = P.quizAverage();
		root.querySelector('[data-quiz-average]').textContent = avg === null ? '—' : avg + '%';
	}

	root.addEventListener('click', function (e) {
		var b = e.target.closest('[data-tick]');
		if (!b || !window.MWMProgress) { return; }
		window.MWMProgress.toggleTick(pathwayId, b.getAttribute('data-tick'));
		paint();
	});

	root.querySelectorAll('[data-select]').forEach(function (sel) {
		sel.addEventListener('change', function () {
			var level = root.querySelector('[data-select="level"]').value;
			var board = root.querySelector('[data-select="board"]').value;
			if (window.MWMProgress) { window.MWMProgress.setPrefs({ level: level, board: board }); }
			window.location.href = base + level + '/' + board + '/';
		});
	});

	if (window.MWMProgress) { paint(); } else { document.addEventListener('mwm:ready', paint); }
	document.addEventListener('mwm:progress', paint);
})();
