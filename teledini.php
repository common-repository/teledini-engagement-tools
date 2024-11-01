<?php
/**  
 * @package		Teledini
 * @author		Marian C. Buford <support@teledini.com>
 * @license		GPL-2.0+
 * @link		http://teledini.com
 * @copyright	2013 Teledini
 *
 * @wordpress-plugin
 * Plugin Name:	Teledini Engagement Tools
 * Plugin URI:	http://teledini.com
 * Description:	Teledini (teledini.com) enables instant interaction between you and visitors to your blog, while simultaneously delivering valuable data about the visitor to you. Teledini's multi-channel engagement  tools can be placed on your blog (or any website) in less than 20 minutes. Teledini is completely free to set up and install. Teledini's free version includes; Feedback, About Us and Social Networking. Teledini's paid version is pay-for-usage only and adds click to talk, click to chat and click to video. You are only charged for paid channels when you are connected to a visitor.
 * Version:		1.0.2
 * Author:		Marian C. Buford, Teledini
 * Author URI:	http://teledini.com
 * Text Domain:	teledini
 * Domain Path:	/languages
 * License: 	GPLv2 or later.
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-teledini.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-teledini-admin.php' );

register_activation_hook( __FILE__, array( 'Teledini', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Teledini', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'Teledini', 'uninstall' ) );

add_action( 'plugins_loaded', array( 'Teledini', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'Teledini_Admin', 'get_instance' ) );

