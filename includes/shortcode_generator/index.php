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

function create_default_shortcode_posts() {

    // Check if the first default post exists
    $first_post_exists = get_posts(array(
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

    // If not, create the first default post
    if (empty($first_post_exists)) {
        $first_post_content = '<!-- wp:group {"className":"blog-cta-block-one","layout":{"type":"constrained"}} -->
<div class="wp-block-group blog-cta-block-one"><!-- wp:group {"backgroundColor":"black","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-black-background-color has-background"><!-- wp:spacer {"height":"5px"} -->
<div style="height:5px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"25px"} -->
<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Call Our Office for More Information</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">New Patients:&nbsp;[phone] | &nbsp;Existing Patients:&nbsp;[phone_ex]</p>
<!-- /wp:paragraph -->

<!-- wp:acf/btn {"name":"acf/btn","data":{"field_624317dd4fc9f":"Request an Appointment","field_624317e24fca0":{"title":"","url":"","target":""},"field_6243180a0d010":"","field_62431dc4b7ef6":"0","field_635e3a8ca0159":"0"},"align":"center","mode":"preview"} /--></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"25px"} -->
<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"backgroundColor":"black","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-black-background-color has-background"><!-- wp:spacer {"height":"5px"} -->
<div style="height:5px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->';
        $first_post = array(
            'post_title'    => 'CTA with New and Existing phone numbers',
            'post_content'  => $first_post_content,
            'post_status'   => 'publish',
            'post_type'     => 'shortcode_gen'
        );

        $first_post_id = wp_insert_post($first_post);

        if ($first_post_id) {
            add_post_meta($first_post_id, '_is_default_shortcode', '1');
        }
    }

    // Check if the second default post exists
    $second_post_exists = get_posts(array(
        'post_type' => 'shortcode_gen',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_is_second_default_shortcode',
                'value' => '1'
            )
        )
    ));

    // If not, create the second default post
    if (empty($second_post_exists)) {
        $second_post_content = '<!-- wp:group {"className":"blog-cta-two","layout":{"type":"constrained","contentSize":"750px"}} -->
<div class="wp-block-group blog-cta-two"><!-- wp:group {"layout":{"type":"constrained","contentSize":""}} -->
<div class="wp-block-group"><!-- wp:group {"style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"pale-blue","textColor":"white","layout":{"type":"constrained","contentSize":"500px"}} -->
<div class="wp-block-group has-white-color has-pale-blue-background-color has-text-color has-background has-link-color"><!-- wp:spacer {"height":"25px"} -->
<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"align":"center","className":"is-style-default","backgroundColor":"white"} -->
<hr class="wp-block-separator aligncenter has-text-color has-white-color has-alpha-channel-opacity has-white-background-color has-background is-style-default"/>
<!-- /wp:separator -->

<!-- wp:spacer {"height":"20px"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">CALL OUR OFFICE TODAY at <strong>[phone] </strong>OR</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"10px"} -->
<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:acf/btn {"name":"acf/btn","data":{"text":"Request an Appointment","_text":"field_624317dd4fc9f","link":"","_link":"field_624317e24fca0","icon":"","_icon":"field_6243180a0d010","phone_number":"0","_phone_number":"field_62431dc4b7ef6","custom_attribute":"0","_custom_attribute":"field_635e3a8ca0159"},"align":"center","mode":"preview"} /-->

<!-- wp:spacer {"height":"20px"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"align":"center","className":"is-style-default","backgroundColor":"white"} -->
<hr class="wp-block-separator aligncenter has-text-color has-white-color has-alpha-channel-opacity has-white-background-color has-background is-style-default"/>
<!-- /wp:separator -->

<!-- wp:spacer {"height":"25px"} -->
<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->';
        $second_post = array(
            'post_title'    => 'CTA Only with Call tracking Numer',
            'post_content'  => $second_post_content,
            'post_status'   => 'publish',
            'post_type'     => 'shortcode_gen'
        );

        $second_post_id = wp_insert_post($second_post);

        if ($second_post_id) {
            add_post_meta($second_post_id, '_is_second_default_shortcode', '1');
        }
    }
}

add_action('init', 'create_default_shortcode_posts');
