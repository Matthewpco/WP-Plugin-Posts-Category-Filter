<?php
/*
Plugin Name: Posts Category Filter
Plugin URI: https://github.com/Matthewpco/WP-Plugin-Posts-Category-Filter
Description: Creates a shortcode to display a container with a selection of WordPress categories. When a category is chosen, the posts with that category are displayed on the page.
Version: 1.3.0
Author: Gary Matthew Payne
Author URI: https://wpwebdevelopment.com/
License: GPL2
*/

// Register the shortcode
add_shortcode('posts_category_filter', 'posts_category_filter');

// Register the scripts
add_action('wp_enqueue_scripts', 'posts_category_filter_scripts');

// Register the AJAX actions
add_action('wp_ajax_get_category_posts', 'get_category_posts');
add_action('wp_ajax_nopriv_get_category_posts', 'get_category_posts');

/**
 * The category shortcode callback function.
 *
 * @return string The HTML output for the category selection and posts.
 */
function posts_category_filter() {
    $categories = get_categories();
    $output = '<div class="category-container">';
    $output .= '<div class="filter-container">';
    $output .= '<h3 class="center">FILTER BLOG POSTS</h3>';
    $output .= '<p>Select Category</p>';
    $output .= '<select id="category-select">';
    foreach ($categories as $category) {
        $output .= sprintf(
            '<option value="%d">%s</option>',
            esc_attr($category->term_id),
            esc_html($category->name)
        );
    }
    $output .= '</select>';
    $output .= '</div>';
    $output .= '<div id="category-posts"></div>';
    $output .= '</div>';

    return $output;
}

/**
 * Enqueue the JavaScript and CSS files for the category shortcode.
 */
function posts_category_filter_scripts() {
    wp_enqueue_script(
        'posts-category-filter-script',
        plugin_dir_url(__FILE__) . 'js/posts-category-filter.js',
        array('jquery'),
        '1.0',
        true
    );

    wp_enqueue_style(
    'posts-category-filter-style',
    plugin_dir_url(__FILE__) . 'css/posts-category-filter.css'
    );
}

/**
 * The AJAX callback function to get posts for a selected category.
 */
function get_category_posts() {
    // Get the category ID and page number from the AJAX request
    $category_id = isset($_POST['category_id']) ? absint($_POST['category_id']) : 0;
    $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
   
    // Set up the query arguments
    $posts_per_page = 2;
    $offset = ($page - 1) * $posts_per_page;
    $args = array(
    'cat' => $category_id,
    'posts_per_page' => $posts_per_page,
    'offset' => $offset
    );
   
    // Create a new WP_Query instance
    $query = new WP_Query($args);
   
    // Output the posts using a traditional WordPress loop with alternative syntax
    if ($query->have_posts()) :
    while ($query->have_posts()) :
    $query->the_post();
    ?>
    <div class="post-info">
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p><?php echo get_the_date(); ?></P>
    </div>
    <?php if (has_post_thumbnail()) : ?>
    <div class="one-third-column"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></div>
    <?php endif; ?>
    <div class="two-thirds-column"><a href="<?php the_permalink(); ?>"><?php the_excerpt(); ?></a></div>
    <hr style="border-top: 1px solid darkgray; width: 100%">
    <?php
    endwhile;
    wp_reset_postdata();
    endif;
   
    // Get the total number of posts in the selected category
    $category = get_category($category_id);
    if ($category->count > 2) :
    // Calculate the total number of pages
    $total_pages = ceil($category->count / $posts_per_page);
    if ($total_pages > 1) :
    // Output the pagination
    ?>
    <ul class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++) :
    if ($i == $page) : ?>
    <li class="page-item active"><span class="page-link"><?php echo esc_html($i); ?></span></li>
    <?php else : ?>
    <li class="page-item"><a href="#" class="page-link" data-page="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></a></li>
    <?php endif;
    endfor; ?>
    </ul>
    <?php
    endif;
    endif;
   
    wp_die();
   }
   