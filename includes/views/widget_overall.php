<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use AllAgentsReviews\Controllers\Shortcode;

$class      = new Shortcode();
$overall    = $class->fetch_votes();
$image_url  = plugins_url('../../assets/images/logo.png', __FILE__);
$text_color = get_option("allagentsreviews_overall_widget_text_color");
$bg_color   = get_option("allagentsreviews_overall_widget_background_color");
$review_url = get_option("allagentsreviews_api_url_reviews");
$review_url = str_replace('/api/v1/firms', '', $review_url);
$profile_url = explode("/branches", $review_url);

?>

<div class="allAgents-widget-overall">
    <div class="scroll-container-overall">
        <!-- Your scrollable content goes here -->
        <div class="scroll-item-overall" style="color: <?php echo esc_attr($text_color); ?>;">
            <div class="card d-block w-100" style="background: <?php echo esc_attr($bg_color); ?>;">
                <a target="_blank" href="<?php echo esc_url($profile_url[0]); ?>" class="card-img-top d-flex justify-content-center align-items-center text-center">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php esc_attr_e('allAgent', 'allagents-reviews'); ?>" class="text-center w-50 mt-4">
                </a>
                <div class="card-body m-0 p-1">
                    <div class="card-title fs-4 d-flex justify-content-center text-center bg-transparent border-0">
						<?php $class->displayRatingIcons($overall['rating']); ?>
                    </div>
                    <div class="d-flex justify-content-center">
                        <span class="fw-bold" style="font-size: 14px;">
                            <?php
                            printf(
	                            esc_html('Rated %1$s | %2$s reviews on allAgents'),
	                            esc_html($overall['rating']),
	                            esc_html($overall['votes'])
                            );
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add more items as needed -->
    </div>
</div>
