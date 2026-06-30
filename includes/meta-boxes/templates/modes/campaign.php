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
									<?php foreach ($all_posts as $p): ?>
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

        <button type="button" id="cloth-qrcode-add-campaign-row" class="button button-primary" style="margin:10px 0 20px 0;">
            + <?php _e('Add Campaign Entry', 'cloth-qrcode'); ?>
        </button>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_fallback_link_type">
				<?php _e('Fallback Link Type', 'cloth-qrcode'); ?>
            </label>
            <select id="cloth_qrcodes_fallback_link_type" name="cloth_qrcodes_campaign_fallback_link_type" style="width: 100%;">
                <option value="external" <?php selected($fallback_link_type, 'external'); ?>><?php _e('External URL', 'cloth-qrcode'); ?></option>
                <option value="internal" <?php selected($fallback_link_type, 'internal'); ?>><?php _e('Internal URL', 'cloth-qrcode'); ?></option>
            </select>
        </div>

        <div id="cloth_qrcodes_external_fallback_link_container" style="margin-bottom: 15px; <?= $fallback_link_type === 'external' || empty($fallback_link_type) ? '' : 'display: none;'; ?>">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_campaign_fallback_link">
				<?php _e('External Fallback Link', 'cloth-qrcode'); ?>
            </label>
            <input type="url" id="cloth_qrcodes_fallback_link" name="cloth_qrcodes_campaign_fallback_link" value="<?= esc_url($fallback_link); ?>" style="width: 100%;"/>
            <p class="description"><?php _e('Enter the external fallback URL for the QR code after the expiry date.', 'cloth-qrcode') ?></p>
        </div>

        <div id="cloth_qrcodes_internal_fallback_link_container" style="margin-bottom: 15px; <?= $fallback_link_type === 'internal' ? '' : 'display: none;'; ?>">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_campaign_internal_fallback_link">
				<?php _e('Internal Fallback Link', 'cloth-qrcode'); ?>
            </label>
            <select name="cloth_qrcodes_campaign_internal_fallback_link" style="width: 100%;">
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