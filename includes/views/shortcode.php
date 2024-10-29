<?php
namespace AllAgentsReviews\Controllers;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use AllAgentsReviews\Controllers\Shortcode;

$class     = new Shortcode();
$reviews   = $class->fetch_reviews();
$perpage   = 5;
$index     = 0;
$slice     = array_slice($reviews, $index, $perpage);
$lastCount = $perpage;
$page      = 1;
$votes     = $class->fetch_votes();

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
							<?php $class->displayRatingIcons($review['rating']); ?>
                        </div>
                        <div class="allAgents-review-date" style="color: <?php echo esc_attr($text_color); ?>">
							<?php echo esc_html($class->timeAgo($review['date_added'])); ?>
                        </div>
                    </div>
                    <div class="card-body m-0">
                        <div class="header"><?php echo esc_html($title); ?></div>
                        <div class="text truncated-text"><?php echo esc_html($review['review']); ?></div>
                        <a target="_blank" href="https://www.allagents.co.uk/review/<?php echo esc_attr($review['rid']); ?>" class="btn btn-link btn-sm view-more-btn p-0 bg-transparent" style="font-size: 12px"><?php esc_html_e("Read More", "allagents-reviews"); ?></a>
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
