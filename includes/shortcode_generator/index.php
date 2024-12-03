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
echo apply_filters('the_content',$shortcode->post_content);

     return ob_get_clean();


});




/*  default post */
function create_default_shortcode_post() {

    $existing_posts = get_posts(array(
        'post_type' => 'shortcode_gen',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_is_default_shortcode',
                'value' => '1'
            )
        )
    ));

    if (empty($existing_posts)) {

        $default_content = '<!-- wp:acf/section {"name":"acf/section","data":{"field_6246d140cafac":"","field_6246d140cea01":{"title":"","url":"","target":""},"field_6246d140d2450":"auto","field_6246d140d5ee1":"center top","field_6246d140d9a50":"no-repeat","field_6246d140dd4d3":"scroll","field_6246d140e1179":"0","field_6246d140e4be3":"0","field_6246d141a5192":{"title":"","url":"","target":""},"field_6246d141a8b3b":{"title":"","url":"","target":""},"field_62c4fcc045f26":"section","field_6248528571533":".blog-cta-section{\r\n\tborder-top: 5ox solid var(\u002d\u002dcolor_one);\r\n\tpadding: 15px 10px;\r\n}\r\n.blog-cta-section h2{\r\n\tmargin-top: 0;\r\n}"},"mode":"preview","className":"blog-cta-section"} -->
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Call Our Office for More Information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">New Patients: [phone] |  Existing Patients: [phone_ex]</p>
<!-- /wp:paragraph -->

<!-- wp:acf/btn {"name":"acf/btn","data":{"field_624317dd4fc9f":"Request an Appointment","field_624317e24fca0":{"title":"","url":"","target":""},"field_6243180a0d010":"","field_62431dc4b7ef6":"0","field_635e3a8ca0159":"0"},"align":"center","mode":"preview"} /--></div>
<!-- /wp:group -->
<!-- /wp:acf/section -->';


        $default_post = array(
            'post_title'    => 'CTA with New and Existing phone numbers',
            'post_content'  => $default_content,
            'post_status'   => 'publish',
            'post_type'     => 'shortcode_gen'
        );


        $post_id = wp_insert_post($default_post);


        if ($post_id) {
            add_post_meta($post_id, '_is_default_shortcode', '1');
        }
    }
}


add_action('init', 'create_default_shortcode_post');