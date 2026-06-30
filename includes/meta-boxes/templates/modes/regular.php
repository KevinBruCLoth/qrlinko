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