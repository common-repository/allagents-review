<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use AllAgentsReviews\Controllers\Shortcode;

$class      = new Shortcode();
$reviews    = $class->fetch_reviews();
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
                        <a target="_blank" href="https://www.allagents.co.uk/review/<?php echo esc_attr($review['rid']); ?>" class="btn btn-link btn-sm view-more-btn p-0 bg-transparent" style="font-size: 12px;">
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
