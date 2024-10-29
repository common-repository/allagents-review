<?php
namespace AllAgentsReviews\Controllers;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use WP_Widget;

class Widget extends WP_Widget {
	// Constructor
	function __construct() {
		parent::__construct(
			'all_reviews_widget', // Base ID
			__('allAgent Reviews', 'allagents-reviews'), // Name
			array( 'description' => __('Displays reviews for both classic and block editors', 'allagents-reviews') ) // Args
		);
	}

	public function display_reviews($reviews) {
		$output = "";
		if ($reviews) {
			$class      = new Shortcode();
			$perpage    = 5;
			$slice      = array_slice($reviews, 0, $perpage);
			$lastCount  = $perpage;
			$page       = 1;
			$text_color = get_option("allagentsreviews_widget_text_color");
			$bg_color   = get_option("allagentsreviews_widget_background_color");
			?>

            <div class="allAgents-widget">
                <div class="scroll-container">
					<?php foreach ($slice as $key => $review): ?>
						<?php
						// Find the position of the last space within the first 15 characters
						$lastSpacePos = strrpos(substr($review['review'], 0, 15), ' ');

						// If a space was found, extract the substring up to that position
						$title = ($lastSpacePos !== false) ? substr($review['review'], 0, $lastSpacePos) : substr($review['review'], 0, 15);
						?>
                        <div class="scroll-item" style="color: <?php echo esc_attr($text_color); ?>;">
                            <div class="card d-block w-100" style="user-select: none; background: <?php echo esc_attr($bg_color); ?>;">
                                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-baseline">
                                    <div class="rating">
										<?php $class->displayRatingIcons($review['rating']); ?>
                                    </div>
                                    <div class="allAgents-review-date text-secondary">
										<?php echo esc_html($class->timeAgo($review['date_added'])); ?>
                                    </div>
                                </div>
                                <div class="card-body m-0">
                                    <div class="header"><?php echo esc_html($title); ?></div>
                                    <div class="text truncated-text"><?php echo esc_html($review['review']); ?></div>
                                    <a target="_blank" href="https://www.allagents.co.uk/review/<?php echo esc_attr($review['rid']); ?>" class="btn btn-link btn-sm view-more-btn p-0" style="font-size: 12px; color: <?php echo esc_attr($text_color); ?>;">
										<?php esc_html_e("Read More", "allagents-reviews"); ?>
                                    </a>
                                    <div class="date-and-user-info-wrapper mt-2">
                                        <div class="name secondary-text"><?php echo esc_html($review['name']); ?></div>
                                        <div class="fw-light secondary-text">
											<?php
											$capacity = isset($review['capacityString']) ? $review['capacityString'] : esc_html__('No Service Used', 'allagents-reviews');
											echo esc_html($capacity);
											?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php endforeach; ?>
                </div>
                <div class="scroll-footer d-flex justify-content-end  mt-3">
                    <div class="scroll-arrow-container d-flex gap-2">
                        <div role="button" class="scroll-arrow left">
                            <img  alt="Arrow Left" width="20" height="20" src="<?php echo plugin_dir_url( __FILE__).'../../assets/images/arrow-left-circle.svg' ?>">
                        </div>
                        <div role="button" class="scroll-arrow right" id="loading-spinner">
                            <img  alt="Arrow Rigth" width="20" height="20" src="<?php echo plugin_dir_url( __FILE__).'../../assets/images/arrow-right-circle.svg' ?>">
                            <div class="spinner-border text-secondary d-none" id="loading-spinner" role="status">
                                <span class="visually-hidden"><?php esc_html_e('Loading...', 'allagents-reviews'); ?></span>
                            </div>
                            <input type="hidden" value="<?php echo esc_attr($lastCount); ?>" id="itemLast">
                            <input type="hidden" value="<?php echo esc_attr($perpage); ?>" id="perPage">
                            <input type="hidden" value="<?php echo esc_attr($page); ?>" id="currentPage">
                        </div>
                    </div>
                </div>
            </div>
			<?php
		} else {
			$output .= '<p>' . __('API Error: Error loading reviews.', 'allagents-reviews') . '</p>';
		}

		return $output;
	}

	// Widget Output
	public function widget( $args, $instance ) {
		$reviews = (new Shortcode())->fetch_reviews();
		// Widget content here
		echo '<div class="allAgents-reviews-widget">';
		// Escaping the title for safe output
		echo '<h2>' . esc_html( $instance['title'] ) . '</h2>';
		$instance["content"] = $this->display_reviews($reviews); // Assuming display_reviews() returns safe HTML
		// Ensuring content is properly escaped for output
		echo wp_kses_post($instance["content"]);
		echo '</div>';
	}

	// Widget Form
	public function form( $instance ) {
		// Sanitizing the title
		$title = ! empty( $instance['title'] ) ? sanitize_text_field($instance['title'] ) : '';
		?>
        <!-- Title Field -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'allagents-reviews'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
		<?php
	}

	// Update Widget Settings
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		// Sanitizing title and content before saving
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['content'] = ( ! empty( $new_instance['content'] ) ) ? wp_strip_all_tags($new_instance['content'] ) : '';
		return $instance;
	}
}

function register_review_widget() {
	register_widget( 'AllAgentsReviews\Controllers\Widget' );
}

// Hook widget registration
add_action( 'widgets_init', 'AllAgentsReviews\Controllers\register_review_widget' );

?>
