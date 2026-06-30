<?php
	
	namespace ClothQrcode;
	
	class Helpers {
		/**
		 * Convert a string to a URL-friendly slug.
		 *
		 * @param string $string The string to slugify.
		 * @return string The slugified string.
		 */
		public static function slugify($string) {
			// Replace special characters like ö, ü, etc.
			$string = self::replace_special_chars($string);
			
			// Convert to lowercase
			$string = mb_strtolower($string, 'UTF-8');
			
			// Replace spaces and other separators with a hyphen
			$string = preg_replace('/[^a-z0-9\-]+/', '-', $string);
			
			// Trim hyphens from the start and end
			$string = trim($string, '-');
			
			return $string;
		}
		
		/**
		 * Replace special characters with their closest ASCII equivalents.
		 *
		 * @param string $string The string to process.
		 * @return string The processed string.
		 */
		private static function replace_special_chars($string) {
			$transliteration = [
				'ä' => 'a', 'ö' => 'o', 'ü' => 'u', 'ß' => 'ss',
				'Ä' => 'A', 'Ö' => 'O', 'Ü' => 'U',
				'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
				'æ' => 'ae',
				'ç' => 'c',
				'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
				'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
				'ð' => 'd',
				'ñ' => 'n',
				'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
				'ù' => 'u', 'ú' => 'u', 'û' => 'u',
				'ý' => 'y', 'ÿ' => 'y',
				'Ø' => 'O', 'ø' => 'o',
				'Å' => 'A', 'å' => 'a',
				'Æ' => 'AE', 'æ' => 'ae',
				'Ç' => 'C', 'ç' => 'c',
				'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
				'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
				'Ð' => 'D',
				'Ñ' => 'N',
				'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
				'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
				'Ý' => 'Y',
				'/' => '-', ' ' => '-', '_' => '-',
			];
			
			return strtr($string, $transliteration);
		}
		
		
	}
