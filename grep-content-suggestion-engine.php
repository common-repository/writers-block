<?php
/*
Plugin Name: GrepWords.com Content Suggestion Engine
Plugin URI: http://www.grepwords.com
Description: Suggests content for you to write basted on keywords
Version: 1.1
Author: Carter Cole
Author URI: http://www.cartercole.com
*/
define( 'GCS_URL', plugins_url('/', __FILE__) );
define( 'GCS_DIR', dirname(__FILE__) );
define( 'GCS_VERSION', '1.0' );
define( 'GCS_OPTION', 'gcs_ext' );

require_once( GCS_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'class.client.php' );
require_once( GCS_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'functions.plugin.php' );
require_once( GCS_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'functions.tpl.php' );

// Activation, uninstall
register_activation_hook( __FILE__, 'GCS_Install' );
register_deactivation_hook ( __FILE__, 'GCS_Uninstall' );

function GCS_Init() {
	global $GCS;

	// Load translations
	load_plugin_textdomain ( 'grep-content-suggestion-engine', false, basename(rtrim(dirname(__FILE__), '/')) . '/languages' );

	// Load client
	$GCS['client'] = new contentSuggestions_Client();

	// Admin
	if ( is_admin() ) {
		require_once( GCS_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'class.admin.php' );
		require_once( GCS_DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'class.admin.page.php' );
		$GCS['admin'] = new myExtension_Admin();
		$GCS['admin_page'] = new myExtension_Admin_Page();
	}
}

add_action('admin_menu', 'grepwords_cse_admin_menu');
function grepwords_cse_admin_menu() {
	add_options_page('Content Suggestions', 'Content Suggestions', 'manage_options', 'content-suggestions', 'grepwords_cse_settings_page');
}

add_action('admin_init','grepwords_cse_admin_init');
function grepwords_cse_admin_init() {
	register_setting('grepwords_cse_options', 'grepwords_cse_options', 'grepwords_cse_validate');	
	add_settings_section('grepwords_cse_general', '', 'grepwords_cse_section_text', 'grepwords_cse');
	add_settings_field('API key', 'API key', 'grepwords_cse_API_handler', 'grepwords_cse', 'grepwords_cse_general');
	
}

function grepwords_cse_section_text(){
}

function grepwords_cse_API_handler(){
	$options = get_option('grepwords_cse_options');
	echo '<input type="text" id="grewords_cse_options_API" name="grepwords_cse_options[API]" value="'.$options[API].'">';
}

function grepwords_cse_settings_page() { ?>
    <div class="wrap">
		<h2>GrepWords API Settings</h2>
		<form method="post" action="options.php" name="wp_auto_commenter_form">
			<?php settings_fields('grepwords_cse_options'); ?>
			<?php do_settings_sections('grepwords_cse'); ?>		
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
		</form>		
    </div>
<?php
}

function grepwords_cse_validate($input){
	return $input;
}






?>
