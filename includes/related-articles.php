<?php

function has_related_posts() {

    if (is_front_page()) {
        $category_slug = 'featured-articles';
    } else {
        global $post;
        if ($post) {
            $category_slug = $post->post_name;
        } else {
            return false;
        }
    }


    $category = get_category_by_slug($category_slug);
    if (!$category) {
        return false;
    }


    $query_args = array(
        'category_name' => $category_slug,
        'posts_per_page' => 1,
        'fields' => 'ids',
    );

    $posts = get_posts($query_args);

    return !empty($posts);
}



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
                                        <a href="<?php echo get_page_slug_by_id(get_theme_mod('author_page'));?>">
                                        <i class="fa-regular fa-user"></i>
                                        By <?php the_author(); ?></a>
                                    </div>
                                    <div class="date">
                                        <div class="icon-box">
                                        <!-- <i class="fa-solid fa-calendar-days"></i> -->
                                        </div>
                                        <?php
                                        $day = get_the_date('j'); // Day of the month without leading zeros
                                        $month = get_the_date('M'); // Three-letter month abbreviation
                                        ?>
                                        <div class="date-container">
                                        <div class="date-box">
                                            <div class="day"><?php echo $day;?></div>
                                            <div class="month"><?php echo $month;?></div>
                                        </div>
                                        </div>

                                     </div>
                                </div>

                                <h2 class="article-title">
                                    <a href="<?php echo get_permalink();?>"><?php echo get_the_title();?></a>
                                </h2>

                                <div class="article-desc">
                                <?php echo limit_content(18);?>
                                    </div>

                                <div class="read-more-btn">
                                    <a class="btn" href="<?php echo get_permalink();?>">
                                    Continue Reading <i class="fa-solid fa-arrow-right"></i>
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


// Add some basic CSS
function add_category_posts_styles() {
    if (has_related_posts()):
    $article_method = carbon_get_theme_option('related_article_display');
    if(isset($article_method) && $article_method == 'auto_load'):

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
padding-top: 10px;
color: #000;
}
.single-article .text-holder .meta-box{
position: relative;
display: flex;
justify-content: space-between;
align-items: center;
}
.article-col .date-container {
    display: inline-block;
    text-align: center;
    padding: 6px;
}

.article-col .day {
    font-size: 1rem;
    font-weight: bold;
    margin: 0;
    position: relative;
}

.article-col .day::after {
    content: '';
    display: block;
    width: 60%;
    height: 2px;
    background-color: grey;
    margin: 8px auto 0;
}

.article-col .month {
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    margin: 8px 0 0;
    letter-spacing: 0.05em;
}
.article-col .author a{
    color: <?php echo $color_one;?>;
}
.single-article .text-holder .meta-box .date .icon-box, .blog-role .single-article .text-holder .read-more-btn .icon-box{
display: inline;
}

.single-article .text-holder .article-title{
margin-top: 15px;
margin-bottom: 20px;
font-size: 20px;
font-weight: 700;
width: 100%;
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

.single-article:hover .img-holder:after{
transform: scaleY(1);
transition: .5s ease;
}
.single-article:hover .img-holder img{
transform: scale(1.2, 1.2);
}

.single-article:hover .read-more-btn a, .blog-role .single-article .article-title a:hover{
color:<?php echo $color_two;?>;
}
.single-article:hover .read-more-btn .icon-box > svg{
fill: <?php echo $color_two;?>;
}

.article-col .date{
    display: flex;
    align-items: center;
    gap: 4px;

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
    endif;
endif;
}
add_action('wp_footer', 'add_category_posts_styles');