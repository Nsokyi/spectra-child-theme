(function (blocks, element, blockEditor, components) {
	var el = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var MediaUpload = blockEditor.MediaUpload;
	var PanelBody = components.PanelBody;
	var RangeControl = components.RangeControl;
	var Button = components.Button;
	var TextControl = components.TextControl;

	blocks.registerBlockType("spectra-child/logo-carousel", {
		attributes: {
			logos: {
				type: "array",
				default: [],
				items: {
					type: "object",
				},
			},
			speed: {
				type: "number",
				default: 30,
			},
			logoHeight: {
				type: "number",
				default: 50,
			},
		},
		edit: function (props) {
			var blockProps = blockEditor.useBlockProps();
			var logos = props.attributes.logos || [];
			var speed = props.attributes.speed || 30;
			var logoHeight = props.attributes.logoHeight || 50;

			function updateLogo(index, key, value) {
				var updated = logos.map(function (logo, i) {
					if (i === index) {
						var copy = {};
						for (var k in logo) copy[k] = logo[k];
						copy[key] = value;
						return copy;
					}
					return logo;
				});
				props.setAttributes({ logos: updated });
			}

			function removeLogo(index) {
				var updated = logos.filter(function (_, i) {
					return i !== index;
				});
				props.setAttributes({ logos: updated });
			}

			function onSelectImages(media) {
				var mediaArray = Array.isArray(media) ? media : [media];
				var newLogos = mediaArray.map(function (img) {
					return {
						id: img.id,
						url: img.url,
						alt: img.alt || img.title || "",
						link: "",
					};
				});
				props.setAttributes({ logos: logos.concat(newLogos) });
			}

			return el(
				element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: "Carousel Settings", initialOpen: true },
						el(RangeControl, {
							label: "Scroll Speed (seconds per cycle)",
							value: speed,
							onChange: function (val) {
								props.setAttributes({ speed: val });
							},
							min: 5,
							max: 120,
							help: "Lower = faster. Default is 30 seconds.",
						}),
						el(RangeControl, {
							label: "Logo Height (px)",
							value: logoHeight,
							onChange: function (val) {
								props.setAttributes({ logoHeight: val });
							},
							min: 20,
							max: 200,
						}),
					),
					el(
						PanelBody,
						{ title: "Logo Links", initialOpen: false },
						logos.length > 0
							? logos.map(function (logo, index) {
									return el(TextControl, {
										key: index,
										label: (logo.alt || "Logo " + (index + 1)) + " — Link URL",
										value: logo.link || "",
										onChange: function (val) {
											updateLogo(index, "link", val);
										},
										placeholder: "https://",
									});
								})
							: el("p", null, "Add logos first."),
					),
				),
				el(
					"div",
					blockProps,
					el(
						"div",
						{ className: "logo-carousel-editor" },
						el(
							"p",
							{
								className: "logo-carousel-editor__hint",
								style: {
									fontSize: "12px",
									color: "#757575",
									marginBottom: "12px",
									fontStyle: "italic",
								},
							},
							"For best results, use SVG logos. Add or remove logos below.",
						),
						logos.length > 0
							? el(
									"div",
									{
										className: "logo-carousel-editor__grid",
										style: {
											display: "grid",
											gridTemplateColumns:
												"repeat(auto-fill, minmax(100px, 1fr))",
											gap: "12px",
											marginBottom: "16px",
										},
									},
									logos.map(function (logo, index) {
										return el(
											"div",
											{
												key: index,
												className: "logo-carousel-editor__item",
												style: {
													position: "relative",
													background: "#f0f0f0",
													borderRadius: "4px",
													padding: "12px",
													textAlign: "center",
												},
											},
											el("img", {
												src: logo.url,
												alt: logo.alt,
												style: {
													maxHeight: "40px",
													maxWidth: "100%",
													objectFit: "contain",
												},
											}),
											el(Button, {
												icon: "no-alt",
												isDestructive: true,
												size: "small",
												onClick: function () {
													removeLogo(index);
												},
												style: {
													position: "absolute",
													top: "-6px",
													right: "-6px",
													minWidth: "24px",
													height: "24px",
													padding: "0",
													borderRadius: "50%",
												},
												label: "Remove logo",
											}),
										);
									}),
								)
							: null,
						el(MediaUpload, {
							onSelect: onSelectImages,
							allowedTypes: ["image"],
							multiple: true,
							render: function (obj) {
								return el(
									Button,
									{
										onClick: obj.open,
										variant: "secondary",
									},
									logos.length > 0 ? "Add More Logos" : "Add Logos",
								);
							},
						}),
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
