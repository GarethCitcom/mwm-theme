/* Worksheet page: reveal answers. */
(function () {
	'use strict';
	var root = document.querySelector('[data-worksheet]');
	if (!root) { return; }
	var reveal = root.querySelector('[data-reveal-answers]');
	if (!reveal) { return; }
	reveal.addEventListener('click', function () {
		root.querySelector('[data-answers-hidden]').setAttribute('hidden', '');
		root.querySelector('[data-answers-shown]').removeAttribute('hidden');
		var panel = root.querySelector('[data-answers-panel]');
		if (panel) { panel.removeAttribute('hidden'); panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
	});
})();
