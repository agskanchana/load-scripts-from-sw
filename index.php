<?php
/*
Plugin Name: Ekwa Settings
Plugin URI: www.ekwa.com
Description: Loading theird party scripts from service worker, add Progressive web app
Author URI: www.sameera.com
Version: 1.0.3

*/

require 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/agskanchana/load-scripts-from-sw/',
	__FILE__,
	'load-scripts-from-sw'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

//Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('your-token-here');


if( !function_exists('carbon_fields_boot_plugin')){
    include_once( plugin_dir_path( __FILE__ ) . 'includes/carbon-fields/carbon-fields-plugin.php' );
  }


  use Carbon_Fields\Container;
  use Carbon_Fields\Field;


  add_action( 'carbon_fields_register_fields', 'service_worker_fields' );
  function service_worker_fields() {

    Container::make( 'theme_options', __( 'Ekwa Settings' ) )
    ->set_icon( 'https://www.ekwa-testbench.info/logo-1.png' )
    ->add_tab( __( 'Google analytic' ), array(
        Field::make( 'text', 'measurement_id', __( 'Measurement ID' ) ),

        Field::make( 'select', 'method', __( 'Method' ) )
        ->set_options( array(
            'Service Worker' => 'Service Worker',
            'Mouse moment' => 'Mouse moment',
            'General' => 'General',
        ) ),
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
        Field::make( 'select', 'fb_method', __( 'Method' ) )
        ->set_options( array(
            'Service Worker' => 'Service Worker',
            'Mouse moment' => 'Mouse moment',
            'General' => 'General',
        ) ),

        Field::make( 'html', 'fb_pixel_msg' )
        ->set_html( '<h2>Put the script without Script tags</h2>' ),
        Field::make( 'textarea', 'fb_pixel_code', __( 'Fb Pixel code' ) )

    ) );



}


function partytown_configuration() {
    if(carbon_get_theme_option('method') == 'Service Worker'){

	$config = array(
		'lib' => str_replace( site_url(), '', plugin_dir_url( __FILE__ ) ) . 'js/partytown/',
	);


	$config = apply_filters( 'partytown_configuration', $config );

	?>
	<script>
		window.partytown = <?php echo wp_json_encode( $config ); ?>;
        /* party town js*/
        !function(t,e,n,i,r,o,a,d,s,c,l,p){function u(){p||(p=1,"/"==(a=(o.lib||"/~partytown/")+(o.debug?"debug/":""))[0]&&(s=e.querySelectorAll('script[type="text/partytown"]'),i!=t?i.dispatchEvent(new CustomEvent("pt1",{detail:t})):(d=setTimeout(f,1e4),e.addEventListener("pt0",w),r?h(1):n.serviceWorker?n.serviceWorker.register(a+(o.swPath||"partytown-sw.js"),{scope:a}).then((function(t){t.active?h():t.installing&&t.installing.addEventListener("statechange",(function(t){"activated"==t.target.state&&h()}))}),console.error):f())))}function h(t){c=e.createElement(t?"script":"iframe"),t||(c.setAttribute("style","display:block;width:0;height:0;border:0;visibility:hidden"),c.setAttribute("aria-hidden",!0)),c.src=a+"partytown-"+(t?"atomics.js?v=0.8.0":"sandbox-sw.html?"+Date.now()),e.querySelector(o.sandboxParent||"body").appendChild(c)}function f(n,r){for(w(),i==t&&(o.forward||[]).map((function(e){delete t[e.split(".")[0]]})),n=0;n<s.length;n++)(r=e.createElement("script")).innerHTML=s[n].innerHTML,e.head.appendChild(r);c&&c.parentNode.removeChild(c)}function w(){clearTimeout(d)}o=t.partytown||{},i==t&&(o.forward||[]).map((function(e){l=t,e.split(".").map((function(e,n,i){l=l[i[n]]=n+1<i.length?"push"==i[n+1]?[]:l[i[n]]||{}:function(){(t._ptf=t._ptf||[]).push(i,arguments)}}))})),"complete"==e.readyState?u():(t.addEventListener("DOMContentLoaded",u),t.addEventListener("load",u))}(window,document,navigator,top,window.crossOriginIsolated);
	</script>
	<?php
    }
}

add_action( 'wp_head',  'partytown_configuration', 1 );

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
             if(carbon_get_theme_option('method') == 'Service Worker'){

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
            if(carbon_get_theme_option('method') == 'Mouse moment'){
                ?>

                <?php
            }
            if(carbon_get_theme_option('method') == 'General'){
                ?>

                <script   async src="https://www.googletagmanager.com/gtag/js?id=<?php echo carbon_get_theme_option('measurement_id');?>"></script>
                <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '<?php echo carbon_get_theme_option('measurement_id');?>');
                </script>
                <?php
            }
        }

    }

    if(carbon_get_theme_option('fb_pixel_code')){
        if(carbon_get_theme_option('fb_method') == 'Service Worker'){
        ?>
        <script  type="text/partytown">
            <?php echo carbon_get_theme_option('fb_pixel_code');?>
        </script>

        <?php
        }
        if(carbon_get_theme_option('fb_method') == 'Mouse moment'){
            ?>
           <?php
        }
        if(carbon_get_theme_option('fb_method') == 'General'){
            ?>
        <script>
            <?php echo carbon_get_theme_option('fb_pixel_code');?>
        </script>
           <?php
        }

    }


  }

  add_action('wp_footer', 'print_tracking_codes');