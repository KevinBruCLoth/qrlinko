<?php
	namespace ClothQrcode;
	
	class Vcard {
		public static function get_vcard_url($post_id) {
			return add_query_arg('qr_vcard_id', absint($post_id), home_url('/'));
		}
		
		public static function get_filename($post_id) {
			$first_name = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_first_name', true);
			$last_name = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_last_name', true);
			$full_name = trim($first_name . ' ' . $last_name);
			
			if ($full_name === '') :
				$full_name = get_the_title($post_id);
			endif;
			
			$filename = sanitize_title($full_name);
			if ($filename === '') :
				$filename = 'contact-' . absint($post_id);
			endif;
			
			return $filename . '.vcf';
		}
		
		public static function build_payload($post_id) {
			$first_name = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_first_name', true);
			$last_name = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_last_name', true);
			$full_name = trim($first_name . ' ' . $last_name);
			
			if ($full_name === '') :
				$full_name = get_the_title($post_id);
			endif;
			
			$lines = [
				'BEGIN:VCARD',
				'VERSION:3.0',
				'N:' . self::escape_value($last_name) . ';' . self::escape_value($first_name) . ';;;',
				'FN:' . self::escape_value($full_name),
			];
			
			$organization = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_organization', true);
			$job_title = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_job_title', true);
			$mobile = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_mobile', true);
			$phone = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_phone', true);
			$email = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_email', true);
			$website = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_website', true);
			$street = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_street', true);
			$city = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_city', true);
			$region = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_region', true);
			$postal_code = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_postal_code', true);
			$country = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_country', true);
			$note = (string) get_post_meta($post_id, 'cloth_qrcodes_vcard_note', true);
			
			if ($organization !== '') :
				$lines[] = 'ORG:' . self::escape_value($organization);
			endif;
			
			if ($job_title !== '') :
				$lines[] = 'TITLE:' . self::escape_value($job_title);
			endif;
			
			if ($mobile !== '') :
				$lines[] = 'TEL;TYPE=CELL,VOICE:' . self::escape_value($mobile);
			endif;
			
			if ($phone !== '') :
				$lines[] = 'TEL;TYPE=WORK,VOICE:' . self::escape_value($phone);
			endif;
			
			if ($email !== '') :
				$lines[] = 'EMAIL;TYPE=INTERNET:' . self::escape_value($email);
			endif;
			
			if ($website !== '') :
				$lines[] = 'URL:' . self::escape_value($website);
			endif;
			
			if ($street !== '' || $city !== '' || $region !== '' || $postal_code !== '' || $country !== '') :
				$lines[] = 'ADR;TYPE=WORK:;;' . implode(';', [
					self::escape_value($street),
					self::escape_value($city),
					self::escape_value($region),
					self::escape_value($postal_code),
					self::escape_value($country),
				]);
			endif;
			
			if ($note !== '') :
				$lines[] = 'NOTE:' . self::escape_value($note);
			endif;
			
			$lines[] = 'REV:' . gmdate('Ymd\\THis\\Z', (int) get_post_modified_time('U', true, $post_id));
			$lines[] = 'END:VCARD';
			
			return implode("\r\n", $lines) . "\r\n";
		}
		
		private static function escape_value($value) {
			$value = str_replace('\\', '\\\\', (string) $value);
			$value = str_replace(["\r\n", "\r", "\n"], '\\n', $value);
			$value = str_replace([';', ','], ['\\;', '\\,'], $value);
			
			return $value;
		}
	}
