<?php



function shortcode_gen_post_type(){
    $labels = array(
            'name' => 'CTA Shortcodes',
            'singular_name' => 'Shortcodes',
            'add_new' => 'Add New Shortcode Item'
    );

    $args = array(
                        'labels' => $labels,
                        'supports'              => array( 'title', 'editor', ),
                        'hierarchical'          => false,
                        'public'                => false,
                        'show_ui'               => true,
                        'show_in_menu'          => true,
                        'menu_position'         => 5,
                        'menu_icon'             => 'dashicons-shortcode',
                        'show_in_admin_bar'     => true,
                        'show_in_nav_menus'     => false,
                        'show_in_rest' => true,
                        'can_export'            => true,
                        'has_archive'           => false,
                        'exclude_from_search'   => true,
                        'publicly_queryable'    => false,
                        'query_var'                        => false,
                        'capability_type'       => 'post'


    );
    register_post_type( 'shortcode_gen', $args );
}

add_action( 'init', 'shortcode_gen_post_type');


add_filter('manage_shortcode_gen_posts_columns', 'shortcodes_table_head');
function shortcodes_table_head( $defaults ) {
    $defaults['shortcode']  = 'Shortcode';
    return $defaults;
}




add_action( 'manage_shortcode_gen_posts_custom_column', 'shortcode_gen_table_content', 10, 2 );

function shortcode_gen_table_content( $column_name, $post_id ) {
    if ($column_name == 'shortcode') {
        echo  '[cta id="'.$post_id.'"]';
    }

}


add_shortcode('cta',function($atts){
    ob_start();

   $post_id = $atts['id'];

$shortcode = get_post($post_id);
if($shortcode){
    echo apply_filters('the_content',$shortcode->post_content);
}
     return ob_get_clean();


});

add_action('init', function () {
    // Define the custom post type name.
    $post_type = 'shortcode_gen';

    // Query for posts with either '_is_default_shortcode' or '_is_second_default_shortcode'.
    $posts_to_delete = get_posts([
        'post_type'   => $post_type,
        'meta_query'  => [
            'relation' => 'OR', // Match either of the meta keys.
            [
                'key' => '_is_default_shortcode',
            ],
            [
                'key' => '_is_second_default_shortcode',
            ],
        ],
        'numberposts' => -1, // Fetch all matching posts.
        'fields'      => 'ids', // Retrieve only post IDs for efficiency.
    ]);

    // Loop through and delete the matching posts.
    foreach ($posts_to_delete as $post_id) {
        wp_delete_post($post_id, true); // 'true' ensures permanent deletion.
    }
});


/*  default post*/

function create_default_shortcode_posts() {

    // Check if the first default post exists
    $first_post_exists = get_posts(array(
        'post_type' => 'shortcode_gen',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_default_shortcode_one',
                'value' => '1'
            )
        )
    ));

    // If not, create the first default post
    if (empty($first_post_exists)) {
        $first_post_content = '<!-- wp:carbon-fields/blog-cta-two /-->';
        $first_post = array(
            'post_title'    => 'CTA with New and Existing phone numbers',
            'post_content'  => $first_post_content,
            'post_status'   => 'publish',
            'post_type'     => 'shortcode_gen'
        );

        $first_post_id = wp_insert_post($first_post);

        if ($first_post_id) {
            add_post_meta($first_post_id, '_default_shortcode_one', '1');
        }
    }

    // Check if the second default post exists
    $second_post_exists = get_posts(array(
        'post_type' => 'shortcode_gen',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_default_shortcode_two',
                'value' => '1'
            )
        )
    ));

    // If not, create the second default post
    if (empty($second_post_exists)) {
        $second_post_content = '<!-- wp:carbon-fields/blog-cta /-->';
        $second_post = array(
            'post_title'    => 'CTA Only with Call tracking Number',
            'post_content'  => $second_post_content,
            'post_status'   => 'publish',
            'post_type'     => 'shortcode_gen'
        );

        $second_post_id = wp_insert_post($second_post);

        if ($second_post_id) {
            add_post_meta($second_post_id, '_default_shortcode_two', '1');
        }
    }
}

add_action('init', 'create_default_shortcode_posts');
