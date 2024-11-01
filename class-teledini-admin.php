<?php
/**
 * Teledini Engagement Tools
 *
 * @package		Teledini_Admin
 * @author		Teledini Support <support@teledini.com>
 * @license		GPL-2.0+
 * @link		http://teledini.com
 * @copyright	2013 Teledini
 */

class Teledini_Admin{
	/** Instance of this class.
	 * @since    1.0.0
	 * @var      object
	**/
	protected static $instance = null;

	/** Slug of the plugin screen.
	 * @since    1.0.0
	 * @var      string
	**/
	protected $plugin_screen_hook_suffix = null;

	/** Whether or not the user is authenticated with Teledini
	 * @since    1.0.0
	 * @var      boolean
	**/
	protected $teledini_auth = false;

	/** Initialize the plugin by loading admin scripts & styles and adding a settings page and menu.
	 * @since     1.0.0
	**/
	private function __construct() {
		$plugin 				= Teledini::get_instance();
		$this->plugin_slug 		= $plugin->get_plugin_slug();
		$this->config_options 	= get_option( 'teledini-settings' );
		$this->buttons_empty	= false;
		$this->teledini_url 	= 'https://www.teledini.com/EngagementTools/';
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_filter('plugin_action_links', array( $this, 'add_action_links' ), 10, 2);
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/** Return an instance of this class.
	 * @since     1.0.0
	 * @return    object
	**/
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 * @since     1.0.0
	 * @return    null
	**/
	public function enqueue_admin_styles() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), Teledini::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 * @since     1.0.0
	 * @return    null    Return early if no settings page is registered.
	**/
	public function enqueue_admin_scripts() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), Teledini::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 * @since    1.0.0
	**/
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Teledini Engagement Tools', $this->plugin_slug ),
			__( 'Teledini', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 * @since    1.0.0
	**/
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 * @since    1.0.0
	**/
	public function add_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>';
		return $links;
	}

	/**
	 * Entry point of the Teledin-specific work.
	 * @since    1.0.0
	**/
	function init() {
		register_setting( 'teledini-settings-group', 'teledini-settings' );
		$this->teledini_auth = ( isset( $this->config_options['selected_config'] ) && $this->config_options['selected_config'] != '' ) ? true : false;
	} // init

	/**
	 * Determine how to draw the admin page based on authentication or existing settings 
	 * @since    1.0.0 
	**/

	function config_page() {
		if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['teledini_auth'] ) ){
			$this->return = $this->authenticate(); // THIS IS AN ATTEMPT TO AUTHENTICATE; MAY RETURN EITHER FORM
//			print_r( $this->return );
			if( isset( $this->return['message'] ) AND $this->return['message'] == 'Unable to Authenticate' ){
				$this->teledini_auth = false; 
				$this->message = 'We could not find an account that matches that username and password combination.';
			}else if( empty( $this->return['available_configs'] ) ){
				$this->teledini_auth = false; 
				$this->buttons_empty = true;
			}else{
				$this->teledini_auth = true;
				$this->config_options['available_configs'] = $this->return['available_configs'];
			}
		} 
		if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['teledini_refresh'] ) ){ 
			$this->teledini_auth = false; // THIS PRESENTS THE LOGIN FORM AT THE USER'S REQUEST TO REFRESH CONFIGS
		} 
	} // End function config_page()

	/**
	 * Authenticate with the Teledini System
	 * @since    1.0.0
	**/

	function authenticate() {
		$username = $_POST['teledini_username'];
		$password = $_POST['teledini_password'];
		$stack = array();
		$Buttons = wp_remote_post( $this->teledini_url, array(
			'body' => array( 'username' => $username, 'password' => $password ),
			'method' => 'POST',
			'headers' => array( 'App-Key' => '380ad88e92e6e0f8230642c68c16d8fc6f0b390ef4c19662' )
		) );
		if( wp_remote_retrieve_response_code( $Buttons ) == "200" ){
			$raw_buttons = json_decode(wp_remote_retrieve_body( $Buttons ));
			$configs = array();
			if( isset( $raw_buttons->available_configs ) ) {
				$this->authenticated = true;
				foreach( $raw_buttons->available_configs as $config_id => $button ){
					$configs[$config_id] = array(
						'name' => $button->name,
						'org_name' => $button->org_name,
						'detail' => (array)$button->detail
					);
				}
				$stack['available_configs'] = $configs;
			}else if( $raw_buttons->message ) {
				$stack['message'] = $raw_buttons->message;
			}
		}
		return $stack;
	} // End function authenticate()

	function config_fields( $args ) {
		add_settings_field( 'available_configs[' . $args['configId'] . '][name]', 
			'',
			array( $this, 'hidden_field_callback' ), 
			'teledini', 
			'section-one', 
			array( 'name' => 'teledini-settings[available_configs][' . $args['configId'] . '][name]', 'value' => $args['btn_name'] ) 
		);
		add_settings_field( 'available_configs[' . $args['configId'] . '][org_name]', 
			'',
			array( $this, 'hidden_field_callback' ), 
			'teledini', 
			'section-one', 
			array( 'name' => 'teledini-settings[available_configs][' . $args['configId'] . '][org_name]', 'value' => $args['org_name'] ) 
		);
		add_settings_field( 'available_configs[' . $args['configId'] . '][detail]', 
			'',
			array( $this, 'hidden_field_callback' ), 
			'teledini', 
			'section-one', 
			array( 'name' => 'teledini-settings[available_configs][' . $args['configId'] . '][detail]', 'value' => $args['btn_detail'] ) 
		);
	} // End function config_fields()

	function select_field_callback( $args ) {
		print '<select name="teledini-settings[selected_config]">';
		print '<option value="">-- select --</option>';
		foreach( $args['configs'] as $org => $buttons ) {
			if( count( $buttons > 0 ) ) {
				print '<optgroup label="' . $org . '">';
				foreach( $buttons as $button ) {
					$selected = ( $args['selected_config'] == $button['id'] ) ? 'selected' : null;
					print '<option value="' . $button['id'] . '" ' . $selected . ' >' . $button['label'] . '</option>';
				}
				print '</optgroup>';
			}
		}
		print '</select>';
	} // End function select_field_callback()

	function text_field_callback( $args ) {
		$name 	= esc_attr( $args['name'] );
		$value 	= esc_attr( $args['value'] );
		echo '<input type="text" name="' . $name . '" value="' . $value . '" />';
	} // End function text_field_callback()
	
	function hidden_field_callback( $args ) {
		$name 	= esc_attr( $args['name'] );
		$value 	= esc_attr( $args['value'] );
		echo '<input type="hidden" class="teledini_hidden" name="' . $name . '" value="' . $value . '" />';
	} // End function hidden_field_callback()
} // Teledini
