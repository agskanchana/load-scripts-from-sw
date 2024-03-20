<?php
function create_block_ekwa_yoast_faq_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_ekwa_yoast_faq_init' );




//Convert Yoast FAQ question to H2 tag
function ekwa_replace_schema_faq_question_with_h2( $content ) {
    // Define the pattern to match
    $pattern = '/<strong class="schema-faq-question">(.*?)<\/strong>/';
    // Replace the pattern with <h2> tags
    $replacement = '<h2 class="schema-faq-question">$1</h2>';
    // Perform the replacement
    $content = preg_replace( $pattern, $replacement, $content );
    return $content;
}
add_filter( 'the_content', 'ekwa_replace_schema_faq_question_with_h2' );
