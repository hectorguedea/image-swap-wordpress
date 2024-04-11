<?php
/*
Plugin Name: Images Swap
Description: Enhance your website's interactivity with Images Swap, a versatile plugin that allows for seamless image or background swapping from any link, button, or accordion across themes and page builders, including Elementor. Customizable CSS classes and dynamic transition effects.
Version: 1.0
Author: Héctor Guedea
Author URI: https://hectorguedea.com
Plugin URI: https://hectorguedea.com/images-swap-plugin
*/

function image_swap_plugin_settings_link( $links ) {
    $settings_link = '<a href="tools.php?page=image-swap-plugin-settings">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'image_swap_plugin_settings_link' );

function image_swap_plugin_enqueue_script() {

    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_script( 'imageSwap-script', $plugin_url . 'js/script.js', array( 'jquery' ), null, true );
    wp_localize_script( 'imageSwap-script', 'imageSwapScriptSettings', array(
        'toggleImg' => get_option('image_swap_toggle_img', 'toggle-img'),
        'toggleBg' => get_option('image_swap_toggle_bg', 'toggle-bg'),
        'accordionItem' => get_option('image_swap_accordion_item', 'e-n-accordion-item'),
        'linkItem' => get_option('image_swap_link_item', 'imageSwap'),
        'effect' => get_option('image_swap_transition_effect', 'fade'),
        'transitionTime' => get_option('image_swap_transition_time', 'fast') 
    ));

    wp_enqueue_style( 'imageSwap-styles', $plugin_url . '/css/image-swap-styles.css' );
}
add_action( 'wp_enqueue_scripts', 'image_swap_plugin_enqueue_script' );

// Función para agregar la página de configuración al panel de administración
function image_swap_plugin_settings_page() {
    add_management_page(
        'Images Swap',
        'Images Swap',
        'manage_options',
        'image-swap-plugin-settings',
        'image_swap_plugin_settings_page_content'
    );
}
add_action( 'admin_menu', 'image_swap_plugin_settings_page' );

function image_swap_plugin_settings_page_content() {
    ?>
    <style>
        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #fff;
			
        }

        .card-body {
            padding: 20px;
        }

    </style>
        <div class="wrap">
        <h2>Images Swap Plugin</h2>
        <form method="post" action="options.php">
            <div class="card">
                <div class="card-body">
                    <?php settings_fields( 'image_swap_plugin_settings' ); ?>
                    <?php do_settings_sections( 'image_swap_plugin_settings' ); ?>
                    <?php submit_button(); ?>
                </div>
            </div>
         </form>
        </div>
    <?php
}

function image_swap_plugin_register_settings() {
    register_setting( 'image_swap_plugin_settings', 'image_swap_toggle_img' );
    register_setting( 'image_swap_plugin_settings', 'image_swap_toggle_bg' );
    register_setting( 'image_swap_plugin_settings', 'image_swap_accordion_item' );
    register_setting( 'image_swap_plugin_settings', 'image_swap_link_item' );
    register_setting( 'image_swap_plugin_settings', 'image_swap_transition_effect' );
    register_setting( 'image_swap_plugin_settings', 'image_swap_transition_time' ); 

    add_settings_section( 'image_swap_plugin_main_section', 'Images Swap Settings', '__return_false', 'image_swap_plugin_settings' );

    add_settings_field( 'image_swap_toggle_img', 'Toggle Image Class', 'image_swap_toggle_img_callback', 'image_swap_plugin_settings', 'image_swap_plugin_main_section' );
    add_settings_field( 'image_swap_toggle_bg', 'Toggle Background Class', 'image_swap_toggle_bg_callback', 'image_swap_plugin_settings', 'image_swap_plugin_main_section' );
    add_settings_field( 'image_swap_accordion_item', 'Accordion Elementor Item Class', 'image_swap_accordion_item_callback', 'image_swap_plugin_settings', 'image_swap_plugin_main_section' );

    add_settings_field( 'image_swap_link_item', 'Link Item Class', 'image_swap_link_item_callback', 'image_swap_plugin_settings', 'image_swap_plugin_main_section' );

    add_settings_field( 'image_swap_transition_effect', 'Transition Effect', 'image_swap_transition_effect_callback', 'image_swap_plugin_settings', 'image_swap_plugin_main_section' );

    add_settings_field( 'image_swap_transition_time', 'Transition Time', 'image_swap_transition_time_callback', 'image_swap_plugin_settings', 'image_swap_plugin_main_section' ); 


}
add_action( 'admin_init', 'image_swap_plugin_register_settings' );

function image_swap_toggle_img_callback() {
    $toggle_img = get_option('image_swap_toggle_img', 'toggle-img');
    echo "<input type='text' name='image_swap_toggle_img' value='" . esc_attr($toggle_img) . "' />";
}

function image_swap_toggle_bg_callback() {
    $toggle_bg = get_option('image_swap_toggle_bg', 'toggle-bg');
    echo "<input type='text' name='image_swap_toggle_bg' value='" . esc_attr($toggle_bg) . "' />";
}

function image_swap_accordion_item_callback() {
    $accordion_item = get_option('image_swap_accordion_item', 'e-n-accordion-item');
    echo "<input type='text' name='image_swap_accordion_item' value='" . esc_attr($accordion_item) . "' />";
}

function image_swap_link_item_callback() {
    $link_item = get_option('image_swap_link_item', 'imageSwap');
    echo "<input type='text' name='image_swap_link_item' value='" . esc_attr($link_item) . "' />";
}

function image_swap_transition_effect_callback() {
    $effects = array(
        'fade' => 'Fade',
        'zoomIn' => 'zoomIn',
        'zoomOut' => 'zoomOut'
    );
    $selected_effect = get_option('image_swap_transition_effect', 'fade');

    echo "<select name='image_swap_transition_effect'>";
    foreach ($effects as $key => $value) {
        echo "<option value='$key' " . selected( $selected_effect, $key, false ) . ">$value</option>";
    }
    echo "</select>";
}

function image_swap_transition_time_callback() {
    $selected_time = get_option('image_swap_transition_time', 'fast');

    echo "<select name='image_swap_transition_time'>";
    echo "<option value='fast' " . selected( $selected_time, 'fast', false ) . ">Fast</option>";
    echo "<option value='slow' " . selected( $selected_time, 'slow', false ) . ">Slow</option>";
    echo "</select>";
}
