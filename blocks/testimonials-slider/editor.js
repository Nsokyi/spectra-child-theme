(function (blocks, element, blockEditor, components) {
	var el = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var MediaUpload = blockEditor.MediaUpload;
	var PanelBody = components.PanelBody;
	var RangeControl = components.RangeControl;
	var TextControl = components.TextControl;
	var TextareaControl = components.TextareaControl;
	var ToggleControl = components.ToggleControl;
	var Button = components.Button;

	/* ── Shared inline styles ── */
	var styles = {
		wrapper: {
			padding: "24px",
			fontFamily:
				'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
		},
		header: {
			display: "flex",
			alignItems: "center",
			gap: "10px",
			marginBottom: "6px",
		},
		headerIcon: {
			fontSize: "20px",
			color: "#1E1E1E",
			lineHeight: "1",
		},
		headerTitle: {
			margin: "0",
			fontSize: "15px",
			fontWeight: "600",
			color: "#1E1E1E",
		},
		headerDesc: {
			fontSize: "13px",
			color: "#757575",
			margin: "0 0 20px",
			lineHeight: "1.5",
		},
		emptyState: {
			textAlign: "center",
			padding: "32px 20px",
			border: "2px dashed #ddd",
			borderRadius: "8px",
			marginBottom: "16px",
			background: "#fafafa",
		},
		emptyIcon: {
			fontSize: "36px",
			marginBottom: "8px",
			display: "block",
			color: "#9CA3AF",
		},
		emptyTitle: {
			margin: "0 0 4px",
			fontSize: "14px",
			fontWeight: "600",
			color: "#374151",
		},
		emptyText: {
			margin: "0",
			fontSize: "13px",
			color: "#9CA3AF",
		},
		card: {
			border: "1px solid #E5E7EB",
			borderRadius: "8px",
			marginBottom: "12px",
			background: "#fff",
			overflow: "hidden",
		},
		cardHeader: {
			display: "flex",
			justifyContent: "space-between",
			alignItems: "center",
			padding: "12px 16px",
			background: "#F9FAFB",
			borderBottom: "1px solid #E5E7EB",
		},
		cardHeaderLeft: {
			display: "flex",
			alignItems: "center",
			gap: "8px",
		},
		cardNumber: {
			display: "inline-flex",
			alignItems: "center",
			justifyContent: "center",
			width: "24px",
			height: "24px",
			borderRadius: "50%",
			background: "#1E1E1E",
			color: "#fff",
			fontSize: "12px",
			fontWeight: "600",
			flexShrink: "0",
		},
		cardName: {
			fontSize: "13px",
			fontWeight: "600",
			color: "#374151",
		},
		cardActions: {
			display: "flex",
			gap: "2px",
		},
		cardBody: {
			padding: "16px",
		},
		sectionLabel: {
			display: "block",
			fontSize: "11px",
			fontWeight: "600",
			textTransform: "uppercase",
			letterSpacing: "0.05em",
			color: "#6B7280",
			marginBottom: "6px",
		},
		sectionDivider: {
			borderTop: "1px solid #F3F4F6",
			margin: "14px 0",
		},
		fieldRow: {
			display: "grid",
			gridTemplateColumns: "1fr 1fr",
			gap: "12px",
		},
		fieldHelp: {
			fontSize: "12px",
			color: "#9CA3AF",
			margin: "2px 0 0",
			lineHeight: "1.4",
		},
		photoSection: {
			display: "flex",
			alignItems: "center",
			gap: "12px",
			padding: "12px",
			background: "#F9FAFB",
			borderRadius: "6px",
		},
		photoPreview: {
			width: "52px",
			height: "52px",
			borderRadius: "50%",
			objectFit: "cover",
			filter: "grayscale(100%)",
			border: "2px solid #E5E7EB",
			flexShrink: "0",
		},
		photoPlaceholder: {
			width: "52px",
			height: "52px",
			borderRadius: "50%",
			background: "#E5E7EB",
			display: "flex",
			alignItems: "center",
			justifyContent: "center",
			fontSize: "20px",
			color: "#9CA3AF",
			flexShrink: "0",
		},
		photoInfo: {
			flex: "1",
		},
		photoLabel: {
			display: "block",
			fontSize: "13px",
			fontWeight: "500",
			color: "#374151",
			marginBottom: "4px",
		},
		photoHint: {
			display: "block",
			fontSize: "12px",
			color: "#9CA3AF",
			marginBottom: "6px",
		},
		photoButtons: {
			display: "flex",
			gap: "6px",
		},
		addBtn: {
			marginTop: "4px",
			width: "100%",
			justifyContent: "center",
		},
		count: {
			fontSize: "12px",
			color: "#9CA3AF",
			textAlign: "right",
			margin: "8px 0 0",
		},
	};

	function renderStarPicker(count) {
		var stars = [];
		for (var i = 0; i < 5; i++) {
			stars.push(
				el(
					"span",
					{
						key: i,
						style: {
							color: i < count ? "#4B5563" : "#D1D5DB",
							fontSize: "20px",
							cursor: "default",
						},
					},
					"\u2605",
				),
			);
		}
		return el("span", { style: { letterSpacing: "2px" } }, stars);
	}

	blocks.registerBlockType("spectra-child/testimonials-slider", {
		attributes: {
			testimonials: {
				type: "array",
				default: [],
				items: { type: "object" },
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
			var testimonials = props.attributes.testimonials || [];
			var autoSlide = props.attributes.autoSlide;
			var autoSlideSpeed = props.attributes.autoSlideSpeed || 5;

			function updateTestimonial(index, keyOrObj, value) {
				var changes = typeof keyOrObj === "string" ? {} : keyOrObj;
				if (typeof keyOrObj === "string") changes[keyOrObj] = value;
				var updated = testimonials.map(function (item, i) {
					if (i === index) {
						var copy = {};
						for (var k in item) copy[k] = item[k];
						for (var c in changes) copy[c] = changes[c];
						return copy;
					}
					return item;
				});
				props.setAttributes({ testimonials: updated });
			}

			function removeTestimonial(index) {
				var updated = testimonials.filter(function (_, i) {
					return i !== index;
				});
				props.setAttributes({ testimonials: updated });
			}

			function addTestimonial() {
				props.setAttributes({
					testimonials: testimonials.concat([
						{
							quote: "",
							stars: 5,
							name: "",
							position: "",
							company: "",
							photoId: 0,
							photoUrl: "",
						},
					]),
				});
			}

			function moveTestimonial(index, direction) {
				var newIndex = index + direction;
				if (newIndex < 0 || newIndex >= testimonials.length) return;
				var updated = testimonials.slice();
				var temp = updated[index];
				updated[index] = updated[newIndex];
				updated[newIndex] = temp;
				props.setAttributes({ testimonials: updated });
			}

			return el(
				element.Fragment,
				null,
				/* ── Sidebar Settings ── */
				el(
					InspectorControls,
					null,
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
							"The slider displays up to 3 testimonials at a time on desktop. Additional testimonials overflow off-screen and can be reached with the navigation arrows.",
						),
						el(
							"p",
							{ style: { fontSize: "13px", color: "#555", marginTop: "8px" } },
							"Each testimonial card shows a quote icon, the client quote, a star rating, and the client's name, role, company, and photo.",
						),
					),
				),
				/* ── Block Content ── */
				el(
					"div",
					blockProps,
					el(
						"div",
						{ style: styles.wrapper },
						/* Header */
						el(
							"div",
							{ style: styles.header },
							el("span", { style: styles.headerIcon }, "\u275D"),
							el("h3", { style: styles.headerTitle }, "Testimonials Slider"),
						),
						el(
							"p",
							{ style: styles.headerDesc },
							"Add client testimonials below. Each card will display a quote, star rating, client photo and details. ",
							el("strong", null, "Tip: "),
							"3 cards are visible on desktop \u2014 add more and visitors can scroll through them with the arrows.",
						),

						/* Empty state */
						testimonials.length === 0
							? el(
									"div",
									{ style: styles.emptyState },
									el("span", { style: styles.emptyIcon }, "\u275D"),
									el("p", { style: styles.emptyTitle }, "No testimonials yet"),
									el(
										"p",
										{ style: styles.emptyText },
										"Click the button below to add your first client testimonial.",
									),
								)
							: null,

						/* Testimonial cards */
						testimonials.length > 0
							? testimonials.map(function (item, index) {
									var displayName = item.name || "Testimonial " + (index + 1);
									return el(
										"div",
										{ key: index, style: styles.card },

										/* Card header */
										el(
											"div",
											{ style: styles.cardHeader },
											el(
												"div",
												{ style: styles.cardHeaderLeft },
												el(
													"span",
													{ style: styles.cardNumber },
													String(index + 1),
												),
												el("span", { style: styles.cardName }, displayName),
											),
											el(
												"div",
												{ style: styles.cardActions },
												index > 0
													? el(Button, {
															icon: "arrow-up-alt2",
															size: "small",
															label: "Move up",
															onClick: function () {
																moveTestimonial(index, -1);
															},
														})
													: null,
												index < testimonials.length - 1
													? el(Button, {
															icon: "arrow-down-alt2",
															size: "small",
															label: "Move down",
															onClick: function () {
																moveTestimonial(index, 1);
															},
														})
													: null,
												el(Button, {
													icon: "no-alt",
													isDestructive: true,
													size: "small",
													label: "Remove this testimonial",
													onClick: function () {
														removeTestimonial(index);
													},
												}),
											),
										),

										/* Card body */
										el(
											"div",
											{ style: styles.cardBody },

											/* Quote section */
											el(
												"span",
												{ style: styles.sectionLabel },
												"Client Quote",
											),
											el(TextareaControl, {
												value: item.quote || "",
												onChange: function (val) {
													updateTestimonial(index, "quote", val);
												},
												rows: 3,
												placeholder:
													"e.g. Working with the team was a fantastic experience from start to finish...",
												help: "The full testimonial quote from your client. This is displayed as the main text on the card.",
											}),

											el("div", { style: styles.sectionDivider }),

											/* Rating section */
											el("span", { style: styles.sectionLabel }, "Star Rating"),
											el(
												"div",
												{
													style: {
														display: "flex",
														alignItems: "center",
														gap: "12px",
														marginBottom: "4px",
													},
												},
												renderStarPicker(item.stars || 5),
											),
											el(RangeControl, {
												value: item.stars || 5,
												onChange: function (val) {
													updateTestimonial(index, "stars", val);
												},
												min: 1,
												max: 5,
												help: "Choose between 1 and 5 stars. This is shown below the client quote.",
											}),

											el("div", { style: styles.sectionDivider }),

											/* Client details section */
											el(
												"span",
												{ style: styles.sectionLabel },
												"Client Details",
											),
											el(
												"p",
												{ style: styles.fieldHelp },
												"The client's name, role and company appear together below the star rating.",
											),
											el(TextControl, {
												label: "Full Name",
												value: item.name || "",
												onChange: function (val) {
													updateTestimonial(index, "name", val);
												},
												placeholder: "e.g. James Calder",
												help: "The client's full name as it should appear on the card.",
											}),
											el(
												"div",
												{ style: styles.fieldRow },
												el(TextControl, {
													label: "Position / Role",
													value: item.position || "",
													onChange: function (val) {
														updateTestimonial(index, "position", val);
													},
													placeholder: "e.g. Marketing Director",
													help: "Their job title or role.",
												}),
												el(TextControl, {
													label: "Company Name",
													value: item.company || "",
													onChange: function (val) {
														updateTestimonial(index, "company", val);
													},
													placeholder: "e.g. Northbridge Consulting",
													help: "The company or organisation they represent.",
												}),
											),

											el("div", { style: styles.sectionDivider }),

											/* Photo section */
											el(
												"span",
												{ style: styles.sectionLabel },
												"Client Photo",
											),
											el(
												"div",
												{ style: styles.photoSection },
												item.photoUrl
													? el("img", {
															src: item.photoUrl,
															alt: item.name || "Client photo",
															style: styles.photoPreview,
														})
													: el(
															"div",
															{ style: styles.photoPlaceholder },
															"\uD83D\uDC64",
														),
												el(
													"div",
													{ style: styles.photoInfo },
													el(
														"span",
														{ style: styles.photoLabel },
														item.photoUrl ? "Photo uploaded" : "No photo added",
													),
													el(
														"span",
														{ style: styles.photoHint },
														"A small circular headshot. It will be displayed in black & white.",
													),
													el(
														"div",
														{ style: styles.photoButtons },
														el(MediaUpload, {
															onSelect: function (media) {
																updateTestimonial(index, {
																	photoId: media.id,
																	photoUrl: media.url,
																});
															},
															allowedTypes: ["image"],
															value: item.photoId || 0,
															render: function (obj) {
																return el(
																	Button,
																	{
																		onClick: obj.open,
																		variant: "secondary",
																		size: "small",
																	},
																	item.photoUrl
																		? "Replace Photo"
																		: "Upload Photo",
																);
															},
														}),
														item.photoUrl
															? el(
																	Button,
																	{
																		isDestructive: true,
																		variant: "tertiary",
																		size: "small",
																		onClick: function () {
																			updateTestimonial(index, {
																				photoId: 0,
																				photoUrl: "",
																			});
																		},
																	},
																	"Remove",
																)
															: null,
													),
												),
											),
										),
									);
								})
							: null,

						/* Add button */
						el(
							Button,
							{
								onClick: addTestimonial,
								variant: "primary",
								style: styles.addBtn,
							},
							"+ Add Testimonial",
						),

						/* Count */
						testimonials.length > 0
							? el(
									"p",
									{ style: styles.count },
									testimonials.length +
										" testimonial" +
										(testimonials.length !== 1 ? "s" : "") +
										" added",
								)
							: null,
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
);
