/**
 * Project Stills Gallery — Click-to-Expand
 *
 * Handles click events on production stills to expand/collapse them.
 */
(function () {
	"use strict";

	document.addEventListener("click", function (e) {
		var item = e.target.closest(".project-stills__item");
		if (!item) return;

		var grid = item.closest(".project-stills__grid");
		if (!grid) return;

		var isExpanded = item.classList.contains("is-expanded");

		function collapse(el) {
			el.classList.add("is-collapsing");
			el.addEventListener(
				"animationend",
				function handler() {
					el.classList.remove("is-expanded", "is-collapsing");
					var img = el.querySelector("img");
					if (el.dataset.thumb) img.src = el.dataset.thumb;
					el.removeEventListener("animationend", handler);
				},
				{ once: true }
			);
		}

		// Collapse all other expanded items in this grid
		grid.querySelectorAll(".project-stills__item.is-expanded").forEach(function (el) {
			collapse(el);
		});

		// If this item wasn't expanded, expand it
		if (!isExpanded) {
			item.classList.add("is-expanded");
			var img = item.querySelector("img");
			if (item.dataset.full) img.src = item.dataset.full;
			item.scrollIntoView({ behavior: "smooth", block: "nearest" });
		}
	});
})();
