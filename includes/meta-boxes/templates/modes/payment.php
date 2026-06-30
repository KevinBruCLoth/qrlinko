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