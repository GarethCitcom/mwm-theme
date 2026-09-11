/* Lesson page: load the player on demand, reveal answers, Save / Mark complete. */
(function () {
	'use strict';
	var root = document.querySelector('[data-lesson]');
	if (!root) { return; }
	var lessonId = Number(root.getAttribute('data-lesson'));

	var player = root.querySelector('[data-player]');
	var play = root.querySelector('[data-play]');
	if (player && play) {
		play.addEventListener('click', function () {
			var src = player.getAttribute('data-embed');
			if (!src) { return; }
			var iframe = document.createElement('iframe');
			iframe.src = src;
			iframe.title = play.getAttribute('aria-label') || 'Lesson video';
			iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
			iframe.setAttribute('allowfullscreen', '');
			iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
			player.innerHTML = '';
			player.appendChild(iframe);
			iframe.focus();
		});
	}

	var reveal = root.querySelector('[data-reveal-answers]');
	if (reveal) {
		reveal.addEventListener('click', function () {
			root.querySelector('[data-answers-hidden]').setAttribute('hidden', '');
			root.querySelector('[data-answers-shown]').removeAttribute('hidden');
			var panel = root.querySelector('[data-answers-panel]');
			if (panel) { panel.removeAttribute('hidden'); }
		});
	}

	function paint() {
		if (!window.MWMProgress) { return; }
		root.querySelectorAll('[data-ctl]').forEach(function (b) {
			var on = window.MWMProgress.has(b.getAttribute('data-ctl'), lessonId);
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
			b.textContent = b.getAttribute(on ? 'data-on' : 'data-off');
		});
	}
	root.addEventListener('click', function (e) {
		var b = e.target.closest('[data-ctl]');
		if (!b || !window.MWMProgress) { return; }
		window.MWMProgress.toggleList(b.getAttribute('data-ctl'), lessonId);
		paint();
	});
	if (window.MWMProgress) { paint(); } else { document.addEventListener('mwm:ready', paint); }
})();
