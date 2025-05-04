<?php
/**
 * Simple Container Block
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Create necessary files if they don't exist
 */
function ensure_container_block_files() {
    $build_dir = plugin_dir_path(__FILE__) . 'build';

    // Create build directory if it doesn't exist
    if (!file_exists($build_dir)) {
        mkdir($build_dir, 0755, true);
    }

    // Create asset file if it doesn't exist
    $asset_file_path = $build_dir . '/index.asset.php';
    if (!file_exists($asset_file_path)) {
        file_put_contents($asset_file_path, '<?php return array("dependencies" => array("wp-blocks", "wp-element", "wp-block-editor", "wp-i18n"), "version" => "1.0.0");');
    }

    // Create editor.css if it doesn't exist
    if (!file_exists($build_dir . '/editor.css')) {
        $editor_css = ".ekwa-container-block { border: 2px dashed #ccc; padding: 20px; position: relative; background: #f9f9f9; min-height: 100px; }";
        $editor_css .= ".ekwa-container-block::before { content: \"Container Block\"; position: absolute; top: -12px; left: 10px; background: #fff; padding: 0 10px; font-size: 12px; color: #888; }";
        $editor_css .= ".ekwa-container-content { min-height: 50px; }";
        file_put_contents($build_dir . '/editor.css', $editor_css);
    }

    // Create style.css if it doesn't exist
    if (!file_exists($build_dir . '/style.css')) {
        $style_css = ".ekwa-container-block { padding: 1em; margin-bottom: 1.5em; }";
        file_put_contents($build_dir . '/style.css', $style_css);
    }
}

/**
 * Register the container block
 */
function register_container_block() {
    // Check if Gutenberg is available
    if (!function_exists('register_block_type')) {
        return;
    }

    // Ensure necessary files exist
    ensure_container_block_files();

    // Register the block assets
    $asset_file = include(plugin_dir_path(__FILE__) . 'build/index.asset.php');

    // Register the script
    wp_register_script(
        'container-block-editor',
        plugins_url('build/index.js', __FILE__),
        $asset_file['dependencies'],
        $asset_file['version']
    );

    // Register the editor style
    wp_register_style(
        'container-block-editor-style',
        plugins_url('build/editor.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'build/editor.css')
    );

    // Register the frontend style
    wp_register_style(
        'container-block-style',
        plugins_url('build/style.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'build/style.css')
    );

    // Register the block
    register_block_type('ekwa/container-block', array(
        'editor_script' => 'container-block-editor',
        'editor_style' => 'container-block-editor-style',
        'style' => 'container-block-style',
    ));
}
add_action('init', 'register_container_block');

/**
 * FAQ Section Block
 *
 * A container block for FAQ sections with schema markup
 */

/**
 * Create necessary files if they don't exist
 */
function ensure_faq_section_files() {
    $build_dir = plugin_dir_path(__FILE__) . 'build';

    // Create build directory if it doesn't exist
    if (!file_exists($build_dir)) {
        mkdir($build_dir, 0755, true);
    }

    // Create asset file if it doesn't exist
    $asset_file_path = $build_dir . '/index.asset.php';
    if (!file_exists($asset_file_path)) {
        file_put_contents($asset_file_path, '<?php return array("dependencies" => array("wp-blocks", "wp-element", "wp-block-editor", "wp-i18n"), "version" => "1.0.0");');
    }

    // Create editor.css if it doesn't exist
    if (!file_exists($build_dir . '/editor.css')) {
        $editor_css = ".ekwa-faq-section { border: 2px dashed #ccc; padding: 20px; position: relative; background: #f9f9f9; min-height: 100px; }";
        $editor_css .= ".ekwa-faq-section::before { content: \"FAQ Section\"; position: absolute; top: -12px; left: 10px; background: #fff; padding: 0 10px; font-size: 12px; color: #888; }";
        $editor_css .= ".ekwa-faq-content { min-height: 50px; }";
        file_put_contents($build_dir . '/editor.css', $editor_css);
    }

    // Create style.css if it doesn't exist
    if (!file_exists($build_dir . '/style.css')) {
        $style_css = ".ekwa-faq-section { padding: 1em; margin-bottom: 1.5em; }";
        file_put_contents($build_dir . '/style.css', $style_css);
    }
}

/**
 * Register the FAQ section block
 */
function register_faq_section_block() {
    // Ensure necessary files exist
    ensure_faq_section_files();

    // Register the block assets
    $asset_file_path = plugin_dir_path(__FILE__) . 'build/index.asset.php';
    if (file_exists($asset_file_path)) {
        $asset_file = include($asset_file_path);
    } else {
        $asset_file = [
            'dependencies' => ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n'],
            'version' => '1.0.0'
        ];
    }

    // Register the script
    wp_register_script(
        'faq-section-block-editor',
        plugins_url('build/index.js', __FILE__),
        $asset_file['dependencies'],
        $asset_file['version'],
        true
    );

    // Register the editor style
    wp_register_style(
        'faq-section-block-editor-style',
        plugins_url('build/editor.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'build/editor.css')
    );

    // Register the frontend style
    wp_register_style(
        'faq-section-block-style',
        plugins_url('build/style.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'build/style.css')
    );

    // Register the block
    register_block_type('ekwa/faq-section', array(
        'editor_script' => 'faq-section-block-editor',
        'editor_style' => 'faq-section-block-editor-style',
        'style' => 'faq-section-block-style',
    ));
}
add_action('init', 'register_faq_section_block');