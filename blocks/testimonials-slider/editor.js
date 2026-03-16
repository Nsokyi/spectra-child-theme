(function (blocks, element, blockEditor, components, data) {
	var el = element.createElement;
	var useState = element.useState;
	var useEffect = element.useEffect;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var RangeControl = components.RangeControl;
	var SelectControl = components.SelectControl;
	var ToggleControl = components.ToggleControl;

	var styles = {
		wrapper: {
			padding: "32px 24px",
			fontFamily:
				'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
			border: "1px solid #E5E7EB",
			borderRadius: "8px",
			background: "#FAFAFA",
			textAlign: "center",
		},
		icon: {
			fontSize: "40px",
			color: "#9CA3AF",
			marginBottom: "12px",
			display: "block",
		},
		title: {
			margin: "0 0 6px",
			fontSize: "16px",
			fontWeight: "600",
			color: "#1E1E1E",
		},
		description: {
			fontSize: "13px",
			color: "#757575",
			margin: "0 0 16px",
			lineHeight: "1.5",
		},
		countBadge: {
			display: "inline-block",
			padding: "4px 12px",
			borderRadius: "12px",
			fontSize: "13px",
			fontWeight: "500",
		},
		countHas: {
			background: "#E8F5E9",
			color: "#2E7D32",
		},
		countNone: {
			background: "#FFF3E0",
			color: "#E65100",
		},
		hint: {
			fontSize: "12px",
			color: "#9CA3AF",
			margin: "12px 0 0",
			lineHeight: "1.4",
		},
	};

	blocks.registerBlockType("spectra-child/testimonials-slider", {
		attributes: {
			testimonialGroup: {
				type: "string",
				default: "",
			},
			autoSlide: {
				type: "boolean",
				default: false,
			},
			autoSlideSpeed: {
				type: "number",
				default: 5,
			},
		},
		edit: function (props) {
			var blockProps = blockEditor.useBlockProps();
			var testimonialGroup = props.attributes.testimonialGroup || "";
			var autoSlide = props.attributes.autoSlide;
			var autoSlideSpeed = props.attributes.autoSlideSpeed || 5;

			var groupsState = useState([]);
			var groups = groupsState[0];
			var setGroups = groupsState[1];

			var countState = useState(null);
			var count = countState[0];
			var setCount = countState[1];

			useEffect(function () {
				wp.apiFetch({
					path: "/wp/v2/testimonial_group?per_page=100",
				})
					.then(function (terms) {
						setGroups(
							terms.map(function (t) {
								return {
									label: t.name + " (" + t.count + ")",
									value: String(t.id),
								};
							}),
						);
					})
					.catch(function () {
						setGroups([]);
					});
			}, []);

			useEffect(
				function () {
					var path = "/wp/v2/testimonial?per_page=1&status=publish";
					if (testimonialGroup) {
						path += "&testimonial_group=" + testimonialGroup;
					}
					wp.apiFetch({ path: path, parse: false })
						.then(function (response) {
							var total = response.headers.get("X-WP-Total");
							setCount(total ? parseInt(total, 10) : 0);
						})
						.catch(function () {
							setCount(0);
						});
				},
				[testimonialGroup],
			);

			var countText =
				count === null
					? "Loading\u2026"
					: count + " testimonial" + (count !== 1 ? "s" : "") + " published";

			var badgeStyle = Object.assign(
				{},
				styles.countBadge,
				count === null
					? styles.countNone
					: count > 0
						? styles.countHas
						: styles.countNone,
			);

			return el(
				element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: "Testimonial Source", initialOpen: true },
						el(SelectControl, {
							label: "Group",
							value: testimonialGroup,
							options: [{ label: "All Testimonials", value: "" }].concat(
								groups,
							),
							onChange: function (val) {
								props.setAttributes({ testimonialGroup: val });
							},
							help: "Choose a group to display specific testimonials, or show all.",
						}),
					),
					el(
						PanelBody,
						{ title: "Slider Behaviour", initialOpen: true },
						el(ToggleControl, {
							label: "Auto-slide testimonials",
							checked: autoSlide,
							onChange: function (val) {
								props.setAttributes({ autoSlide: val });
							},
							help: autoSlide
								? "Testimonials will cycle automatically. Visitors can still use the arrows."
								: "Visitors use the left/right arrows to navigate between testimonials.",
						}),
						autoSlide
							? el(RangeControl, {
									label: "Seconds between slides",
									value: autoSlideSpeed,
									onChange: function (val) {
										props.setAttributes({ autoSlideSpeed: val });
									},
									min: 2,
									max: 15,
									help: "How long each testimonial is shown before sliding to the next.",
								})
							: null,
					),
					el(
						PanelBody,
						{ title: "Display Info", initialOpen: false },
						el(
							"p",
							{ style: { fontSize: "13px", color: "#555" } },
							"This block automatically displays all published testimonials. Manage them under the Testimonials menu in the admin sidebar.",
						),
						el(
							"p",
							{ style: { fontSize: "13px", color: "#555", marginTop: "8px" } },
							"The slider displays up to 3 testimonials at a time on desktop. Each card shows a quote icon, the client quote, a star rating, and the client's name, role, company, and photo.",
						),
					),
				),
				el(
					"div",
					blockProps,
					el(
						"div",
						{ style: styles.wrapper },
						el("span", { style: styles.icon }, "\u275D"),
						el("h3", { style: styles.title }, "Testimonials Slider"),
						el(
							"p",
							{ style: styles.description },
							testimonialGroup
								? "Displaying testimonials from the selected group."
								: "Displays all published testimonials.",
						),
						el("span", { style: badgeStyle }, countText),
						el(
							"p",
							{ style: styles.hint },
							"To add or edit testimonials, go to Testimonials \u2192 All Testimonials in the admin sidebar.",
						),
					),
				),
			);
		},
	});
})(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.data,
);
