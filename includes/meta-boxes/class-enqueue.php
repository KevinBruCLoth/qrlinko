<?php
	namespace ClothQrcode\MetaBoxes;
	
	class Enqueue {
		public function __construct() {
			add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
		}
		
		public function enqueue_admin_scripts($hook) {
			if (in_array($hook, ['post.php', 'post-new.php'], true)) :
				global $post_type;
				
				if ($post_type === 'cloth-qrcodes') :
					wp_enqueue_style(
						'cloth-qrcode-admin-css',
						CLOTH_QRCODE_URL . 'assets/css/admin.css',
						[],
						filemtime(CLOTH_QRCODE_PATH . 'assets/css/admin.css')
					);
					
					wp_enqueue_script(
						'cloth-qrcode-admin-js',
						CLOTH_QRCODE_URL . 'assets/js/admin.js',
						['jquery', 'jquery-ui-datepicker'],
						filemtime(CLOTH_QRCODE_PATH . 'assets/js/admin.js'),
						true
					);
					
					wp_localize_script('cloth-qrcode-admin-js', 'plugin_ajax_object', [
						'ajax_url' => admin_url('admin-ajax.php'),
						'nonce' => wp_create_nonce('cloth_qrcode_admin_ajax'),
					]);
					
					wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
					wp_enqueue_script('jquery-ui-timepicker-addon', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', ['jquery-ui-datepicker'], '1.6.3', true);
					wp_enqueue_style('jquery-ui-timepicker-addon-css', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css');
				endif;
			endif;
		}
	}
