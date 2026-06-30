<?php
	
	namespace ClothQrcode;
	require_once CLOTH_QRCODE_PATH . 'vendor/autoload.php';
	
	use Endroid\QrCode\Builder\Builder;
	use Endroid\QrCode\Writer\PngWriter;
	use Endroid\QrCode\ErrorCorrectionLevel;
	
	class Redirect {
		public function __construct() {
			add_action('init', [$this, 'add_redirect_rule']);
			add_filter('query_vars', [$this, 'add_query_vars']);
			add_action('template_redirect', [$this, 'handle_redirect']);
		}
		
		/**
		 * Add rewrite rule for QR redirect URLs.
		 */
		public function add_redirect_rule() {
			add_rewrite_rule('qr-redirect/([0-9]+)/?$', 'index.php?qr_redirect_id=$matches[1]', 'top');
			add_rewrite_rule('qr-vcard/([0-9]+)/?.*$', 'index.php?qr_vcard_id=$matches[1]', 'top');
		}
		
		/**
		 * Add custom query vars.
		 */
		public function add_query_vars($vars) {
			$vars[] = 'qr_redirect_id';
			$vars[] = 'qr_vcard_id';
			return $vars;
		}
		
		/**
		 * Handle the redirect logic.
		 */
		public function handle_redirect() {
			$qr_vcard_id = absint(get_query_var('qr_vcard_id'));
			if ($qr_vcard_id) :
				$this->serve_vcard($qr_vcard_id);
				return;
			endif;
			
			$qr_redirect_id = absint(get_query_var('qr_redirect_id'));
			
			// Also allow the normal QR Code post permalink to use the same redirect/stat logic.
			if (!$qr_redirect_id && is_singular('cloth-qrcodes')) :
				$qr_redirect_id = absint(get_queried_object_id());
			endif;
			
			// Validate the QR redirect ID
			if (!$qr_redirect_id) :
				return;
			endif;
			
			$post = get_post($qr_redirect_id);
			if (!$post || $post->post_type !== 'cloth-qrcodes') :
				return;
			endif;
			
			// Get all meta data at once for performance
			$meta = get_post_meta($qr_redirect_id);
			$qrcode_mode = $meta['cloth_qrcodes_mode'][0] ?? '';
			$fallback_link = $meta['cloth_qrcodes_fallback_link'][0] ?? '';
			$fallback_link_type = $meta['cloth_qrcodes_fallback_link_type'][0] ?? '';
			$target_link = home_url();
			
			
			// Handle campaign mode
			if ($qrcode_mode === 'campaign') :
				$this->handle_campaign_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, $target_link);
			// Handle regular mode
			elseif ($qrcode_mode === 'regular') :
				$this->handle_regular_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, $target_link);
			// Handle payment mode
			elseif ($qrcode_mode === 'maps') :
				$this->handle_map_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, $target_link);
			// Handle payment mode
			elseif ($qrcode_mode === 'payment') :
				//$this->handle_payment_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, $target_link);
			elseif ($qrcode_mode === 'wifi') :
				$this->handle_wifi_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, $target_link);
			elseif ($qrcode_mode === 'limit') :
				$this->handle_limit_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, $target_link);
			elseif ($qrcode_mode === 'vcard') :
				$target_link = Vcard::get_vcard_url($qr_redirect_id);
				$this->log_scan($qr_redirect_id, $target_link);
			endif;
			
			
			// Redirect to the target URL
			if ($target_link !== home_url()) :
				wp_redirect(esc_url_raw($target_link));
				exit;
			endif;
		}
		
		

		/**
		 * Serve the current vCard data for a dynamic contact QR code.
		 */
		private function serve_vcard($qr_vcard_id) {
			$post = get_post($qr_vcard_id);
			if (!$post || $post->post_type !== 'cloth-qrcodes' || $post->post_status !== 'publish') :
				status_header(404);
				exit;
			endif;
			
			$mode = get_post_meta($qr_vcard_id, 'cloth_qrcodes_mode', true);
			if ($mode !== 'vcard') :
				status_header(404);
				exit;
			endif;
			
			$payload = Vcard::build_payload($qr_vcard_id);
			$filename = Vcard::get_filename($qr_vcard_id);
			
			$this->log_scan($qr_vcard_id, Vcard::get_vcard_url($qr_vcard_id));
			
			nocache_headers();
			header('Content-Type: text/vcard; charset=utf-8');
			header('Content-Disposition: inline; filename="' . $filename . '"');
			header('X-Content-Type-Options: nosniff');
			header('X-Robots-Tag: noindex, nofollow', true);
			echo $payload;
			exit;
		}
		
		/**
		 * Handle Limit Scan mode logic.
		 */
		private function handle_limit_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, &$target_link) {
			$limit_entries = maybe_unserialize($meta['cloth_qrcodes_limit_entries'][0] ?? []);
			$scan_stats = get_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_stats', true);
			
			
			$scan_stats = is_array($scan_stats) ? $scan_stats : [];
			$valid_index = null;
			
			if (is_array($limit_entries)):
				foreach ($limit_entries as $index => $entry):
					$link = $this->get_link_url($entry, $entry['link_type']);
					$scan_count = $scan_stats[$link] ?? 0;
					$scan_limit = $entry['scan_limit'] ?? 0;
					
					if ($scan_count < $scan_limit):
						$valid_index = $index;
						break;
					endif;
				endforeach;
			endif;
			
			
			if ($valid_index !== null):
				$valid_entry = $limit_entries[$valid_index];
				$target_link = $this->get_link_url($valid_entry, $valid_entry['link_type']);
				
				// Increment the scan count for this entry
				$link = $target_link;
				$scan_stats[$link] = ($scan_stats[$link] ?? 0) + 1;
				
				$this->log_scan($qr_redirect_id, $target_link);
			elseif ($fallback_link):
				$target_link = $this->get_link_url($fallback_link, $fallback_link_type);
				
				// Increment the scan count for the fallback link
				$fallback_link_url = $this->get_link_url($fallback_link, $fallback_link_type);
				$scan_stats[$fallback_link_url] = ($scan_stats[$fallback_link_url] ?? 0) + 1;
			endif;
			
			update_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_stats', $scan_stats);
		}
		
		
		/*
		private function handle_limit_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, &$target_link) {
			$limit_entries = maybe_unserialize($meta['cloth_qrcodes_limit_entries'][0] ?? []);
			$valid_index = null;
			
			debug($limit_entries);
			
			
			if (is_array($limit_entries)) {
				foreach ($limit_entries as $index => $entry) {
					$scan_count = get_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_count_' . $index, true);
					
					debug($scan_count);
					
					$scan_limit = $entry['scan_limit'] ?? 0;
					
					if ($scan_count < $scan_limit) {
						$valid_index = $index;
						break;
					}
				}
			}
			
			if ($valid_index !== null) {
				$valid_entry = $limit_entries[$valid_index];
				$target_link = $this->get_link_url($valid_entry, $valid_entry['link_type']);
				
				// Increment the scan count for this entry
				$scan_count = get_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_count_' . $valid_index, true);
				update_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_count_' . $valid_index, ($scan_count ? $scan_count + 1 : 1));
				
				$this->log_scan($qr_redirect_id, $target_link);
			} elseif ($fallback_link) {
				$scan_count = get_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_count_' . $valid_index, true);
				update_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_count_' . $valid_index, ($scan_count ? $scan_count + 1 : 1));
				$target_link = $this->get_link_url($fallback_link, $fallback_link_type);
				
			}
		}
		*/
		
		
		/**
		 * Handle Wi-Fi mode logic.
		 */
		private function handle_wifi_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, &$target_link) {
			$ssid = $meta['cloth_qrcodes_wifi_ssid'][0] ?? '';
			$password = $meta['cloth_qrcodes_wifi_password'][0] ?? '';
			$auth_type = $meta['cloth_qrcodes_wifi_auth_type'][0] ?? 'nopass';
			
			// Build the Wi-Fi QR code payload
			$wifiPayload = sprintf(
				"WIFI:S:%s;T:%s;P:%s;;",
				$ssid,
				$auth_type,
				$password
			);
			
			// Generate and output the QR code image
			$builder = new Builder(
				data: $wifiPayload,
		        writer: new PngWriter(),
		        errorCorrectionLevel: ErrorCorrectionLevel::High,
		        size: 300,
		    );
		
		    $result = $builder->build();
		
		    // Output the QR code image directly
		    header('Content-Type: ' . $result->getMimeType());
		    echo $result->getString();
		    exit;
		}
		

		
		
		/**
		 * Handle Map mode logic.
		 */
		private function handle_map_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, &$target_link) {
			$lat = $meta['cloth_qrcodes_maps_lat'][0] ?? '';
			$lng = $meta['cloth_qrcodes_maps_lng'][0] ?? '';
			$address = $meta['cloth_qrcodes_maps_address'][0] ?? '';
			
			// Build the Google Maps URL
			if (!empty($address)) :
				$mapsUrl = "https://www.google.com/maps?q=" . urlencode($address);
			else :
				$mapsUrl = "https://www.google.com/maps?q={$lat},{$lng}";
			endif;
			
			// Set the target link to the Google Maps URL
			$target_link = $mapsUrl;
			
			// Log the scan
			$this->log_scan($qr_redirect_id, $target_link);
		}
		
		/**
		 * Handle campaign mode logic.
		 */
		
		private function handle_campaign_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, &$target_link) {
			$campaigns = maybe_unserialize($meta['cloth_qrcodes_campaign_entries'][0] ?? []);
			$current_time = new \DateTime('now', new \DateTimeZone('Europe/Brussels')); // Set timezone
			$valid_index = null;
			
			if (is_array($campaigns)) :
				foreach ($campaigns as $index => $campaign) :
					$start = \DateTime::createFromFormat('d-m-Y H:i:s', $campaign['start_datetime'] ?? '', new \DateTimeZone('Europe/Brussels'));
					$end = \DateTime::createFromFormat('d-m-Y H:i:s', $campaign['end_datetime'] ?? '', new \DateTimeZone('Europe/Brussels'));
					
					if ($end) :
						$end->setTime(23, 59, 59); // Include the entire end day
					endif;
					
					if ($start && $end && $start <= $end && $current_time >= $start && $current_time <= $end) :
						$valid_index = $index;
						break;
					endif;
				endforeach;
			endif;
			
			if ($valid_index !== null) :
				$valid_campaign = $campaigns[$valid_index];
				$target_link = $this->get_link_url($valid_campaign, $valid_campaign['link_type']);
				$this->log_scan($qr_redirect_id, $target_link);
			elseif ($fallback_link) :
				$target_link = $this->get_link_url($fallback_link, $fallback_link_type);
			endif;
		}
		
		
		/**
		 * Handle regular mode logic.
		 */
		private function handle_regular_mode($qr_redirect_id, $meta, $fallback_link, $fallback_link_type, &$target_link) {
			$current_link = get_post_meta($qr_redirect_id, 'cloth_qrcodes_link', true) ?? '';
			$link_type = $meta['cloth_qrcodes_link_type'][0] ?? '';
			$params = maybe_unserialize($meta['cloth_qrcodes_params'][0] ?? []);
			$param_type = $meta['cloth_qrcodes_url_params_type'][0] ?? '';
			
			// Set default link if empty
			if (empty($current_link)) :
				$current_link = get_permalink($qr_redirect_id);
			endif;
			
			$target_link = $this->get_link_url($current_link, $link_type);
			
			
			// Add parameters to the URL
			if (is_array($params) && !empty($params)) :
				$target_link = $this->append_params_to_url($target_link, $params, $param_type);
			endif;
			
			$this->log_scan($qr_redirect_id, $target_link);
		}
		
		
		/**
		 * Append parameters to the URL.
		 */
		private function append_params_to_url($url, $params, $param_type) {
			if ($param_type === 'query_param') :
				$query_params = [];
				foreach ($params as $param) :
					if (!empty($param['key']) && isset($param['value'])) :
						$query_params[] = urlencode($param['key']) . '=' . urlencode($param['value']);
					endif;
				endforeach;
				if (!empty($query_params)) :
					$url .= (strpos($url, '?') === false ? '?' : '&') . implode('&', $query_params);
				endif;
			elseif ($param_type === 'path_param') :
				foreach ($params as $param) :
					if (!empty($param['value'])) :
						$url .= '/' . urlencode($param['value']);
					endif;
				endforeach;
			endif;
			return $url;
		}
		
		/**
		 * Log the scan for the target link.
		 * @param $qr_redirect_id
		 * @param $target_link
		 */
		private function log_scan($qr_redirect_id, $target_link) {
			$qrcode_mode = get_post_meta($qr_redirect_id, 'cloth_qrcodes_mode', true);
			$scan_stats = get_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_stats', true);
			
			if (!is_array($scan_stats)) :
				$scan_stats = [];
			endif;
			
			if ($qrcode_mode === 'campaign') :
				$today = date('Y-m-d');
				
				// Initialize the URL entry if it doesn't exist
				if (!isset($scan_stats[$target_link])) :
					$scan_stats[$target_link] = [];
				endif;
				
				// Initialize the date entry if it doesn't exist
				if (!isset($scan_stats[$target_link][$today])) :
					$scan_stats[$target_link][$today] = 0;
				endif;
				
				// Increment the count for today
				$scan_stats[$target_link][$today]++;
			else :
				// For non-campaign modes, increment the count for the URL
				$scan_stats[$target_link] = ($scan_stats[$target_link] ?? 0) + 1;
			endif;
			
			update_post_meta($qr_redirect_id, 'cloth_qrcodes_scan_stats', $scan_stats);
		}
		
		/**
		 * Get the link URL based on type.
		 */
		private function get_link_url($link, $link_type) {
			if ($link_type === 'internal') :
				$internal_link_id = is_array($link) ? absint($link['internal_link'] ?? 0) : absint($link);
				if (!$internal_link_id) :
					return home_url();
				endif;
				
				if (defined('ICL_SITEPRESS_VERSION')) :
					global $sitepress;
					$current_language = $sitepress->get_current_language();
					$language_info = apply_filters('wpml_post_language_details', null, $internal_link_id);
					if ($language_info) :
						$sitepress->switch_lang($language_info['language_code']);
						$permalink = get_permalink($internal_link_id);
						$sitepress->switch_lang($current_language);
						return $permalink;
					endif;
				endif;
				
				return get_permalink($internal_link_id);
			endif;
			
			return is_array($link) ? esc_url_raw($link['link'] ?? '') : esc_url_raw($link);
		}
	}