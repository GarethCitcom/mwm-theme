/* Past papers: filter by tier, series and paper; group by series. */
(function () {
	'use strict';
	var root = document.querySelector('[data-past-papers]');
	var island = document.getElementById('mwm-past-papers-data');
	if (!root || !island) { return; }
	var D;
	try { D = JSON.parse(island.textContent); } catch (e) { return; }
	var S = D.state;
	function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
	function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

	function row(p) {
		var ws = p.worksheets && p.worksheets.length
			? '<div class="mwm-pp-row__practise"><span class="mwm-pp-row__practise-label">Practise what came up</span>' + p.worksheets.map(function (w) { return '<a href="' + esc(w.url) + '" class="mwm-pp-row__ws">' + esc(w.name) + '</a>'; }).join('') + '</div>'
			: '';
		var files = '';
		if (p.qp) { files += '<a href="' + esc(p.qp.url) + '" class="mwm-filebtn" target="_blank" rel="noopener">' + D.icon + 'Question paper<span class="mwm-filebtn__size">' + esc(p.qp.label) + '</span></a>'; }
		if (p.ms) { files += '<a href="' + esc(p.ms.url) + '" class="mwm-filebtn mwm-filebtn--ms" target="_blank" rel="noopener">' + D.icon + 'Mark scheme<span class="mwm-filebtn__size">' + esc(p.ms.label) + '</span></a>'; }
		else { files += '<span class="mwm-pp-row__soon">Mark scheme coming soon</span>'; }
		return '<div class="mwm-pp-row"><div class="mwm-pp-row__main"><div class="mwm-pp-row__title">' + esc(p.title) + '</div><div class="mwm-pp-row__meta">' + esc(p.meta) + '</div>' + ws + '</div><div class="mwm-pp-row__files">' + files + '</div></div>';
	}

	function render() {
		root.querySelectorAll('[data-filter]').forEach(function (b) {
			var on = String(S[b.getAttribute('data-filter')]) === b.getAttribute('data-value');
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
		var list = D.papers.filter(function (p) {
			return p.tier === S.tier && (S.series === 'All' || p.series === S.series) && (S.paper === 'All' || String(p.paper) === String(S.paper));
		});
		var html = D.series.filter(function (s) { return list.some(function (p) { return p.series === s; }); }).map(function (s) {
			return '<div class="mwm-pp-group"><h2 class="mwm-h2">' + esc(s) + ' · ' + esc(cap(S.tier)) + '</h2><div class="mwm-list mwm-list--pp">' + list.filter(function (p) { return p.series === s; }).map(row).join('') + '</div></div>';
		}).join('');
		if (!list.length) {
			html = '<div class="mwm-empty"><p class="mwm-empty__title">No papers match these filters yet</p><p class="mwm-empty__body">Try a different series or paper — new ' + esc(D.board) + ' papers are added as they are released.</p></div>';
		}
		root.querySelector('[data-groups]').innerHTML = html;
		root.querySelector('[data-count]').textContent = list.length === 1 ? '1 paper' : list.length + ' papers';
		var q = ['tier=' + S.tier];
		if (S.series !== 'All') { q.push('series=' + encodeURIComponent(S.series)); }
		if (S.paper !== 'All') { q.push('paper=' + S.paper); }
		try { history.replaceState(null, '', location.pathname + '?' + q.join('&')); } catch (e) {}
	}
	root.addEventListener('click', function (e) {
		var b = e.target.closest('[data-filter]');
		if (!b) { return; }
		S[b.getAttribute('data-filter')] = b.getAttribute('data-value');
		render();
	});
	render();
})();
