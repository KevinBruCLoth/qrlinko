<?php
	
	namespace ClothQrcode;
	
	class Admin {
		public function __construct() {
			add_action('admin_menu', [$this, 'admin_menu']);
			add_action('admin_init', [$this, 'register_settings']);
			add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
		}
		
		/**
		 * ADMIN MENU
		 */
		public function admin_menu() {
			// Add main menu item
			
			// Add submenu for QR codes list (this will appear first)
			add_submenu_page(
				'cloth-qrcode',
				__('All Qr-codes', 'cloth-qrcode'),
				__('All Qr-codes', 'cloth-qrcode'),
				'edit_posts',
				'edit.php?post_type=cloth-qrcodes',
				null
			);
			
			// Add submenu for QR code categories
			add_submenu_page(
				'cloth-qrcode',
				__('QR-codes categories', 'cloth-qrcode'),
				__('QR-codes categories', 'cloth-qrcode'),
				'manage_categories',
				'edit-tags.php?taxonomy=cloth-qrcodes-categories&post_type=cloth-qrcodes',
				null
			);
			
			add_menu_page(
				__('Settings', 'cloth-qrcode'),
				__('Qrlinko', 'cloth-qrcode'),
				'manage_options',
				'cloth-qrcode',
				[$this, 'render_settings_page'],
				'dashicons-cloth',
				28
			);
			
			
			// Add submenu for settings (this will appear last)
			add_submenu_page(
				'cloth-qrcode',
				__('Settings', 'cloth-qrcode'),
				__('Settings', 'cloth-qrcode'),
				'manage_options',
				'cloth-qrcode-settings',
				[$this, 'render_settings_page']
			);
			
		}
		
		/**
		 * ADMIN SETTINGS
		 */
		public function register_settings() {
			// Register a setting and its sanitization callback
			register_setting(
				'cloth_qrcode_settings_group', // Option group
				'cloth_qrcode_settings', // Option name
				[$this, 'sanitize_settings'] // Sanitize callback
			);
			
			// Add a settings section
			add_settings_section(
				'cloth_qrcode_settings_section', // ID
				__('QR Code Settings', 'cloth-qrcode'), // Title
				[$this, 'settings_section_callback'], // Callback
				'cloth-qrcode-settings' // Page
			);
			
			// Add settings fields
			add_settings_field(
				'default_qr_size', // ID
				__('Default QR Code Size', 'cloth-qrcode'), // Title
				[$this, 'default_qr_size_callback'], // Callback
				'cloth-qrcode-settings', // Page
				'cloth_qrcode_settings_section' // Section
			);
			
			add_settings_field(
				'default_qr_color', // ID
				__('Default QR Code Color', 'cloth-qrcode'), // Title
				[$this, 'default_qr_color_callback'], // Callback
				'cloth-qrcode-settings', // Page
				'cloth_qrcode_settings_section' // Section
			);
		}
		
		public function sanitize_settings($input) {
			$new_input = [];
			
			if (isset($input['default_qr_size'])) :
				$new_input['default_qr_size'] = absint($input['default_qr_size']);
			endif;
			
			if (isset($input['default_qr_color'])) :
				$new_input['default_qr_color'] = sanitize_hex_color($input['default_qr_color']);
			endif;
			
			return $new_input;
		}
		
		public function settings_section_callback() {
			echo '<p>' . esc_html__('Configure the default settings for QR codes.', 'cloth-qrcode') . '</p>';
		}
		
		public function default_qr_size_callback() {
			$options = get_option('cloth_qrcode_settings');
			$value = isset($options['default_qr_size']) ? $options['default_qr_size'] : 300;
			?>
            <input type="number" id="default_qr_size" name="cloth_qrcode_settings[default_qr_size]" value="<?php echo esc_attr($value); ?>" min="100" max="1000"/>
            <p class="description"><?php esc_html_e('Enter the default size for QR codes (in pixels).', 'cloth-qrcode'); ?></p>
			<?php
		}
		
		public function default_qr_color_callback() {
			$options = get_option('cloth_qrcode_settings');
			$value = isset($options['default_qr_color']) ? $options['default_qr_color'] : '#000000';
			?>
            <input type="text" id="default_qr_color" name="cloth_qrcode_settings[default_qr_color]" value="<?php echo esc_attr($value); ?>" class="color-picker"/>
            <p class="description"><?php esc_html_e('Enter the default color for QR codes.', 'cloth-qrcode'); ?></p>
			<?php
		}
		
		public function render_settings_page() {
			?>
            <div class="wrap">
                <h1><?php echo esc_html__('Cloth QR Code Settings', 'cloth-qrcode'); ?></h1>
                <form method="post" action="options.php">
					<?php
						settings_fields('cloth_qrcode_settings_group');
						do_settings_sections('cloth-qrcode-settings');
						submit_button(__('Save Settings', 'cloth-qrcode'));
					?>
                </form>
            </div>
			<?php
		}
		
		/**
		 * ADMIN ADD SCRIPTS
		 */
		public function enqueue_admin_scripts($hook) {
			wp_enqueue_style(
				'cloth-qrcode-admin-css',
				plugins_url('assets/css/admin.css', __DIR__),
				[],
				filemtime(plugin_dir_path(__DIR__) . 'assets/css/admin.css')
			);
		 
			if (in_array($hook, ['post.php', 'post-new.php'])) :
				global $post_type;
			
			
			
				if ($post_type === 'cloth-qrcodes') :
					wp_enqueue_script(
						'cloth-qrcode-admin-js',
						plugins_url('assets/js/admin.js', __DIR__),
						['jquery', 'jquery-ui-datepicker'],
						filemtime(plugin_dir_path(__DIR__) . 'assets/js/admin.js'),
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
