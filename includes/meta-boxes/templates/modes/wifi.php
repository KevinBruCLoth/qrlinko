<?php if (empty($code_mode) || $code_mode === 'wifi'): ?>
	<div id="cloth_qrcodes_wifi_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'wifi' ? 'display: block;' : 'display: none;'; ?>">
		<div style="margin-bottom: 15px;">
			<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_wifi_ssid">
				<?php _e('Wi-Fi SSID', 'cloth-qrcode'); ?>
			</label>
			<input type="text" id="cloth_qrcodes_wifi_ssid" name="cloth_qrcodes_wifi_ssid"
			       value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_wifi_ssid', true)); ?>" style="width: 100%;" data-required="required"/>
			<p class="description"><?php _e('Enter the Wi-Fi network name (SSID).', 'cloth-qrcode') ?></p>
		</div>
		
		<div style="margin-bottom: 15px;">
			<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_wifi_password">
				<?php _e('Wi-Fi Password', 'cloth-qrcode'); ?>
			</label>
			<input type="text" id="cloth_qrcodes_wifi_password" name="cloth_qrcodes_wifi_password" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_wifi_password', true)); ?>" style="width: 100%;"/>
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