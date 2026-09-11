/* Maths with Melissa — global behaviour: theme toggle, mobile menu, scroll strips, progress store. */
(function () {
	'use strict';

	var root = document.documentElement;

	/* ---- Light / dark ---------------------------------------------------- */
	function setTheme(dark) {
		try { localStorage.setItem('mwm-theme', dark ? 'dark' : 'light'); } catch (e) {}
		if (dark) { root.setAttribute('data-theme', 'dark'); } else { root.removeAttribute('data-theme'); }
	}
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-mwm-theme-toggle]');
		if (!btn) { return; }
		setTheme(root.getAttribute('data-theme') !== 'dark');
	});

	/* ---- Mobile menu ------------------------------------------------------ */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-mwm-menu]');
		if (!btn) { return; }
		var nav = document.getElementById(btn.getAttribute('aria-controls'));
		if (!nav) { return; }
		var open = nav.hasAttribute('hidden');
		if (open) { nav.removeAttribute('hidden'); } else { nav.setAttribute('hidden', ''); }
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
	});
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-mwm-search-toggle]');
		if (!btn) { return; }
		var box = document.getElementById(btn.getAttribute('aria-controls'));
		if (!box) { return; }
		var open = box.hasAttribute('hidden');
		if (open) { box.removeAttribute('hidden'); box.querySelector('input') && box.querySelector('input').focus(); } else { box.setAttribute('hidden', ''); }
		btn.setAttribute('aria-expanded', open ? 'true' : 'false');
	});

	/* ---- Horizontal strips (Quick Maths band) ----------------------------- */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-mwm-scroll]');
		if (!btn) { return; }
		var strip = document.getElementById(btn.getAttribute('aria-controls'));
		if (!strip) { return; }
		var dir = btn.getAttribute('data-mwm-scroll') === 'prev' ? -1 : 1;
		strip.scrollBy({ left: dir * 522, behavior: 'smooth' });
	});

	/* ---- Shorts lightbox (plays in the page instead of leaving for YouTube) --- */
	var lightbox = null;
	function closeIcon() {
		return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><line x1="5" y1="5" x2="15" y2="15"></line><line x1="15" y1="5" x2="5" y2="15"></line></svg>';
	}
	function openShort(id, title, watchUrl) {
		if (!lightbox) {
			lightbox = document.createElement('dialog');
			lightbox.className = 'mwm-lightbox';
			lightbox.setAttribute('aria-label', 'Video');
			lightbox.addEventListener('close', function () { lightbox.querySelector('.mwm-lightbox__frame').innerHTML = ''; });
			lightbox.addEventListener('click', function (e) {
				if (e.target === lightbox || e.target.closest('[data-lightbox-close]')) { lightbox.close(); }
			});
			document.body.appendChild(lightbox);
		}
		var safeTitle = String(title || '').replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; });
		lightbox.innerHTML = '<div class="mwm-lightbox__box" role="document">' +
			'<div class="mwm-lightbox__head"><h2 class="mwm-lightbox__title">' + safeTitle + '</h2>' +
			'<button type="button" class="mwm-iconbtn" aria-label="Close" data-lightbox-close>' + closeIcon() + '</button></div>' +
			'<div class="mwm-lightbox__frame"></div>' +
			'<div class="mwm-lightbox__foot"><span class="mwm-meta">Press Esc or the cross to close.</span>' +
			'<a href="' + watchUrl.replace(/"/g, '&quot;') + '" target="_blank" rel="noopener" class="mwm-arrow">Watch on YouTube<span aria-hidden="true">→</span></a></div></div>';
		var iframe = document.createElement('iframe');
		iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0&playsinline=1';
		iframe.title = title || 'Video';
		iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
		iframe.setAttribute('allowfullscreen', '');
		iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
		lightbox.querySelector('.mwm-lightbox__frame').appendChild(iframe);
		if (typeof lightbox.showModal === 'function') { lightbox.showModal(); } else { lightbox.setAttribute('open', ''); }
		lightbox.querySelector('[data-lightbox-close]').focus();
	}
	document.addEventListener('click', function (e) {
		var el = e.target.closest('[data-short]');
		if (!el) { return; }
		e.preventDefault();
		openShort(el.getAttribute('data-short'), el.getAttribute('data-short-title'), el.getAttribute('href') || '#');
	});

	/* ---- Progress store --------------------------------------------------- */
	var KEY = 'mwm-progress';
	var empty = function () { return { ticks: {}, saved: [], completed: [], quizzes: {}, prefs: {} }; };
	var state = empty();
	var signedIn = !!(window.MWM && window.MWM.signedIn);

	function load() {
		try {
			var raw = localStorage.getItem(KEY);
			if (raw) { state = Object.assign(empty(), JSON.parse(raw)); }
		} catch (e) { state = empty(); }
		var island = document.getElementById('mwm-progress-data');
		if (island) {
			try { merge(JSON.parse(island.textContent || '{}')); } catch (e) {}
		}
	}
	function persist() {
		try { localStorage.setItem(KEY, JSON.stringify(state)); } catch (e) {}
	}
	function union(a, b) {
		var out = a.slice();
		(b || []).forEach(function (v) { if (out.indexOf(v) === -1) { out.push(v); } });
		return out;
	}
	function merge(server) {
		if (!server) { return; }
		state.saved = union(state.saved, server.saved);
		state.completed = union(state.completed, server.completed);
		Object.keys(server.ticks || {}).forEach(function (p) {
			state.ticks[p] = union(state.ticks[p] || [], server.ticks[p]);
		});
		Object.keys(server.quizzes || {}).forEach(function (q) {
			var s = server.quizzes[q], l = state.quizzes[q];
			if (!l || (s.at || '') >= (l.at || '')) { state.quizzes[q] = s; }
		});
		state.prefs = Object.assign({}, server.prefs || {}, state.prefs || {});
		persist();
	}
	function push(patch) {
		if (!signedIn || !window.MWM || !window.MWM.rest) { return; }
		try {
			fetch(window.MWM.rest + 'me/progress', {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.MWM.nonce },
				body: JSON.stringify(patch)
			}).catch(function () {});
		} catch (e) {}
	}
	function emit() {
		document.dispatchEvent(new CustomEvent('mwm:progress', { detail: state }));
	}

	var Progress = {
		get: function () { return state; },
		has: function (list, id) { return (state[list] || []).indexOf(Number(id)) !== -1; },
		toggleList: function (list, id) {
			id = Number(id);
			var i = state[list].indexOf(id), now;
			if (i === -1) { state[list].push(id); now = true; } else { state[list].splice(i, 1); now = false; }
			persist();
			var patch = {}; patch[now ? list : 'remove'] = now ? [id] : {}; if (!now) { patch.remove[list] = [id]; }
			push(patch);
			emit();
			return now;
		},
		hasTick: function (pathway, key) { return (state.ticks[String(pathway)] || []).indexOf(key) !== -1; },
		toggleTick: function (pathway, key) {
			var p = String(pathway);
			state.ticks[p] = state.ticks[p] || [];
			var i = state.ticks[p].indexOf(key), now;
			if (i === -1) { state.ticks[p].push(key); now = true; } else { state.ticks[p].splice(i, 1); now = false; }
			persist();
			var patch = now ? { ticks: {} } : { remove: { ticks: {} } };
			(now ? patch.ticks : patch.remove.ticks)[p] = [key];
			push(patch);
			emit();
			return now;
		},
		setQuiz: function (quizId, score, total) {
			var r = { score: score, total: total, at: new Date().toISOString().slice(0, 10) };
			state.quizzes[String(quizId)] = r;
			persist();
			var patch = { quizzes: {} }; patch.quizzes[String(quizId)] = r;
			push(patch);
			emit();
		},
		setPrefs: function (prefs) {
			state.prefs = Object.assign({}, state.prefs, prefs);
			persist();
			push({ prefs: prefs });
			emit();
		},
		quizAverage: function () {
			var s = 0, t = 0;
			Object.keys(state.quizzes).forEach(function (q) { s += state.quizzes[q].score || 0; t += state.quizzes[q].total || 0; });
			return t ? Math.round(s / t * 100) : null;
		},
		signedIn: signedIn
	};

	load();
	// First sign-in on this device: send anything saved while signed out.
	if (signedIn) {
		try {
			var mark = localStorage.getItem('mwm-progress-synced');
			if (mark !== String(window.MWM.userId || '1')) {
				push({ saved: state.saved, completed: state.completed, ticks: state.ticks, quizzes: state.quizzes });
				localStorage.setItem('mwm-progress-synced', String(window.MWM.userId || '1'));
			}
		} catch (e) {}
	}
	window.MWMProgress = Progress;
	document.dispatchEvent(new CustomEvent('mwm:ready', { detail: state }));
})();
