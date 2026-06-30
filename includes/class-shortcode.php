<?php
	namespace ClothQrcode;
	
	class Shortcode {
		public function __construct() {
			add_shortcode('cloth_qrcode', [$this, 'render_shortcode']);
		}
		
		public function render_shortcode($atts) {
			$atts = shortcode_atts([
				'id' => 0,
				'size' => 'medium',
			], $atts, 'cloth_qrcode');
			
			$post_id = absint($atts['id']);
			if (!$post_id || get_post_type($post_id) !== 'cloth-qrcodes') :
				return '';
			endif;
			
			$qr_attachment_id = get_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id', true);
			
			if ($qr_attachment_id) :
				return wp_get_attachment_image(absint($qr_attachment_id), sanitize_key($atts['size']));
			endif;
			
			return '';
		}
	}
