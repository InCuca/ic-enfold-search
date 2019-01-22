<?php
/**
 * Plugin Name:     Enfold Search
 * Plugin URI:      https://incuca.net
 * Description:     Enfold Search Plugin
 * Author:          INCUCA
 * Author URI:      https://incuca.net
 * Text Domain:     ic-enfold-search
 * Version:         0.1.0
 *
 * @package         Ic_Enfold
 */

function recursive_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}

function isEnabled($post) {
    $enabled = get_post_meta( $post->ID, '_ic_enfold_search', true );
    return $enabled !== 'no';
}

function ic_enfold_search_builder_elements($elements) {
    $posttype = avia_backend_get_post_type();
    if ($posttype !== 'page') return $elements;
    $titleElementIndex = recursive_array_search('header_title_bar', $elements);
    $element = array(
        "slug"  => "layout",
        "name"  => __("Enfold Search Settings",'ic_enfold_search'),
        "desc"  => __("Display the Search Field below menu?",'ic_enfold_search'),
        "id"    => "_ic_enfold_search",
        "type"  => "select",
        "std"   => "no",
        "class" => "avia-style",
        "subtype" => array(
            __("No",'ic_enfold_search') => 'no',
            __('Yes','ic_enfold_search')    =>'yes',
        ),
        
    );
    array_splice($elements, $titleElementIndex, 0, array($element));
    return $elements;
}
add_filter('avf_builder_elements', 'ic_enfold_search_builder_elements');

function ic_enfold_search_after_main_menu() {
    global $post;
    if (isEnabled($post)) {
        $settings = avia_header_setting();
        $style = "";
        if($settings['header_size'] == "custom") {
            $size = $settings['header_custom_size'];
            $style = ' style="padding-top: ' . $size . 'px; height: ' . intval($size) * 2 . 'px;"';
        }
            
        echo '<div class="ic-enfold-search"' . $style .'>';
        the_widget('WP_Widget_Search');
        echo '</div>';
    }
}
add_action('ava_after_main_menu', 'ic_enfold_search_after_main_menu');

function ic_enfold_search_scripts() {
    global $post;
    if (isEnabled($post)) {
        $plugin_dir = plugin_dir_url(__FILE__);
        wp_enqueue_style( 'ic-enfold-search' , $plugin_dir.'css/ic-enfold-search.css' , array(), false );
    }
}
add_action('wp_enqueue_scripts', 'ic_enfold_search_scripts');