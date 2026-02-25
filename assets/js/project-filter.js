/**
 * Project Filter — Vanilla JS
 *
 * Handles multi-select taxonomy filtering via REST API,
 * URL state management, and smooth CSS transitions.
 */
(function () {
	"use strict";

	var wrap = document.querySelector(".project-filters-wrap");
	if (!wrap) return;

	var grid = wrap.querySelector(".project-grid");
	var loading = wrap.querySelector(".project-grid__loading");
	if (!grid || !loading) return;

	var loadMore = wrap.querySelector(".project-grid__load-more");
	var featured = wrap.dataset.featured || "";
	var perPage = parseInt(wrap.dataset.perPage, 10) || 12;
	var debounceId = null;

	var secondaryFilter = wrap.querySelector(".project-filters--secondary");

	var state = {
		service: [],
		industry: [],
		paged: 1,
	};

	// Initialise state from URL params.
	function initFromURL() {
		var params = new URLSearchParams(window.location.search);
		var s = params.get("service");
		var i = params.get("industry");
		if (s) state.service = s.split(",");
		if (i) state.industry = i.split(",");

		// Also check for pre-selected term from taxonomy archive context.
		if (
			projectFilterData.currentTerm &&
			projectFilterData.currentTerm.taxonomy === "industry" &&
			!state.industry.length
		) {
			state.industry = [projectFilterData.currentTerm.slug];
		}
		if (
			projectFilterData.currentTerm &&
			projectFilterData.currentTerm.taxonomy === "service" &&
			!state.service.length
		) {
			state.service = [projectFilterData.currentTerm.slug];
		}

		syncButtons();
		syncSecondaryVisibility();
	}

	function syncButtons() {
		wrap.querySelectorAll(".project-filters").forEach(function (group) {
			var taxonomy = group.dataset.taxonomy;
			var active = state[taxonomy] || [];

			group.querySelectorAll(".project-filters__btn").forEach(function (btn) {
				var slug = btn.dataset.slug;
				if (slug === "") {
					btn.classList.toggle("is-active", active.length === 0);
				} else {
					btn.classList.toggle("is-active", active.indexOf(slug) !== -1);
				}
			});
		});
	}

	function syncSecondaryVisibility() {
		if (!secondaryFilter) return;
		if (state.industry.length > 0) {
			secondaryFilter.classList.add("is-visible");
		} else {
			secondaryFilter.classList.remove("is-visible");
		}
	}

	// Bind primary filter (industry) clicks — single-select.
	var primaryFilter = wrap.querySelector(".project-filters--primary");
	if (primaryFilter) {
		primaryFilter.addEventListener("click", function (e) {
			var btn = e.target.closest(".project-filters__btn");
			if (!btn) return;

			var slug = btn.dataset.slug;

			if (slug === "") {
				// "All Projects" — clear industry and service selections.
				state.industry = [];
				state.service = [];
			} else {
				// Single-select: clicking the same industry deselects it.
				if (state.industry.length === 1 && state.industry[0] === slug) {
					state.industry = [];
					state.service = [];
				} else {
					state.industry = [slug];
					state.service = [];
				}
			}

			state.paged = 1;
			syncButtons();
			syncSecondaryVisibility();
			updateURL();
			debounceFetch();
		});
	}

	// Bind secondary filter (service) clicks — single-select.
	if (secondaryFilter) {
		secondaryFilter.addEventListener("click", function (e) {
			var btn = e.target.closest(".project-filters__btn");
			if (!btn) return;

			var slug = btn.dataset.slug;

			if (slug === "") {
				// "All Services" — clear service selection.
				state.service = [];
			} else {
				// Single-select: clicking the same service deselects it.
				if (state.service.length === 1 && state.service[0] === slug) {
					state.service = [];
				} else {
					state.service = [slug];
				}
			}

			state.paged = 1;
			syncButtons();
			updateURL();
			debounceFetch();
		});
	}

	// Load more button.
	if (loadMore) {
		loadMore.addEventListener("click", function () {
			state.paged++;
			fetchProjects(true);
		});
	}

	function updateURL() {
		var params = new URLSearchParams();
		if (state.service.length) params.set("service", state.service.join(","));
		if (state.industry.length) params.set("industry", state.industry.join(","));

		var qs = params.toString();
		var url = window.location.pathname + (qs ? "?" + qs : "");
		window.history.replaceState(null, "", url);
	}

	function debounceFetch() {
		clearTimeout(debounceId);
		debounceId = setTimeout(function () {
			fetchProjects(false);
		}, 300);
	}

	function fetchProjects(append) {
		var params = new URLSearchParams();
		if (state.service.length) params.set("service", state.service.join(","));
		if (state.industry.length) params.set("industry", state.industry.join(","));
		if (featured) params.set("featured", featured);
		params.set("paged", state.paged);
		params.set("per_page", perPage);

		var url = projectFilterData.restUrl + "?" + params.toString();

		if (!append) {
			grid.classList.add("is-loading");
			loading.hidden = false;
		}

		fetch(url, {
			headers: { "X-WP-Nonce": projectFilterData.nonce },
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (data) {
				if (!append) {
					grid.innerHTML = "";
				}

				if (data.projects && data.projects.length) {
					data.projects.forEach(function (project, index) {
						var item = buildProjectItem(project);
						item.style.opacity = "0";
						item.style.transform = "translateY(20px)";
						grid.appendChild(item);

						// Stagger fade-in via CSS transitions.
						requestAnimationFrame(function () {
							setTimeout(function () {
								item.style.transition =
									"opacity 0.3s ease, transform 0.3s ease";
								item.style.opacity = "1";
								item.style.transform = "translateY(0)";
							}, index * 60);
						});
					});
				} else if (!append) {
					grid.innerHTML =
						'<div class="project-grid__empty"><p>No projects found.</p></div>';
				}

				// Update load more button.
				if (loadMore) {
					var pagination = wrap.querySelector(".project-grid__pagination");
					if (data.current_page >= data.total_pages) {
						pagination.hidden = true;
					} else {
						pagination.hidden = false;
						loadMore.dataset.page = data.current_page;
						loadMore.dataset.max = data.total_pages;
					}
				}

				grid.classList.remove("is-loading");
				loading.hidden = true;
			})
			.catch(function (err) {
				console.error("Project filter error:", err);
				grid.classList.remove("is-loading");
				loading.hidden = true;
			});
	}

	function buildProjectItem(project) {
		var a = document.createElement("a");
		a.href = project.permalink;
		a.className = "project-item";

		var figure = document.createElement("figure");
		figure.className = "project-item__image";

		if (project.thumbnail) {
			var img = document.createElement("img");
			img.src = project.thumbnail;
			img.alt = project.title;
			img.loading = "lazy";
			img.width = 640;
			img.height = 360;
			figure.appendChild(img);
		} else {
			var placeholder = document.createElement("div");
			placeholder.className = "project-item__placeholder";
			figure.appendChild(placeholder);
		}

		var meta = document.createElement("div");
		meta.className = "project-item__meta";

		var h3 = document.createElement("h3");
		h3.className = "project-item__title";
		h3.textContent = project.title;
		meta.appendChild(h3);

		if (project.client) {
			var client = document.createElement("span");
			client.className = "project-item__client";
			client.textContent = project.client;
			meta.appendChild(client);
		}

		a.appendChild(figure);
		a.appendChild(meta);

		return a;
	}

	// Respect prefers-reduced-motion.
	var reducedMotion = window.matchMedia(
		"(prefers-reduced-motion: reduce)",
	).matches;
	if (reducedMotion) {
		var style = document.createElement("style");
		style.textContent = ".project-item { transition: none !important; }";
		document.head.appendChild(style);
	}

	// Init.
	initFromURL();
})();
