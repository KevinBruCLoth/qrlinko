<?php
	namespace ClothQrcode\MetaBoxes;
	
	class Ajax {
		public function __construct() {
			add_action('wp_ajax_get_template_row_campaign', [$this, 'get_template_row_campaign']);
			add_action('wp_ajax_get_template_row_limit', [$this, 'get_template_row_limit']);
			add_action('wp_ajax_get_template_row_regular_params', [$this, 'get_template_row_regular_params']);
		}
		
		private function verify_request() {
			check_ajax_referer('cloth_qrcode_admin_ajax', 'nonce');
			
			if (!current_user_can('edit_posts')) :
				wp_send_json_error([
					'message' => __('You are not allowed to edit QR codes.', 'cloth-qrcode'),
				], 403);
			endif;
		}
		
		private function get_index() {
			return isset($_POST['index']) ? absint($_POST['index']) : time();
		}
		
		private function get_available_posts($current_post_id = 0) {
			$qr_post_ids = get_posts([
				'post_type' => 'cloth-qrcodes',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
			]);
			
			$exclude_ids = array_merge($qr_post_ids, [absint($current_post_id)]);
			$all_posts = [];
			
			if (defined('ICL_SITEPRESS_VERSION')) :
				global $sitepress;
				$current_language = $sitepress->get_current_language();
				$active_languages = $sitepress->get_active_languages();
				
				foreach ($active_languages as $language) :
					$sitepress->switch_lang($language['code']);
					$posts = get_posts([
						'post_type' => ['post', 'page'],
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'post__not_in' => $exclude_ids,
						'orderby' => 'title',
						'order' => 'ASC',
						'suppress_filters' => false,
					]);
					
					foreach ($posts as $post) :
						$all_posts[$post->ID] = $post;
					endforeach;
				endforeach;
				
				$sitepress->switch_lang($current_language);
			else :
				$all_posts = get_posts([
					'post_type' => ['post', 'page'],
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'post__not_in' => $exclude_ids,
					'orderby' => 'title',
					'order' => 'ASC',
				]);
			endif;
			
			return $all_posts;
		}
		
		public function get_template_row_regular_params() {
			$this->verify_request();
			$index = $this->get_index();
			
			ob_start();
			?>
			<td><input type="text" required name="cloth_qrcodes_params[<?= esc_attr($index); ?>][key]" style="width:100%;"></td>
			<td><input required type="text" name="cloth_qrcodes_params[<?= esc_attr($index); ?>][value]" style="width:100%;"></td>
			<td>
				<button type="button" class="button cloth-qrcode-remove-row"><?php _e('Remove', 'cloth-qrcode'); ?></button>
			</td>
			<?php
			$html = ob_get_clean();
			wp_send_json_success([
				'row' => $html,
			]);
		}
		
		public function get_template_row_campaign() {
			$this->verify_request();
			$current_post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
			$posts = $this->get_available_posts($current_post_id);
			$index = $this->get_index();
			
			ob_start();
			?>
			<td>
				<select name="cloth_qrcodes_campaign_entries[<?= esc_attr($index); ?>][link_type]" style="width: 100%;" class="cloth-qrcode-campaign-link-type">
					<option value="external"><?php _e('External', 'cloth-qrcode'); ?></option>
					<option value="internal"><?php _e('Internal', 'cloth-qrcode'); ?></option>
				</select>
			</td>
			<td>
				<div class="cloth-qrcode-campaign-link-container">
					<input required type="url" name="cloth_qrcodes_campaign_entries[<?= esc_attr($index); ?>][link]" style="width: 100%;" class="cloth-qrcode-campaign-external-link"/>
					<select name="cloth_qrcodes_campaign_entries[<?= esc_attr($index); ?>][internal_link]" style="width: 100%; display: none;" class="cloth-qrcode-campaign-internal-link">
						<option value=""><?php _e('— Select a page or post —', 'cloth-qrcode'); ?></option>
						<?php foreach ($posts as $p) : ?>
							<option value="<?= esc_attr($p->ID); ?>">
								<?= esc_html(get_post_type_object($p->post_type)->labels->singular_name) . ': ' . esc_html($p->post_title); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</td>
			<td>
				<input required type="text" name="cloth_qrcodes_campaign_entries[<?= esc_attr($index); ?>][start_datetime]" class="cloth-qrcode-datetimepicker" style="width: 100%;"/>
			</td>
			<td>
				<input required type="text" name="cloth_qrcodes_campaign_entries[<?= esc_attr($index); ?>][end_datetime]" class="cloth-qrcode-datetimepicker" style="width: 100%;"/>
			</td>
			<td>
				<textarea name="cloth_qrcodes_campaign_entries[<?= esc_attr($index); ?>][params]" style="width: 100%;"></textarea>
			</td>
			<td>
				<button type="button" class="button cloth-qrcode-remove-campaign-row"><?php _e('Remove', 'cloth-qrcode'); ?></button>
			</td>
			<?php
			$html = ob_get_clean();
			wp_send_json_success([
				'row' => $html,
			]);
		}
		
		public function get_template_row_limit() {
			$this->verify_request();
			$current_post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
			$posts = $this->get_available_posts($current_post_id);
			$index = $this->get_index();
			
			ob_start();
			?>
			<td>
				<select name="cloth_qrcodes_limit_entries[<?= esc_attr($index); ?>][link_type]" style="width: 100%;" class="cloth-qrcode-limit-link-type">
					<option value="external"><?php _e('External', 'cloth-qrcode'); ?></option>
					<option value="internal"><?php _e('Internal', 'cloth-qrcode'); ?></option>
				</select>
			</td>
			<td>
				<div class="cloth-qrcode-limit-link-container">
					<input required type="url" name="cloth_qrcodes_limit_entries[<?= esc_attr($index); ?>][link]" style="width: 100%;" class="cloth-qrcode-limit-external-link"/>
					<select name="cloth_qrcodes_limit_entries[<?= esc_attr($index); ?>][internal_link]" style="width: 100%; display: none;" class="cloth-qrcode-limit-internal-link">
						<option value=""><?php _e('— Select a page or post —', 'cloth-qrcode'); ?></option>
						<?php foreach ($posts as $p) : ?>
							<option value="<?= esc_attr($p->ID); ?>">
								<?= esc_html(get_post_type_object($p->post_type)->labels->singular_name) . ': ' . esc_html($p->post_title); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</td>
			<td>
				<input type="number" name="cloth_qrcodes_limit_entries[<?= esc_attr($index); ?>][scan_limit]" style="width: 100%;" min="1" placeholder="<?php esc_attr_e('Scan Limit', 'cloth-qrcode'); ?>"/>
			</td>
			<td>
				<textarea name="cloth_qrcodes_limit_entries[<?= esc_attr($index); ?>][params]" style="width: 100%;"></textarea>
			</td>
			<td>
				<button type="button" class="button cloth-qrcode-remove-limit-row"><?php _e('Remove', 'cloth-qrcode'); ?></button>
			</td>
			<?php
			$html = ob_get_clean();
			wp_send_json_success([
				'row' => $html,
			]);
		}
	}
