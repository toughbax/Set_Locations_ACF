<?php
/*
Plugin Name: Advanced Custom Fields: Set Locations
Plugin URI: http://www.advancedcustomfields.com/
Description: This is a custom ACF field for use with Station Square
Version: 0.1.1
Author: Andrew Baxter
Author URI: http://www.toughbaxter.com/
License: GPL
Copyright: Andrew Baxter
*/


add_action('acf/register_fields', 'acfsl_register_fields');

function acfsl_register_fields()
{
	include_once('set-locations.php');
	include_once('ajax.php');
}


?>
