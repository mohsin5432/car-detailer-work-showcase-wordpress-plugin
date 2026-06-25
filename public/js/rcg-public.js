/**
 * Car Detailers Showcase Gallery — Public JavaScript
 *
 * Lightweight, zero-dependency vanilla JS for all 5 gallery layouts.
 * Handles: filtering, slider navigation, lightbox, card flip, scroll-reveal.
 *
 * @package Car_Detailers_Showcase
 */

(function () {
	'use strict';

	/**
	 * Run when DOM is ready.
	 */
	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	ready(function () {
		initScrollReveal();
		initFilterBar();
		initSlider();
		initLightbox();
		initCardFlip();
	});

	/* ============================================
	   Scroll Reveal (IntersectionObserver)
	   ============================================ */

	function initScrollReveal() {
		var items = document.querySelectorAll('.rcg-reveal');
		if (!items.length) return;

		// Check reduced-motion preference.
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			items.forEach(function (el) {
				el.classList.add('rcg-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('rcg-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
		);

		items.forEach(function (el, index) {
			el.style.transitionDelay = (index % 6) * 0.08 + 's';
			observer.observe(el);
		});
	}

	/* ============================================
	   Filter Bar
	   ============================================ */

	function initFilterBar() {
		var filterBars = document.querySelectorAll('.rcg-filter-bar');
		filterBars.forEach(function (bar) {
			var pills = bar.querySelectorAll('.rcg-filter-pill');
			var wrapper = bar.closest('.rcg-gallery-wrapper');
			if (!wrapper) return;

			var items = wrapper.querySelectorAll('[data-service]');

			pills.forEach(function (pill) {
				pill.addEventListener('click', function () {
					var filterVal = this.getAttribute('data-filter');

					// Update active pill.
					pills.forEach(function (p) { p.classList.remove('rcg-filter-active'); });
					this.classList.add('rcg-filter-active');

					// Filter items.
					items.forEach(function (item) {
						if (filterVal === 'all' || item.getAttribute('data-service') === filterVal) {
							item.classList.remove('rcg-hidden');
						} else {
							item.classList.add('rcg-hidden');
						}
					});
				});
			});
		});
	}

	/* ============================================
	   Slider / Carousel
	   ============================================ */

	function initSlider() {
		var sliders = document.querySelectorAll('.rcg-slider-wrapper');
		sliders.forEach(function (slider) {
			var track     = slider.querySelector('.rcg-slider-track');
			var slides    = slider.querySelectorAll('.rcg-slider-slide');
			var prevBtn   = slider.querySelector('.rcg-slider-prev');
			var nextBtn   = slider.querySelector('.rcg-slider-next');
			var dots      = slider.querySelectorAll('.rcg-slider-dot');
			var autoplay  = slider.getAttribute('data-autoplay') === 'true';
			var current   = 0;
			var total     = slides.length;
			var interval  = null;

			if (total < 2) {
				if (prevBtn) prevBtn.style.display = 'none';
				if (nextBtn) nextBtn.style.display = 'none';
				return;
			}

			function goTo(index) {
				if (index < 0) index = total - 1;
				if (index >= total) index = 0;
				current = index;

				track.scrollTo({
					left: slides[current].offsetLeft,
					behavior: 'smooth',
				});

				updateDots();
			}

			function updateDots() {
				dots.forEach(function (dot, i) {
					dot.classList.toggle('rcg-dot-active', i === current);
					dot.setAttribute('aria-selected', i === current ? 'true' : 'false');
				});
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					goTo(current - 1);
					resetAutoplay();
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					goTo(current + 1);
					resetAutoplay();
				});
			}

			dots.forEach(function (dot) {
				dot.addEventListener('click', function () {
					goTo(parseInt(this.getAttribute('data-index'), 10));
					resetAutoplay();
				});
			});

			// Auto-play.
			function startAutoplay() {
				if (!autoplay) return;
				interval = setInterval(function () {
					goTo(current + 1);
				}, 5000);
			}

			function resetAutoplay() {
				clearInterval(interval);
				startAutoplay();
			}

			// Pause on hover.
			slider.addEventListener('mouseenter', function () {
				clearInterval(interval);
			});

			slider.addEventListener('mouseleave', function () {
				startAutoplay();
			});

			// Sync dots with scroll (for swipe gestures).
			var scrollTimer;
			track.addEventListener('scroll', function () {
				clearTimeout(scrollTimer);
				scrollTimer = setTimeout(function () {
					var scrollLeft = track.scrollLeft;
					var closestIndex = 0;
					var closestDist = Infinity;

					slides.forEach(function (slide, i) {
						var dist = Math.abs(slide.offsetLeft - scrollLeft);
						if (dist < closestDist) {
							closestDist = dist;
							closestIndex = i;
						}
					});

					if (closestIndex !== current) {
						current = closestIndex;
						updateDots();
					}
				}, 100);
			});

			// Keyboard nav.
			slider.setAttribute('tabindex', '0');
			slider.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowLeft') {
					e.preventDefault();
					goTo(current - 1);
					resetAutoplay();
				} else if (e.key === 'ArrowRight') {
					e.preventDefault();
					goTo(current + 1);
					resetAutoplay();
				}
			});

			startAutoplay();
		});
	}

	/* ============================================
	   Lightbox
	   ============================================ */

	function initLightbox() {
		var dialog    = document.getElementById('rcg-lightbox-dialog');
		var dataEl    = document.getElementById('rcg-lightbox-data');
		if (!dialog || !dataEl) return;

		var carData;
		try {
			carData = JSON.parse(dataEl.textContent);
		} catch (e) {
			return;
		}

		var image     = document.getElementById('rcg-lightbox-image');
		var carName   = document.getElementById('rcg-lightbox-car-name');
		var service   = document.getElementById('rcg-lightbox-service');
		var counter   = document.getElementById('rcg-lightbox-counter');
		var thumbsEl  = document.getElementById('rcg-lightbox-thumbs');
		var closeBtn  = document.getElementById('rcg-lightbox-close');
		var prevBtn   = document.getElementById('rcg-lightbox-prev');
		var nextBtn   = document.getElementById('rcg-lightbox-next');

		var currentCar = 0;
		var currentImg = 0;

		// Open lightbox.
		var triggers = document.querySelectorAll('.rcg-open-lightbox');
		triggers.forEach(function (trigger) {
			function handleTrigger(e) {
				e.stopPropagation();
				var carIdx = trigger.getAttribute('data-car-index');
				var imgIdx = trigger.getAttribute('data-img-index');

				if (carIdx !== null) {
					currentCar = parseInt(carIdx, 10);
				}
				if (imgIdx !== null) {
					currentImg = parseInt(imgIdx, 10);
				} else {
					currentImg = 0;
				}
				openLightbox();
			}

			trigger.addEventListener('click', handleTrigger);
			trigger.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					handleTrigger(e);
				}
			});
		});

		function openLightbox() {
			updateLightboxContent();
			dialog.showModal();
			document.body.style.overflow = 'hidden';
		}

		function closeLightbox() {
			dialog.close();
			document.body.style.overflow = '';
		}

		function updateLightboxContent() {
			var car = carData[currentCar];
			if (!car) return;

			var img = car.images[currentImg];
			image.src = img.full;
			image.alt = img.alt;
			carName.textContent = car.name;

			if (car.work_done) {
				service.textContent = car.work_done;
				service.style.display = '';
			} else {
				service.style.display = 'none';
			}

			counter.textContent = (currentImg + 1) + ' / ' + car.images.length;

			// Render thumbnails.
			thumbsEl.innerHTML = '';
			car.images.forEach(function (thumbImg, i) {
				var thumbEl = document.createElement('img');
				thumbEl.src = thumbImg.thumb;
				thumbEl.alt = thumbImg.alt;
				thumbEl.loading = 'lazy';
				thumbEl.decoding = 'async';
				if (i === currentImg) thumbEl.classList.add('rcg-thumb-active');

				thumbEl.addEventListener('click', function () {
					currentImg = i;
					updateLightboxContent();
				});

				thumbsEl.appendChild(thumbEl);
			});

			// Preload adjacent images.
			preloadImage(car.images[currentImg + 1]);
			preloadImage(car.images[currentImg - 1]);
		}

		function preloadImage(imgData) {
			if (!imgData) return;
			var preload = new Image();
			preload.src = imgData.full;
		}

		function nextImage() {
			var car = carData[currentCar];
			if (!car) return;
			currentImg = (currentImg + 1) % car.images.length;
			updateLightboxContent();
		}

		function prevImage() {
			var car = carData[currentCar];
			if (!car) return;
			currentImg = (currentImg - 1 + car.images.length) % car.images.length;
			updateLightboxContent();
		}

		if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
		if (prevBtn) prevBtn.addEventListener('click', prevImage);
		if (nextBtn) nextBtn.addEventListener('click', nextImage);

		// Close on backdrop click.
		dialog.addEventListener('click', function (e) {
			if (e.target === dialog) closeLightbox();
		});

		// Keyboard navigation.
		dialog.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				closeLightbox();
			} else if (e.key === 'ArrowRight') {
				e.preventDefault();
				nextImage();
			} else if (e.key === 'ArrowLeft') {
				e.preventDefault();
				prevImage();
			}
		});
	}

	/* ============================================
	   Card Flip (mobile tap toggle)
	   ============================================ */

	function initCardFlip() {
		var cards = document.querySelectorAll('.rcg-cardflip-item');
		if (!cards.length) return;

		// On mobile, use tap to flip instead of hover.
		var isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

		if (isTouchDevice) {
			cards.forEach(function (card) {
				card.addEventListener('click', function (e) {
					// Don't flip if clicking a link or lightbox trigger inside.
					if (e.target.closest('a') || e.target.closest('.rcg-open-lightbox')) return;

					// Close other flipped cards.
					cards.forEach(function (c) {
						if (c !== card) c.classList.remove('rcg-flipped');
					});

					card.classList.toggle('rcg-flipped');
				});
			});
		}
	}

})();
