(function () {
	'use strict';

	function initLightbox(root) {
		var box = root || document.getElementById('jn-lightbox');
		var grid = document.getElementById('jn-album-grid');
		if (!box || !grid) {
			return;
		}

		var items = Array.prototype.slice.call(grid.querySelectorAll('.album-grid__item'));
		if (!items.length) {
			return;
		}

		var img = box.querySelector('.jn-lightbox__img');
		var counter = box.querySelector('.jn-lightbox__counter');
		var btnPrev = box.querySelector('.jn-lightbox__nav--prev');
		var btnNext = box.querySelector('.jn-lightbox__nav--next');
		var btnClose = box.querySelector('.jn-lightbox__close');
		var urls = items.map(function (el) { return el.getAttribute('data-full'); });
		var current = 0;
		var lastFocused = null;
		var isOpen = false;

		function render() {
			img.src = urls[current];
			counter.textContent = (current + 1) + ' / ' + urls.length;
			btnPrev.disabled = current === 0;
			btnNext.disabled = current === urls.length - 1;
		}

		function trapFocus(event) {
			if (event.key !== 'Tab') {
				return;
			}
			var focusable = box.querySelectorAll('button');
			var first = focusable[0];
			var last = focusable[focusable.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				last.focus();
				event.preventDefault();
			} else if (!event.shiftKey && document.activeElement === last) {
				first.focus();
				event.preventDefault();
			}
		}

		function open(index) {
			current = index;
			render();
			lastFocused = document.activeElement;
			isOpen = true;
			box.classList.add('is-open');
			box.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
			btnClose.focus();
		}

		function close() {
			isOpen = false;
			box.classList.remove('is-open');
			box.setAttribute('aria-hidden', 'true');
			document.body.style.overflow = '';
			if (lastFocused && lastFocused.focus) {
				lastFocused.focus();
			}
		}

		function prev() {
			if (current > 0) {
				current--;
				render();
			}
		}

		function next() {
			if (current < urls.length - 1) {
				current++;
				render();
			}
		}

		items.forEach(function (el, i) {
			el.addEventListener('click', function (event) {
				event.preventDefault();
				open(i);
			});
		});

		btnPrev.addEventListener('click', function (event) {
			event.stopPropagation();
			prev();
		});

		btnNext.addEventListener('click', function (event) {
			event.stopPropagation();
			next();
		});

		btnClose.addEventListener('click', close);

		box.addEventListener('click', function (event) {
			if (event.target === box || event.target.classList.contains('jn-lightbox__figure')) {
				close();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (!isOpen) {
				return;
			}
			if (event.key === 'Escape') {
				close();
			} else if (event.key === 'ArrowLeft') {
				prev();
			} else if (event.key === 'ArrowRight') {
				next();
			} else if (event.key === 'Tab') {
				trapFocus(event);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { initLightbox(); });
	} else {
		initLightbox();
	}
})();
