<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class AllAgentsReview_AutoLoader {

	/**
	 * Registers the autoloader function with SPL.
	 */
	public static function register() {
		spl_autoload_register([__CLASS__, 'autoload']);
	}

	/**
	 * Autoloads the class.
	 *
	 * @param string $class The class name to autoload.
	 */
	public static function autoload($class) {
		// Define the base directory for the namespace prefix
		$base_dir = plugin_dir_path(__FILE__) . 'includes/';

		// Remove the namespace prefix and convert it to a file path
		$class = str_replace('AllAgentsReviews\\', '', $class);
		$file = $base_dir . str_replace('\\', '/', $class) . '.php';

		// If the file exists, require it
		if (file_exists($file)) {
			require $file;
		}
	}
}

// Register the autoloader
AllAgentsReview_AutoLoader::register();
