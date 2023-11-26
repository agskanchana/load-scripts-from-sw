<?php
/*
Plugin Name: Loading scripts from service worker
Plugin URI: www.ekwa.com
Description: Loading theird party scripts from service worker
Author URI: www.sameera.com
Version: 1.0

*/


if( !function_exists('carbon_fields_boot_plugin')){
    include_once( plugin_dir_path( __FILE__ ) . 'carbon-fields/carbon-fields-plugin.php' );
  }


  use Carbon_Fields\Container;
  use Carbon_Fields\Field;


  add_action( 'carbon_fields_register_fields', 'service_worker_fields' );
  function service_worker_fields() {

    Container::make( 'theme_options', __( 'External Scripts' ) )
    ->add_tab( __( 'Google analytic' ), array(
        Field::make( 'text', 'measurement_id', __( 'Measurement ID' ) ),


        Field::make( 'checkbox', 'ga_show_custom', 'Custom' ),




        Field::make( 'text', 'ga_script_link', 'Script Link' )
            ->set_conditional_logic( array(
                    array(
                        'field' => 'ga_show_custom',
                        'value' => true,
                    )
            ) ),


            Field::make( 'html', 'ga_custom_message' )
            ->set_html( '<h2>Put the script without Script tags</h2>' )
            ->set_conditional_logic( array(
                array(
                    'field' => 'ga_show_custom',
                    'value' => true,
                )
            ) ),

        Field::make( 'textarea', 'ga_custom_script', 'Custom Script' )
        ->set_conditional_logic( array(
                array(
                    'field' => 'ga_show_custom',
                    'value' => true,
                )
        ) ),


        /* end */
    ) )
    ->add_tab( __( 'Facebook pixel' ), array(
        Field::make( 'text', 'fb_no_script', __( 'No script Image Source' ) ),
        Field::make( 'html', 'fb_pixel_msg' )
        ->set_html( '<h2>Put the script without Script tags</h2>' ),
        Field::make( 'textarea', 'fb_pixel_code', __( 'Fb Pixel code' ) )

    ) );



}



function print_tracking_codes_in_header(){
    if(carbon_get_theme_option('fb_no_script')){
        ?>
        <noscript>
            <img height="1" width="1" style="display:none" src="<?php echo carbon_get_theme_option('fb_no_script');?>"/>
        </noscript>
        <?php
    }
}
add_action('wp_head', 'print_tracking_codes_in_header');

function print_tracking_codes(){

    if(carbon_get_theme_option('ga_show_custom')){
        if(carbon_get_theme_option('ga_script_link')){
            ?>
                <script  type="text/partytown" src="<?php echo carbon_get_theme_option('ga_script_link');?>"></script>
            <?php
        }
        if(carbon_get_theme_option('ga_custom_script')){
            ?>
                <script type="text/partytown">
                    <?php echo  carbon_get_theme_option('ga_custom_script');?>
                </script>
            <?php
        }

    }else{
        if(carbon_get_theme_option('measurement_id')){
            ?>
                <script  type="text/partytown" async src="https://www.googletagmanager.com/gtag/js?id=<?php echo carbon_get_theme_option('measurement_id');?>"></script>
                <script  type="text/partytown">
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '<?php echo carbon_get_theme_option('measurement_id');?>');
                </script>
            <?php
        }

    }

    if(carbon_get_theme_option('fb_pixel_code')){
        ?>
        <script  type="text/partytown">
            <?php echo carbon_get_theme_option('fb_pixel_code');?>
        </script>
        <?php
    }


  }

  add_action('wp_footer', 'print_tracking_codes');