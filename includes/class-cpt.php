<?php
	namespace ClothQrcode;
	
	class CPT {
		public function __construct() {
			add_action('init', [$this, 'register_post_types'], 5);
		}
		
		public function register_post_types() {
			register_taxonomy(
				'cloth-qrcodes-categories',
				array('cloth-qrcodes'),
				array(
					'labels' => array(
						'name' => __('Categories', 'cloth-qrcode'),
						'singular_name' => __('Category', 'cloth-qrcode'),
						'search_items' => __('Search', 'cloth-qrcode'),
						'all_items' => __('All', 'cloth-qrcode'),
						'parent_item' => __('Parents', 'cloth-qrcode'),
						'parent_item_colon' => __('Parents:', 'cloth-qrcode'),
						'edit_item' => __('Edit', 'cloth-qrcode'),
						'update_item' => __('Update', 'cloth-qrcode'),
						'add_new_item' => __('Add', 'cloth-qrcode'),
						'new_item_name' => __('Name', 'cloth-qrcode'),
						'menu_name' => __('Categories', 'cloth-qrcode'),
					),
					'hierarchical' => false,
					'show_ui' => true,
					'show_admin_column' => true,
					'query_var' => true,
					'rewrite' => array('slug' => 'cloth-qrcodes-categories'),
					'wpml_config' => true,
				)
			);
			
			// Register Cloth Qrcodes CPT
			register_post_type('cloth-qrcodes', [
				'labels' => [
					'name' => __('Cloth Qrcode', 'cloth-qrcode'),
					'singular_name' => __('Cloth Qrcode', 'cloth-qrcode'),
					'add_new' => __('Add New', 'cloth-qrcode'),
					'add_new_item' => __('Add New Qrcode', 'cloth-qrcode'),
					'edit_item' => __('Edit Qrcode', 'cloth-qrcode'),
					'new_item' => __('New Qrcode', 'cloth-qrcode'),
					'view_item' => __('View Qrcodes', 'cloth-qrcode'),
					'search_items' => __('Search Qrcodes', 'cloth-qrcode'),
					'not_found' => __('No Qrcode found', 'cloth-qrcode'),
					'not_found_in_trash' => __('No Qrcodes found in Trash', 'cloth-qrcode'),
				],
				'public' => true,
				'hierarchical' => false,
				'has_archive' => false,
				'supports' => ['title'],
				'show_in_rest' => true,
				'show_in_menu' => false,
				'can_export' => true,
				
				'rewrite' => [
					'slug' => 'qrcodes',
					'with_front' => false,
				],
				'wpml_config' => true,
			]);
		}
	}
