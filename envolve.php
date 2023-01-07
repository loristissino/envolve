<?php 

/*
Plugin Name: Envolve
Version: 1.0
Description: A WP plugin to sign petitions and ask for changes
Author: Loris Tissino
Author URI: https://loris.tissino.it
*/

require_once( dirname( __FILE__ ) . '/envolve_functions.php' );

wp_register_style('style-css',plugin_dir_url(__FILE__).'css/style.css');
wp_enqueue_style('style-css');

