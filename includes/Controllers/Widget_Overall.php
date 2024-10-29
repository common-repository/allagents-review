<?php
namespace AllAgentsReviews\Controllers;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use AllAgentsReviews\Controllers\Shortcode;
use WP_Widget;

// Custom review widget class
class Widget_Overall extends WP_Widget {
	// Constructor
	public function __construct() {
		parent::__construct(
			'reviews_overall_widget', // Base ID
			__('allAgent Overall Reviews', 'allagents-reviews'), // Name
			array( 'description' => __('Displays reviews overall for both classic and block editors', 'allagents-reviews') ) // Args
		);
	}

	// Function to display reviews overall
	public function display_overall($reviews) {
		$output = "";
		if ($reviews) {
			ob_start();
			include plugin_dir_path(__FILE__) . "../views/widget_overall.php";
			$output = ob_get_clean();
		} else {
			$output .= '<p>' . __('API Error: Error loading reviews.', 'allagents-reviews') . '</p>';
		}

		return $output;
	}

	// Widget Output
	public function widget($args, $instance) {
		$reviews = (new Shortcode())->fetch_reviews();

		// Widget content here
		echo '<div class="allAgents-reviews-widget-overall">';
		// Escape the title for safe output
		echo '<h2>' . esc_html($instance['title']) . '</h2>';
		$instance["content"] = $this->display_overall($reviews); // Assuming display_overall() returns safe HTML
		// Ensure content is properly escaped for output
		echo wp_kses_post($instance["content"]);
		echo '</div>';
	}

	// Widget Form
	public function form($instance) {
		// Sanitize the title
		$title = !empty($instance['title']) ? sanitize_text_field($instance['title']) : '';
		?>
        <!-- Title Field -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'allagents-reviews'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
		<?php
	}

	// Update Widget Settings
	public function update($new_instance, $old_instance) {
		$instance = array();
		// Sanitize title before saving
		$instance['title'] = (!empty($new_instance['title'])) ? wp_strip_all_tags($new_instance['title']) : '';
		$instance['content'] = (!empty($new_instance['content'])) ? wp_strip_all_tags($new_instance['content']) : '';
		return $instance;
	}
}

function register_review_overall_widget() {
	register_widget('AllAgentsReviews\Controllers\Widget_Overall');
}

// Hook widget registration
add_action('widgets_init', 'AllAgentsReviews\Controllers\register_review_overall_widget');
?>
