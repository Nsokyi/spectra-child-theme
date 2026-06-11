(function () {
	"use strict";

	document.addEventListener("DOMContentLoaded", function () {
		var testimonials = document.querySelectorAll(".project-testimonial");

		testimonials.forEach(function (testimonial) {
			var quote  = testimonial.querySelector(".project-testimonial__quote");
			var fade   = testimonial.querySelector(".project-testimonial__fade");
			var toggle = testimonial.querySelector(".project-testimonial__toggle");

			if (!quote || !fade || !toggle) return;

			function checkOverflow() {
				var isOverflowing = quote.scrollHeight > quote.clientHeight + 2;

				if (!isOverflowing) {
					fade.classList.add("is-hidden");
					toggle.classList.add("is-hidden");
				} else {
					fade.classList.remove("is-hidden");
					toggle.classList.remove("is-hidden");
				}
			}

			toggle.addEventListener("click", function () {
				var expanded = quote.classList.contains("is-expanded");

				if (expanded) {
					quote.classList.remove("is-expanded");
					fade.classList.remove("is-hidden");
					toggle.classList.remove("is-expanded");
					toggle.setAttribute("aria-expanded", "false");
					toggle.querySelector("span").textContent = "Read more";
				} else {
					quote.style.setProperty(
						"--pt-expanded-height",
						quote.scrollHeight + "px"
					);
					quote.classList.add("is-expanded");
					fade.classList.add("is-hidden");
					toggle.classList.add("is-expanded");
					toggle.setAttribute("aria-expanded", "true");
					toggle.querySelector("span").textContent = "Read less";
				}
			});

			checkOverflow();

			var resizeTimer;
			window.addEventListener("resize", function () {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function () {
					if (!quote.classList.contains("is-expanded")) {
						checkOverflow();
					} else {
						quote.style.setProperty(
							"--pt-expanded-height",
							quote.scrollHeight + "px"
						);
					}
				}, 150);
			});
		});
	});
})();
