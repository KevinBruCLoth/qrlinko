<?php
	
	namespace ClothQrcode\MetaBoxes;
	
	class Renderer {
		public function __construct() {
			add_action('add_meta_boxes', [$this, 'add_meta_box']);
		}
		
		
		/**********************************************************************
		 * ADD META BOX LOGIC
		 **********************************************************************/
		public function add_meta_box($post) {
			global $post;
			add_meta_box(
				'cloth_qrcodes_options_meta_box',
				__('QR Code Settings', 'cloth-qrcode'),
				[$this, 'render_meta_box_options'],
				'cloth-qrcodes',
				'normal',
				'high'
			);
			
			// Add stats meta box conditionally
			$code_mode = get_post_meta($post->ID, 'cloth_qrcodes_mode', true);
			if (!empty($code_mode) && $code_mode !== 'payment') :
				add_meta_box(
					'cloth_qrcodes_stats_meta_box',
					__('QR Code Statistics', 'cloth-qrcode'),
					[$this, 'render_meta_box_stats'],
					'cloth-qrcodes',
					'normal',
					'high'
				);
			endif;
		}
		
		
		/**********************************************************************
		 * RENDERING META BOX OPTIONS LOGIC
		 **********************************************************************/
		public function render_meta_box_options($post) {
			
			
			/**********************************************************************
			 * VARIABLES
			 **********************************************************************/
			$link = get_post_meta($post->ID, 'cloth_qrcodes_link', true);
			$code_mode = get_post_meta($post->ID, 'cloth_qrcodes_mode', true);
			$link_type = get_post_meta($post->ID, 'cloth_qrcodes_link_type', true);
			$fallback_link = get_post_meta($post->ID, 'cloth_qrcodes_fallback_link', true);
			$fallback_link_type = get_post_meta($post->ID, 'cloth_qrcodes_fallback_link_type', true);
			$qr_attachment_id = get_post_meta($post->ID, 'cloth_qrcodes_qr_attachment_id', true);
			$qr_attachment_png_id = get_post_meta($post->ID, 'cloth_qrcodes_qr_attachment_id_png', true);
			$params = get_post_meta($post->ID, 'cloth_qrcodes_params', true);
			$param_type = get_post_meta($post->ID, 'cloth_qrcodes_url_params_type', true);
			
			// Get all QR code post IDs
			$qr_post_ids = get_posts([
				'post_type' => 'cloth-qrcodes',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
			]);
			
			// Add current post ID to the exclusion list
			$exclude_ids = array_merge($qr_post_ids, [$post->ID]);
			
			$posts = get_posts([
				'post_type' => ['post', 'page'],
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'post__not_in' => $exclude_ids,
				'orderby' => 'title',
				'order' => 'ASC',
				'suppress_filters' => false,
			]);
			
			
			// Check if WPML is active
			$is_wpml_active = defined('ICL_SITEPRESS_VERSION');
			$posts_with_translations = [];
			$all_posts = [];
			
			
			// If WPML is active, fetch posts in all languages
			if ($is_wpml_active):
				global $sitepress;
				$current_language = $sitepress->get_current_language();
				$active_languages = $sitepress->get_active_languages();
				
				// Loop through each language
				foreach ($active_languages as $language):
					// Switch to the current language
					$sitepress->switch_lang($language['code']);
					
					// Fetch posts in the current language
					$posts = get_posts([
						'post_type' => ['post', 'page'],
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'post__not_in' => $exclude_ids,
						'orderby' => 'title',
						'order' => 'ASC',
						'suppress_filters' => false,
					]);
					
					// Add posts to the $all_posts array
					foreach ($posts as $language_post):
						$all_posts[$language_post->ID] = $language_post;
					endforeach;
				endforeach;
				
				// Switch back to the original language
				$sitepress->switch_lang($current_language);
			else:
				// Fallback: Fetch posts without WPML
				$all_posts = get_posts([
					'post_type' => ['post', 'page'],
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'post__not_in' => $exclude_ids,
					'orderby' => 'title',
					'order' => 'ASC',
				]);
			endif;
			
			
			//debug($all_posts);
			wp_nonce_field('cloth_qrcodes_meta_box', 'cloth_qrcodes_meta_box_nonce');
			$disabled = ($code_mode === 'payment') ? 'disabled' : '';
			
			/**********************************************************************
			 * RENDERING
			 **********************************************************************/
			
			include __DIR__ . '/templates/global/mode-selector.php';
			
			include __DIR__ . '/templates/global/url.php';
			
			if ($code_mode):
				$mode_template = __DIR__ . '/templates/modes/' . sanitize_key($code_mode) . '.php';
				if (file_exists($mode_template)) :
					include $mode_template;
				endif;
			else:
				$directory = __DIR__ . '/templates/modes/';
				$files = scandir($directory);
				$files = array_diff($files, array('.', '..'));
				if (!empty($files)):
					foreach ($files as $file) :
						if (pathinfo($file, PATHINFO_EXTENSION) === 'php') :
							include __DIR__ . '/templates/modes/' . basename($file);
						endif;
					endforeach;
				endif;
			endif;
			include __DIR__ . '/templates/global/qrcode.php';
			
		}
		
		
		/**********************************************************************
		 * RENDERING META BOX STATS LOGIC
		 **********************************************************************/
		public function render_meta_box_stats($post) {
			include __DIR__ . '/templates/global/stats.php';
		}
	}
