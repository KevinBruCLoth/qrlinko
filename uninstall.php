<?php
	/**
	 * Fired when the plugin is uninstalled.
	 *
	 * @package ClothQrcode
	 */
	
	// If uninstall is not called from WordPress, exit.
	if (!defined('WP_UNINSTALL_PLUGIN')) :
		exit;
	endif;
	
	// Check if the user has the authority to uninstall plugins.
	if (!current_user_can('activate_plugins')) :
		exit;
	endif;
	
	// Load WordPress functions if not already loaded.
	if (!function_exists('get_posts')) :
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/post.php';
	endif;
	
	// Delete all posts of custom post types.
	$post_types = ['cloth-qrcodes'];
	foreach ($post_types as $post_type) :
		$posts = get_posts([
			'post_type' => $post_type,
			'numberposts' => -1,
			'post_status' => 'any',
		]);
		
		foreach ($posts as $post) :
			wp_delete_post($post->ID, true);
		endforeach;
	endforeach;
	
	// Delete all terms of custom taxonomies.
	$taxonomies = ['cloth-qrcode-categories'];
	foreach ($taxonomies as $taxonomy) :
		$terms = get_terms([
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		]);
		
		foreach ($terms as $term) :
			wp_delete_term($term->term_id, $taxonomy);
		endforeach;
	endforeach;
	
	// Delete plugin options and meta data.
	global $wpdb;
	$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'cloth_qrcodes_%'");
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'cloth_qrcodes_%'");
	
	// Flush rewrite rules to remove any custom rules.
	flush_rewrite_rules();
