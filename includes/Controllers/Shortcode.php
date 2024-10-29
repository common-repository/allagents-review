<?php
namespace AllAgentsReviews\Controllers;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use AllAgentsReviews\Modules\Modules;

class Shortcode extends Modules {

	private $api_url_overall;
	private $api_url_reviews;
	private $api_key_overall;
	private $api_key_reviews;

	public function __construct() {
		$this->api_url_reviews = get_option("allagentsreviews_api_url_reviews");
		$this->api_key_reviews = get_option("allagentsreviews_api_key_reviews");
		$this->api_key_overall = get_option("allagentsreviews_api_key_overall");
		$this->api_url_overall = get_option("allagentsreviews_api_url_overall");

		// Register shortcode and AJAX actions
		add_action("wp_ajax_allagentsreviews_load_more_reviews_action", [$this, "load_more_reviews"]);
		add_action("wp_ajax_nopriv_allagentsreviews_load_more_reviews_action", [$this, "load_more_reviews"]);
		add_shortcode('allagents-display-reviews', [$this, 'display_reviews_shortcode']);
	}

	public function fetch_reviews() {
		// Check if transient data exists
		$cached_reviews = get_transient('allagentsreviews_review_cached_reviews');

		// If transient data exists, return cached reviews
		if ($cached_reviews) {
			return $cached_reviews;
		}

		// Fetch reviews from the API
		$user_agent = sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']));
		$headers    = array(
			'Authorization' => 'APIKEY ' . sanitize_text_field($this->api_key_reviews),
			'Content-Type'  => 'application/json',
			'User-Agent'    => $user_agent,
			'timeout'       => 10
		);

		$response = wp_remote_get(sanitize_url($this->api_url_reviews), array('headers' => $headers));
		
		// Check if request was successful
		if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
			$reviews = json_decode(wp_remote_retrieve_body($response), true);

			if (json_last_error() === JSON_ERROR_NONE) {
				// Set transient data to cache reviews for 1 hour
				set_transient('allagentsreviews_review_cached_reviews', $reviews, HOUR_IN_SECONDS);

				return $reviews;
			}
		}

		return false;
	}

	public function fetch_votes() {
		// Check if transient data exists
		$cached_reviews = get_transient('allagentsreviews_review_cached_reviews_overall');

		// If transient data exists, return cached reviews
		if ($cached_reviews) {
			return $cached_reviews;
		}

		// Fetch reviews from the API
		$user_agent = sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']));
		$headers    = array(
			'Authorization' => 'APIKEY ' . sanitize_text_field($this->api_key_overall),
			'Content-Type'  => 'application/json',
			'User-Agent'    => $user_agent,
			'timeout'       => 10
		);

		$response = wp_remote_get(sanitize_url($this->api_url_overall), array('headers' => $headers));

		// Check if request was successful
		if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
			$votes = json_decode(wp_remote_retrieve_body($response), true);
			if (json_last_error() === JSON_ERROR_NONE) {
				// Set transient data to cache reviews for 1 hour
				set_transient('allagentsreviews_review_cached_reviews_overall', $votes, HOUR_IN_SECONDS);

				return $votes;
			}
		}

		return false;
	}

	public function display_reviews($reviews) {

		$output = "";
		if ($reviews) {

			$perpage   = 5;
			$index     = 0;
			$slice     = array_slice($reviews, $index, $perpage);
			$lastCount = $perpage;
			$page      = 1;
			$votes     = $this->fetch_votes();

			$text_color = get_option("allagentsreviews_shortcode_text_color");
			$bg_color = get_option("allagentsreviews_shortcode_container_bg");
			$review_url = get_option("allagentsreviews_api_url_reviews");

			$main_branch = explode("/reviews", $review_url);
			$main_url = explode("/api/v1/firms/", $main_branch[0]);


			?>
			<div class="container-fluid allAgents-shortcode">

				<div class="scroll-footer d-flex justify-content-end mt-3">

					<div class="scroll-arrow-container">

						<div role="button" class="scroll-arrow left">
							<img  alt="Arrow Left" width="30" height="30" src="<?php echo plugin_dir_url( __FILE__).'../../assets/images/arrow-left-circle.svg' ?>">
						</div>
						<div role="button" class="scroll-arrow right" id="load-more-btn">
							<img  alt="Arrow Rigth" width="30" height="30" src="<?php echo plugin_dir_url( __FILE__).'../../assets/images/arrow-right-circle.svg' ?>">
							<div class="spinner-border text-secondary d-none" id="loading-spinner-allAgents" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							<input type="hidden" value="<?php echo esc_attr($lastCount); ?>" id="itemLast">
							<input type="hidden" value="<?php echo esc_attr($perpage); ?>" id="perPage">
							<input type="hidden" value="<?php echo esc_attr($page); ?>" id="currentPage">
						</div>
					</div>
				</div>
				<div class="scroll-container">
					<?php foreach ($slice as $key => $review): ?>
						<?php
						// Find the position of the last space within the first 40 characters
						$lastSpacePos = strrpos(substr($review['review'], 0, 40), ' ');

						$title = "";
						// If a space was found, extract the substring up to that position
						if ($lastSpacePos !== false) {
							$title = substr($review['review'], 0, $lastSpacePos);
						} else {
							// If no space was found, use the first 15 characters as the title
							$title = substr($review['review'], 0, 15);
						}
						?>
						<div class="scroll-item" style="color: <?php echo esc_attr($text_color); ?>">
							<div class="card border border-0 d-block w-100" style="width: 400px; min-width: 400px; height: auto; user-select: none; background: <?php echo esc_attr($bg_color); ?>">
								<div class="card-header bg-transparent border border-0 d-flex justify-content-between align-items-baseline">
									<div class="rating">


										<?php $this->displayRatingIcons($review['rating']); ?>
									</div>
									<div class="allAgents-review-date" style="color: <?php echo esc_attr($text_color); ?>">
										<?php echo esc_html($this->timeAgo($review['date_added'])); ?>
									</div>
								</div>
								<div class="card-body m-0">
									<div class="header"><?php echo esc_html($title); ?></div>
									<div class="text truncated-text"><?php echo esc_html($review['review']); ?></div>
									<a target="_blank" href="https://www.allagents.co.uk/review/<?php echo esc_attr($review['rid']); ?>" class="btn btn-link btn-sm view-more-btn p-0 " style="font-size: 12px; color: <?php echo esc_attr($text_color); ?>"><?php esc_html_e("Read More", "allagents-reviews"); ?></a>
									<div class="date-and-user-info-wrapper mt-2">
										<div class="name secondary-text"><?php echo esc_html($review['name']); ?></div>
										<div class="fw-light secondary-text">
											<?php
											$capacity = isset($review['capacityString']) ? $review['capacityString'] : 'No Service Used';
											echo esc_html($capacity);
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="d-flex justify-content-center mt-5">
        <span>
            <span class="fw-bold" style="font-size: 16px"><?php echo esc_html($votes['rating']); ?> </span>
            <?php esc_html_e("out of 5 based on", "allagents-reviews"); ?>
            <a href="<?php echo esc_url($main_url[0] . "/" . $main_url[1]) ?>.co.uk" target="_blank" class="fw-bold text-dark text-decoration-underline" style="font-size: 16px">
                <?php echo esc_html($votes['votes']); ?> <?php esc_html_e("reviews", "allagents-reviews"); ?>
            </a>
            <?php esc_html_e("on allAgents", "allagents-reviews"); ?>
        </span>
				</div>
			</div>

<?php
		} else {
			$output .= '<p>API Error: Error loading reviews.</p>';
		}

		return $output;
	}

	public function display_reviews_shortcode($atts) {
		// Fetch reviews
		$reviews = $this->fetch_reviews();


		// Display reviews
		$output = $this->display_reviews($reviews);

		return wp_kses_post($output);
	}

	public function load_more_reviews() {
		if (isset($_POST["action"]) && $_POST["action"] === "allagentsreviews_load_more_reviews_action" && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'allagentsreviews_ajax_nonce')) {
			wp_send_json($this->fetch_reviews());
		} else {
			wp_send_json_error('Invalid request');
		}
	}
}

?>
