<?php
/*
  Plugin Name: Ratings for bbPress
  Plugin URI: https://www.elementous.com
  Description: Adds compatibility beetween Ratings Manager and bbPress plugin.
  Author: Elementous
  Author URI: https://www.elementous.com
  Version: 1.0.0
*/

define( 'ELM_BBP_VERSION', '1.0.0' );
define( 'ELM_BBP_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'ELM_BBP_PLUGIN_FOLDER', basename( ELM_BBP_PLUGIN_PATH ) );
define( 'ELM_BBP_PLUGIN_URL', plugins_url() . '/' . ELM_BBP_PLUGIN_FOLDER );

include_once ELM_BBP_PLUGIN_PATH . '/ratings_for_bbpress.class.php';

// Initiate plugin
$elm_ratings_for_bbpress = new Elm_Ratings_For_BBPress();

// Install
register_activation_hook( __FILE__, array( $elm_ratings_for_bbpress, 'install' ) );
