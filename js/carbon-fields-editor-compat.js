/**
 * Carbon Fields <-> iframed block editor compatibility.
 *
 * Since WordPress 6.9 the post editor canvas is an iframe, and as of 7.1 the
 * visual editor renders `BlockCanvas` with a hardcoded `shouldIframe: true`,
 * so there is no longer any way for a block to opt out of it.
 *
 * Carbon Fields boots the WYSIWYG of its `rich_text` fields with
 * `tinymce.init( { selector: '#' + id } )`. That selector is resolved against
 * the top level document, so the textarea - which now lives inside the canvas
 * iframe - is never found and the field silently stays a plain textarea. The
 * Visual/Text tabs and the Add Media button break for the same reason: both are
 * driven by click handlers delegated on the top level `document`.
 *
 * Core hit this exact wall with the Classic block and solved it by moving
 * TinyMCE into a Modal, which portals to the top level `document.body`. We do
 * the same here: the block canvas shows a preview plus an Edit button, and the
 * real editor is initialized inside a modal that lives outside the iframe.
 *
 * When the canvas is not iframed (older WordPress, metabox context) the
 * original Carbon Fields component is used untouched.
 */
( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.hooks || ! wp.element || ! wp.components ) {
		return;
	}

	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var useEffect = wp.element.useEffect;
	var useRef = wp.element.useRef;
	var Modal = wp.components.Modal;
	var Button = wp.components.Button;
	var ColorPicker = wp.components.ColorPicker;
	var __ = wp.i18n.__;

	/**
	 * Whether the block editor canvas is rendered inside an iframe.
	 *
	 * @return {boolean}
	 */
	function isIframedCanvas() {
		return !! document.querySelector( 'iframe[name="editor-canvas"]' );
	}

	/**
	 * Carbon Fields scopes its block editor styles to `.block-editor`, the
	 * wrapper that wp-admin/edit-form-blocks.php prints in the top level
	 * document. Now that blocks render inside the canvas iframe that ancestor is
	 * out of reach, so every one of those rules stops applying: the `.cf-field`
	 * padding that offsets the negative margin on `.cf-block__fields` (hence the
	 * clipped labels), the typography, the complex field borders.
	 *
	 * Core keeps its own stylesheets out of the iframe - `getCompatibilityStyles`
	 * skips every `wp-` prefixed handle - so the only `.block-editor` rules that
	 * reach it are third party ones. Putting the class back on the iframe body
	 * restores Carbon Fields' intended cascade and nothing else.
	 *
	 * @return {void}
	 */
	function restoreCanvasStyleScope() {
		var frames = document.querySelectorAll( 'iframe[name="editor-canvas"]' );

		Array.prototype.forEach.call( frames, function ( frame ) {
			var doc = frame.contentDocument;

			if ( doc && doc.body ) {
				doc.body.classList.add( 'block-editor' );
			}
		} );
	}

	function watchCanvas() {
		restoreCanvasStyleScope();

		// The canvas iframe is torn down and rebuilt whenever the device
		// preview or zoom level changes, and each rebuild starts from a fresh
		// document, so the class has to be reapplied. Adding an existing class
		// is a no-op, which keeps this cheap enough to just poll.
		window.setInterval( restoreCanvasStyleScope, 1000 );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', watchCanvas );
	} else {
		watchCanvas();
	}

	/**
	 * The wp_editor() settings Carbon Fields printed for this field.
	 *
	 * @param  {string} reference
	 * @return {Object|null}
	 */
	function getEditorSettings( reference ) {
		var preInit = window.tinyMCEPreInit;

		if ( ! preInit || ! reference ) {
			return null;
		}

		return {
			mce: preInit.mceInit ? preInit.mceInit[ reference ] : null,
			qt: preInit.qtInit ? preInit.qtInit[ reference ] : null
		};
	}

	/**
	 * Boot TinyMCE and the quicktags toolbar on a textarea.
	 *
	 * @param  {string} id
	 * @param  {Object} field
	 * @return {void}
	 */
	function initEditor( id, field ) {
		var settings = getEditorSettings( field.settings_reference );

		if ( ! settings ) {
			return;
		}

		if ( field.rich_editing && settings.mce && window.tinymce ) {
			window.tinymce.init(
				Object.assign( {}, settings.mce, {
					selector: '#' + id,
					setup: function ( editor ) {
						editor.on( 'blur Change', function () {
							editor.save();
						} );
					}
				} )
			);
		}

		if ( settings.qt && window.quicktags ) {
			var instance = window.quicktags( Object.assign( {}, settings.qt, { id: id } ) );

			// `quicktags()` bails out and leaves `id` undefined when the
			// textarea cannot be found, so only wire up the buttons when the
			// instance actually took.
			if ( instance && instance.id && window.QTags ) {
				window.QTags._buttonsInit( instance.id );
			}
		}

		// Tells wp.media which editor "Add Media" should insert into.
		window.wpActiveEditor = id;
	}

	/**
	 * Tear down both editors so the id can be reused next time the modal opens.
	 *
	 * @param  {string} id
	 * @return {void}
	 */
	function destroyEditor( id ) {
		var editor = window.tinymce ? window.tinymce.get( id ) : null;

		if ( editor ) {
			editor.remove();
		}

		if ( window.QTags && window.QTags.instances ) {
			delete window.QTags.instances[ id ];
		}
	}

	/**
	 * Read the current content, from whichever of the two tabs is active.
	 *
	 * @param  {string} id
	 * @return {string}
	 */
	function getEditorContent( id ) {
		var editor = window.tinymce ? window.tinymce.get( id ) : null;

		if ( editor && ! editor.isHidden() ) {
			return editor.getContent();
		}

		var textarea = document.getElementById( id );

		return textarea ? textarea.value : '';
	}

	/**
	 * Resolve the `<%- id %>` placeholders in the Add Media button markup, the
	 * same way Carbon Fields does.
	 *
	 * @param  {string} markup
	 * @param  {string} id
	 * @return {string}
	 */
	function renderMediaButtons( markup, id ) {
		if ( window.lodash && window.lodash.template ) {
			return window.lodash.template( markup )( { id: id } );
		}

		return markup.replace( /<%-\s*id\s*%>/g, id );
	}

	/**
	 * The wp_editor() markup, rebuilt to match what Carbon Fields renders so
	 * that its stylesheet keeps applying.
	 */
	function EditorFrame( props ) {
		var id = props.id;
		var field = props.field;

		useEffect( function () {
			// Let the modal finish opening so TinyMCE measures a settled
			// container - `wp_autoresize_on` depends on it.
			var timer = window.setTimeout( function () {
				initEditor( id, field );
			}, 100 );

			return function () {
				window.clearTimeout( timer );
				destroyEditor( id );
			};
		}, [] );

		var classes = [ 'carbon-wysiwyg', 'wp-editor-wrap' ];

		classes.push( field.rich_editing ? 'tmce-active' : 'html-active' );

		return el(
			'div',
			{
				id: 'wp-' + id + '-wrap',
				className: classes.join( ' ' )
			},
			field.media_buttons
				? el( 'div', {
					id: 'wp-' + id + '-media-buttons',
					className: 'hide-if-no-js wp-media-buttons',
					dangerouslySetInnerHTML: {
						__html: renderMediaButtons( field.media_buttons, id )
					}
				} )
				: null,
			field.rich_editing
				? el(
					'div',
					{ className: 'wp-editor-tabs' },
					el(
						'button',
						{
							type: 'button',
							id: id + '-tmce',
							className: 'wp-switch-editor switch-tmce',
							'data-wp-editor-id': id
						},
						__( 'Visual', 'carbon-fields-ui' )
					),
					el(
						'button',
						{
							type: 'button',
							id: id + '-html',
							className: 'wp-switch-editor switch-html',
							'data-wp-editor-id': id
						},
						__( 'Text', 'carbon-fields-ui' )
					)
				)
				: null,
			el(
				'div',
				{
					id: 'wp-' + id + '-editor-container',
					className: 'wp-editor-container'
				},
				el( 'textarea', {
					id: id,
					className: 'regular-text',
					style: { width: '100%', minHeight: '360px' },
					defaultValue: props.value
				} )
			)
		);
	}

	/**
	 * Replacement for the `rich_text` field in block context.
	 *
	 * @param  {Function} OriginalComponent
	 * @return {Function}
	 */
	function withModalEditor( OriginalComponent ) {
		return function CarbonRichTextIframeCompat( props ) {
			// Hooks run unconditionally so the hook order stays stable
			// regardless of which branch renders below.
			var openState = useState( false );
			var isOpen = openState[ 0 ];
			var setOpen = openState[ 1 ];

			var field = props.field || {};

			// Outside an iframe the upstream component works as it always did,
			// and without rich editing the plain textarea is already usable.
			if ( ! isIframedCanvas() || ! field.rich_editing ) {
				return el( OriginalComponent, props );
			}

			var value = props.value || '';

			return el(
				'div',
				{ className: 'cf-rich-text-modal' },
				value
					? el( 'div', {
						className: 'cf-rich-text-modal__preview',
						style: {
							padding: '10px 12px',
							border: '1px solid #ddd',
							borderBottom: 'none',
							background: '#fff',
							maxHeight: '220px',
							overflow: 'auto'
						},
						dangerouslySetInnerHTML: { __html: value }
					} )
					: el(
						'div',
						{
							className: 'cf-rich-text-modal__preview is-empty',
							style: {
								padding: '10px 12px',
								border: '1px solid #ddd',
								borderBottom: 'none',
								background: '#fff',
								color: '#757575',
								fontStyle: 'italic'
							}
						},
						__( 'No content yet.', 'carbon-fields-ui' )
					),
				el(
					'div',
					{
						style: {
							padding: '8px 12px',
							border: '1px solid #ddd',
							background: '#f6f7f7'
						}
					},
					el(
						Button,
						{
							variant: 'secondary',
							onClick: function () {
								setOpen( true );
							}
						},
						value ? __( 'Edit content', 'carbon-fields-ui' ) : __( 'Add content', 'carbon-fields-ui' )
					)
				),
				isOpen
					? el(
						Modal,
						{
							title: field.label || __( 'Edit content', 'carbon-fields-ui' ),
							size: 'large',
							shouldCloseOnClickOutside: false,
							className: 'cf-rich-text-modal__dialog',
							onRequestClose: function () {
								setOpen( false );
							}
						},
						el( EditorFrame, {
							id: props.id,
							field: field,
							value: value
						} ),
						el(
							'div',
							{
								style: {
									display: 'flex',
									gap: '8px',
									justifyContent: 'flex-end',
									marginTop: '16px'
								}
							},
							el(
								Button,
								{
									variant: 'tertiary',
									onClick: function () {
										setOpen( false );
									}
								},
								__( 'Cancel', 'carbon-fields-ui' )
							),
							el(
								Button,
								{
									variant: 'primary',
									onClick: function () {
										props.onChange( props.id, getEditorContent( props.id ) );
										setOpen( false );
									}
								},
								__( 'Save', 'carbon-fields-ui' )
							)
						)
					)
					: null
			);
		};
	}

	/**
	 * Build the swatch background, mirroring Carbon Fields' `getBackgroundColor`.
	 *
	 * @param  {string}  hex
	 * @param  {boolean} alphaEnabled
	 * @return {string}
	 */
	function hexToRgbaString( hex, alphaEnabled ) {
		var value = ( hex || '#FFFFFFFF' ).replace( '#', '' );

		if ( 3 === value.length ) {
			value = value.split( '' ).map( function ( character ) {
				return character + character;
			} ).join( '' );
		}

		var r = parseInt( value.substr( 0, 2 ), 16 ) || 0;
		var g = parseInt( value.substr( 2, 2 ), 16 ) || 0;
		var b = parseInt( value.substr( 4, 2 ), 16 ) || 0;
		var a = alphaEnabled && 8 === value.length
			? ( parseInt( value.substr( 6, 2 ), 16 ) || 0 ) / 255
			: 1;

		return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + a + ')';
	}

	/**
	 * Replacement for the `color` field in block context.
	 *
	 * Carbon Fields picks colours with `react-color`, which does not survive
	 * being rendered in an iframe:
	 *
	 * - `Saturation.getContainerRenderWindow()` walks *up* from `window` looking
	 *   for the document holding the picker. The script runs in the top frame
	 *   and the picker is one frame *down*, so the walk exits immediately and
	 *   the drag listeners are bound to the top window, which never sees the
	 *   pointer while it is over the canvas.
	 * - its `calculateChange` helpers offset iframe relative `pageX`/`pageY`
	 *   against the *top* window's `pageXOffset`/`pageYOffset`. Scrolled down a
	 *   post, that lands hundreds of pixels out of range and every click clamps
	 *   to the same extreme - which is why the picker only ever returns black.
	 *
	 * WordPress' own ColorPicker is built on react-colorful, which resolves its
	 * window from the element's `ownerDocument` and works off `clientX`/`clientY`
	 * against the element's own rect, so it is iframe safe.
	 *
	 * @param  {Function} OriginalComponent
	 * @return {Function}
	 */
	function withNativeColorPicker( OriginalComponent ) {
		return function CarbonColorIframeCompat( props ) {
			var openState = useState( false );
			var isOpen = openState[ 0 ];
			var setOpen = openState[ 1 ];
			var wrapperRef = useRef( null );

			// Close on click outside. This is what `react-onclickoutside` is
			// meant to do for the upstream picker, but it only ever listens on
			// the top level document - so it has to be watched on the canvas
			// document too, otherwise clicks inside the iframe never register.
			useEffect( function () {
				if ( ! isOpen ) {
					return;
				}

				var documents = [ document ];
				var wrapper = wrapperRef.current;
				var ownerDocument = wrapper && wrapper.ownerDocument;

				if ( ownerDocument && -1 === documents.indexOf( ownerDocument ) ) {
					documents.push( ownerDocument );
				}

				function handleOutside( event ) {
					if ( wrapperRef.current && ! wrapperRef.current.contains( event.target ) ) {
						setOpen( false );
					}
				}

				documents.forEach( function ( doc ) {
					doc.addEventListener( 'mousedown', handleOutside );
				} );

				return function () {
					documents.forEach( function ( doc ) {
						doc.removeEventListener( 'mousedown', handleOutside );
					} );
				};
			}, [ isOpen ] );

			if ( ! isIframedCanvas() || ! ColorPicker ) {
				return el( OriginalComponent, props );
			}

			var field = props.field || {};
			var value = props.value || '';

			function change( hex ) {
				props.onChange( props.id, ( hex || '' ).toUpperCase() );
			}

			// `.cf-color__inner` is `display: flex`, so the picker has to live
			// outside it - as a flex sibling it would sit in the button row and
			// squeeze the toggle instead of dropping below it.
			return el(
				'div',
				{ ref: wrapperRef },
				el(
					'div',
					{ className: 'cf-color__inner' },
					el( 'input', {
						type: 'hidden',
						id: props.id,
						name: props.name,
						value: value,
						readOnly: true
					} ),
					el(
						'button',
						{
							type: 'button',
							className: 'button cf-color__toggle',
							onClick: function () {
								setOpen( ! isOpen );
							}
						},
						el( 'span', {
							className: 'cf-color__preview',
							style: {
								backgroundColor: hexToRgbaString( value, field.alphaEnabled )
							}
						} ),
						el(
							'span',
							{ className: 'cf-color__toggle-text' },
							__( 'Select a color', 'carbon-fields-ui' )
						)
					),
					el(
						'button',
						{
							type: 'button',
							className: 'button-link cf-color__reset',
							'aria-label': __( 'Clear', 'carbon-fields-ui' ),
							onClick: function () {
								change( '' );
							}
						},
						el( 'span', { className: 'dashicons dashicons-no' } )
					)
				),
				isOpen
					? el(
						'div',
						{
							className: 'cf-color__picker',
							// Carbon Fields positions its own picker absolutely.
							// Keeping this one in normal flow avoids having to
							// reason about offsets across the iframe boundary.
							style: {
								position: 'static',
								marginTop: '8px',
								padding: '8px',
								border: '1px solid #e2e4e7',
								background: '#fff',
								display: 'inline-block'
							}
						},
						el( ColorPicker, {
							color: value || '#000000',
							enableAlpha: !! field.alphaEnabled,
							onChange: change
						} ),
						el(
							'div',
							{ style: { marginTop: '8px', textAlign: 'right' } },
							el(
								Button,
								{
									variant: 'secondary',
									onClick: function () {
										setOpen( false );
									}
								},
								__( 'Done', 'carbon-fields-ui' )
							)
						)
					)
					: null
			);
		};
	}

	wp.hooks.addFilter(
		'carbon-fields.rich_text.block',
		'ekwa/carbon-fields-editor-compat',
		withModalEditor
	);

	wp.hooks.addFilter(
		'carbon-fields.color.block',
		'ekwa/carbon-fields-editor-compat',
		withNativeColorPicker
	);
} )( window.wp );
