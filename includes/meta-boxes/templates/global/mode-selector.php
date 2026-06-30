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
        <?php /*
        <option value="wifi" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'wifi'); ?>>
			<?php _e('Wi-Fi', 'cloth-qrcode'); ?>
        </option>
        */ ?>
        <option value="vcard" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'vcard'); ?>>
		    <?php _e('vCard Contact', 'cloth-qrcode'); ?>
        </option>
        <option value="limit" <?php selected(get_post_meta($post->ID, 'cloth_qrcodes_mode', true), 'limit'); ?>>
		    <?php _e('Limit scan', 'cloth-qrcode'); ?>
        </option>
    </select>
	<?php if (!empty($code_mode)): ?>
        <input type="hidden" id="cloth_qrcodes_mode_hidden" name="cloth_qrcodes_mode" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_mode', true)); ?>">
	<?php endif; ?>
</div>