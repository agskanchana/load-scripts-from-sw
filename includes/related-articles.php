<?php
// Add this code to your theme's functions.php or a custom plugin



function show_related_articles(){
    $article_method = carbon_get_theme_option('related_article_display');
    if(isset($article_method) && $article_method == 'auto_load'):

    // Get current page/post slug
    $current_slug = get_post_field('post_name', get_post());

    // Initialize output variable
    $output = '';

    // Check if we're on the front page
    if (is_front_page()) {
        $category_slug = 'featured-articles';
    } else {
        // For other pages, use the current page slug
        $category_slug = $current_slug;
    }

    // Setup query arguments
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $category_slug
            )
        )
    );

    // Run the query
    $posts_query = new WP_Query($args);

    if ($posts_query->have_posts()) {
        $post_count = $posts_query->post_count;
        $heading_text = '';

        if (is_front_page()) {
            if ($post_count > 1) {
            $heading_text = 'Featured Articles';
            } else {
            $heading_text = 'Featured Article';
            }
        } else {
            if ($post_count > 1) {
            $heading_text = 'Related Articles';
            } else {
            $heading_text = 'Related Article';
            }
        }
        ?>
       <div class="ek-article-carousel-container">
        <h2 class="related-articles-heading"><?php echo $heading_text; ?></h2>
        <div class="ek-article-carousel-wrapper">
    <?php
        while ($posts_query->have_posts()) {
            $posts_query->the_post();
            ?>
            <div class="ek-article-carousel-item">
            <div class="ac-item-wrapper articles-item">
           <div class="article-col">
                        <div class="single-article">
                            <div class="img-holder">
                                <?php
                                 if (has_post_thumbnail()) {
                                    $thumbnail_id = get_post_thumbnail_id(get_the_ID());
                                    $image_data = wp_get_attachment_image_src($thumbnail_id, 'full');
                                    $image_url = $image_data[0];
                                    $image_width = $image_data[1];
                                    $image_height = $image_data[2];
                                    $image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                                    ?>
                                    <img class="lazyload" data-src="<?php echo $image_url;?>" alt="<?php echo $image_alt;?>" width="<?php echo $image_width;?>" height="<?php echo $image_height;?>">
                                    <?php
                                 }
                                ?>
                            </div>

                            <div class="text-holder">
                                <div class="meta-box">
                                    <div class="author">
                                        <a href="<?php echo get_page_slug_by_id(get_theme_mod('author_page'));?>">By <?php the_author(); ?></a>
                                    </div>
                                    <div class="date">
                                        <div class="icon-box">
                                            <svg width="20px" height="20px" viewBox="0 0 25.135 23.336" xmlns="http://www.w3.org/2000/svg"><switch transform="translate(-.662 -1.56) scale(.26458)"><g><path d="M86.8 13.2H72.9v-4c0-1.8-1.5-3.3-3.3-3.3s-3.3 1.5-3.3 3.3v4H33.8v-4c0-1.8-1.5-3.3-3.3-3.3s-3.3 1.5-3.3 3.3v4h-14C7.3 13.2 2.5 18 2.5 23.8v59.6c0 5.9 4.8 10.7 10.7 10.7h73.6c5.9 0 10.7-4.8 10.7-10.7V23.8c0-5.8-4.8-10.6-10.7-10.6zm4.1 70.2c0 2.2-1.8 4-4.1 4H13.2c-2.2 0-4.1-1.8-4.1-4V41h81.7v42.4zm0-49.1H9.1V23.8c0-2.2 1.8-4 4.1-4h13.9v4c0 1.8 1.5 3.3 3.3 3.3s3.3-1.5 3.3-3.3v-4h32.5v4c0 1.8 1.5 3.3 3.3 3.3s3.3-1.5 3.3-3.3v-4h13.9c2.2 0 4.1 1.8 4.1 4v10.5z"></path><path d="M24.7 61h7.7c.9 0 1.7-.8 1.7-1.7v-5.5c0-.9-.8-1.7-1.7-1.7h-7.7c-.9 0-1.7.8-1.7 1.7v5.5c-.1.9.7 1.7 1.7 1.7zM46.2 61h7.7c.9 0 1.7-.8 1.7-1.7v-5.5c0-.9-.8-1.7-1.7-1.7h-7.7c-.9 0-1.7.8-1.7 1.7v5.5c-.1.9.7 1.7 1.7 1.7zM67.6 61h7.7c.9 0 1.7-.8 1.7-1.7v-5.5c0-.9-.8-1.7-1.7-1.7h-7.7c-.9 0-1.7.8-1.7 1.7v5.5c0 .9.8 1.7 1.7 1.7zM24.7 76.6h7.7c.9 0 1.7-.8 1.7-1.7v-5.5c0-.9-.8-1.7-1.7-1.7h-7.7c-.9 0-1.7.8-1.7 1.7v5.5c-.1.9.7 1.7 1.7 1.7zM46.2 76.6h7.7c.9 0 1.7-.8 1.7-1.7v-5.5c0-.9-.8-1.7-1.7-1.7h-7.7c-.9 0-1.7.8-1.7 1.7v5.5c-.1.9.7 1.7 1.7 1.7zM67.6 76.6h7.7c.9 0 1.7-.8 1.7-1.7v-5.5c0-.9-.8-1.7-1.7-1.7h-7.7c-.9 0-1.7.8-1.7 1.7v5.5c0 .9.8 1.7 1.7 1.7z"></path></g></switch></svg>
                                        </div>
                                        <?php echo get_the_date('F jS, Y');?>                               </div>
                                </div>

                                <h2 class="article-title">
                                    <a href="<?php echo get_permalink();?>"><?php echo get_the_title();?></a>
                                </h2>

                                <div class="article-desc">
                                <?php echo limit_content(18);?>
                                    </div>

                                <div class="read-more-btn">
                                    <a href="<?php echo get_permalink();?>">
                                    <div class="icon-box">
                                        <svg width="32px" height="10px" viewBox="0 0 16.933 6.351" xmlns="http://www.w3.org/2000/svg"><path d="M13.493.006a.79.79 0 00-.53 1.382l-.001.002 1.108.997H.793a.794.794 0 100 1.588H14.07l-1.108.997.002.003a.79.79 0 00-.265.588c0 .438.356.794.794.794.204 0 .389-.08.53-.207l.001.002 2.646-2.38-.002-.003c.161-.145.265-.354.265-.588s-.104-.442-.265-.588l.002-.002L14.024.21l-.002.002a.79.79 0 00-.529-.206z"></path></svg>
                                    </div>
                                    Continue Reading
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </div>

            <?php
            }
        ?>
        </div>
        <button class="ek-article-carousel-nav ek-article-carousel-prev">←</button>
        <button class="ek-article-carousel-nav ek-article-carousel-next">→</button>
      </div>
        <?php
        // Reset post data
        wp_reset_postdata();
    } else {
        $output .= '';
    }

    // echo $output;
    endif;
}
// add the action
add_action( "get_footer", "show_related_articles" , 10, 2);

function custom_category_posts_shortcode($atts) {
    // Get current page/post slug
    $current_slug = get_post_field('post_name', get_post());

    // Initialize output variable
    $output = '';

    // Check if we're on the front page
    if (is_front_page()) {
        $category_slug = 'featured-articles';
    } else {
        // For other pages, use the current page slug
        $category_slug = $current_slug;
    }

    // Setup query arguments
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $category_slug
            )
        )
    );

    // Run the query
    $posts_query = new WP_Query($args);

    if ($posts_query->have_posts()) {
        $output .= '<div class="category-posts-container">';

        while ($posts_query->have_posts()) {
            $posts_query->the_post();

            $output .= '<article class="category-post">';

            // Add featured image if exists
            if (has_post_thumbnail()) {
                $output .= '<div class="post-thumbnail">';
                $output .= get_the_post_thumbnail(get_the_ID(), 'medium');
                $output .= '</div>';
            }

            // Post title
            $output .= '<h2 class="post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';

            // Post excerpt
            $output .= '<div class="post-excerpt">' . get_the_excerpt() . '</div>';

            // Read more link
            $output .= '<a href="' . get_permalink() . '" class="read-more">Read More</a>';

            $output .= '</article>';
        }

        $output .= '</div>';

        // Reset post data
        wp_reset_postdata();
    } else {
        $output .= '<p>No posts found in this category.</p>';
    }

    return $output;
}
add_shortcode('category_posts', 'custom_category_posts_shortcode');

// Add some basic CSS
function add_category_posts_styles() {
    $color_one = 'var(--color_one)';
    if(carbon_get_theme_option('article_color_one')){
        $color_one = carbon_get_theme_option('article_color_one');
    }
    $color_two = 'var(--color_two)';
    if(carbon_get_theme_option('article_color_two')){
        $color_two = carbon_get_theme_option('article_color_two');
    }
    ?>
    <style>

.related-articles-heading {
    padding-left: 10px;
}
.article-col{
margin-bottom: 30px;
}
.single-article .img-holder{
position: relative;
display: block;
overflow: hidden;
}
.single-article .img-holder:after{
position: absolute;
top: 0;
left: 0;
width: 100%;
height: 100%;
content: "";
background: rgba(0, 0, 0, 0.65);
transform: scaleX(0);
transition: .5s ease;
}
.single-article .img-holder img{
transition: all 0.5s ease-in-out 0.6s;
width: 100%;
height: auto;
max-width: 100%;
border: none;
-webkit-border-radius: 0;
border-radius: 0;
-webkit-box-shadow: none;
box-shadow: none;
}
.single-article .text-holder {
position: relative;
display: block;
border: 1px solid #eeeeee;
padding: 30px 30px 22px;
color: #000;
}
.single-article .text-holder .meta-box{
position: relative;
}
.single-article .text-holder .meta-box .author{
position: absolute;
top: -42px;
right: 0;
}
.single-article .text-holder .meta-box .author a{
background: <?php echo $color_one;?>;
color: #fff;
padding: 12px 20px 12px;
font-size: 12px;
line-height: 13px;
font-weight: 400;
text-transform: uppercase;
transition: all 500ms ease;
border-radius: 6px;
letter-spacing: 1px;
}
.single-article .text-holder .meta-box .date .icon-box, .blog-role .single-article .text-holder .read-more-btn .icon-box{
display: inline;
}
.single-article .text-holder .meta-box .date .icon-box > svg{
height: 20px;
width: 20px;
fill: #000;
margin-top: -4px;
margin-right: 4px;
}
.single-article .text-holder .article-title{
margin-top: 15px;
margin-bottom: 20px;
font-size: 20px;
font-weight: 700;
width: 100%;
/* white-space: nowrap; */
/* overflow: hidden !important; */
/* text-overflow: ellipsis; */
}
.single-article .text-holder .article-title a{
color: <?php echo $color_one;?>;
transition: color 500ms ease;
}
.single-article .text-holder .article-desc{
margin-bottom: 20px;
}
.single-article .text-holder .read-more-btn .icon-box > svg{
height: 10px;
width: 32px;
fill: <?php echo $color_one;?>;
margin-top: -4px;
margin-right: 4px;
transition: fill 500ms ease;
}
.single-article .text-holder .read-more-btn a{
color: <?php echo $color_one;?>;
font-size: 13px;
font-weight: 800;
text-transform: uppercase;
transition: color 500ms ease;
}
.single-article:hover .img-holder:after{
transform: scaleY(1);
transition: .5s ease;
}
.single-article:hover .img-holder img{
transform: scale(1.2, 1.2);
}
.single-article:hover .meta-box .author a{
background: <?php echo $color_two;?>;
}
.single-article:hover .read-more-btn a, .blog-role .single-article .article-title a:hover{
color:<?php echo $color_two;?>;
}
.single-article:hover .read-more-btn .icon-box > svg{
fill: <?php echo $color_two;?>;
}


.invisible{
visibility: visible !important;
opacity: 1;
}

@media screen and (max-width: 835px){
.article-col{
    margin-bottom: 20px;
}
}

.ek-article-carousel-container {
    position: relative;
    width: 100%;
    margin: 0 auto;
    overflow: hidden;
    max-width: 1200px;
}

.ek-article-carousel-wrapper {
    display: flex;
    transition: transform 0.3s ease-in-out;
    width: 100%;
    /* gap: 25px; */
}

.ek-article-carousel-item {
    flex: 0 0 auto;
    padding: 10px;
    box-sizing: border-box;
}

.ek-article-carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: none;
    border-radius: 50%;
}

.ek-article-carousel-prev {
    left: 10px;
}

.ek-article-carousel-next {
    right: 10px;
}
    </style>

<script>
        class Carousel {
            constructor(container, config) {
                this.container = container;
                this.config = config;
                this.wrapper = container.querySelector('.ek-article-carousel-wrapper');
                this.items = container.querySelectorAll('.ek-article-carousel-item');
                this.prevBtn = container.querySelector('.ek-article-carousel-prev');
                this.nextBtn = container.querySelector('.ek-article-carousel-next');

                this.currentIndex = 0;
                this.totalItems = this.items.length;

                this.init();
            }

            init() {
                this.updateItemsPerView();
                this.addEventListeners();
                window.addEventListener('resize', () => this.updateItemsPerView());
            }

            updateItemsPerView() {
                const viewportWidth = window.innerWidth;

                // Find the appropriate breakpoint
                const activeBreakpoint = Object.entries(this.config.breakpoints)
                    .sort(([a], [b]) => b - a) // Sort breakpoints in descending order
                    .find(([breakpoint]) => viewportWidth >= breakpoint);

                this.itemsPerView = activeBreakpoint ? activeBreakpoint[1] : 1;

                // Update items width based on items per view
                const itemWidth = 100 / this.itemsPerView;
                this.items.forEach(item => {
                    item.style.width = `${itemWidth}%`;
                });

                this.maxIndex = Math.max(0, this.totalItems - this.itemsPerView);
                this.slideToIndex(Math.min(this.currentIndex, this.maxIndex));
            }

            addEventListeners() {
                this.prevBtn.addEventListener('click', () => this.prev());
                this.nextBtn.addEventListener('click', () => this.next());
            }

            prev() {
                this.slideToIndex(Math.max(0, this.currentIndex - 1));
            }

            next() {
                this.slideToIndex(Math.min(this.maxIndex, this.currentIndex + 1));
            }

            slideToIndex(index) {
                this.currentIndex = index;
                const itemWidth = 100 / this.itemsPerView;
                const translateX = -index * itemWidth;
                this.wrapper.style.transform = `translateX(${translateX}%)`;

                // Update button states
                this.prevBtn.style.display = this.currentIndex <= 0 ? 'none' : 'flex';
                this.nextBtn.style.display = this.currentIndex >= this.maxIndex ? 'none' : 'flex';
            }
        }

        // Configure your breakpoints and items per view here
        const carouselConfig = {
            breakpoints: {
                1400: 3,    // 3 items above 1400px
                1200: 3,    // 3 items between 1200px and 1399px
                992: 3,     // 3 items between 992px and 1199px
                768: 2,     // 2 items between 768px and 991px
                320: 1      // 1 item below 768px
            }
        };

        // Initialize the carousel with config
        const carouselContainer = document.querySelector('.ek-article-carousel-container');
        new Carousel(carouselContainer, carouselConfig);
    </script>
    <?php
}
add_action('wp_footer', 'add_category_posts_styles');