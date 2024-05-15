<?php
/*
Plugin Name: Ekwa Settings
Plugin URI: www.ekwa.com
Description: Loading theird party scripts from service worker, add Progressive web app
Author URI: www.sameera.com
Version: 1.1.7

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
  use Carbon_Fields\Block;

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

    ) )

    ->add_tab( __( 'Progressive web app' ), array(

        Field::make( 'checkbox', 'pwa_disable', 'Disable' ),
        Field::make( 'text', 'name', __( 'Name' ) ),
        Field::make( 'text', 'short_name', __( 'Short Name' ) ),
        Field::make( 'color', 'background_color', __( 'Background Color' ) ),
        Field::make( 'color', 'theme_color', __( 'Theme Color' ) ),
        Field::make( 'image', 'icon_512', __( 'Icon  Size: 512px' ) )
            ->set_value_type( 'url' ),
        Field::make( 'image', 'icon_192', __( 'Icon  Size: 192px' ) )
        ->set_value_type( 'url' ),
        Field::make( 'image', 'icon_144', __( 'Icon  Size: 144px' ) )
            ->set_value_type( 'url' ),


    ) );






Block::make( __( 'Ekwa FAQ Collapse' ) )

->add_fields( array(

    Field::make( 'checkbox', 'enable_collapse', __( 'Enable Collapse' ) )
    ->set_option_value( 'yes' )
    ->set_default_value(true),

    Field::make( 'complex', 'faq_collapes', __( 'FAQ Collapse' ) )
    ->add_fields( array(
        Field::make( 'text', 'faq_question', __( 'Question' ) ),
        Field::make( 'rich_text', 'faq_answer', __( 'Answer' ) ),
    ) ),

    Field::make( 'color', 'ekwa_faq_title_bg', __( 'Title Background ' ) )
    ->set_default_value('#000000'),
    Field::make( 'color', 'ekwa_faq_title_color', __( 'Title Text color ' ) )
    ->set_default_value('#ffffff'),
    Field::make( 'color', 'ekwa_faq_content_bg', __( 'Content Background ' ) )
    ->set_default_value('#ffffff'),
    Field::make( 'color', 'ekwa_faq_content_text', __( 'Content Text color ' ) )
    ->set_default_value('#000000')

) )
->set_mode( 'edit' )
->set_render_callback( function ( $fields, $attributes, $inner_blocks, $post_id ) {

if($fields['faq_collapes']):
 $uniq_id = 'ekwa-'.uniqid().'-collapse';
 $uniq_id_qs = 'faq_'.uniqid().'_question';
?>
<?php //var_dump($post_id);?>
<div class="ekwa-faq-block" id="<?php echo $uniq_id;?>">
    <?php foreach($fields['faq_collapes'] as $key => $faq_item ):?>
    <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" id="ekwa-faq-<?php echo uniqid();?>-item" class="ekwa-faq-item <?php if($key == 0){echo 'active';}?>">
        <h2  itemprop="name" id="<?php echo $uniq_id_qs;?>" class="ekwa-faq-question"><?php  echo $faq_item['faq_question'];?></h2>
        <div class="ekwa-faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <div itemprop="text">
                <?php echo $faq_item['faq_answer'];?>
            </div>
        </div>
    </div>
    <?php endforeach;?>
</div>
<?php if($fields['enable_collapse']):?>
<style>
    #<?php echo $uniq_id;?> .ekwa-faq-item {
	background: <?php if($fields['ekwa_faq_content_bg']){echo $fields['ekwa_faq_content_bg'];}else{echo '#fff';}?>;
	margin-bottom: 15px;
    color: <?php if($fields['ekwa_faq_content_text']){echo $fields['ekwa_faq_content_text'];}else{echo '#000';}?>;
}
#<?php echo $uniq_id;?> .ekwa-faq-question {
	color: <?php if($fields['ekwa_faq_title_color']){echo $fields['ekwa_faq_title_color'];}else{echo '#ffffff';}?>;
	display: block;
	cursor: pointer;
	font-size: 16px;
	padding: 15px 20px;
	background: <?php if($fields['ekwa_faq_title_bg']){echo $fields['ekwa_faq_title_bg'];}else{echo '#000';}?>;
	font-weight: normal;
}
#<?php echo $uniq_id;?> .ekwa-faq-item.active .ekwa-faq-question:before {
	content: "\f106";
}
#<?php echo $uniq_id;?> .ekwa-faq-item .ekwa-faq-question:before {
	float: right;
	line-height: 1;
	font-size: 21px;
	content: "\f107";
	font-weight: 600;
	font-family: "Font Awesome 5 Free";
}
#<?php echo $uniq_id;?> .ekwa-faq-item  .ekwa-faq-question:after {
	clear: both;
	content: '';
	display: block;
}
#<?php echo $uniq_id;?> .ekwa-faq-answer {
	padding: 25px;
	display: none;
	margin-bottom: 0;
}
#<?php echo $uniq_id;?> .ekwa-faq-item.active .ekwa-faq-answer {
	display: block;
}
</style>
<?php endif;?>
<script>

var htmlAttribute = document.querySelector('html');
// console.log(htmlAttribute);
 htmlAttribute.setAttribute("itemscope", " ");
htmlAttribute.setAttribute("itemtype", "https://schema.org/FAQPage");

<?php if($fields['enable_collapse']):?>
// add FAQ active class on click





/*
let $clickFaq = document.getElementsByClassName('ekwa-faq-question');

for (let i = 0; i < $clickFaq.length; i++) {
	$clickFaq[i].addEventListener('click', function(e) {
		var $items = document.querySelectorAll('.ekwa-faq-item');
		[].forEach.call($items, function(el) {
			el.classList.remove('active');
		});

		let $elem = this.closest('.ekwa-faq-item');
		let $elemId = $elem.getAttribute('id');
		let $item = document.getElementById($elemId);
		$item.classList.add('active');
	});
}
*/

<?php endif;?>
</script>

    <?php

    endif;
} );





}


function show_pwa_on_header(){
    if(!carbon_get_theme_option('pwa_disable')){
        ?>

<link rel="manifest" href="<?php echo plugin_dir_url( __FILE__ ); ?>manifest.json">
<meta name="msapplication-TileColor" content="<?php echo carbon_get_theme_option('background_color'); ?>">
<meta name="msapplication-TileImage" content="<?php echo carbon_get_theme_option('icon_144'); ?>">
<meta name="theme-color" content="<?php echo carbon_get_theme_option('theme_color'); ?>">
<script>
// Check if service worker is available.
<?php
$cachName = get_option('siteurl');
$cachName = preg_replace('#^https?://#i', '', $cachName);

?>
const cacheName = '<?php echo $cachName;?>-pwa-2.2.5';
const startPage = '<?php echo get_option('siteurl');?>';
const offlinePage = '<?php echo get_option('siteurl');?>';
const filesToCache = [startPage, offlinePage];
const neverCacheUrls = [/\/wp-admin/,/\/wp-login/,/preview=true/];


if ('serviceWorker' in navigator) {

navigator.serviceWorker.register('<?php echo plugin_dir_url( __FILE__ ); ?>js/pwa-sw.js').then(function(registration) {
console.log('SW registration succeeded with scope:', registration.scope);
}).catch(function(e) {
console.log('SW registration failed with error:', e);
});
}
</script>
        <?php
    }


}
add_action( 'wp_head',  'show_pwa_on_header', 1 );

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


  function option_pages_on_save(){



   $json_content =  '{
        "name": "'.carbon_get_theme_option('name').'",
        "short_name": "'.carbon_get_theme_option('short_name').'",
        "icons": [
            { "src": "'.carbon_get_theme_option('icon_512').'", "sizes": "512x512", "type": "image/png", "purpose": "any" },
            { "src": "'.carbon_get_theme_option('icon_192').'", "sizes": "192x192", "type": "image/png", "purpose": "maskable" }
        ],
        "background_color": "'.carbon_get_theme_option('background_color').'",
        "theme_color": "'.carbon_get_theme_option('theme_color').'",
        "display": "standalone",
        "orientation": "portrait",
        "start_url": "'.get_option('siteurl').'",
        "scope": "/"
    }';

    // Generate json file
    if(file_put_contents(plugin_dir_path( __FILE__ )."manifest.json", $json_content)){
        echo "worked";
    }else{

            echo "didnt work";

    }

// var_dump($_POST);
    // exit();
    ?>
    <script>
        console.log('its working');
    </script>
    <?php
  }
//   add_action('carbon_fields_theme_options_container_saved', 'option_pages_on_save');
  add_action( 'carbon_fields_theme_options_container_saved', 'option_pages_on_save' );




include('yoast-faq-block/ekwa-yoast-faq.php');

function faq_init() {
    ?>
    <script>
let $clickFaq = document.getElementsByClassName('ekwa-faq-block');

if($clickFaq.length > 0){


    Array.prototype.forEach.call($clickFaq , function(el) {
    // Write your code here
     let FaqItemID =  el.getAttribute("id");
      FaqItemID = document.getElementById(FaqItemID);

     let faqQuestions =  FaqItemID.querySelectorAll('.ekwa-faq-question');


for (let i = 0; i < faqQuestions.length; i++) {
	faqQuestions[i].addEventListener('click', function(e) {
		var $items = FaqItemID.querySelectorAll('.ekwa-faq-item');
		[].forEach.call($items, function(el) {
			el.classList.remove('active');
		});

		let $elem = this.closest('.ekwa-faq-item');
		let $elemId = $elem.getAttribute('id');
		let $item = document.getElementById($elemId);
		$item.classList.add('active');
	});
}


})


}
    </script>
    <?php }
    add_action('wp_footer', 'faq_init');