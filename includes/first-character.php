<?php



function add_inline_first_character_script() {
    if (carbon_get_theme_option('style_first_character')) {
    if (is_single() && get_post_type() === 'post') {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select the first paragraph of the main content
            const content = document.querySelector('.blog-content'); // Adjust selector if needed
            if (content) {
                const firstChild = content.querySelector('p');
                if (firstChild) {
                    const text = firstChild.textContent.trim();
                    if (text.length > 0) {
                        // Wrap the first letter in a span
                        firstChild.innerHTML = `<span class="firstcharacter">${text.charAt(0)}</span>${text.slice(1)}`;
                    }
                }
            }
        });
        </script>
        <?php
        }
    }
}
add_action('wp_footer', 'add_inline_first_character_script');


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

