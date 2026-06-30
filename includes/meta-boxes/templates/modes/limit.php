<?php if (empty($code_mode) || $code_mode === 'limit'): ?>
    <div id="cloth_qrcodes_limit_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'limit' ? 'display: block;' : 'display: none;'; ?>">
        <label style="font-weight: bold; margin-bottom: 10px; display:block;">
			<?php _e('Scan Limit Entries', 'cloth-qrcode'); ?>
        </label>
        <table id="cloth-qrcode-limit-table" class="widefat striped">
            <thead>
            <tr>
                <th><?php _e('Link Type', 'cloth-qrcode'); ?></th>
                <th><?php _e('Link', 'cloth-qrcode'); ?></th>
                <th><?php _e('Scan Limit', 'cloth-qrcode'); ?></th>
                <th><?php _e('Params', 'cloth-qrcode'); ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
			<?php
				$limit_entries = get_post_meta($post->ID, 'cloth_qrcodes_limit_entries', true);
				if (!is_array($limit_entries)) $limit_entries = [];
				foreach ($limit_entries as $index => $entry):
					?>
                    <tr id="<?= uniqid() ?>" draggable="true" data-index="<?= $index ?>">
                        <td>
                            <select name="cloth_qrcodes_limit_entries[<?= $index; ?>][link_type]" style="width: 100%;" class="cloth-qrcode-limit-link-type">
                                <option value="external" <?php selected($entry['link_type'] ?? '', 'external'); ?>><?php _e('External', 'cloth-qrcode'); ?></option>
                                <option value="internal" <?php selected($entry['link_type'] ?? '', 'internal'); ?>><?php _e('Internal', 'cloth-qrcode'); ?></option>
                            </select>
                        </td>
                        <td>
                            <div class="cloth-qrcode-limit-link-container">
                                <input type="url" name="cloth_qrcodes_limit_entries[<?= $index; ?>][link]" value="<?= esc_url($entry['link'] ?? ''); ?>"
                                       style="width: 100%;<?= ($entry['link_type'] ?? '') === 'external' ? 'display:block' : 'display:none' ?>"
                                       class="cloth-qrcode-limit-external-link"/>
                                <select name="cloth_qrcodes_limit_entries[<?= $index; ?>][internal_link]" style="width: 100%; <?= ($entry['link_type'] ?? '') === 'internal' ? 'display:block' : 'display:none' ?>"
                                        class="cloth-qrcode-limit-internal-link">
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
                            <input type="number" name="cloth_qrcodes_limit_entries[<?= $index; ?>][scan_limit]" value="<?= esc_attr($entry['scan_limit'] ?? ''); ?>"
                                   style="width: 100%;" min="1" placeholder="Scan Limit"/>
                        </td>
                        <td>
                            <textarea name="cloth_qrcodes_limit_entries[<?= $index; ?>][params]" style="width: 100%;"><?= esc_textarea($entry['params'] ?? ''); ?></textarea>
                        </td>
                        <td>
                            <button type="button" class="button cloth-qrcode-remove-limit-row"><?php _e('Remove', 'cloth-qrcode'); ?></button>
                        </td>
                    </tr>
				<?php endforeach; ?>
            </tbody>
        </table>

        <button type="button" id="cloth-qrcode-add-limit-row" class="button button-primary" style="margin:10px 0 20px 0;">
            + <?php _e('Add Scan Limit Entry', 'cloth-qrcode'); ?>
        </button>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_fallback_link_type">
				<?php _e('Fallback Link Type', 'cloth-qrcode'); ?>
            </label>
            <select id="cloth_qrcodes_fallback_link_type" name="cloth_qrcodes_limit_fallback_link_type" style="width: 100%;">
                <option value="external" <?php selected($fallback_link_type, 'external'); ?>><?php _e('External URL', 'cloth-qrcode'); ?></option>
                <option value="internal" <?php selected($fallback_link_type, 'internal'); ?>><?php _e('Internal URL', 'cloth-qrcode'); ?></option>
            </select>
        </div>

        <div id="cloth_qrcodes_external_fallback_link_container" style="margin-bottom: 15px; <?= $fallback_link_type === 'external' || empty($fallback_link_type) ? '' : 'display: none;'; ?>">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_limit_fallback_link">
				<?php _e('External Fallback Link', 'cloth-qrcode'); ?>
            </label>
            <input type="url" id="cloth_qrcodes_fallback_link" name="cloth_qrcodes_limit_fallback_link" value="<?= esc_url($fallback_link); ?>" style="width: 100%;"/>
            <p class="description"><?php _e('Enter the external fallback URL for the QR code after scan limits are reached.', 'cloth-qrcode'); ?></p>
        </div>

        <div id="cloth_qrcodes_internal_fallback_link_container" style="margin-bottom: 15px; <?= $fallback_link_type === 'internal' ? '' : 'display: none;'; ?>">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;">
				<?php _e('Internal Fallback Link', 'cloth-qrcode'); ?>
            </label>
            <select name="cloth_qrcodes_limit_internal_fallback_link" style="width: 100%;">
                <option value=""><?php _e('— Select a page or post —', 'cloth-qrcode'); ?></option>
				<?php
					$internal_fallback_link = $fallback_link_type === 'internal' ? $fallback_link : '';
					foreach ($all_posts as $p):
						$selected = selected($internal_fallback_link, $p->ID, false);
						
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