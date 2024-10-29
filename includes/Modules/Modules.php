<?php

namespace AllAgentsReviews\Modules;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Modules {


	function displayRatingIcons($rating) {
		$maxRating = 5; // Maximum rating value
		$fullStar = "<img width='20' style='color:#FFD700' class='allagents-reviews-rating-icon' src='".plugins_url('../../assets/images/full-star.svg', __FILE__)."' alt='five star' />" ; // HTML for full star icon
		$halfStar =  "<img width='20' style='color:#FFD700' class='allagents-reviews-rating-icon' src='".plugins_url('../../assets/images/half-star.svg', __FILE__)."' alt='five star' />" ; // HTML for half star icon
		$emptyStar =   "<img width='20' style='color:#FFD700' class='allagents-reviews-rating-icon' src='".plugins_url('../../assets/images/empty-star.svg', __FILE__)."' alt='five star' />" ; // HTML for empty star icon
		
	
		$output = '';

		// Calculate the number of full stars
		$fullStars = floor($rating);

		// Calculate whether there's a half star
		$hasHalfStar = $rating - $fullStars >= 0.5;

		// Add full stars
		for ($i = 0; $i < $fullStars; $i++) {
			$output .= $fullStar;
		}

		// Add half star if necessary
		if ($hasHalfStar) {
			$output .= $halfStar;
			$fullStars++; // Increment full stars count to adjust for half star
		}

		// Add empty stars to reach maximum rating
		$emptyStars = $maxRating - $fullStars;
		for ($i = 0; $i < $emptyStars; $i++) {
			$output .= $emptyStar;
		}
		
		echo wp_kses_post($output);
	}


	function timeAgo($date) {
		$timestamp    = strtotime( $date );
		$current_time = time();
		$time_diff    = $current_time - $timestamp;
		$seconds      = $time_diff;
		$minutes      = round( $seconds / 60 );
		$hours        = round( $seconds / 3600 );
		$days         = round( $seconds / 86400 );
		$weeks        = round( $seconds / 604800 );
		$months       = round( $seconds / 2629440 );
		$years        = round( $seconds / 31553280 );

		if ( $seconds <= 60 ) {
			return "Just now";
		} elseif ( $minutes <= 60 ) {
			if ( $minutes == 1 ) {
				return "1 minute ago";
			} else {
				return "$minutes minutes ago";
			}
		} elseif ( $hours <= 24 ) {
			if ( $hours == 1 ) {
				return "1 hour ago";
			} else {
				return "$hours hours ago";
			}
		} elseif ( $days <= 7 ) {
			if ( $days == 1 ) {
				return "Yesterday";
			} else {
				return "$days days ago";
			}
		} elseif ( $weeks <= 4.3 ) {
			if ( $weeks == 1 ) {
				return "1 week ago";
			} else {
				return "$weeks weeks ago";
			}
		} elseif ( $months <= 12 ) {
			if ( $months == 1 ) {
				return "1 month ago";
			} else {
				return "$months months ago";
			}
		} else {
			if ( $years == 1 ) {
				return "1 year ago";
			} else {
				return "$years years ago";
			}
		}
	}

	function averagerating( $reviews ) {
		$totalRating = 0; // Initialize total rating count
		$totalReviews = count($reviews); // Get the total number of reviews

// Loop through each review to calculate total rating count
		foreach ($reviews as $review) {
			$totalRating += $review->rating;
		}

// Calculate average rating count
		if ($totalReviews > 0) {
			$averageRating = $totalRating / $totalReviews;
		} else {
			$averageRating = 0; // To avoid division by zero if there are no reviews
		}
		return $averageRating;
	}
}