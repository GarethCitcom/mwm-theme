/* Predicted papers: filter by level; group by exam year. */
(function () {
	'use strict';
	var root = document.querySelector('[data-predicted-papers]');
	var island = document.getElementById('mwm-predicted-papers-data');
	if (!root || !island) { return; }
	var D;
	try { D = JSON.parse(island.textContent); } catch (e) { return; }
	var S = D.state;
	function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }

	function row(p) {
		var ws = p.worksheets && p.worksheets.length
			? '<div class="mwm-pp-row__practise"><span class="mwm-pp-row__practise-label">Practise what comes up</span>' + p.worksheets.map(function (w) { return '<a href="' + esc(w.url) + '" class="mwm-pp-row__ws">' + esc(w.name) + '</a>'; }).join('') + '</div>'
			: '';
		var files = '';
		if (p.qp) { files += '<a href="' + esc(p.qp.url) + '" class="mwm-filebtn" target="_blank" rel="noopener">' + D.icon + 'Question paper<span class="mwm-filebtn__size">' + esc(p.qp.label) + '</span></a>'; }
		if (p.sol) { files += '<a href="' + esc(p.sol.url) + '" class="mwm-filebtn mwm-filebtn--ms" target="_blank" rel="noopener">' + D.icon + 'Worked solutions<span class="mwm-filebtn__size">' + esc(p.sol.label) + '</span></a>'; }
		else { files += '<span class="mwm-pp-row__soon">Worked solutions coming soon</span>'; }
		return '<div class="mwm-pp-row"><div class="mwm-pp-row__main"><div class="mwm-pp-row__title">' + esc(p.title) + '</div><div class="mwm-pp-row__meta">' + esc(p.meta) + '</div>' + ws + '</div><div class="mwm-pp-row__files">' + files + '</div></div>';
	}

	function render() {
		root.querySelectorAll('[data-filter]').forEach(function (b) {
			var on = String(S[b.getAttribute('data-filter')]) === b.getAttribute('data-value');
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		var list = D.papers.filter(function (p) { return p.level === S.level; });
		// Papers arrive newest exam year first, so the first time a group appears decides its order.
		var groups = [];
		list.forEach(function (p) { if (groups.indexOf(p.group) === -1) { groups.push(p.group); } });
		var html = groups.map(function (g) {
			return '<div class="mwm-pp-group"><h2 class="mwm-h2">' + esc(g) + '</h2><div class="mwm-list mwm-list--pp">' + list.filter(function (p) { return p.group === g; }).map(row).join('') + '</div></div>';
		}).join('');
		if (!list.length) {
			html = '<div class="mwm-empty"><p class="mwm-empty__title">No predicted papers for this level yet</p><p class="mwm-empty__body">Try another level — new ' + esc(D.board) + ' predicted papers are added in the run-up to each exam season.</p></div>';
		}
		root.querySelector('[data-groups]').innerHTML = html;
		root.querySelector('[data-count]').textContent = list.length === 1 ? '1 paper' : list.length + ' papers';
		var q = ['board=' + encodeURIComponent(D.boardSlug), 'level=' + encodeURIComponent(S.level)];
		try { history.replaceState(null, '', location.pathname + '?' + q.join('&')); } catch (e) {}
		// Keep the board tabs and the Past/Predicted switch pointing at the level that's showing.
		root.querySelectorAll('[data-seg="board"] a, [data-seg="papers"] a').forEach(function (a) {
			try { var u = new URL(a.href, location.href); if (u.searchParams.has('level')) { u.searchParams.set('level', S.level); } if (u.searchParams.has('tier')) { u.searchParams.set('tier', S.level === 'gcse-foundation' ? 'foundation' : 'higher'); } a.href = u.toString(); } catch (e) {}
		});
	}
	root.addEventListener('click', function (e) {
		var b = e.target.closest('[data-filter]');
		if (!b) { return; }
		S[b.getAttribute('data-filter')] = b.getAttribute('data-value');
		render();
	});
	render();
})();
