<?php
/**
*@package DLCPlugin
*/
/*
Plugin Name: DLC Plugin
Plugin URI: http://dlcph.com/plugins
Description: DLC Plugin for WP
Version: 1.0.0
Author: Roasted Legumes
Author URI: http://roastedlegumes.org/
License: GPLv2 or later
Text Domain: dlc-plugin
*/

defined( 'ABSPATH' ) or die;

class DLCPlugin
{
     public $plugin;

     function __construct() {
          $this->plugin = plugin_basename( __FILE__ );
          add_action( 'init', array( $this, 'init_sessions' ) );

          add_filter( 'login_headerurl', array( $this, 'load_login_logo_url') );
          add_action( 'login_enqueue_scripts', array( $this, 'load_login_logo') );

          add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
     }

     function register() {
          //admin pages
          add_action( 'init', array( $this, 'custom_post_type') );
          add_action( 'admin_menu', array( $this, 'add_admin_pages') );
          add_filter( "plugin_action_links_$this->plugin", array( $this, 'settings_link'));

          // frontend
          add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
     }

//initialize
     function init_sessions(){
		if(!isset($_SESSION)) {
			 session_set_cookie_params(0);
			 session_start();
		}

          global $dlcuser;
          $dlcuser['on'] = 1;
     }

     function enqueue() {
          // Register the JS file with a unique handle, file location, and an array of dependencies
          wp_register_script( "dlcpluginjs", plugins_url( 'assets/script.js', __FILE__ ), array('jquery') );

          // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
          wp_localize_script( 'dlcpluginjs', 'dlcAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),  'cart_url' => plugins_url( 'templates/init_cart.php', __FILE__ )));

          // enqueue jQuery library and the script you registered above
          wp_enqueue_script( 'jquery' );
          wp_enqueue_script( 'dlcpluginjs' );

          // enqueue styles
          wp_enqueue_style( 'dlcplugincss', plugins_url( 'assets/styles.css', __FILE__ ) );

          wp_enqueue_style( 'add_google_fonts', 'https://fonts.googleapis.com/css2?family=Spartan&display=swap', false );
     }

//frontpage
	function load_login_logo() {
		?>
			<style type="text/css">body.login div#login h1 a { background-image: url('http://dlcph.com/src/dlc_logo.png');background-size:260px;height:120px;width:260px; } </style>
		<?php
	}

     function load_login_logo_url($url) {
          return "http://www.dlcph.com";
     }

//admin
     function custom_post_type() {
          register_post_type( 'dlcpost', ['public' => true, 'label' => 'DLC Posts', 'menu_icon' => 'dashicons-lock'] );
     }

     function settings_link( $links) {
          // custom plugin settings
          $settings_link = '<a href="admin.php?page=dlc_plugin">Settings</a>';
          array_push( $links, $settings_link );
          return $links;
     }

     function add_admin_pages() {
          add_menu_page( 'DLC Plugin', 'DLC Plugin', 'manage_options', 'dlc_plugin', array( $this, 'admin_index' ), 'dashicons-rest-api', 110 );
     }

     function admin_index() {
          require_once plugin_dir_path( __FILE__ ) . 'templates/admin.php';
     }

     function update() {
          require_once plugin_dir_path( __FILE__ ) . 'inc/update.php';
     }

     function deactivate() {
          require_once plugin_dir_path( __FILE__ ) . 'inc/deactivate.php';
          DLCPluginDeactivate::deactivate();
     }

}

if ( class_exists( 'DLCPlugin' ) ) {
     $dlcplugin = new DLCPlugin( );
     $dlcplugin->register();

     require_once plugin_dir_path( __FILE__ ) . 'inc/account.php';
     $dlcaccounts = new DLCPluginAccount( );
     $dlcaccounts->register();

     require_once plugin_dir_path( __FILE__ ) . 'inc/product.php';
     $dlcproducts = new DLCPluginProducts( );
     $dlcproducts->register();
}

// activation
require_once plugin_dir_path( __FILE__ ) . 'inc/activate.php';
register_activation_hook( __FILE__, array( 'DLCPluginActivate', 'activate' ) );

// deactivation
register_activation_hook( __FILE__, array( $dlcplugin, 'deactivate' ) );
