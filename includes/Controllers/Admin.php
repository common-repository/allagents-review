<?php
namespace AllAgentsReviews\Controllers;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Admin {

	function __construct() {
		add_action('admin_menu', [$this, 'allagentsreviews_review_menu']);
		add_action('admin_init', [$this, 'allagentsreviews_review_settings_init']);
	}

	// Add plugin to admin menu
	function allagentsreviews_review_menu() {
		add_menu_page(
			__('allAgents Settings', 'allagents-reviews'),
			__('allAgents', 'allagents-reviews'),
			'manage_options',
			'allagentsreviews-review-settings',
			[$this, 'allagentsreviews_review_settings_page'],
			'dashicons-admin-generic',
			30
		);
	}

	// Settings page content
	function allagentsreviews_review_settings_page() {
		?>
        <div class="wrap">
            <h2><?php esc_html_e('Review Plugin Settings', 'allagents-reviews'); ?></h2>
            <form method="post" action="options.php">
				<?php
				settings_fields('allagentsreviews_review_options');
				do_settings_sections('allagentsreviews-review-settings');
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	// Register settings and fields
	function allagentsreviews_review_settings_init() {
		register_setting('allagentsreviews_review_options', 'allagentsreviews_review_settings', 'sanitize_text_field');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_api_key_reviews', 'sanitize_text_field');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_api_key_overall', 'sanitize_text_field');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_api_url_reviews', 'esc_url_raw');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_api_url_overall', 'esc_url_raw');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_shortcode_container_bg', 'sanitize_hex_color');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_shortcode_text_color', 'sanitize_hex_color');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_widget_background_color', 'sanitize_hex_color');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_widget_text_color', 'sanitize_hex_color');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_overall_widget_background_color', 'sanitize_hex_color');
		register_setting('allagentsreviews_review_options', 'allagentsreviews_overall_widget_text_color', 'sanitize_hex_color');

		add_settings_section(
			'allagentsreviews_review_section',
			__('API Settings', 'allagents-reviews'),
			[$this, 'allagentsreviews_review_section_cb'],
			'allagentsreviews-review-settings'
		);

		add_settings_field(
			'allagentsreviews_api_url_reviews',
			__('API URL Reviews', 'allagents-reviews'),
			[$this, 'allagentsreviews_api_url_reviews_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
		add_settings_field(
			'allagentsreviews_api_url_overall',
			__('API URL Overall', 'allagents-reviews'),
			[$this, 'allagentsreviews_api_url_overall_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);

		add_settings_field(
			'allagentsreviews_api_key_reviews',
			__('API Key Reviews', 'allagents-reviews'),
			[$this, 'allagentsreviews_api_key_reviews_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
		add_settings_field(
			'allagentsreviews_api_key_overall',
			__('API Key Overall', 'allagents-reviews'),
			[$this, 'allagentsreviews_api_key_overall_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);

		add_settings_field(
			'allagentsreviews_shortcode_container_bg',
			__('Container Background Color (Shortcode)', 'allagents-reviews'),
			[$this, 'allagentsreviews_shortcode_container_bg_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
		add_settings_field(
			'allagentsreviews_shortcode_text_color',
			__('Text Color (Shortcode)', 'allagents-reviews'),
			[$this, 'allagentsreviews_shortcode_text_color_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
		add_settings_field(
			'allagentsreviews_widget_background_color',
			__('Container Background Color (Widget)', 'allagents-reviews'),
			[$this, 'allagentsreviews_widget_background_color_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
		add_settings_field(
			'allagentsreviews_widget_text_color',
			__('Text Color (Widget)', 'allagents-reviews'),
			[$this, 'allagentsreviews_widget_text_color_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
		add_settings_field(
			'allagentsreviews_overall_widget_background_color',
			__('Background Color (Overall Widget)', 'allagents-reviews'),
			[$this, 'allagentsreviews_overall_widget_background_color_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
		add_settings_field(
			'allagentsreviews_overall_widget_text_color',
			__('Text Color (Overall Widget)', 'allagents-reviews'),
			[$this, 'allagentsreviews_overall_widget_text_color_cb'],
			'allagentsreviews-review-settings',
			'allagentsreviews_review_section'
		);
	}

	// Section callback
	function allagentsreviews_review_section_cb() {
		echo '<p>' . esc_html__('Enter your API settings below:', 'allagents-reviews') . '</p>';
	}

	// API URL field callbacks
	function allagentsreviews_api_url_reviews_cb() {
		$options = get_option('allagentsreviews_api_url_reviews');
		echo '<input type="text" id="allagentsreviews_api_url_reviews" name="allagentsreviews_api_url_reviews" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	function allagentsreviews_api_url_overall_cb() {
		$options = get_option('allagentsreviews_api_url_overall');
		echo '<input type="text" id="allagentsreviews_api_url_overall" name="allagentsreviews_api_url_overall" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	// API key field callbacks
	function allagentsreviews_api_key_reviews_cb() {
		$options = get_option('allagentsreviews_api_key_reviews');
		echo '<input type="text" id="allagentsreviews_api_key_reviews" name="allagentsreviews_api_key_reviews" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	function allagentsreviews_api_key_overall_cb() {
		$options = get_option('allagentsreviews_api_key_overall');
		echo '<input type="text" id="allagentsreviews_api_key_overall" name="allagentsreviews_api_key_overall" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	// Color field callbacks
	function allagentsreviews_shortcode_container_bg_cb() {
		$options = get_option('allagentsreviews_shortcode_container_bg');
		echo '<input type="color" id="allagentsreviews_shortcode_container_bg" name="allagentsreviews_shortcode_container_bg" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	function allagentsreviews_shortcode_text_color_cb() {
		$options = get_option('allagentsreviews_shortcode_text_color');
		echo '<input type="color" id="allagentsreviews_shortcode_text_color" name="allagentsreviews_shortcode_text_color" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	function allagentsreviews_widget_background_color_cb() {
		$options = get_option('allagentsreviews_widget_background_color');
		echo '<input type="color" id="allagentsreviews_widget_background_color" name="allagentsreviews_widget_background_color" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	function allagentsreviews_widget_text_color_cb() {
		$options = get_option('allagentsreviews_widget_text_color');
		echo '<input type="color" id="allagentsreviews_widget_text_color" name="allagentsreviews_widget_text_color" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	function allagentsreviews_overall_widget_background_color_cb() {
		$options = get_option('allagentsreviews_overall_widget_background_color');
		echo '<input type="color" id="allagentsreviews_overall_widget_background_color" name="allagentsreviews_overall_widget_background_color" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

	function allagentsreviews_overall_widget_text_color_cb() {
		$options = get_option('allagentsreviews_overall_widget_text_color');
		echo '<input type="color" id="allagentsreviews_overall_widget_text_color" name="allagentsreviews_overall_widget_text_color" style="width: 50%" value="' . esc_attr($options) . '" />';
	}

}

?>
