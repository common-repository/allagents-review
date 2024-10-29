<?php

/*
Plugin Name: allAgents Reviews
Plugin URI: https://www.allagents.co.uk/plugins/allagents-reviews
Description: This WordPress plugin allows users to fetch and display reviews from AllAgents.co.uk directly on their website. With seamless integration, users can showcase authentic reviews from AllAgents.co.uk to build trust and credibility with their audience.
Version: 1.4.0
Author: allAgents
Author URI: https://www.allagents.co.uk
License: GNU
License URI: https://opensource.org/licenses/MIT
Text Domain: allagents-reviews
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


require_once plugin_dir_path( __FILE__ ) . "AllAgentsReview_AutoLoader.php";




add_action( 'plugin_loaded',["AllAgentsReviews\\Controllers\\Init", "instance"] );