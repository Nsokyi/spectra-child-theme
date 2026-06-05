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
			var expandedCount = 0;

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
				if (!autoSlide || expandedCount > 0) return;
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

			// ── Read More / Read Less ──
			function initReadMore() {
				cards.forEach(function (card) {
					var quote = card.querySelector(".testimonials-slider__quote");
					var fade = card.querySelector(".testimonials-slider__fade");
					var toggle = card.querySelector(".testimonials-slider__toggle");

					if (!quote || !toggle || !fade) return;

					var isOverflowing = quote.scrollHeight > quote.clientHeight;

					if (!isOverflowing) {
						fade.classList.add("is-hidden");
						toggle.classList.add("is-hidden");
					}

					toggle.addEventListener("click", function () {
						var expanded = quote.classList.contains("is-expanded");

						if (expanded) {
							quote.classList.remove("is-expanded");
							fade.classList.remove("is-hidden");
							toggle.classList.remove("is-expanded");
							toggle.setAttribute("aria-expanded", "false");
							toggle.querySelector("span").textContent = "Read more";
							expandedCount = Math.max(0, expandedCount - 1);
							startAutoSlide();
						} else {
							quote.style.setProperty(
								"--ts-expanded-height",
								quote.scrollHeight + "px",
							);
							quote.classList.add("is-expanded");
							fade.classList.add("is-hidden");
							toggle.classList.add("is-expanded");
							toggle.setAttribute("aria-expanded", "true");
							toggle.querySelector("span").textContent = "Read less";
							expandedCount++;
							stopAutoSlide();
						}
					});
				});
			}

			// Recalculate on resize
			var resizeTimer;
			window.addEventListener("resize", function () {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function () {
					slideTo(currentIndex);
					recalcOverflow();
				}, 150);
			});

			function recalcOverflow() {
				cards.forEach(function (card) {
					var quote = card.querySelector(".testimonials-slider__quote");
					var fade = card.querySelector(".testimonials-slider__fade");
					var toggle = card.querySelector(".testimonials-slider__toggle");

					if (!quote || !toggle || !fade) return;

					// Update expanded height if currently expanded
					if (quote.classList.contains("is-expanded")) {
						quote.style.setProperty(
							"--ts-expanded-height",
							quote.scrollHeight + "px",
						);
						return;
					}

					var isOverflowing = quote.scrollHeight > quote.clientHeight;

					if (isOverflowing) {
						fade.classList.remove("is-hidden");
						toggle.classList.remove("is-hidden");
					} else {
						fade.classList.add("is-hidden");
						toggle.classList.add("is-hidden");
					}
				});
			}

			// Initial state
			initReadMore();
			updateButtons();
			startAutoSlide();
		});
	});
})();
