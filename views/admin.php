<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package		Teledini_Admin
 * @author		Teledini Support <support@teledini.com>
 * @license		GPL-2.0+
 * @link		http://teledini.com
 * @copyright	2013 Teledini
 */
?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php 
	$this->config_page(); 

	if( !$this->teledini_auth AND isset( $this->message ) ){
	?>
			<h3>Retrieve Your Teledini Button Information</h3>
			<div class="error"><p><strong><?php echo $this->message ?></strong></p><p>If you need assistance, please use this page: <a href="https://www.teledini.com/auth/forgotpass" target="_blank" >http://teledini.com/auth/forgotpass</a>.</p></div>
			<div>Please log in to Teledini using the form below so that we can retrieve your button information.</div>
			<form name="teledini_auth_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="hidden" name="teledini_auth" value="" />
				<ul>
					<li><label for="teledini_username">Teledini Username: </label>
						<input type="text" name="teledini_username"  maxlength="45" size="10" value="" />
						</li>
					<li><label for="teledini_password">Teledini Password: </label>
						<input type="password" name="teledini_password"  maxlength="45" size="10" value="" />
						</li>
				</ul>
				<input type="submit" value="Get Button Configurations" class="button-primary" />
			</form>
			</div>
	<?php
	} else if( $this->teledini_auth) {
	
		add_settings_section( 'section-one', 'Button Configuration','', 'teledini' );

		$inx = 0;
		
		foreach( $this->config_options['available_configs'] as $config_id => $btn ) {

			$btn_detail = is_string($btn['detail']) ? $btn['detail'] : json_encode( $btn['detail'] );

			$selections['available_configs'][ $btn['org_name'] ][ $inx ]['id'] = $config_id;
			$selections['available_configs'][ $btn['org_name'] ][ $inx ]['label'] = $btn['name'];
			
			$this->config_fields( array( 'configId' => $config_id,
				'org_name' => $btn['org_name'],
				'btn_name' => $btn['name'],
				'btn_detail' => $btn_detail ) 
			);
			$inx++;
		}
	
		add_settings_field( 'selected_config', 
			'Choose a Button',
			array( $this, 'select_field_callback' ), 
			'teledini', 
			'section-one', 
			array( 'configs' => $selections['available_configs'], 'selected_config' => isset( $this->config_options['selected_config'] ) ? $this->config_options['selected_config'] : null ) 
		);
		add_settings_field( 'last_updated', 
			'',
			array( $this, 'hidden_field_callback' ), 
			'teledini', 
			'section-one', 
			array( 'name' => 'teledini-settings[last_updated]', 'value' => isset( $this->config_options['last_updated'] ) ? $this->config_options['last_updated'] : date( 'F j, Y g:i a' ) ) 
		);
	
		print '<form action="options.php" method="POST">';
			settings_fields( 'teledini-settings-group' );
			do_settings_sections( 'teledini' );
			submit_button();
		print '</form>';

		if( isset( $this->config_options['last_updated'] ) && $this->config_options['last_updated'] != '' ) {
			print '<hr />';
			print 'Last updated: ' . $this->config_options['last_updated'] . '<br><br>';
			print '<form method="POST" action="' . $_SERVER['REQUEST_URI'] . '">';
			print '<input type="submit" name="teledini_refresh" value="Refresh Button Configurations" class="button-secondary" />';
			print '</form>';

		}

?>
	<?php
	}else{
	?>
			<h3>Retrieve Your Teledini Button Information</h3>
			<?php print $this->buttons_empty ? 
				 '<div class="error"><p><strong>No Button Configurations Found.</strong></p><p> Please ensure you have buttons configured at <a href="http://teledini.com" target="_blank" >http://teledini.com</a> before proceeding.</p></div>' : null; ?>
			<div>Please log in to Teledini using the form below so that we can retreive your button information.</div>
			<form name="teledini_auth_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="hidden" name="teledini_auth" value="" />
				<ul>
					<li><label for="teledini_username">Teledini Username: </label>
						<input type="text" name="teledini_username"  maxlength="45" size="10" value="" />
						</li>
					<li><label for="teledini_password">Teledini Password: </label>
						<input type="password" name="teledini_password"  maxlength="45" size="10" value="" />
						</li>
				</ul>
				<input type="submit" value="Get Button Configurations" class="button-primary" />
			</form>
			</div>
	<?php 
	}
	?>

</div>
