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
		<?php if ($code_mode === 'regular' && is_array($link)) : ?>
			<?php $preview_link = array_key_exists('link', $link) ? esc_url($link['link']) : esc_url(get_permalink($link['internal_link'] ?? 0)); ?>
			<p style="margin-top: 5px; font-size: 12px; color: #666;"><?php _e('This QR code will redirect to:', 'cloth-qrcode') ?>
				<strong><?= $preview_link; ?></strong>
			</p>
		<?php elseif ($code_mode === 'vcard') : ?>
			<p style="margin-top: 5px; font-size: 12px; color: #666;"><?php _e('This QR code opens a dynamic vCard file. You can edit the contact fields later without changing the printed QR code.', 'cloth-qrcode'); ?></p>
			<p style="margin-top: 5px; font-size: 12px; color: #666;"><?php _e('vCard URL:', 'cloth-qrcode'); ?> <strong><?= esc_url(\ClothQrcode\Vcard::get_vcard_url($post->ID)); ?></strong></p>
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
