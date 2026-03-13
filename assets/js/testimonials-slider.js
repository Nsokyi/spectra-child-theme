(function () {
	"use strict";

	document.addEventListener("DOMContentLoaded", function () {
		var sliders = document.querySelectorAll(
			".wp-block-spectra-child-testimonials-slider",
		);

		sliders.forEach(function (slider) {
			var track = slider.querySelector(".testimonials-slider__track");
			var cards = slider.querySelectorAll(".testimonials-slider__card");
			var prevBtn = slider.querySelector(".testimonials-slider__arrow--prev");
			var nextBtn = slider.querySelector(".testimonials-slider__arrow--next");

			if (!track || cards.length < 2) return;

			var currentIndex = 0;
			var autoSlide = slider.getAttribute("data-auto-slide") === "true";
			var autoSpeed =
				parseInt(slider.getAttribute("data-auto-speed"), 10) || 5;
			var autoTimer = null;

			function getScrollAmount() {
				var card = cards[currentIndex];
				if (!card) return 0;
				var trackRect = track.getBoundingClientRect();
				var cardRect = card.getBoundingClientRect();
				var trackPaddingLeft = parseFloat(
					window.getComputedStyle(track).paddingLeft,
				);
				var offset =
					cardRect.left - trackRect.left + track.scrollLeft - trackPaddingLeft;
				return offset;
			}

			function slideTo(index) {
				if (index < 0) index = 0;
				if (index >= cards.length) index = cards.length - 1;
				currentIndex = index;

				var card = cards[currentIndex];
				var trackPaddingLeft = parseFloat(
					window.getComputedStyle(track).paddingLeft,
				);
				var offset = card.offsetLeft - trackPaddingLeft;

				track.style.transform = "translateX(-" + offset + "px)";

				updateButtons();
			}

			function updateButtons() {
				if (!prevBtn || !nextBtn) return;
				prevBtn.disabled = currentIndex === 0;

				// Check if last card is fully visible
				var sliderRect = slider.getBoundingClientRect();
				var lastCard = cards[cards.length - 1];
				var lastCardRight =
					lastCard.offsetLeft + lastCard.offsetWidth;
				var trackPaddingLeft = parseFloat(
					window.getComputedStyle(track).paddingLeft,
				);
				var currentOffset = cards[currentIndex].offsetLeft - trackPaddingLeft;
				var visibleRight = currentOffset + sliderRect.width;

				nextBtn.disabled = visibleRight >= lastCardRight;
			}

			function startAutoSlide() {
				if (!autoSlide) return;
				stopAutoSlide();
				autoTimer = setInterval(function () {
					if (currentIndex < cards.length - 1) {
						slideTo(currentIndex + 1);
					} else {
						slideTo(0);
					}
				}, autoSpeed * 1000);
			}

			function stopAutoSlide() {
				if (autoTimer) {
					clearInterval(autoTimer);
					autoTimer = null;
				}
			}

			if (prevBtn) {
				prevBtn.addEventListener("click", function () {
					stopAutoSlide();
					slideTo(currentIndex - 1);
					startAutoSlide();
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener("click", function () {
					stopAutoSlide();
					slideTo(currentIndex + 1);
					startAutoSlide();
				});
			}

			// Pause auto-slide on hover
			slider.addEventListener("mouseenter", stopAutoSlide);
			slider.addEventListener("mouseleave", startAutoSlide);

			// Touch support
			var touchStartX = 0;
			var touchEndX = 0;

			track.addEventListener(
				"touchstart",
				function (e) {
					touchStartX = e.changedTouches[0].screenX;
					stopAutoSlide();
				},
				{ passive: true },
			);

			track.addEventListener(
				"touchend",
				function (e) {
					touchEndX = e.changedTouches[0].screenX;
					var diff = touchStartX - touchEndX;
					if (Math.abs(diff) > 50) {
						if (diff > 0) {
							slideTo(currentIndex + 1);
						} else {
							slideTo(currentIndex - 1);
						}
					}
					startAutoSlide();
				},
				{ passive: true },
			);

			// Recalculate on resize
			var resizeTimer;
			window.addEventListener("resize", function () {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function () {
					slideTo(currentIndex);
				}, 150);
			});

			// Initial state
			updateButtons();
			startAutoSlide();
		});
	});
})();
