<?php
/**
 * Plugin Name:       Big Magic AI
 * Description:       Big Magic AI, powered by ChatGPT, is a WordPress blogger's assistant that helps overcome writer's block by generating content and images for you. It also provides English grammar correction and translation support. Requires an OpenAI API Key.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Jeesun Kim (codeandfood)
 * Author URI:        https://www.codeandfood.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       big-magic-ai
 *
 * @package           create-block
 */

 if ( ! defined( 'ABSPATH' ) ) exit;

 if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'BigMagicAI__VERSION', '0.0.1' );
define( 'BigMagicAI__MINIMUM_WP_VERSION', '5.0' );
define( 'BigMagicAI__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// register_activation_hook( __FILE__, array( 'BigMagicAI', 'plugin_activation' ) );
// register_deactivation_hook( __FILE__, array( 'BigMagicAI', 'plugin_deactivation' ) );

require_once( BigMagicAI__PLUGIN_DIR . 'class.big-magic-ai.php' );
require_once( BigMagicAI__PLUGIN_DIR . 'class.big-magic-ai-rest-api.php' );

add_action( 'init', array( 'BigMagicAI', 'create_bigmagicai_block' ));
add_action( 'rest_api_init', array( 'BigMagicAI_REST_API', 'bigmagicai_init' ) );

add_action( 'enqueue_block_editor_assets', 'bigmagicai_wpdocs_enqueue_scripts' );

function bigmagicai_matches_openai_key_pattern($input) {
    $pattern = '/^sk-[a-zA-Z0-9]{50}$/';
    return preg_match($pattern, $input);
}

function bigmagicai_plugin_section_text() {
    echo '<p>Set your OpenAI API Key</p>';
}

function bigmagicai_plugin_openai_api_key_cb() {
    $options = get_option('bigmagicai_plugin_options');

	// two good ways to print an array in PHP
	// var_dump($array); 
	// print_r($array); 
	// echo '<pre>'; var_dump($options); echo '</pre>';
	echo '<input type="password" id="bigmagicai_plugin_openai_api_key" name="bigmagicai_plugin_options[openai_api_key]" size="40" value="' . esc_attr($options['openai_api_key']) . '" />';
}

function bigmagicai_render_settings_html() {
    ?>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'bigmagicai_plugin_options' );
        do_settings_sections( 'bigmagicai_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function bigmagicai_plugin_options_validate($input) {
  $newinput = ['openai_api_key'=>''];
  $newinput['openai_api_key'] = trim($input['openai_api_key']);

  // @TODO fix this
  // if (bigmagicai_matches_openai_key_pattern($newinput['openai_api_key'])) {
  //   echo "Valid OpenAI API key";
  //   $newinput['openai_api_key'] = $input['openai_api_key'];
  // } else {
  //   echo "Invalid OpenAI API key";
  //   $newinput['openai_api_key'] = "";
  // }
  
    return $newinput;
}

function bigmagicai_add_settings_page() {
	$hookname = add_options_page(
		'OpenAI API key',
		'OpenAI API key',
		'manage_options',
		'bigmagicai-plugin',
		'bigmagicai_render_settings_html'
	);
}

function bigmagicai_register_settings() {
    register_setting( 'bigmagicai_plugin_options', 'bigmagicai_plugin_options', 'bigmagicai_plugin_options_validate' );
    add_settings_section( 'opean_ai_api_setting', 'OpenAI API Key Setting', 'bigmagicai_plugin_section_text', 'bigmagicai_plugin' );
    add_settings_field( 'bigmagicai_plugin_openai_api_key', 'OpenAI API Key', 'bigmagicai_plugin_openai_api_key_cb', 'bigmagicai_plugin', 'opean_ai_api_setting' );
}

add_action('admin_init', 'bigmagicai_register_settings' );
add_action('admin_menu', 'bigmagicai_add_settings_page');

function bigmagicai_wpdocs_enqueue_scripts() {
    $blockPath = '/build/';

    wp_localize_script(plugins_url($blockPath, __FILE__), 'wpApiSettings', array(
        'root' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}