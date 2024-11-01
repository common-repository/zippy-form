<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.zaigoinfotech.com
 * @since      1.0.0
 *
 * @package    Zippy_Form
 * @subpackage Zippy_Form/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Zippy_Form
 * @subpackage Zippy_Form/public
 * @author     Zaigo infotech <sales@zaigoinfotech.com>
 */
class Zippy_Form_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zippy_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zippy_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zippy-form-public.css', array(), $this->version, 'all' );
		if (!wp_style_is('bootstrap', 'enqueued')) {
		wp_enqueue_style('bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css',array(), $this->version);
		}
		wp_enqueue_style('choices-css', plugin_dir_url(__FILE__) . 'css/choices.min.css' ,array(), $this->version);
		wp_enqueue_style('fa-icon', plugin_dir_url(__FILE__) . 'css/all.min.css',array(), $this->version );
	
		

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zippy_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zippy_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zippy-form-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script("zippy-multiselect-js", plugin_dir_url(__FILE__) . 'js/zippy-form-multiselect-public.js', array(), null, true,30);
		wp_enqueue_script('choices', plugin_dir_url(__FILE__) . 'js/choices.min.js',array(), $this->version,true);
		wp_enqueue_script('bootstrap-bundle', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js' ,array(), $this->version,true);
	
		if (!wp_script_is('bootstrap', 'enqueued')) {
		wp_enqueue_script('bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js',array(), $this->version,true);
		}
		
		

	}

}
