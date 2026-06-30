<?php
	if (!function_exists('debug')) {
		function debug($data) {
			if (!defined('WP_DEBUG') || !WP_DEBUG) :
				return;
			endif;
			
			$message = is_scalar($data) ? (string) $data : print_r($data, true);
			
			if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) :
				error_log($message);
			endif;
		}
	}
