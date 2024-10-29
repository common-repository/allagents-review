<?php
namespace AllAgentsReviews\Controllers;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Init {

	private function __construct() {
		$this->class_setup();
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	public static function instance() {
		static $instance = null;

		if (is_null($instance)) {
			$instance = new Init();
		}

		return $instance;
	}

	function enqueue_scripts() {
		if (!is_admin()) {
			wp_enqueue_style('allagentsreviews_main_style', plugin_dir_url(__FILE__) . "../../assets/css/style.css?v=".time(), array(), '1.0');
			wp_enqueue_style('allagentsreviews_bootstrap', plugin_dir_url(__FILE__) . "../../assets/bootstrap/css/bootstrap.css?v=".time(), array(), '5.1.3');
			wp_enqueue_script('allagentsreviews_bootstrap_bundle', plugin_dir_url(__FILE__) . "../../assets/bootstrap/js/bootstrap.bundle.js?v=".time(), array('jquery'), '1.11.3', true);
			wp_enqueue_script('allagentsreviews_main_script', plugin_dir_url(__FILE__) . "../../assets/js/main.js?v=".time(), array('jquery'), '1.0', true);
			wp_localize_script('allagentsreviews_main_script', 'ajax_object', array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'ajax_nonce' => wp_create_nonce('allagentsreviews_ajax_nonce'),
				'card_text_color'=>  get_option("allagentsreviews_shortcode_text_color"),
				'card_bg_color'=>get_option("allagentsreviews_shortcode_container_bg"),
				'widget_text_color' =>get_option("allagentsreviews_widget_text_color"),
				'widget_bg_color'  => get_option("allagentsreviews_widget_background_color")
			));
		}
	}

	function class_setup() {
		new Widget();
		new Widget_Overall();
		new Shortcode();
		new Admin();
	}
}
