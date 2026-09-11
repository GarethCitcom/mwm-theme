/* Quiz: choice, choice-image and put-in-order questions; explanation after each; score screen. */
(function () {
	'use strict';
	var root = document.querySelector('[data-quiz]');
	var island = document.getElementById('mwm-quiz-data');
	if (!root || !island) { return; }
	var D;
	try { D = JSON.parse(island.textContent); } catch (e) { return; }
	var app = root.querySelector('[data-quiz-app]');
	var Q = D.questions;
	var N = Q.length;
	var S = { qi: 0, picked: null, seq: [], score: 0, done: false };
	function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

	function render() {
		if (S.done) { return renderResult(); }
		var q = Q[S.qi];
		var isOrder = q.type === 'order';
		var orderComplete = isOrder && S.seq.length === q.options.length;
		var answered = isOrder ? orderComplete : S.picked !== null;
		var html = '<div class="mwm-quiz__head"><h1 class="mwm-quiz__h1">Question ' + (S.qi + 1) + ' of ' + N + '</h1><span class="mwm-meta">' + S.score + ' correct so far</span></div>' +
			'<div class="mwm-bar mwm-bar--12"><div class="mwm-bar__fill" style="width:' + ((S.qi + (answered ? 1 : 0)) / N * 100) + '%"></div></div>' +
			'<div class="mwm-quiz__card"><p class="mwm-quiz__q">' + esc(q.q) + '</p>';
		if (q.type === 'choice-image') {
			html += '<figure class="mwm-quiz__figure">' + (q.imageUrl ? '<img src="' + esc(q.imageUrl) + '" alt="' + esc(q.imageAlt || q.image || '') + '" width="240">' : '<p class="mwm-quiz__figure-desc">' + esc(q.image) + '</p>') + (q.image && q.imageUrl ? '<figcaption>' + esc(q.image) + '</figcaption>' : '') + '</figure>';
		}
		var correctThis;
		if (!isOrder) {
			html += '<div class="mwm-quiz__options">' + q.options.map(function (text, i) {
				var isCorrect = i === q.correct, isPicked = i === S.picked, cls = '', mark = '';
				if (answered && isCorrect) { cls = ' is-correct'; mark = isPicked ? 'Correct' : 'Answer'; }
				else if (answered && isPicked) { cls = ' is-wrong'; mark = 'Your answer'; }
				else if (answered) { cls = ' is-dim'; }
				var img = q.optionImages && q.optionImages[i] ? '<img class="mwm-opt__img" src="' + esc(q.optionImages[i]) + '" alt="">' : '';
				return '<button type="button" class="mwm-opt' + cls + '" data-pick="' + i + '"' + (answered ? ' disabled' : '') + '><span class="mwm-opt__letter">' + 'ABCDE'[i] + '</span>' + img + '<span class="mwm-opt__text">' + esc(text) + '</span><span class="mwm-opt__mark">' + mark + '</span></button>';
			}).join('') + '</div>';
			correctThis = S.picked === q.correct;
		} else {
			html += '<p class="mwm-quiz__order-hint">Tap the steps in the order you’d do them.</p><div class="mwm-quiz__options mwm-quiz__options--order">' + q.options.map(function (text, i) {
				var pos = S.seq.indexOf(i), placed = pos >= 0, rightSpot = placed && q.correctOrder[pos] === i;
				var cls = placed ? ' is-placed' : '', badgeCls = placed ? ' is-on' : '', badge = placed ? String(pos + 1) : '·', mark = '';
				if (orderComplete && placed) {
					if (rightSpot) { mark = '✓'; } else { mark = 'Should be ' + (q.correctOrder.indexOf(i) + 1); cls = ' is-wrong'; badgeCls = ' is-bad'; }
				}
				return '<button type="button" class="mwm-opt' + cls + '" data-order="' + i + '"' + (answered || placed ? ' disabled' : '') + '><span class="mwm-opt__badge' + badgeCls + '">' + badge + '</span><span class="mwm-opt__text">' + esc(text) + '</span><span class="mwm-opt__mark">' + mark + '</span></button>';
			}).join('') + '</div>';
			if (S.seq.length && !orderComplete) { html += '<button type="button" class="mwm-quiz__reset" data-reset>Start the order again</button>'; }
			correctThis = orderComplete && S.seq.every(function (idx, pos) { return q.correctOrder[pos] === idx; });
		}
		if (answered) {
			html += '<div class="mwm-quiz__after"><p><strong class="' + (correctThis ? 'mwm-quiz__verdict--ok' : 'mwm-quiz__verdict--no') + '">' + (correctThis ? 'Correct.' : 'Not quite.') + '</strong> ' + esc(q.explain) + '</p>' +
				'<button type="button" class="mwm-btn mwm-btn--primary mwm-quiz__next" data-next>' + (S.qi >= N - 1 ? 'See your score' : 'Next question') + '</button></div>';
		}
		html += '</div><p class="mwm-quiz__hint">Take your time — there’s no clock. Each answer shows the working straight away.</p>';
		app.innerHTML = html;
		if (answered) { var nx = app.querySelector('[data-next]'); if (nx) { nx.focus(); } }
	}

	function renderResult() {
		var msg = S.score >= Math.ceil(N * 0.8) ? 'Strong work — you’re ready for exam questions on this topic.' : S.score >= Math.ceil(N * 0.4) ? 'Good start. Rewatch the tricky steps and try the worksheet before going again.' : 'This one takes practice. Watch the lesson again, then come back — the steps will click.';
		app.innerHTML = '<div class="mwm-quiz__result"><div class="mwm-quiz__result-label">Your score</div><div class="mwm-quiz__score">' + S.score + '<span> / ' + N + '</span></div><p class="mwm-quiz__msg">' + msg + '</p>' +
			(D.signedIn ? '<p class="mwm-quiz__saved">✓ Saved to My Learning</p>' : '<p class="mwm-quiz__signin"><a href="' + esc(D.loginUrl) + '">Sign in</a> to keep your quiz scores.</p>') +
			'<div class="mwm-quiz__actions"><button type="button" class="mwm-btn mwm-btn--secondary" data-retry>Try again</button><a href="' + esc(D.lessonUrl) + '" class="mwm-btn mwm-btn--primary">Back to the lesson</a></div>' +
			'<a href="' + esc(D.pathwayUrl) + '" class="mwm-arrow mwm-quiz__continue">Continue your pathway<span aria-hidden="true">→</span></a></div>';
		if (window.MWMProgress) { window.MWMProgress.setQuiz(D.id, S.score, N); }
	}

	app.addEventListener('click', function (e) {
		var el, q = Q[Math.min(S.qi, N - 1)];
		if ((el = e.target.closest('[data-pick]'))) {
			if (S.picked !== null) { return; }
			var i = Number(el.getAttribute('data-pick'));
			S.picked = i;
			if (i === q.correct) { S.score++; }
		} else if ((el = e.target.closest('[data-order]'))) {
			var j = Number(el.getAttribute('data-order'));
			if (S.seq.indexOf(j) !== -1 || S.seq.length >= q.options.length) { return; }
			S.seq.push(j);
			if (S.seq.length === q.options.length && S.seq.every(function (idx, pos) { return q.correctOrder[pos] === idx; })) { S.score++; }
		} else if ((el = e.target.closest('[data-reset]'))) {
			S.seq = [];
		} else if ((el = e.target.closest('[data-next]'))) {
			if (S.qi >= N - 1) { S.done = true; } else { S.qi++; S.picked = null; S.seq = []; }
		} else if ((el = e.target.closest('[data-retry]'))) {
			S = { qi: 0, picked: null, seq: [], score: 0, done: false };
		} else {
			return;
		}
		render();
	});
	render();
})();
