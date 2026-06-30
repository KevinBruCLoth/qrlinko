<?php
	$qr_redirect_url = '';
	$qr_permalink_url = '';

	if (!empty($post->ID) && get_post_status($post->ID) === 'publish') :
		$qr_permalink_url = get_permalink($post->ID);

		if ($code_mode === 'vcard') :
			$qr_redirect_url = \ClothQrcode\Vcard::get_vcard_url($post->ID);
		elseif ($code_mode !== 'payment') :
			$qr_redirect_url = home_url('/qr-redirect/' . absint($post->ID) . '/');
		endif;
	endif;
?>

<?php if (!empty($qr_redirect_url) || !empty($qr_permalink_url)) : ?>
	<div style="margin-bottom: 15px; padding: 12px; border: 1px solid #dcdcde; background: #fff;">
		<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_redirect_url">
			<?php _e('QR Code Redirect URL', 'cloth-qrcode'); ?>
		</label>

		<?php if (!empty($qr_redirect_url)) : ?>
			<div style="display:flex; gap: 8px; align-items:center; margin-bottom: 8px;">
				<input
					type="url"
					id="cloth_qrcodes_redirect_url"
					value="<?= esc_url($qr_redirect_url); ?>"
					readonly
					style="width: 100%;"
				/>
				<button type="button" class="button cloth-qrcode-copy-url" data-copy-target="cloth_qrcodes_redirect_url">
					<?php _e('Copy URL', 'cloth-qrcode'); ?>
				</button>
			</div>
			<p class="description" style="margin-top: 0;">
				<?php _e('This is the URL used inside the QR code image.', 'cloth-qrcode'); ?>
			</p>
		<?php endif; ?>

		<?php if (!empty($qr_permalink_url)) : ?>
			<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_permalink_redirect_url">
				<?php _e('QR Code Permalink', 'cloth-qrcode'); ?>
			</label>
			<div style="display:flex; gap: 8px; align-items:center;">
				<input
					type="url"
					id="cloth_qrcodes_permalink_redirect_url"
					value="<?= esc_url($qr_permalink_url); ?>"
					readonly
					style="width: 100%;"
				/>
				<button type="button" class="button cloth-qrcode-copy-url" data-copy-target="cloth_qrcodes_permalink_redirect_url">
					<?php _e('Copy URL', 'cloth-qrcode'); ?>
				</button>
			</div>
			<p class="description">
				<?php _e('Opening this permalink now uses the same redirect and statistics logic.', 'cloth-qrcode'); ?>
			</p>
		<?php endif; ?>
	</div>
<?php elseif (!empty($post->ID) && get_post_status($post->ID) !== 'publish') : ?>
	<div style="margin-bottom: 15px; padding: 12px; border: 1px solid #dcdcde; background: #fff;">
		<label style="font-weight: bold; margin-bottom: 10px; display:block;">
			<?php _e('QR Code Redirect URL', 'cloth-qrcode'); ?>
		</label>
		<p class="description" style="margin: 0;">
			<?php _e('Publish this QR Code to generate its redirect URL.', 'cloth-qrcode'); ?>
		</p>
	</div>
<?php endif; ?>
