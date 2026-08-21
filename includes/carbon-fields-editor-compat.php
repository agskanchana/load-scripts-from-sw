<?php
/**
 * Carbon Fields <-> iframed block editor compatibility.
 *
 * Loads the script that keeps `rich_text` fields usable now that the block
 * editor canvas is always an iframe. See js/carbon-fields-editor-compat.js for
 * the details of what breaks and why.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'enqueue_block_editor_assets', 'ekwa_enqueue_carbon_fields_editor_compat' );

function ekwa_enqueue_carbon_fields_editor_compat() {
	// Nothing to patch if Carbon Fields is not around.
	if ( ! class_exists( 'Carbon_Fields\\Carbon_Fields' ) ) {
		return;
	}

	wp_enqueue_script(
		'ekwa-carbon-fields-editor-compat',
		plugin_dir_url( dirname( __FILE__ ) ) . 'js/carbon-fields-editor-compat.js',
		array( 'wp-hooks', 'wp-element', 'wp-components', 'wp-i18n' ),
		EKWA_SETTINGS_VERSION,
		true
	);
}
