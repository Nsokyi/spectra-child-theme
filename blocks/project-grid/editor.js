(function (blocks, element, blockEditor, components, serverSideRender) {
	var el = element.createElement;
	var useState = element.useState;
	var useEffect = element.useEffect;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var ToggleControl = components.ToggleControl;
	var RangeControl = components.RangeControl;
	var SelectControl = components.SelectControl;
	var ServerSideRender = serverSideRender;

	blocks.registerBlockType("spectra-child/project-grid", {
		attributes: {
			postsPerPage: { type: "number", default: 6 },
			showLoadMore: { type: "boolean", default: true },
			preSelectedIndustry: { type: "string", default: "" },
			preSelectedService: { type: "string", default: "" },
			showFeaturedOnly: { type: "boolean", default: false },
			showFilterBar: { type: "boolean", default: true },
			showServiceRow: { type: "boolean", default: true },
			columns: { type: "number", default: 3 },
			orderBy: { type: "string", default: "date" },
		},
		edit: function (props) {
			var blockProps = blockEditor.useBlockProps();
			var attrs = props.attributes;
			var set = props.setAttributes;

			var industryState = useState([]);
			var industries = industryState[0];
			var setIndustries = industryState[1];

			var serviceState = useState([]);
			var services = serviceState[0];
			var setServices = serviceState[1];

			useEffect(function () {
				wp.apiFetch({ path: "/wp/v2/industry?per_page=100" }).then(
					function (terms) {
						setIndustries(
							terms.map(function (t) {
								return { label: t.name, value: t.slug };
							}),
						);
					},
				);
				wp.apiFetch({ path: "/wp/v2/service?per_page=100" }).then(
					function (terms) {
						setServices(
							terms.map(function (t) {
								return { label: t.name, value: t.slug };
							}),
						);
					},
				);
			}, []);

			var industryOptions = [{ label: "— None —", value: "" }].concat(
				industries,
			);
			var serviceOptions = [{ label: "— None —", value: "" }].concat(
				services,
			);

			return el(
				element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: "Essential", initialOpen: true },
						el(RangeControl, {
							label: "Per Page",
							help: "How many projects load initially.",
							value: attrs.postsPerPage,
							onChange: function (val) {
								set({ postsPerPage: val });
							},
							min: 1,
							max: 48,
						}),
						el(ToggleControl, {
							label: "Show Load More",
							help: "When disabled, no Load More button is rendered.",
							checked: attrs.showLoadMore,
							onChange: function (val) {
								set({ showLoadMore: val });
							},
						}),
						el(SelectControl, {
							label: "Pre-selected Industry",
							help: "Sets the active industry filter on initial load.",
							value: attrs.preSelectedIndustry,
							options: industryOptions,
							onChange: function (val) {
								set({ preSelectedIndustry: val });
							},
						}),
						el(SelectControl, {
							label: "Pre-selected Service",
							help: "Only relevant when an industry is pre-selected.",
							value: attrs.preSelectedService,
							options: serviceOptions,
							onChange: function (val) {
								set({ preSelectedService: val });
							},
						}),
					),
					el(
						PanelBody,
						{ title: "Display Options", initialOpen: false },
						el(ToggleControl, {
							label: "Featured Only",
							help: "Only show projects with Featured = yes.",
							checked: attrs.showFeaturedOnly,
							onChange: function (val) {
								set({ showFeaturedOnly: val });
							},
						}),
						el(ToggleControl, {
							label: "Show Filter Bar",
							help: "Hide to replicate homepage-style usage.",
							checked: attrs.showFilterBar,
							onChange: function (val) {
								set({ showFilterBar: val });
							},
						}),
						el(ToggleControl, {
							label: "Show Service Row",
							help: "Hide the service filter row on pages already scoped to a specific service.",
							checked: attrs.showServiceRow,
							onChange: function (val) {
								set({ showServiceRow: val });
							},
						}),
						el(SelectControl, {
							label: "Columns",
							value: String(attrs.columns),
							options: [
								{ label: "2 Columns", value: "2" },
								{ label: "3 Columns", value: "3" },
							],
							onChange: function (val) {
								set({ columns: parseInt(val, 10) });
							},
						}),
						el(SelectControl, {
							label: "Order By",
							value: attrs.orderBy,
							options: [
								{ label: "Date (newest first)", value: "date" },
								{
									label: "Menu Order (manual)",
									value: "menu_order",
								},
							],
							onChange: function (val) {
								set({ orderBy: val });
							},
						}),
					),
				),
				el(
					"div",
					blockProps,
					el(ServerSideRender, {
						block: "spectra-child/project-grid",
						attributes: attrs,
					}),
				),
			);
		},
	});
})(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.serverSideRender,
);
