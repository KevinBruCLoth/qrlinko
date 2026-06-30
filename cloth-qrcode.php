<?php
	/**
	 * Plugin Name: Cloth Qrcode
	 * Plugin URI: http://cloth.be/
	 * Description: QR code Plugin Management for WordPress
	 * Version: 1.1.1
	 * Author: Cloth Web Team
	 * License: GPL v2 or later
	 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
	 * Text Domain: cloth-qrcode
	 * Domain Path: /languages
	 */
	
	// Security check
	defined('ABSPATH') or die('Access denied.');
	
	// Define plugin constants
	define('CLOTH_QRCODE_VERSION', '1.1.1');
	define('CLOTH_QRCODE_PATH', plugin_dir_path(__FILE__));
	define('CLOTH_QRCODE_URL', plugin_dir_url(__FILE__));
	
	// Load helper functions
	if (file_exists(CLOTH_QRCODE_PATH . 'includes/debug.php')) {
		require_once CLOTH_QRCODE_PATH . 'includes/debug.php';
	}
	if (file_exists(CLOTH_QRCODE_PATH . 'includes/helpers.php')) {
		require_once CLOTH_QRCODE_PATH . 'includes/helpers.php';
	}
	
	// Load Composer autoloader with isolation to avoid conflicts with ACF
	if (file_exists(__DIR__ . '/vendor/autoload.php')) {
		// Load the autoloader
		$autoloader = require_once __DIR__ . '/vendor/autoload.php';
		
		// Manually include the AbstractEnum class if it's not found
		if (!class_exists('DASPRiD\Enum\AbstractEnum')) {
			$enumPath = __DIR__ . '/vendor/dasprid/enum/src/AbstractEnum.php';
			if (file_exists($enumPath)) {
				require_once $enumPath;
			} else {
				add_action('admin_notices', function () {
					echo '<div class="notice notice-error"><p>';
					_e('Cloth QR Code plugin error: Required dependency missing. Please run <code>composer install</code> in the plugin directory.', 'cloth-qrcode');
					echo '</p></div>';
				});
				return;
			}
		}
	} else {
		add_action('admin_notices', function () {
			echo '<div class="notice notice-error"><p>';
			_e('Cloth QR Code plugin error: Composer autoloader not found. Please run <code>composer install</code> in the plugin directory.', 'cloth-qrcode');
			echo '</p></div>';
		});
		return;
	}
	
	// Load the autoloader for plugin classes
	if (file_exists(CLOTH_QRCODE_PATH . 'includes/class-autoloader.php')) {
		require_once CLOTH_QRCODE_PATH . 'includes/class-autoloader.php';
	}
	
	/**
	 * Initialize the plugin.
	 */
	function cloth_qrcode_init() {
		if (class_exists('ClothQrcode\Plugin')) {
			$plugin = new ClothQrcode\Plugin();
			$plugin->run();
		}
	}
	
	add_action('plugins_loaded', 'cloth_qrcode_init', 20); // Increase priority to load after ACF
	
	/**
	 * Load plugin textdomain for translations.
	 */
	function cloth_qrcode_load_textdomain() {
		load_plugin_textdomain(
			'cloth-qrcode',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages/'
		);
	}
	
	add_action('plugins_loaded', 'cloth_qrcode_load_textdomain');
	
	/**
	 * Flush rewrite rules on plugin activation and deactivation.
	 */
	function cloth_qrcode_activate() {
		flush_rewrite_rules();
	}
	
	register_activation_hook(__FILE__, 'cloth_qrcode_activate');
	
	function cloth_qrcode_deactivate() {
		flush_rewrite_rules();
	}
	
	register_deactivation_hook(__FILE__, 'cloth_qrcode_deactivate');
