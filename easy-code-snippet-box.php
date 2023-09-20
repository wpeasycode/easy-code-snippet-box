<?php
/**
 * Plugin Name: Easy Code Snippet Box
 * Plugin URI: https://www.wpeasycode.com/plugin/easy-code-snippet-box
 * Description: A simple plugin to display code snippets in posts.
 * Version: 1.0.0
 * Author: WP Easy Code
 * Author URI: https://www.wpeasycode.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: easy-code-snippet-box
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

function enqueue_prism_assets() {
    wp_enqueue_style('prism-css', esc_url(plugin_dir_url(__FILE__) . 'prism.css'));
    wp_enqueue_script('prism-js', esc_url(plugin_dir_url(__FILE__) . 'prism.js'), array(), '', true);
}
add_action('wp_enqueue_scripts', 'enqueue_prism_assets');

function easy_code_snippet_box_display($atts = [], $content = null, $tag = '') {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // Sanitize and validate 'lang' attribute
    $wporg_atts = shortcode_atts([
        'lang' => 'php',
    ], $atts, $tag);
    $wporg_atts['lang'] = sanitize_text_field($wporg_atts['lang']);

    // start output
    $output = '<div style="position: relative; border-radius: 4px 4px 4px 4px; overflow: hidden;">';

    // add a header
    $output .= '<div style="background-color: #343541; padding: 5px 10px; font-size: 14px; color: #fff; display: flex; justify-content: space-between; align-items: center; margin: 0; border-radius: 4px 4px 0 0;">';
    $output .= '<span>' . esc_html(strtoupper($wporg_atts['lang'])) . '</span>';
    $output .= '<button class="copy-button" style="background: none; border: none; cursor: pointer; color: #fff;" onclick="copyToClipboard(this)">📋 Copy Code</button>';
    $output .= '</div>';

    // start box
    $output .= '<pre style="margin: 0; border-radius: 0 0 4px 4px;"><code class="language-' . esc_attr($wporg_atts['lang']) . '">';

    // add content
    $output .= do_shortcode(wp_kses_post($content));

    // end box
    $output .= '</code></pre>';

    // end output
    $output .= '</div>';

    return $output;
}
add_shortcode('easy_codebox', 'easy_code_snippet_box_display');

// Add settings page in WordPress admin
add_action('admin_menu', 'easy_code_snippet_box_menu');

function easy_code_snippet_box_menu(){
    add_menu_page(
        'Easy Code Snippet Box Settings', // Page title
        'Easy Code Snippet Box', // Menu title
        'manage_options', // Capability
        'easy-code-snippet-box-settings', // Menu slug
        'easy_code_snippet_box_settings_page' // Callback function
    );
}

function easy_code_snippet_box_settings_page(){
    ?>
    <div class="wrap">
        <h1>Easy Code Snippet Box Settings</h1>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="postbox">
                        <h2 class="hndle"><span>How to Use Easy Code Snippet Box</span></h2>
                        <div class="inside">
                            <p>Welcome to Easy Code Snippet Box! Here's how to use this plugin effectively:</p>
                            <h4>Basic Usage</h4>
                            <p>Simply wrap your code inside the <code>[codebox][/codebox]</code> shortcode.</p>
                            <pre><code>[codebox]Your code here[/codebox]</code></pre>
                            <h4>Adding Language</h4>
                            <p>You can specify the programming language for syntax highlighting using the <code>lang</code> attribute.</p>
                            <pre><code>[codebox lang="php"]Your PHP code here[/codebox]</code></pre>
                            <h4>Closing the Shortcode</h4>
                            <p>Always remember to close the shortcode with <code>[/codebox]</code> to ensure proper rendering.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Add inline script for copy functionality
function easy_code_snippet_box_add_inline_script() {
    echo '<script>
    function copyToClipboard(button) {
        var codeBlock = button.parentElement.nextElementSibling.querySelector("code");
        var textArea = document.createElement("textarea");
        textArea.value = codeBlock.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();
    }
    </script>';
}
add_action('wp_footer', 'easy_code_snippet_box_add_inline_script');

// Add inline script for button animation and style
function easy_code_snippet_box_add_button_animation_script() {
    echo '<script>
    function copyToClipboard(button) {
        var codeBlock = button.parentElement.nextElementSibling.querySelector("code");
        var textArea = document.createElement("textarea");
        textArea.value = codeBlock.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();

        // Animation and text change
        button.innerHTML = "✅ Copied!";
        setTimeout(function() {
            button.innerHTML = "📋 Copy Code";
        }, 2000);
    }
    </script>';
    echo '<style>
    .copy-button:focus {
        outline: none;
        border: none;
    }
    </style>';
}
add_action('wp_footer', 'easy_code_snippet_box_add_button_animation_script');
?>
