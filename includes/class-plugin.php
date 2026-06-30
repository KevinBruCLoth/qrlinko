<?php
	
	namespace ClothQrcode;
	
	class Plugin {
		private static $instance = null;
		
		public static function get_instance() {
			if (null === self::$instance) :
				self::$instance = new self();
			endif;
			return self::$instance;
		}
		
		public function run() {
			new CPT();
			new QrGenerator();
			new Redirect();
			new Admin();
			new ExportStats();
			new Shortcode();
			new MetaBoxes();
			
		}
	}
