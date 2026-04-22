(function (blocks, element, blockEditor, components) {
	var el = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;

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
		badge: {
			display: "inline-block",
			padding: "4px 12px",
			borderRadius: "12px",
			fontSize: "13px",
			fontWeight: "500",
			background: "#E8F5E9",
			color: "#2E7D32",
		},
	};

	blocks.registerBlockType("spectra-child/site-header", {
		edit: function (props) {
			var attributes = props.attributes;

			return el(
				"div",
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: "CTA Button", initialOpen: true },
						el(TextControl, {
							label: "Button Text",
							value: attributes.ctaText,
							onChange: function (val) {
								props.setAttributes({ ctaText: val });
							},
						}),
						el(TextControl, {
							label: "Button URL",
							value: attributes.ctaUrl,
							onChange: function (val) {
								props.setAttributes({ ctaUrl: val });
							},
							help: "Relative URL e.g. /contact/ or full URL",
						})
					)
				),
				el(
					"div",
					{ style: styles.wrapper },
					el("span", { style: styles.icon }, "☰"),
					el("h3", { style: styles.title }, "Site Header"),
					el(
						"p",
						{ style: styles.description },
						'Renders the site header with logo, "Primary" menu, mobile drawer, and "',
						attributes.ctaText,
						'" CTA button.'
					),
					el(
						"span",
						{ style: styles.badge },
						"Menu: Appearance → Menus → Primary"
					)
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components
);
