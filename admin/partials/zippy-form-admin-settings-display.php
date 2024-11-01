<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.zaigoinfotech.com
 * @since      1.0.0
 *
 * @package    Zippy_Form
 * @subpackage Zippy_Form/admin/partials
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
		        <div id="icon-themes" class="icon32"></div>  
		        <h2>Zippy Forms Settings</h2>  
		         <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
				<?php settings_errors(); ?>  
		        <form method="POST" action="options.php">  
		            <?php 
		                settings_fields( 'dynamic_form_general_settings' );
		                do_settings_sections( 'dynamic_form_general_settings' ); 
		            ?>             
		            <?php submit_button(); ?>  
		        </form> 
</div>