<?php
	namespace ClothQrcode\MetaBoxes;
	
	class Saver {
		public function __construct() {
			add_action('save_post_cloth-qrcodes', [$this, 'save_meta_box'], 10, 2);
		}
		
		
		/**********************************************************************
		 * SAVE META BOX LOGIC
		 **********************************************************************/
		public function save_meta_box($post_id) {
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
			if (!isset($_POST['cloth_qrcodes_meta_box_nonce']) || !wp_verify_nonce($_POST['cloth_qrcodes_meta_box_nonce'], 'cloth_qrcodes_meta_box')) return;
			if (!current_user_can('edit_post', $post_id)) return;
			
			$mode = isset($_POST['cloth_qrcodes_mode']) ? sanitize_key(wp_unslash($_POST['cloth_qrcodes_mode'])) : '';
			
			// Save QRCODE MODE
			if (isset($_POST['cloth_qrcodes_mode'])) :
				update_post_meta($post_id, 'cloth_qrcodes_mode', $mode);
			endif;
   
			switch ($mode) {
				case 'regular':
					$this->save_regular_mode($post_id);
					break;
				case 'campaign':
					$this->save_campaign_mode($post_id);
					break;
				case 'payment':
					$this->save_payment_mode($post_id);
					break;
				case 'maps':
					$this->save_maps_mode($post_id);
					break;
				case 'wifi':
					$this->save_wifi_mode($post_id);
					break;
				case 'vcard':
					$this->save_vcard_mode($post_id);
					break;
				case 'limit':
					$this->save_limit_mode($post_id);
					break;
			}
		}
		
		
		/**********************************************************************
		 * SAVE REGULAR META BOX LOGIC
		 **********************************************************************/
		private function save_regular_mode($post_id) {
			delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
			
			// Save the link type
			if (isset($_POST['cloth_qrcodes_link_type'])) :
				update_post_meta($post_id, 'cloth_qrcodes_link_type', sanitize_text_field($_POST['cloth_qrcodes_link_type']));
			endif;
			
			// Save the link
			$link_type = isset($_POST['cloth_qrcodes_link_type']) ? sanitize_text_field($_POST['cloth_qrcodes_link_type']) : '';
			if ($link_type === 'external' && isset($_POST['cloth_qrcodes_link'])) :
				$link_data['link'] = esc_url_raw($_POST['cloth_qrcodes_link']);
				update_post_meta($post_id, 'cloth_qrcodes_link', $link_data);
			elseif ($link_type === 'internal' && isset($_POST['cloth_qrcodes_internal_link'])) :
				$link_data['internal_link'] = absint($_POST['cloth_qrcodes_internal_link']);
				update_post_meta($post_id, 'cloth_qrcodes_link', $link_data);
			endif;
			
			// Save start and end datetime
			if (isset($_POST['cloth_qrcodes_start_datetime'])) :
				update_post_meta($post_id, 'cloth_qrcodes_start_datetime', sanitize_text_field($_POST['cloth_qrcodes_start_datetime']));
			endif;
			
			if (isset($_POST['cloth_qrcodes_end_datetime'])) :
				update_post_meta($post_id, 'cloth_qrcodes_end_datetime', sanitize_text_field($_POST['cloth_qrcodes_end_datetime']));
			endif;
			
			// Save repeatable params
			if (isset($_POST['cloth_qrcodes_params'])) :
				$params = [];
				foreach ($_POST['cloth_qrcodes_params'] as $row) :
					$key = sanitize_text_field($row['key'] ?? '');
					$value = sanitize_text_field($row['value'] ?? '');
					
					if (!empty($key) || !empty($value)) :
						$params[] = [
							'key' => $key,
							'value' => $value
						];
					endif;
				endforeach;
				update_post_meta($post_id, 'cloth_qrcodes_params', $params);
			else :
				delete_post_meta($post_id, 'cloth_qrcodes_params');
			endif;
			
			// Save the URL param type
			if (isset($_POST['cloth_qrcodes_url_params_type'])) :
				update_post_meta($post_id, 'cloth_qrcodes_url_params_type', sanitize_text_field($_POST['cloth_qrcodes_url_params_type']));
			endif;
		
		}
		
		
		/**********************************************************************
		 * SAVE PAYMENT META BOX LOGIC
		 **********************************************************************/
		private function save_payment_mode($post_id) {
			delete_post_meta($post_id, 'cloth_qrcodes_link_type');
			delete_post_meta($post_id, 'cloth_qrcodes_link');
			delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_params');
			delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
			delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
			
			// Save payment details
			if (isset($_POST['cloth_qrcodes_payment_amount'])) :
				update_post_meta($post_id, 'cloth_qrcodes_payment_amount', sanitize_text_field($_POST['cloth_qrcodes_payment_amount']));
			endif;
			if (isset($_POST['cloth_qrcodes_payment_currency'])) :
				update_post_meta($post_id, 'cloth_qrcodes_payment_currency', sanitize_text_field($_POST['cloth_qrcodes_payment_currency']));
			endif;
			if (isset($_POST['cloth_qrcodes_payment_recipient'])) :
				update_post_meta($post_id, 'cloth_qrcodes_payment_recipient', sanitize_text_field($_POST['cloth_qrcodes_payment_recipient']));
			endif;
			if (isset($_POST['cloth_qrcodes_payment_iban'])) :
				update_post_meta($post_id, 'cloth_qrcodes_payment_iban', sanitize_text_field($_POST['cloth_qrcodes_payment_iban']));
			endif;
			if (isset($_POST['cloth_qrcodes_payment_bic'])) :
				update_post_meta($post_id, 'cloth_qrcodes_payment_bic', sanitize_text_field($_POST['cloth_qrcodes_payment_bic']));
			endif;
			if (isset($_POST['cloth_qrcodes_payment_reference'])) :
				update_post_meta($post_id, 'cloth_qrcodes_payment_reference', sanitize_text_field($_POST['cloth_qrcodes_payment_reference']));
			endif;
		
		}
		
		
		/**********************************************************************
		 * SAVE MAP META BOX LOGIC
		 **********************************************************************/
		private function save_maps_mode($post_id) {
			delete_post_meta($post_id, 'cloth_qrcodes_link_type');
			delete_post_meta($post_id, 'cloth_qrcodes_link');
			delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_params');
			delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
			delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
			
			// Save the input type (coordinates or address)
			if (isset($_POST['cloth_qrcodes_maps_input_type'])) :
				update_post_meta($post_id, 'cloth_qrcodes_maps_input_type', sanitize_text_field($_POST['cloth_qrcodes_maps_input_type']));
			endif;
			
			// Save Google Maps details
			if (isset($_POST['cloth_qrcodes_maps_name'])) :
				update_post_meta($post_id, 'cloth_qrcodes_maps_name', sanitize_text_field($_POST['cloth_qrcodes_maps_name']));
			endif;
			if (isset($_POST['cloth_qrcodes_maps_lat'])) :
				update_post_meta($post_id, 'cloth_qrcodes_maps_lat', sanitize_text_field($_POST['cloth_qrcodes_maps_lat']));
			endif;
			if (isset($_POST['cloth_qrcodes_maps_lng'])) :
				update_post_meta($post_id, 'cloth_qrcodes_maps_lng', sanitize_text_field($_POST['cloth_qrcodes_maps_lng']));
			endif;
			if (isset($_POST['cloth_qrcodes_maps_address'])) :
				update_post_meta($post_id, 'cloth_qrcodes_maps_address', sanitize_text_field($_POST['cloth_qrcodes_maps_address']));
			endif;
		
		}
		
		
		/**********************************************************************
		 * SAVE WIFI META BOX LOGIC
		 **********************************************************************/
		private function save_wifi_mode($post_id) {
			delete_post_meta($post_id, 'cloth_qrcodes_link_type');
			delete_post_meta($post_id, 'cloth_qrcodes_link');
			delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_params');
			delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
			delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
			
			// Save Wi-Fi details
			if (isset($_POST['cloth_qrcodes_wifi_ssid'])) :
				update_post_meta($post_id, 'cloth_qrcodes_wifi_ssid', sanitize_text_field($_POST['cloth_qrcodes_wifi_ssid']));
			endif;
			if (isset($_POST['cloth_qrcodes_wifi_password'])) :
				update_post_meta($post_id, 'cloth_qrcodes_wifi_password', sanitize_text_field($_POST['cloth_qrcodes_wifi_password']));
			endif;
			if (isset($_POST['cloth_qrcodes_wifi_auth_type'])) :
				update_post_meta($post_id, 'cloth_qrcodes_wifi_auth_type', sanitize_text_field($_POST['cloth_qrcodes_wifi_auth_type']));
			endif;
		
		}
		
		/**********************************************************************
		 * SAVE VCARD META BOX LOGIC
		 **********************************************************************/
		private function save_vcard_mode($post_id) {
			update_post_meta($post_id, 'cloth_qrcodes_vcard_delivery', 'dynamic');
			
			$this->delete_meta_keys($post_id, [
				'cloth_qrcodes_link_type',
				'cloth_qrcodes_link',
				'cloth_qrcodes_start_datetime',
				'cloth_qrcodes_end_datetime',
				'cloth_qrcodes_params',
				'cloth_qrcodes_url_params_type',
				'cloth_qrcodes_campaign_entries',
				'cloth_qrcodes_limit_entries',
				'cloth_qrcodes_fallback_link_type',
				'cloth_qrcodes_fallback_link',
				'cloth_qrcodes_payment_amount',
				'cloth_qrcodes_payment_currency',
				'cloth_qrcodes_payment_recipient',
				'cloth_qrcodes_payment_iban',
				'cloth_qrcodes_payment_bic',
				'cloth_qrcodes_payment_reference',
				'cloth_qrcodes_maps_input_type',
				'cloth_qrcodes_maps_name',
				'cloth_qrcodes_maps_lat',
				'cloth_qrcodes_maps_lng',
				'cloth_qrcodes_maps_address',
				'cloth_qrcodes_wifi_ssid',
				'cloth_qrcodes_wifi_password',
				'cloth_qrcodes_wifi_auth_type',
			]);
			
			$text_fields = [
				'cloth_qrcodes_vcard_first_name',
				'cloth_qrcodes_vcard_last_name',
				'cloth_qrcodes_vcard_mobile',
				'cloth_qrcodes_vcard_phone',
				'cloth_qrcodes_vcard_organization',
				'cloth_qrcodes_vcard_job_title',
				'cloth_qrcodes_vcard_street',
				'cloth_qrcodes_vcard_city',
				'cloth_qrcodes_vcard_region',
				'cloth_qrcodes_vcard_postal_code',
				'cloth_qrcodes_vcard_country',
			];
			
			foreach ($text_fields as $field) :
				if (isset($_POST[$field])) :
					update_post_meta($post_id, $field, sanitize_text_field(wp_unslash($_POST[$field])));
				else :
					delete_post_meta($post_id, $field);
				endif;
			endforeach;
			
			if (isset($_POST['cloth_qrcodes_vcard_email'])) :
				update_post_meta($post_id, 'cloth_qrcodes_vcard_email', sanitize_email(wp_unslash($_POST['cloth_qrcodes_vcard_email'])));
			else :
				delete_post_meta($post_id, 'cloth_qrcodes_vcard_email');
			endif;
			
			if (isset($_POST['cloth_qrcodes_vcard_website'])) :
				update_post_meta($post_id, 'cloth_qrcodes_vcard_website', esc_url_raw(wp_unslash($_POST['cloth_qrcodes_vcard_website'])));
			else :
				delete_post_meta($post_id, 'cloth_qrcodes_vcard_website');
			endif;
			
			if (isset($_POST['cloth_qrcodes_vcard_note'])) :
				update_post_meta($post_id, 'cloth_qrcodes_vcard_note', sanitize_textarea_field(wp_unslash($_POST['cloth_qrcodes_vcard_note'])));
			else :
				delete_post_meta($post_id, 'cloth_qrcodes_vcard_note');
			endif;
		}
		
		private function delete_meta_keys($post_id, $keys) {
			foreach ($keys as $key) :
				delete_post_meta($post_id, $key);
			endforeach;
		}

		/**********************************************************************
		 * SAVE LIMIT SCAN META BOX LOGIC
		 **********************************************************************/
		private function save_limit_mode($post_id) {
			delete_post_meta($post_id, 'cloth_qrcodes_link_type');
			delete_post_meta($post_id, 'cloth_qrcodes_link');
			delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_params');
			delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
			delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
			
			
			if (isset($_POST['cloth_qrcodes_limit_entries']) && is_array($_POST['cloth_qrcodes_limit_entries'])) :
				$params = [];
				foreach (wp_unslash($_POST['cloth_qrcodes_limit_entries']) as $row) :
					$entry = $this->sanitize_link_entry($row);
					$entry['scan_limit'] = isset($row['scan_limit']) ? absint($row['scan_limit']) : 0;
					$entry['params'] = isset($row['params']) ? sanitize_textarea_field($row['params']) : '';
					
					if ($entry['scan_limit'] > 0 && (!empty($entry['link']) || !empty($entry['internal_link']))) :
						$params[] = $entry;
					endif;
				endforeach;
				update_post_meta($post_id, 'cloth_qrcodes_limit_entries', $params);
			else :
				delete_post_meta($post_id, 'cloth_qrcodes_limit_entries');
			endif;
			
			// Save the fallback link type
			if (isset($_POST['cloth_qrcodes_limit_fallback_link_type'])) :
				update_post_meta($post_id, 'cloth_qrcodes_fallback_link_type', sanitize_text_field($_POST['cloth_qrcodes_limit_fallback_link_type']));
			endif;
			
			// Save the fallback link
			$fallback_link_type = isset($_POST['cloth_qrcodes_limit_fallback_link_type']) ? sanitize_text_field($_POST['cloth_qrcodes_limit_fallback_link_type']) : '';
			if ($fallback_link_type === 'external' && isset($_POST['cloth_qrcodes_limit_fallback_link'])) :
				update_post_meta($post_id, 'cloth_qrcodes_fallback_link', esc_url_raw($_POST['cloth_qrcodes_limit_fallback_link']));
			elseif ($fallback_link_type === 'internal' && isset($_POST['cloth_qrcodes_limit_internal_fallback_link'])) :
				update_post_meta($post_id, 'cloth_qrcodes_fallback_link', absint($_POST['cloth_qrcodes_limit_internal_fallback_link']));
			endif;
		}
		
		/**********************************************************************
		 * SAVE CAMPAIGN META BOX LOGIC
		 **********************************************************************/
		private function save_campaign_mode($post_id) {
			delete_post_meta($post_id, 'cloth_qrcodes_link_type');
			delete_post_meta($post_id, 'cloth_qrcodes_link');
			delete_post_meta($post_id, 'cloth_qrcodes_start_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_end_datetime');
			delete_post_meta($post_id, 'cloth_qrcodes_params');
			delete_post_meta($post_id, 'cloth_qrcodes_url_params_type');
			
			if (isset($_POST['cloth_qrcodes_campaign_entries']) && is_array($_POST['cloth_qrcodes_campaign_entries'])) :
				$params = [];
				foreach (wp_unslash($_POST['cloth_qrcodes_campaign_entries']) as $row) :
					$entry = $this->sanitize_link_entry($row);
					$entry['start_datetime'] = isset($row['start_datetime']) ? sanitize_text_field($row['start_datetime']) : '';
					$entry['end_datetime'] = isset($row['end_datetime']) ? sanitize_text_field($row['end_datetime']) : '';
					$entry['params'] = isset($row['params']) ? sanitize_textarea_field($row['params']) : '';
					
					if (!empty($entry['start_datetime']) && !empty($entry['end_datetime']) && (!empty($entry['link']) || !empty($entry['internal_link']))) :
						$params[] = $entry;
					endif;
				endforeach;
				update_post_meta($post_id, 'cloth_qrcodes_campaign_entries', $params);
			else :
				delete_post_meta($post_id, 'cloth_qrcodes_campaign_entries');
			endif;
			
			// Save the fallback link type
			if (isset($_POST['cloth_qrcodes_campaign_fallback_link_type'])) :
				update_post_meta($post_id, 'cloth_qrcodes_fallback_link_type', sanitize_key(wp_unslash($_POST['cloth_qrcodes_campaign_fallback_link_type'])));
			endif;
			
			// Save the fallback link
			$fallback_link_type = isset($_POST['cloth_qrcodes_campaign_fallback_link_type']) ? sanitize_key(wp_unslash($_POST['cloth_qrcodes_campaign_fallback_link_type'])) : '';
			if ($fallback_link_type === 'external' && isset($_POST['cloth_qrcodes_campaign_fallback_link'])) :
				update_post_meta($post_id, 'cloth_qrcodes_fallback_link', esc_url_raw(wp_unslash($_POST['cloth_qrcodes_campaign_fallback_link'])));
			elseif ($fallback_link_type === 'internal' && isset($_POST['cloth_qrcodes_campaign_internal_fallback_link'])) :
				update_post_meta($post_id, 'cloth_qrcodes_fallback_link', absint($_POST['cloth_qrcodes_campaign_internal_fallback_link']));
			endif;
		}
		
		private function sanitize_link_entry($row) {
			$row = is_array($row) ? $row : [];
			$link_type = isset($row['link_type']) ? sanitize_key($row['link_type']) : 'external';
			$link_type = in_array($link_type, ['external', 'internal'], true) ? $link_type : 'external';
			
			return [
				'link_type' => $link_type,
				'link' => isset($row['link']) ? esc_url_raw($row['link']) : '',
				'internal_link' => isset($row['internal_link']) ? absint($row['internal_link']) : 0,
			];
		}
	}
