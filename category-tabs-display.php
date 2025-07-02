<?php
/**
 * Plugin Name: Category Tabs Display
 * Description: Display posts in tabbed format by category (General Notice, Admission Notification, Admission Results, Tender, Holiday List) with responsive design.
 * Version: 1.2
 * Author:  GCAC
 */

// Enqueue necessary CSS and JS
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('category-tabs-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('category-tabs-script', plugin_dir_url(__FILE__) . 'tabs.js', [], false, true);
});

// Register shortcode
add_shortcode('category_tabs', 'category_tabs_display');

function category_tabs_display() {
    $category_names = ['General Notice', 'Admission Notification', 'Admission Results', 'Tender'];
    $categories = array_map(function($name) {
        $term = get_term_by('name', $name, 'category');
        return $term ? $term->slug : '';
    }, $category_names);

    ob_start();
    echo '<div class="red-tab">';
    foreach ($category_names as $index => $name) {
        $active = $index === 0 ? 'active' : '';
        echo "<div class='tab-title $active' data-tab='tab-$index'><a>{$name}</a></div>";
    }
    // Add static Holiday List tab
    echo "<div class='tab-title' data-tab='tab-holiday'><a>Holiday List</a></div>";
    echo '</div>';

    // WP Categories Content
    foreach ($categories as $index => $slug) {
        echo '<div class="tab-content ' . ($index === 0 ? 'active' : '') . '" id="tab-' . $index . '">';
        echo '<ul class="wp-tab-post-list">';
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 10,
            'category_name' => $slug
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
        } else {
            echo '<li>No posts found.</li>';
        }
        wp_reset_postdata();
        echo '</ul></div>';
    }

    // Holiday tab content (static iframe)
    echo '<div class="tab-content holiday-tab" id="tab-holiday">';
    echo '<div class="holiday-iframe-container">';
    echo '<iframe title="GCAC Holiday" src="https://calendar.google.com/calendar/embed?height=300&amp;wkst=2&amp;bgcolor=%23ffffff&amp;ctz=Asia%2FKolkata&amp;title=Holiday&amp;showTitle=0&amp;showDate=0&amp;showNav=0&amp;showPrint=0&amp;showCalendars=0&amp;mode=AGENDA&amp;showTabs=1&amp;showTz=0&amp;src=NjNhNDM0MWY5OTM0MDU0NmM2YTNhZjJlNGFiOGM0ZGY0ODNhMTJkNTQyY2M4ZTUzNTUxMDMyOGZkNzFiZWJhNUBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&amp;color=%233F51B5" style="border-width:0" width="100%" height="300" frameborder="0" scrolling="no"></iframe>';
    echo '</div></div>';

    return ob_get_clean();
}
