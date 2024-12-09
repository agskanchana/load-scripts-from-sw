<?php


function add_span_to_first_character($content) {

    if (carbon_get_theme_option('style_first_character')) {

        if (is_single() && get_post_type() === 'post' && in_the_loop() && is_main_query()) {

            $content = preg_replace_callback(
                '/^(<p>)?(\s*<[^>]+>\s*)*?(\w)/',
                function ($matches) {
                    return (isset($matches[1]) ? $matches[1] : '') .
                           (isset($matches[2]) ? $matches[2] : '') .
                           '<span class="firstcharacter">' . $matches[3] . '</span>';
                },
                $content,
                1
            );
        }
    }
    return $content;
}
add_filter('the_content', 'add_span_to_first_character');


function add_first_character_styles() {

    if (carbon_get_theme_option('style_first_character')) {


        $color = 'var(--color_one)';
        if(carbon_get_theme_option('first_letter_color')){
            $color = carbon_get_theme_option('first_letter_color');
        }

        echo '<style>
            .firstcharacter {
                    color: '.$color.';
                    float: left;
                    font-weight: bold;
                    font-size: 75px;
                    line-height: 60px;
                    padding-top: 4px;
                    padding-right: 8px;
            }
        </style>';
    }
}
add_action('wp_head', 'add_first_character_styles');

