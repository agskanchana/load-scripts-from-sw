<?php
/**
 * Ekwa FAQ Collapse Block
 *
 * Contains the FAQ collapse block functionality
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Carbon_Fields\Block;
use Carbon_Fields\Field;

/**
 * Register the FAQ collapse block
 */
function register_faq_collapse_block() {
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

            $additional_class = '';

            if(isset($attributes['className'])){
                $additional_class = $attributes['className'];
            }
        ?>
        <div class="ekwa-faq-block <?php echo $additional_class;?>" id="<?php echo $uniq_id;?>">
            <?php foreach($fields['faq_collapes'] as $key => $faq_item ):?>
            <div itemscope itemprop="mainEntity" itemtype="https://schema.org/Question" id="ekwa-faq-<?php echo uniqid();?>-item" class="ekwa-faq-item <?php if($key == 0){echo 'active';}?>">
                <h2 itemprop="name" id="<?php echo $uniq_id_qs;?>" class="ekwa-faq-question"><?php echo $faq_item['faq_question'];?></h2>
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
            #<?php echo $uniq_id;?> .ekwa-faq-item .ekwa-faq-question:after {
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
            htmlAttribute.setAttribute("itemscope", " ");
            htmlAttribute.setAttribute("itemtype", "https://schema.org/FAQPage");
        </script>
        <?php
        endif;
    } );
}

/**
 * Initialize FAQ scripts
 */
function faq_init_scripts() {
    ?>
    <script>
    let $clickFaq = document.getElementsByClassName('ekwa-faq-block');

    if($clickFaq.length > 0){
        Array.prototype.forEach.call($clickFaq, function(el) {
            let FaqItemID = el.getAttribute("id");
            FaqItemID = document.getElementById(FaqItemID);
            let faqQuestions = FaqItemID.querySelectorAll('.ekwa-faq-question');

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
        });
    }
    </script>
    <?php
}

// Register hooks
add_action('carbon_fields_register_fields', 'register_faq_collapse_block');
add_action('wp_footer', 'faq_init_scripts');