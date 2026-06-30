<?php
	
	namespace ClothQrcode;
	
	use ClothQrcode\MetaBoxes\Ajax;
	use ClothQrcode\MetaBoxes\Enqueue;
	use ClothQrcode\MetaBoxes\Renderer;
	use ClothQrcode\MetaBoxes\Saver;
	
	class MetaBoxes {
		
		public function __construct() {
			new Renderer();
			new Saver();
			new Enqueue();
			new Ajax();
			
		}
		
		/**********************************************************************
		 * ENQUEUE SCRIPT AND STYLE (ADMIN)
		 **********************************************************************/
		public function enqueue_admin_scripts($hook) {
			if (in_array($hook, ['post.php', 'post-new.php'])) :
				global $post_type;
				if ($post_type === 'cloth-qrcodes') :
					wp_enqueue_style(
						'cloth-qrcode-admin-css',
						plugins_url('assets/css/admin.css', __DIR__),
						[],
						filemtime(plugin_dir_path(__DIR__) . 'assets/css/admin.css')
					);
					
					wp_enqueue_script(
						'cloth-qrcode-admin-js',
						plugins_url('assets/js/admin.js', __DIR__),
						['jquery', 'jquery-ui-datepicker'],
						filemtime(plugin_dir_path(__DIR__) . 'assets/js/admin.js'),
						true
					);
					
					
					// PASS AJAX_URL TO SCRIPT
					wp_localize_script('cloth-qrcode-admin-js', 'plugin_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
					
					wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
					wp_enqueue_script('jquery-ui-timepicker-addon', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', ['jquery-ui-datepicker'], '1.6.3', true);
					wp_enqueue_style('jquery-ui-timepicker-addon-css', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css');
				endif;
			endif;
		}
		
		/**********************************************************************
		 * ADD META BOXES LOGIC
		 **********************************************************************/
        /*
		public function add_meta_box($id) {
			add_meta_box(
				'cloth_qrcodes_options_meta_box',
				__('QR Code Settings', 'cloth-qrcode'),
				[$this, 'render_meta_box_options'],
				'cloth-qrcodes',
				'normal',
				'high'
			);
			
			global $post;
			$code_mode = get_post_meta($post->ID, 'cloth_qrcodes_mode', true);
			
			if (!empty($code_mode) && $code_mode !== 'payment'):
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
		*/
		/**********************************************************************
		 * RENDERING META BOX OPTIONS LOGIC
		 **********************************************************************/
		/*
		public function render_meta_box_options($post) {
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
			
			
			// If WPML is active, fetch posts in all languages
			if ($is_wpml_active):
				global $sitepress;
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
					foreach ($posts as $post):
						$all_posts[$post->ID] = $post;
					endforeach;
				endforeach;
				
				// Switch back to the original language
				$sitepress->switch_lang($sitepress->get_current_language());
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
			
			
			?>
			<?php //--------- QRCODE TYPE SELECTOR (REGULAR OR CAMPAIGN) ---------//  ?>
            <div style="margin-bottom: 15px;">
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_mode">
					<?php _e('QR Code Mode', 'cloth-qrcode'); ?>
                </label>
                <select <?= !empty($code_mode) ? 'disabled' : ''; ?> id="cloth_qrcodes_mode" name="cloth_qrcodes_mode" style="width: 100%;" required>
                    <option value="">
						<?php _e('Choose QrCode Mode', 'cloth-qrcode'); ?>
                    </option>
                    <option value="regular" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'regular'); ?>>
						<?php _e('Regular', 'cloth-qrcode'); ?>
                    </option>
                    <option value="campaign" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'campaign'); ?>>
						<?php _e('Campaign', 'cloth-qrcode'); ?>
                    </option>
                    <option value="payment" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'payment'); ?>>
						<?php _e('Payment', 'cloth-qrcode'); ?>
                    </option>
                    <option value="maps" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'maps'); ?>>
						<?php _e('Google Maps', 'cloth-qrcode'); ?>
                    </option>
                    <option value="wifi" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'wifi'); ?>>
                        <?php _e('Wi-Fi', 'cloth-qrcode'); ?>
                    </option>
                </select>
				<?php if (!empty($code_mode)): ?>
                    <input type="hidden" id="cloth_qrcodes_mode" name="cloth_qrcodes_mode" value="<?= get_post_meta($post->ID, 'cloth_qrcodes_mode', true) ?>">
				<?php endif; ?>
            </div>
			
			<?php //--------- QRCODE TYPE REGULAR ---------//  ?>
			<?php if (empty($code_mode) || $code_mode === 'regular') : ?>
                <div id="cloth_qrcodes_regular_container" data-element="container" style="<?= $code_mode === 'regular' ? 'display: block;' : 'display: none'; ?>">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_link_type">
							<?php _e('Link Type', 'cloth-qrcode'); ?>
                        </label>
                        <select id="cloth_qrcodes_link_type" name="cloth_qrcodes_link_type" style="width: 100%;">
                            <option value="external" <?php selected($link_type, 'external'); ?>><?php _e('External URL', 'cloth-qrcode'); ?></option>
                            <option value="internal" <?php selected($link_type, 'internal'); ?>><?php _e('Internal URL', 'cloth-qrcode'); ?></option>
                        </select>
                    </div>

                    <div id="cloth_qrcodes_external_link_container" style="margin-bottom: 15px; <?= $link_type === 'external' || empty($link_type) ? '' : 'display: none;'; ?>">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_link">
							<?php _e('External Link', 'cloth-qrcode'); ?>
                        </label>
                        <input type="url" id="cloth_qrcodes_link" name="cloth_qrcodes_link" value="<?= is_array($link) && array_key_exists('link', $link) ? esc_url($link['link']) : ''; ?>" style="width: 100%;"/>
                        <p class="description"><?php _e('Enter the external URL for the QR code.', 'cloth-qrcode') ?></p>
                    </div>
                    <div id="cloth_qrcodes_internal_link_container" style="margin-bottom: 15px; <?= $link_type === 'internal' ? '' : 'display: none;'; ?>">
                        <label for="cloth_qrcodes_internal_link" style="font-weight: bold; margin-bottom: 10px; display:block;">
							<?php _e('Internal Link', 'cloth-qrcode'); ?>
                        </label>
                        <select id="cloth_qrcodes_internal_link" name="cloth_qrcodes_internal_link" style="width: 100%;">
                            <option value=""><?php _e('— Select a page or post —', 'cloth-qrcode'); ?></option>
							<?php
								$internal_link = ($link_type === 'internal') ? $link['internal_link'] : '';
								foreach ($all_posts as $p):
									$selected = selected($internal_link, $p->ID, false);
									
									// Get language info if WPML is active
									if ($is_wpml_active):
										$language_info = apply_filters('wpml_post_language_details', null, $p->ID);
										$lang_name = $language_info['display_name'];
										$post_title_with_lang = esc_html($p->post_title) . ' (' . esc_html($lang_name) . ')';
									else:
										$post_title_with_lang = esc_html($p->post_title);
									endif;
									
									echo '<option value="' . esc_attr($p->ID) . '" ' . $selected . '>' . esc_html(get_post_type_object($p->post_type)->labels->singular_name) . ': ' . $post_title_with_lang . '</option>';
								endforeach;
							?>
                        </select>
                        <p class="description"><?php _e('Select an internal page or post for the QR code.', 'cloth-qrcode') ?></p>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <p style="font-weight: bold; margin-bottom: 10px; display:block;">
							<?php _e('QR Code Params URL Type', 'cloth-qrcode'); ?>
                        </p>
                        <input type="radio" <?= $param_type === 'none' || $param_type !== 'none' && $param_type !== 'query_param' && $param_type !== 'path_param' ? 'checked' : ''; ?> id="query_param_none"
                               name="cloth_qrcodes_url_params_type" value="none">
                        <label for="query_param"><?php _e('None', 'cloth-qrcode') ?></label><br>
                        <input type="radio" <?= $param_type === 'query_param' ? 'checked' : ''; ?> id="query_param" name="cloth_qrcodes_url_params_type" value="query_param">
                        <label for="query_param"><?php _e('Query parameter', 'cloth-qrcode') ?> (e.g., ..?link=www.google.com&id=2929)</label><br>
                        <input type="radio" <?= $param_type === 'path_param' ? 'checked' : ''; ?> id="path_param" name="cloth_qrcodes_url_params_type" value="path_param">
                        <label for="path_param"><?php _e('Path parameter', 'cloth-qrcode') ?> (e.g., ../www.google.com/id/2929)</label>
                    </div>
					
					<?php $params = !is_array($params) ? [] : $params; ?>
                    <div style="margin-bottom: 15px;display: <?= !count($params) || $param_type === 'none' ? 'none' : 'block' ?>;" id="cloth-qrcode-params-block">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;">
							<?php _e('QR Code Params', 'cloth-qrcode'); ?>
                        </label>
                        <table id="cloth-qrcode-params-table" class="widefat striped">
                            <thead>
                            <tr>
                                <th><?php _e('Param Key*', 'cloth-qrcode'); ?></th>
                                <th><?php _e('Param Value*', 'cloth-qrcode'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php foreach ($params as $index => $row): ?>
                                <tr>
                                    <td>
                                        <input type="text" required name="cloth_qrcodes_params[<?= $index; ?>][key]" value="<?= esc_attr($row['key'] ?? ''); ?>" style="width: 100%;">
                                    </td>
                                    <td>
                                        <input type="text" required name="cloth_qrcodes_params[<?= $index; ?>][value]" value="<?= esc_attr($row['value'] ?? ''); ?>" style="width: 100%;">
                                    </td>
                                    <td>
                                        <button type="button" class="button cloth-qrcode-remove-row">
											<?php _e('Remove', 'cloth-qrcode'); ?>
                                        </button>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                            </tbody>
                        </table>
                        <p class="description"><?php _e('Add parameters that will be displayed in the URL for the QR code.', 'cloth-qrcode') ?></p>
                        <button type="button" id="cloth-qrcode-add-row-params" class="button button-primary" style="margin-top:10px;">
                            + <?php _e('Add Param', 'cloth-qrcode'); ?>
                        </button>
                    </div>

                </div>
			<?php endif; ?>
			
			<?php //--------- QRCODE TYPE CAMPAIGN ---------//  ?>
			<?php if (empty($code_mode) || $code_mode === 'campaign'): ?>
                <div id="cloth_qrcodes_campaign_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'campaign' ? 'display: block;' : 'display: none;'; ?>">
                    <label style="font-weight: bold; margin-bottom: 10px; display:block;">
						<?php _e('Campaign Entries', 'cloth-qrcode'); ?>
                    </label>
                    <table id="cloth-qrcode-campaign-table" class="widefat striped">
                        <thead>
                        <tr>
                            <th><?php _e('Link Type', 'cloth-qrcode'); ?></th>
                            <th><?php _e('Link', 'cloth-qrcode'); ?></th>
                            <th><?php _e('Start Date & Time', 'cloth-qrcode'); ?></th>
                            <th><?php _e('End Date & Time', 'cloth-qrcode'); ?></th>
                            <th><?php _e('Params', 'cloth-qrcode'); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
							$campaign_entries = get_post_meta($post->ID, 'cloth_qrcodes_campaign_entries', true);
							if (!is_array($campaign_entries)) $campaign_entries = [];
							foreach ($campaign_entries as $index => $entry):
								?>
                                <tr id="<?= uniqid() ?>">
                                    <td>
                                        <select name="cloth_qrcodes_campaign_entries[<?= $index; ?>][link_type]" style="width: 100%;" class="cloth-qrcode-campaign-link-type">
                                            <option value="external" <?php selected($entry['link_type'] ?? '', 'external'); ?>><?php _e('External', 'cloth-qrcode'); ?></option>
                                            <option value="internal" <?php selected($entry['link_type'] ?? '', 'internal'); ?>><?php _e('Internal', 'cloth-qrcode'); ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="cloth-qrcode-campaign-link-container">
                                            <input type="url" name="cloth_qrcodes_campaign_entries[<?= $index; ?>][link]" value="<?= esc_url($entry['link'] ?? ''); ?>"
                                                   style="width: 100%;<?= $entry['link_type'] === 'external' ? 'display:block' : 'display:none' ?>"
                                                   class="cloth-qrcode-campaign-external-link"/>
                                            <select name="cloth_qrcodes_campaign_entries[<?= $index; ?>][internal_link]" style="width: 100%; <?= $entry['link_type'] === 'internal' ? 'display:block' : 'display:none' ?>"
                                                    class="cloth-qrcode-campaign-internal-link">
                                                <option value=""><?php _e('— Select a page or post —', 'cloth-qrcode'); ?></option>
												<?php foreach ($posts as $p): ?>
                                                    <option value="<?= esc_attr($p->ID); ?>" <?php selected($entry['internal_link'] ?? '', $p->ID); ?>>
														<?= esc_html(get_post_type_object($p->post_type)->labels->singular_name) . ': ' . esc_html($p->post_title); ?>
                                                    </option>
												<?php endforeach; ?>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="cloth_qrcodes_campaign_entries[<?= $index; ?>][start_datetime]" value="<?= esc_attr($entry['start_datetime'] ?? ''); ?>"
                                               class="cloth-qrcode-datetimepicker" style="width: 100%;"/>
                                    </td>
                                    <td>
                                        <input type="text" name="cloth_qrcodes_campaign_entries[<?= $index; ?>][end_datetime]" value="<?= esc_attr($entry['end_datetime'] ?? ''); ?>"
                                               class="cloth-qrcode-datetimepicker" style="width: 100%;"/>
                                    </td>

                                    <td>
                                        <textarea name="cloth_qrcodes_campaign_entries[<?= $index; ?>][params]" style="width: 100%;"><?= esc_textarea($entry['params'] ?? ''); ?></textarea>
                                    </td>
                                    <td>
                                        <button type="button" class="button cloth-qrcode-remove-campaign-row"><?php _e('Remove', 'cloth-qrcode'); ?></button>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                        </tbody>
                    </table>

                    <button type="button" id="cloth-qrcode-add-campaign-row" class="button button-primary" style="margin-top:10px;">
                        + <?php _e('Add Campaign Entry', 'cloth-qrcode'); ?>
                    </button>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_fallback_link_type">
							<?php _e('Fallback Link Type', 'cloth-qrcode'); ?>
                        </label>
                        <select id="cloth_qrcodes_fallback_link_type" name="cloth_qrcodes_fallback_link_type" style="width: 100%;">
                            <option value="external" <?php selected($fallback_link_type, 'external'); ?>><?php _e('External URL', 'cloth-qrcode'); ?></option>
                            <option value="internal" <?php selected($fallback_link_type, 'internal'); ?>><?php _e('Internal URL', 'cloth-qrcode'); ?></option>
                        </select>
                    </div>

                    <div id="cloth_qrcodes_external_fallback_link_container" style="margin-bottom: 15px; <?= $fallback_link_type === 'external' || empty($fallback_link_type) ? '' : 'display: none;'; ?>">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_fallback_link">
							<?php _e('External Fallback Link', 'cloth-qrcode'); ?>
                        </label>
                        <input type="url" id="cloth_qrcodes_fallback_link" name="cloth_qrcodes_fallback_link" value="<?= esc_url($fallback_link); ?>" style="width: 100%;"/>
                        <p class="description"><?php _e('Enter the external fallback URL for the QR code after the expiry date.', 'cloth-qrcode') ?></p>
                    </div>

                    <div id="cloth_qrcodes_internal_fallback_link_container" style="margin-bottom: 15px; <?= $fallback_link_type === 'internal' ? '' : 'display: none;'; ?>">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;">
							<?php _e('Internal Fallback Link', 'cloth-qrcode'); ?>
                        </label>
                        <select name="cloth_qrcodes_internal_fallback_link" style="width: 100%;">
                            <option value=""><?php _e('— Select a page or post —', 'cloth-qrcode'); ?></option>
							<?php
								$internal_fallback_link = $fallback_link_type === 'internal' ? $fallback_link : '';
								foreach ($all_posts as $p):
									$selected = selected($internal_fallback_link, $p->ID, false);
									
									// Get language info if WPML is active
									if ($is_wpml_active):
										$language_info = apply_filters('wpml_post_language_details', null, $p->ID);
										$lang_name = $language_info['display_name'];
										$post_title_with_lang = esc_html($p->post_title) . ' (' . esc_html($lang_name) . ')';
									else:
										$post_title_with_lang = esc_html($p->post_title);
									endif;
									
									echo '<option value="' . esc_attr($p->ID) . '" ' . $selected . '>' . esc_html(get_post_type_object($p->post_type)->labels->singular_name) . ': ' . $post_title_with_lang . '</option>';
								endforeach;
							?>
                        </select>
                        <p class="description"><?php _e('Select an internal fallback page or post for the QR code.', 'cloth-qrcode') ?></p>
                    </div>

                </div>
			<?php endif; ?>
			
			<?php //--------- QRCODE TYPE PAYMENT ---------//  ?>
			<?php if (empty($code_mode) || $code_mode === 'payment'): ?>
                <div id="cloth_qrcodes_payment_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'payment' ? 'display: block;' : 'display: none;'; ?>">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_payment_amount">
							<?php _e('Amount *', 'cloth-qrcode'); ?>
                        </label>
                        <input <?= $disabled ?> data-required="required" type="number" step="0.01" id="cloth_qrcodes_payment_amount" name="cloth_qrcodes_payment_amount"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_payment_amount', true)); ?>" style="width: 100%;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_payment_currency">
							<?php _e('Currency *', 'cloth-qrcode'); ?>
                        </label>
                        <select <?= $disabled ?> data-required="required" id="cloth_qrcodes_payment_currency" name="cloth_qrcodes_payment_currency" style="width: 100%;">
                            <option value="EUR" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_payment_currency', true), 'EUR'); ?>>EUR</option>
                            <option value="USD" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_payment_currency', true), 'USD'); ?>>USD</option>
                            <option value="GBP" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_payment_currency', true), 'GBP'); ?>>GBP</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_payment_recipient">
							<?php _e('Recipient Name *', 'cloth-qrcode'); ?>
                        </label>
                        <input <?= $disabled ?> data-required="required" type="text" id="cloth_qrcodes_payment_recipient" name="cloth_qrcodes_payment_recipient"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_payment_recipient', true)); ?>" style="width: 100%;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_payment_iban">
							<?php _e('IBAN *', 'cloth-qrcode'); ?>
                        </label>
                        <input <?= $disabled ?> data-required="required" type="text" id="cloth_qrcodes_payment_iban" name="cloth_qrcodes_payment_iban"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_payment_iban', true)); ?>" style="width: 100%;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_payment_bic">
							<?php _e('BIC/SWIFT (optional)', 'cloth-qrcode'); ?>
                        </label>
                        <input <?= $disabled ?> type="text" id="cloth_qrcodes_payment_bic" name="cloth_qrcodes_payment_bic"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_payment_bic', true)); ?>" style="width: 100%;"/>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_payment_reference">
							<?php _e('Reference (optional)', 'cloth-qrcode'); ?>
                        </label>
                        <input <?= $disabled ?> type="text" id="cloth_qrcodes_payment_reference" name="cloth_qrcodes_payment_reference"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_payment_reference', true)); ?>"
                               style="width: 100%;"/>
                    </div>
                </div>
			<?php endif; ?>
			
			<?php //--------- QRCODE TYPE GOOGLE MAPS ---------//  ?>
			<?php if (empty($code_mode) || $code_mode === 'maps'): ?>
                <div id="cloth_qrcodes_maps_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'maps' ? 'display: block;' : 'display: none;'; ?>">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_name">
							<?php _e('Location Name:', 'cloth-qrcode'); ?>
                        </label>
                        <input type="text" id="cloth_qrcodes_maps_name" name="cloth_qrcodes_maps_name"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_name', true)); ?>" style="width: 100%;"/>
                        <p class="description"><?php _e('Enter the name of the location (e.g., "My Restaurant").', 'cloth-qrcode') ?></p>
                    </div>

                    <p style="font-weight: bold; margin-bottom: 10px;"><?php _e('Enter either coordinates or address:', 'cloth-qrcode'); ?></p>

                    <div style="margin-bottom: 15px;">
                        <input type="radio" id="cloth_qrcodes_maps_use_coordinates" name="cloth_qrcodes_maps_input_type" value="coordinates"
						       <?= (get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) === 'coordinates' || !get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true)) ? 'checked' : ''; ?>>
                        <label for="cloth_qrcodes_maps_use_coordinates"><?php _e('Use Coordinates', 'cloth-qrcode'); ?></label>

                        <input type="radio" id="cloth_qrcodes_maps_use_address" name="cloth_qrcodes_maps_input_type" value="address"
						       <?= get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) === 'address' ? 'checked' : ''; ?>>
                        <label for="cloth_qrcodes_maps_use_address"><?php _e('Use Address', 'cloth-qrcode'); ?></label>
                    </div>

                    <div id="cloth_qrcodes_maps_coordinates_container" style="margin-bottom: 15px; <?= (get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) === 'address') ? 'display: none;' : ''; ?>">
                        <div style="margin-bottom: 15px;">
                            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_lat">
								<?php _e('Latitude:', 'cloth-qrcode'); ?>
                            </label>
                            <input type="text" id="cloth_qrcodes_maps_lat" name="cloth_qrcodes_maps_lat"
                                   value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_lat', true)); ?>" style="width: 100%;"
                                   data-required-group="maps" data-required="coordinates"/>
                            <p class="description"><?php _e('Enter the latitude of the location (e.g., 50.8503).', 'cloth-qrcode') ?></p>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_lng">
								<?php _e('Longitude:', 'cloth-qrcode'); ?>
                            </label>
                            <input type="text" id="cloth_qrcodes_maps_lng" name="cloth_qrcodes_maps_lng"
                                   value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_lng', true)); ?>" style="width: 100%;"
                                   data-required-group="maps" data-required="coordinates"/>
                            <p class="description"><?php _e('Enter the longitude of the location (e.g., 4.3517).', 'cloth-qrcode') ?></p>
                        </div>
                    </div>

                    <div id="cloth_qrcodes_maps_address_container" style="margin-bottom: 15px; <?= (get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) !== 'address') ? 'display: none;' : ''; ?>">
                        <div style="margin-bottom: 15px;">
                            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_address">
								<?php _e('Address:', 'cloth-qrcode'); ?>
                            </label>
                            <input type="text" id="cloth_qrcodes_maps_address" name="cloth_qrcodes_maps_address"
                                   value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_address', true)); ?>" style="width: 100%;"
                                   data-required-group="maps" data-required="address"/>
                            <p class="description"><?php _e('Enter the full address of the location (e.g., "123 Main St, Brussels, Belgium").', 'cloth-qrcode') ?></p>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
			
			<?php //--------- QRCODE TYPE WIFI ---------//  ?>
			<?php if (empty($code_mode) || $code_mode === 'wifi'): ?>
                <div id="cloth_qrcodes_wifi_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'wifi' ? 'display: block;' : 'display: none;'; ?>">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_wifi_ssid">
							<?php _e('Wi-Fi SSID', 'cloth-qrcode'); ?>
                        </label>
                        <input type="text" id="cloth_qrcodes_wifi_ssid" name="cloth_qrcodes_wifi_ssid"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_wifi_ssid', true)); ?>" style="width: 100%;" required/>
                        <p class="description"><?php _e('Enter the Wi-Fi network name (SSID).', 'cloth-qrcode') ?></p>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_wifi_password">
							<?php _e('Wi-Fi Password', 'cloth-qrcode'); ?>
                        </label>
                        <input type="text" id="cloth_qrcodes_wifi_password" name="cloth_qrcodes_wifi_password"
                               value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_wifi_password', true)); ?>" style="width: 100%;"/>
                        <p class="description"><?php _e('Enter the Wi-Fi password (leave empty for open networks).', 'cloth-qrcode') ?></p>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_wifi_auth_type">
							<?php _e('Authentication Type', 'cloth-qrcode'); ?>
                        </label>
                        <select id="cloth_qrcodes_wifi_auth_type" name="cloth_qrcodes_wifi_auth_type" style="width: 100%;">
                            <option value="WPA" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_wifi_auth_type', true), 'WPA'); ?>><?php _e('WPA/WPA2', 'cloth-qrcode'); ?></option>
                            <option value="WEP" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_wifi_auth_type', true), 'WEP'); ?>><?php _e('WEP', 'cloth-qrcode'); ?></option>
                            <option value="nopass" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_wifi_auth_type', true), 'nopass'); ?>><?php _e('No Password', 'cloth-qrcode'); ?></option>
                        </select>
                        <p class="description"><?php _e('Select the Wi-Fi authentication type.', 'cloth-qrcode') ?></p>
                    </div>
                </div>
			<?php endif; ?>
   
			<?php //--------- QRCODE IMAGES---------//  ?>
			<?php if ($qr_attachment_id): ?>
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; margin-bottom: 10px; display:block;">
						<?php _e('Shortcode', 'cloth-qrcode'); ?>
                    </label>
                    <input type="text" readonly value='[cloth_qrcode id=&quot;<?= $post->ID; ?>&quot;]' style="width: 100%; background-color: #eee;"/>
                    <p class="description"><?php _e('Use this shortcode to display the QR code anywhere on your site.', 'cloth-qrcode') ?></p>
                </div>

                <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; text-align: center;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php _e('QR Code Preview', 'cloth-qrcode') ?>:</label>
                    <div style="background: #f9f9f9; padding: 10px; display: inline-block;">
						<?= wp_get_attachment_image($qr_attachment_id, ''); ?>
                    </div>
					<?php if ($code_mode === 'regular') : ?>
                        <p style="margin-top: 5px; font-size: 12px; color: #666;"><?php _e('This QR code will redirect to:', 'cloth-qrcode') ?>
                            <strong><?= array_key_exists('link', $link) ? esc_url($link['link']) : get_permalink($link['internal_link']); ?></strong>
                        </p>
					<?php endif; ?>
					<?php $slug = \ClothQrcode\Helpers::slugify($post->post_title); ?>
                    <div>
                        <a href="<?= esc_url(wp_get_attachment_url($qr_attachment_id)); ?>" download="<?= esc_attr('qrcode-' . $slug); ?>" class="button button-primary" style="margin-top: 10px;">
							<?php _e('Download QR Code', 'cloth-qrcode'); ?> (SVG)
                        </a>
						<?php if ($qr_attachment_png_id): ?>
                            <a href="<?= esc_url(wp_get_attachment_url($qr_attachment_png_id)); ?>" download="<?= esc_attr('qrcode-' . $slug); ?>" class="button button-primary" style="margin-top: 10px;">
								<?php _e('Download QR Code', 'cloth-qrcode'); ?> (PNG)
                            </a>
						<?php endif; ?>
                    </div>
                </div>
			<?php endif; ?>
			
			<?php
		}
		*/
		/**********************************************************************
		 * RENDERING META BOX STATS LOGIC
		 **********************************************************************/
		/*
		public function render_meta_box_stats($post) {
			$scan_stats = get_post_meta($post->ID, 'cloth_qrcodes_scan_stats', true);
			?>
			
			<?php if (!empty($scan_stats) && is_array($scan_stats)): ?>
                <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php _e('Scan Statistics', 'cloth-qrcode') ?>:</label>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                        <tr>
                            <th>Link</th>
                            <th>Scans</th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ($scan_stats as $url => $count): ?>
                            <tr>
                                <td><?= esc_url($url); ?></td>
                                <td><?= esc_html($count); ?></td>
                            </tr>
						<?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="margin-top: 15px;">
                        <a href="<?= add_query_arg([
							'action' => 'export_scan_stats',
							'post_id' => $post->ID,
							'_wpnonce' => wp_create_nonce('export_scan_stats_nonce')
						], admin_url('admin-post.php')); ?>" class="button button-primary">
							<?php _e('Download Statistics', 'cloth-qrcode'); ?>
                        </a>
                    </div>
                </div>
			<?php endif;
		}
		*/
		/**********************************************************************
		 * SAVE META BOX LOGIC
		 **********************************************************************/
		/*
		public function save_meta_box($post_id) {
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
			if (!isset($_POST['cloth_qrcodes_meta_box_nonce']) || !wp_verify_nonce($_POST['cloth_qrcodes_meta_box_nonce'], 'cloth_qrcodes_meta_box')) return;
			if (!current_user_can('edit_post', $post_id)) return;
			
			// Save QRCODE MODE
			if (isset($_POST['cloth_qrcodes_mode'])) :
				update_post_meta($post_id, 'cloth_qrcodes_mode', sanitize_text_field($_POST['cloth_qrcodes_mode']));
			endif;
			
			//debug($_POST['cloth_qrcodes_mode']);
			
			//-------------- SAVE DATA FOR QrCode REGULAR MODE --------------//
			if ($_POST['cloth_qrcodes_mode'] === 'regular'):
				delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
				
				// Save the link type
				if (isset($_POST['cloth_qrcodes_link_type'])) :
					update_post_meta($post_id, 'cloth_qrcodes_link_type', sanitize_text_field($_POST['cloth_qrcodes_link_type']));
				endif;
				
				// Save the link
				$link_type = isset($_POST['cloth_qrcodes_link_type']) ? sanitize_text_field($_POST['cloth_qrcodes_link_type']) : '';
				if ($link_type === 'external' && isset($_POST['cloth_qrcodes_link'])) :
					$link_data['link'] = esc_url_raw($_POST['cloth_qrcodes_link']);
					update_post_meta($post_id, 'cloth_qrcodes_link', $link_data);
                elseif ($link_type === 'internal' && isset($_POST['cloth_qrcodes_internal_link'])) :
					$link_data['internal_link'] = absint($_POST['cloth_qrcodes_internal_link']);
					update_post_meta($post_id, 'cloth_qrcodes_link', $link_data);
				endif;
				
				// Save start and end datetime
				if (isset($_POST['cloth_qrcodes_start_datetime'])) :
					update_post_meta($post_id, 'cloth_qrcodes_start_datetime', sanitize_text_field($_POST['cloth_qrcodes_start_datetime']));
				endif;
				
				if (isset($_POST['cloth_qrcodes_end_datetime'])) :
					update_post_meta($post_id, 'cloth_qrcodes_end_datetime', sanitize_text_field($_POST['cloth_qrcodes_end_datetime']));
				endif;
				
				// Save repeatable params
				if (isset($_POST['cloth_qrcodes_params'])) :
					$params = [];
					foreach ($_POST['cloth_qrcodes_params'] as $row) :
						$key = sanitize_text_field($row['key'] ?? '');
						$value = sanitize_text_field($row['value'] ?? '');
						
						if (!empty($key) || !empty($value)) :
							$params[] = [
								'key' => $key,
								'value' => $value
							];
						endif;
					endforeach;
					update_post_meta($post_id, 'cloth_qrcodes_params', $params);
				else :
					delete_post_meta($post_id, 'cloth_qrcodes_params');
				endif;
				
				// Save the URL param type
				if (isset($_POST['cloth_qrcodes_url_params_type'])) :
					update_post_meta($post_id, 'cloth_qrcodes_url_params_type', sanitize_text_field($_POST['cloth_qrcodes_url_params_type']));
				endif;
			
			//-------------- SAVE DATA FOR QrCode CAMPAIGN MODE --------------//
            elseif ($_POST['cloth_qrcodes_mode'] === 'campaign'):
				delete_post_meta($post_id, 'cloth_qrcodes_link_type');
				delete_post_meta($post_id, 'cloth_qrcodes_link');
				delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_params');
				delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
				
				if (isset($_POST['cloth_qrcodes_campaign_entries'])) :
					$params = [];
					foreach ($_POST['cloth_qrcodes_campaign_entries'] as $row) :
						$params[] = $row;
					endforeach;
					update_post_meta($post_id, 'cloth_qrcodes_campaign_entries', $params);
				endif;
				
				
				// Save the fallback link type
				if (isset($_POST['cloth_qrcodes_fallback_link_type'])) :
					update_post_meta($post_id, 'cloth_qrcodes_fallback_link_type', sanitize_text_field($_POST['cloth_qrcodes_fallback_link_type']));
				endif;
				
				// Save the fallback link
				$fallback_link_type = isset($_POST['cloth_qrcodes_fallback_link_type']) ? sanitize_text_field($_POST['cloth_qrcodes_fallback_link_type']) : '';
				if ($fallback_link_type === 'external' && isset($_POST['cloth_qrcodes_fallback_link'])) :
					update_post_meta($post_id, 'cloth_qrcodes_fallback_link', esc_url_raw($_POST['cloth_qrcodes_fallback_link']));
                elseif ($fallback_link_type === 'internal' && isset($_POST['cloth_qrcodes_internal_fallback_link'])) :
					update_post_meta($post_id, 'cloth_qrcodes_fallback_link', absint($_POST['cloth_qrcodes_internal_fallback_link']));
				endif;
			
			//-------------- SAVE DATA FOR QrCode PAYMENT MODE --------------//
            elseif ($_POST['cloth_qrcodes_mode'] === 'payment'):
				delete_post_meta($post_id, 'cloth_qrcodes_link_type');
				delete_post_meta($post_id, 'cloth_qrcodes_link');
				delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_params');
				delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
				delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
				
				// Save payment details
				if (isset($_POST['cloth_qrcodes_payment_amount'])) :
					update_post_meta($post_id, 'cloth_qrcodes_payment_amount', sanitize_text_field($_POST['cloth_qrcodes_payment_amount']));
				endif;
				if (isset($_POST['cloth_qrcodes_payment_currency'])) :
					update_post_meta($post_id, 'cloth_qrcodes_payment_currency', sanitize_text_field($_POST['cloth_qrcodes_payment_currency']));
				endif;
				if (isset($_POST['cloth_qrcodes_payment_recipient'])) :
					update_post_meta($post_id, 'cloth_qrcodes_payment_recipient', sanitize_text_field($_POST['cloth_qrcodes_payment_recipient']));
				endif;
				if (isset($_POST['cloth_qrcodes_payment_iban'])) :
					update_post_meta($post_id, 'cloth_qrcodes_payment_iban', sanitize_text_field($_POST['cloth_qrcodes_payment_iban']));
				endif;
				if (isset($_POST['cloth_qrcodes_payment_bic'])) :
					update_post_meta($post_id, 'cloth_qrcodes_payment_bic', sanitize_text_field($_POST['cloth_qrcodes_payment_bic']));
				endif;
				if (isset($_POST['cloth_qrcodes_payment_reference'])) :
					update_post_meta($post_id, 'cloth_qrcodes_payment_reference', sanitize_text_field($_POST['cloth_qrcodes_payment_reference']));
				endif;
			//-------------- SAVE DATA FOR QrCode GOOGLE MAPS MODE --------------//
            elseif ($_POST['cloth_qrcodes_mode'] === 'maps'):
				delete_post_meta($post_id, 'cloth_qrcodes_link_type');
				delete_post_meta($post_id, 'cloth_qrcodes_link');
				delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_params');
				delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
				delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
				
				// Save the input type (coordinates or address)
				if (isset($_POST['cloth_qrcodes_maps_input_type'])) :
					update_post_meta($post_id, 'cloth_qrcodes_maps_input_type', sanitize_text_field($_POST['cloth_qrcodes_maps_input_type']));
				endif;
				
				// Save Google Maps details
				if (isset($_POST['cloth_qrcodes_maps_name'])) :
					update_post_meta($post_id, 'cloth_qrcodes_maps_name', sanitize_text_field($_POST['cloth_qrcodes_maps_name']));
				endif;
				if (isset($_POST['cloth_qrcodes_maps_lat'])) :
					update_post_meta($post_id, 'cloth_qrcodes_maps_lat', sanitize_text_field($_POST['cloth_qrcodes_maps_lat']));
				endif;
				if (isset($_POST['cloth_qrcodes_maps_lng'])) :
					update_post_meta($post_id, 'cloth_qrcodes_maps_lng', sanitize_text_field($_POST['cloth_qrcodes_maps_lng']));
				endif;
				if (isset($_POST['cloth_qrcodes_maps_address'])) :
					update_post_meta($post_id, 'cloth_qrcodes_maps_address', sanitize_text_field($_POST['cloth_qrcodes_maps_address']));
				endif;
			//-------------- SAVE DATA FOR QrCode WIFI MODE --------------//
            elseif ($_POST['cloth_qrcodes_mode'] === 'wifi'):
				delete_post_meta($post_id, 'cloth_qrcodes_link_type');
				delete_post_meta($post_id, 'cloth_qrcodes_link');
				delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
				delete_post_meta($post_id, 'cloth_qrcodes_params');
				delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
				delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
				
				// Save Wi-Fi details
				if (isset($_POST['cloth_qrcodes_wifi_ssid'])) :
					update_post_meta($post_id, 'cloth_qrcodes_wifi_ssid', sanitize_text_field($_POST['cloth_qrcodes_wifi_ssid']));
				endif;
				if (isset($_POST['cloth_qrcodes_wifi_password'])) :
					update_post_meta($post_id, 'cloth_qrcodes_wifi_password', sanitize_text_field($_POST['cloth_qrcodes_wifi_password']));
				endif;
				if (isset($_POST['cloth_qrcodes_wifi_auth_type'])) :
					update_post_meta($post_id, 'cloth_qrcodes_wifi_auth_type', sanitize_text_field($_POST['cloth_qrcodes_wifi_auth_type']));
				endif;
			endif;
		}
		*/
		/**********************************************************************
		 * AJAX METHODS
		 **********************************************************************/
		/*
		public function get_template_row_regular_params() {
			// Start output buffering to capture HTML
			$index = $_POST['index'];
			ob_start();
			?>
            <td><input type="text" required name="cloth_qrcodes_params[<?= $index ?>][key]" style="width:100%;"></td>
            <td><input required type="text" name="cloth_qrcodes_params[<?= $index ?>][value]" style="width:100%;"></td>
            <td>
                <button type="button" class="button cloth-qrcode-remove-row"><?php _e('Remove', 'cloth-qrcode'); ?></button>
            </td>
			
			<?php
			$html = ob_get_clean();
			wp_send_json_success([
				'row' => $html,
				'response' => true,
			]);
		}
		
		public function get_template_row_campaign() {
			$array_response = [];
			
			// Start output buffering to capture HTML
			
			// Get all QR code post IDs
			$qr_post_ids = get_posts([
				'post_type' => 'cloth-qrcodes',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
			]);
			
			// Add current post ID to the exclusion list
			$exclude_ids = array_merge($qr_post_ids, [$_POST['post_id']]);
			
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
			
			
			// If WPML is active, fetch posts in all languages
			if ($is_wpml_active):
				global $sitepress;
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
					foreach ($posts as $post):
						$all_posts[$post->ID] = $post;
					endforeach;
				endforeach;
				
				// Switch back to the original language
				$sitepress->switch_lang($sitepress->get_current_language());
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
			
			
			$index = $_POST['index'];
			
			ob_start();
			?>
            <table id="cloth-qrcode-campaign-row-template">
                <tbody>
                <tr>
                    <td>
                        <select name="cloth_qrcodes_campaign_entries[<?= $index ?>][link_type]" style="width: 100%;" class="cloth-qrcode-campaign-link-type">
                            <option value="external"><?php _e('External', 'cloth-qrcode'); ?></option>
                            <option value="internal"><?php _e('Internal', 'cloth-qrcode'); ?></option>
                        </select>
                    </td>
                    <td>
                        <div class="cloth-qrcode-campaign-link-container">
                            <input required type="url" name="cloth_qrcodes_campaign_entries[<?= $index ?>][link]" style="width: 100%;" class="cloth-qrcode-campaign-external-link"/>
                            <select name="cloth_qrcodes_campaign_entries[<?= $index ?>][internal_link]" style="width: 100%; display: none;" class="cloth-qrcode-campaign-internal-link">
                                <option value=""><?php _e('— Select a page or post —', 'cloth-qrcode'); ?></option>
								<?php foreach ($posts as $p): ?>
                                    <option value="<?= esc_attr($p->ID); ?>">
										<?= esc_html(get_post_type_object($p->post_type)->labels->singular_name) . ': ' . esc_html($p->post_title); ?>
                                    </option>
								<?php endforeach; ?>
                            </select>
                        </div>
                    </td>
                    <td>
                        <input required type="text" name="cloth_qrcodes_campaign_entries[<?= $index ?>][start_datetime]" class="cloth-qrcode-datetimepicker" style="width: 100%;"/>
                    </td>
                    <td>
                        <input required type="text" name="cloth_qrcodes_campaign_entries[<?= $index ?>][end_datetime]" class="cloth-qrcode-datetimepicker" style="width: 100%;"/>
                    </td>
                    <td>
                        <button type="button" class="button cloth-qrcode-remove-campaign-row"><?php _e('Remove', 'cloth-qrcode'); ?></button>
                    </td>
                </tr>
                </tbody>
            </table>
			<?php
			$html = ob_get_clean();
			wp_send_json_success([
				'row' => $html,
				'response' => true,
			]);
		}
		*/
	}
