
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps, InnerBlocks, ColorPalette } from '@wordpress/block-editor';
import { RangeControl, Panel, PanelBody,  ToggleControl,TextareaControl} from '@wordpress/components';

import { useEffect } from 'react';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const blockProps = useBlockProps();

	const { enableCollapse, titleBGColor,titleTextColor,contentBGcolor,contentTextColor } = attributes;
	setAttributes({blockID: clientId})
	const BLOCKS_TEMPLATE = [
		[ 'yoast/faq-block'],
	];

	return (
		<>
		 <InspectorControls>
		 <Panel>
				<PanelBody>
				<ToggleControl

					help={
						enableCollapse
							? 'Enable Collapse.'
							: 'Disable Collapse.'
					}
					checked={ enableCollapse }
					onChange={ (value) => {

						setAttributes({  enableCollapse: value })
						console.log(enableCollapse)
					} }
        		/>
				</PanelBody>
			</Panel>
			<Panel>
				<PanelBody  title={'Collapse Styles '}>
					<h3>Title Background Color</h3>
					<ColorPalette
						value={ titleBGColor }
						onChange={ (value) => setAttributes( {titleBGColor: value} ) }
					/>
					<h3>Title Text Color</h3>
					<ColorPalette
						value={ titleTextColor }
						onChange={ (value) => setAttributes( {titleTextColor: value} ) }
					/>
					<h3> content Background color</h3>
					<ColorPalette
						value={ contentBGcolor }
						onChange={ (value) => setAttributes( {contentBGcolor: value} ) }
					/>
					<h3> content Text color</h3>
					<ColorPalette
						value={ contentTextColor }
						onChange={ (value) => setAttributes( {contentTextColor: value} ) }
					/>
				</PanelBody>
			</Panel>

		 </InspectorControls>
		 <div>
			<InnerBlocks
			template={ BLOCKS_TEMPLATE }
			/>
		</div>
		</>
	);
}
