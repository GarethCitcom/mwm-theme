/* Revision: ticks per subtopic (persisted per level + topic pathway), progress bars, quiz average, level/board switching. */
(function () {
	'use strict';
	var root = document.querySelector('[data-pathway-page]');
	var island = document.getElementById('mwm-pathway-data');
	if (!root || !island) { return; }
	var D;
	try { D = JSON.parse(island.textContent); } catch (e) { return; }
	var ticks = root.querySelectorAll('[data-tick]');
	var doneMarks = root.querySelectorAll('[data-lesson-done]');

	function countTicks(P, pw) {
		var have = P.ticksFor ? P.ticksFor(pw.id) : [];
		return pw.keys.filter(function (k) { return have.indexOf(k) !== -1; }).length;
	}
	function paint() {
		var P = window.MWMProgress;
		if (!P) { return; }
		var count = 0, total = 0;
		doneMarks.forEach(function (m) { m.hidden = !P.has('completed', m.getAttribute('data-lesson-done')); });
		if (D.current) {
			ticks.forEach(function (b) {
				var on = P.hasTick(D.current.id, b.getAttribute('data-tick'));
				b.classList.toggle('is-on', on);
				b.setAttribute('aria-pressed', on ? 'true' : 'false');
				b.setAttribute('aria-label', (on ? 'Unmark ' : 'Mark ') + b.getAttribute('data-topic') + ' as done');
			});
			count = countTicks(P, D.current);
			total = D.current.keys.length;
		} else {
			D.pathways.forEach(function (pw) {
				var n = countTicks(P, pw), t = pw.keys.length;
				count += n; total += t;
				var card = root.querySelector('[data-pathway-card="' + pw.id + '"]');
				if (card) {
					var bar = card.querySelector('[data-card-bar]'), lbl = card.querySelector('[data-card-count]');
					if (bar) { bar.style.width = (t ? Math.round(n / t * 100) : 0) + '%'; }
					if (lbl) { lbl.textContent = t ? n + ' of ' + t + ' ticked' : ''; }
				}
			});
		}
		var pct = total ? Math.round(count / total * 100) : 0;
		root.querySelector('[data-progress-label]').textContent = count + ' of ' + total + ' ticked';
		root.querySelector('[data-progress-pct]').textContent = pct + '%';
		root.querySelector('[data-progress-bar]').style.width = pct + '%';
		var avg = P.quizAverage();
		root.querySelector('[data-quiz-average]').textContent = avg === null ? '—' : avg + '%';
	}

	root.addEventListener('click', function (e) {
		var b = e.target.closest('[data-tick]');
		if (!b || !window.MWMProgress || !D.current) { return; }
		window.MWMProgress.toggleTick(D.current.id, b.getAttribute('data-tick'));
		paint();
	});

	// Level tabs are plain links; remember the choice so other pages follow it.
	root.querySelectorAll('[data-seg="level"] a').forEach(function (a) {
		a.addEventListener('click', function () {
			if (window.MWMProgress) { window.MWMProgress.setPrefs({ level: a.getAttribute('data-value') }); }
		});
	});
	// The exam board only matters for the exam panel and papers links: save it and reload with it.
	var boardSel = root.querySelector('[data-select="board"]');
	if (boardSel) {
		boardSel.addEventListener('change', function () {
			if (window.MWMProgress) { window.MWMProgress.setPrefs({ board: boardSel.value }); }
			var u = new URL(window.location.href);
			u.searchParams.set('board', boardSel.value);
			window.location.href = u.toString();
		});
	}

	if (window.MWMProgress) { paint(); } else { document.addEventListener('mwm:ready', paint); }
	document.addEventListener('mwm:progress', paint);
})();
