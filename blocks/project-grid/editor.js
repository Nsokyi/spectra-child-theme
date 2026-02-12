( function ( blocks, element, blockEditor, components, serverSideRender ) {
	var el                = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody         = components.PanelBody;
	var ToggleControl     = components.ToggleControl;
	var RangeControl      = components.RangeControl;
	var ServerSideRender  = serverSideRender;

	blocks.registerBlockType( 'spectra-child/project-grid', {
		edit: function ( props ) {
			return el(
				element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Project Grid Settings', initialOpen: true },
						el( ToggleControl, {
							label: 'Show Featured Only',
							checked: props.attributes.showFeaturedOnly,
							onChange: function ( val ) {
								props.setAttributes( { showFeaturedOnly: val } );
							},
						} ),
						el( RangeControl, {
							label: 'Posts Per Page',
							value: props.attributes.postsPerPage,
							onChange: function ( val ) {
								props.setAttributes( { postsPerPage: val } );
							},
							min: 3,
							max: 48,
						} )
					)
				),
				el(
					'div',
					blockEditor.useBlockProps(),
					el( ServerSideRender, {
						block: 'spectra-child/project-grid',
						attributes: props.attributes,
					} )
				)
			);
		},
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.serverSideRender
);
