(function () {
	var header    = document.getElementById('site-header');
	var hamburger = document.getElementById('hamburger-btn');
	var drawer    = document.getElementById('mobile-drawer');
	var overlay   = document.getElementById('mobile-overlay');
	var closeBtn  = document.getElementById('drawer-close-btn');

	if (!header || !hamburger || !drawer || !overlay || !closeBtn) return;

	/* --- Scroll frosted bar --- */
	window.addEventListener('scroll', function () {
		header.classList.toggle('is-scrolled', window.scrollY > 8);
	}, { passive: true });
	header.classList.toggle('is-scrolled', window.scrollY > 8);

	/* --- Mobile drawer --- */
	function openMenu() {
		hamburger.setAttribute('aria-expanded', 'true');
		hamburger.setAttribute('aria-label', 'Close menu');
		drawer.setAttribute('aria-hidden', 'false');
		overlay.setAttribute('aria-hidden', 'false');
		document.body.classList.add('nav-open');
	}

	function closeMenu() {
		hamburger.setAttribute('aria-expanded', 'false');
		hamburger.setAttribute('aria-label', 'Open menu');
		drawer.setAttribute('aria-hidden', 'true');
		overlay.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('nav-open');
	}

	hamburger.addEventListener('click', function () {
		hamburger.getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu();
	});
	closeBtn.addEventListener('click', closeMenu);
	overlay.addEventListener('click', closeMenu);
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') closeMenu();
	});
	window.addEventListener('resize', function () {
		if (window.innerWidth >= 1024) closeMenu();
	});

	/* --- Desktop dropdown (click for keyboard/touch) --- */
	document.querySelectorAll('.dropdown-trigger').forEach(function (trigger) {
		trigger.addEventListener('click', function (e) {
			e.stopPropagation();
			var isOpen = this.getAttribute('aria-expanded') === 'true';
			document.querySelectorAll('.dropdown-trigger').forEach(function (t) {
				t.setAttribute('aria-expanded', 'false');
			});
			this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
		});
	});

	document.addEventListener('click', function () {
		document.querySelectorAll('.dropdown-trigger').forEach(function (t) {
			t.setAttribute('aria-expanded', 'false');
		});
	});

	/* --- Mobile sub-menu accordion --- */
	document.querySelectorAll('.mobile-sub-trigger').forEach(function (trigger) {
		trigger.addEventListener('click', function () {
			var sub    = this.nextElementSibling;
			var isOpen = this.getAttribute('aria-expanded') === 'true';
			this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
			sub.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
		});
	});
})();
