<?php
	namespace ClothQrcode;
	
	use Endroid\QrCode\Builder\Builder;
	use Endroid\QrCode\Encoding\Encoding;
	use Endroid\QrCode\ErrorCorrectionLevel;
	use Endroid\QrCode\RoundBlockSizeMode;
	use Endroid\QrCode\Writer\PngWriter;
	use Endroid\QrCode\Writer\SvgWriter;
	
	class QRGenerator {
		private const DIRECT_DATA_MODES = ['payment'];
		private const VCARD_QR_VERSION = 'dynamic-url-v1';
		
		public function __construct() {
			add_action('save_post_cloth-qrcodes', [$this, 'generate_qr_code_on_save'], 20, 2);
			add_action('before_delete_post', [$this, 'delete_qr_code_attachments']);
		}
		
		/************************************************************************
		 * LOGIC CREATE QR CODE IMAGE RELATED TO POST
		 ************************************************************************/
		public function generate_qr_code_on_save($post_id, $post) {
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) :
				return;
			endif;
			
			if (wp_is_post_revision($post_id) || !$post || $post->post_type !== 'cloth-qrcodes' || $post->post_status !== 'publish') :
				return;
			endif;
			
			$code_mode = get_post_meta($post_id, 'cloth_qrcodes_mode', true);
			if (empty($code_mode)) :
				return;
			endif;
			
			$qr_attachment_id = get_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id', true);
			$must_regenerate = in_array($code_mode, self::DIRECT_DATA_MODES, true);
			if ($code_mode === 'vcard' && get_post_meta($post_id, 'cloth_qrcodes_vcard_qr_version', true) !== self::VCARD_QR_VERSION) :
				$must_regenerate = true;
			endif;
			
			if ($qr_attachment_id && !$must_regenerate) :
				return;
			endif;
			
			if ($must_regenerate && $qr_attachment_id) :
				$this->delete_qr_code_files($post_id);
			endif;
			
			$this->generate_qr_code_for_post($post_id, $code_mode);
		}
		
		private function generate_qr_code_for_post($post_id, $code_mode) {
			if ($code_mode === 'payment') :
				$this->generate_payment_qr_code($post_id);
				return;
			endif;
			
			if ($code_mode === 'vcard') :
				$data = Vcard::get_vcard_url($post_id);
				$this->generate_local_qr_code($post_id, $data);
				update_post_meta($post_id, 'cloth_qrcodes_vcard_qr_version', self::VCARD_QR_VERSION);
				return;
			endif;
			
			$data = home_url("/qr-redirect/{$post_id}/");
			$this->generate_local_qr_code($post_id, $data);
		}
		
		private function generate_payment_qr_code($post_id) {
			$amount = get_post_meta($post_id, 'cloth_qrcodes_payment_amount', true);
			$currency = get_post_meta($post_id, 'cloth_qrcodes_payment_currency', true) ?: 'EUR';
			$recipient = get_post_meta($post_id, 'cloth_qrcodes_payment_recipient', true);
			$iban = preg_replace('/\s+/', '', (string) get_post_meta($post_id, 'cloth_qrcodes_payment_iban', true));
			$reference = get_post_meta($post_id, 'cloth_qrcodes_payment_reference', true);
			$bic = preg_replace('/\s+/', '', (string) get_post_meta($post_id, 'cloth_qrcodes_payment_bic', true));
			
			$epc_qr_url = sprintf(
				'https://epc-qr.eu/?bname=%s&iban=%s&euro=%s&info=%s&cut=tlrb&colour=black&logo=none',
				rawurlencode($recipient),
				rawurlencode($iban),
				rawurlencode($amount),
				rawurlencode($reference)
			);
			
			$response = wp_remote_get($epc_qr_url, [
				'timeout' => 15,
			]);
			
			if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) :
				$qr_image = wp_remote_retrieve_body($response);
				if (!empty($qr_image)) :
					$this->save_epc_qr_image($post_id, $qr_image);
					return;
				endif;
			endif;
			
			$fallback_data = sprintf(
				"BCD\n002\n1\nSCT\n%s\n%s\n%s\n%s%s\n\n%s",
				$bic,
				$recipient,
				$iban,
				$currency,
				$amount,
				$reference
			);
			
			$this->generate_local_qr_code($post_id, $fallback_data);
		}
		
		private function save_epc_qr_image($post_id, $qr_image) {
			$upload_dir = wp_upload_dir();
			$filename = trailingslashit($upload_dir['path']) . 'qrcode-' . $post_id . '.png';
			file_put_contents($filename, $qr_image);
			
			$attachment = [
				'post_mime_type' => 'image/png',
				'post_title' => 'QR Code for ' . get_the_title($post_id),
				'post_content' => '',
				'post_status' => 'inherit',
			];
			
			$attach_id = wp_insert_attachment($attachment, $filename, $post_id);
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
			wp_update_attachment_metadata($attach_id, $attach_data);
			
			update_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id', $attach_id);
			delete_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id_png');
		}
		
		private function generate_local_qr_code($post_id, $data, $size = 300) {
			$upload_dir = wp_upload_dir();
			$svg_filename = trailingslashit($upload_dir['path']) . 'qrcode-' . $post_id . '.svg';
			$png_filename = trailingslashit($upload_dir['path']) . 'qrcode-' . $post_id . '.png';
			
			$svg_builder = new Builder(
				writer: new SvgWriter(),
				writerOptions: [
					SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true,
				],
				validateResult: false,
				data: $data,
				encoding: new Encoding('UTF-8'),
				errorCorrectionLevel: ErrorCorrectionLevel::High,
				size: $size,
				margin: 10,
				roundBlockSizeMode: RoundBlockSizeMode::Margin
			);
			
			$svg_result = $svg_builder->build();
			file_put_contents($svg_filename, $svg_result->getString());
			
			$svg_attachment = [
				'post_mime_type' => 'image/svg+xml',
				'post_title' => 'QR Code for ' . get_the_title($post_id),
				'post_content' => '',
				'post_status' => 'inherit',
			];
			
			$svg_attach_id = wp_insert_attachment($svg_attachment, $svg_filename, $post_id);
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$svg_attach_data = wp_generate_attachment_metadata($svg_attach_id, $svg_filename);
			wp_update_attachment_metadata($svg_attach_id, $svg_attach_data);
			update_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id', $svg_attach_id);
			
			$png_builder = new Builder(
				writer: new PngWriter(),
				writerOptions: [],
				validateResult: false,
				data: $data,
				encoding: new Encoding('UTF-8'),
				errorCorrectionLevel: ErrorCorrectionLevel::High,
				size: $size,
				margin: 10,
				roundBlockSizeMode: RoundBlockSizeMode::Margin
			);
			
			$png_result = $png_builder->build();
			file_put_contents($png_filename, $png_result->getString());
			
			$png_attachment = [
				'post_mime_type' => 'image/png',
				'post_title' => 'QR Code PNG for ' . get_the_title($post_id),
				'post_content' => '',
				'post_status' => 'inherit',
			];
			
			$png_attach_id = wp_insert_attachment($png_attachment, $png_filename, $post_id);
			$png_attach_data = wp_generate_attachment_metadata($png_attach_id, $png_filename);
			wp_update_attachment_metadata($png_attach_id, $png_attach_data);
			update_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id_png', $png_attach_id);
		}
		
		/************************************************************************
		 * LOGIC DELETE QR CODE IMAGE RELATED TO POST
		 ************************************************************************/
		public function delete_qr_code_attachments($post_id) {
			$post = get_post($post_id);
			if ($post && $post->post_type === 'cloth-qrcodes') :
				$this->delete_qr_code_files($post_id);
			endif;
		}
		
		private function delete_qr_code_files($post_id) {
			$attachment_ids = [
				get_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id', true),
				get_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id_png', true),
			];
			
			foreach (array_filter(array_map('absint', $attachment_ids)) as $attachment_id) :
				$file_path = get_attached_file($attachment_id);
				if ($file_path && file_exists($file_path)) :
					wp_delete_file($file_path);
				endif;
				wp_delete_attachment($attachment_id, true);
			endforeach;
			
			delete_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id');
			delete_post_meta($post_id, 'cloth_qrcodes_qr_attachment_id_png');
		}
	}
