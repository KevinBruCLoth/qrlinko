<?php
	
	spl_autoload_register(function ($class) {
		$prefix = 'ClothQrcode\\';
		$baseDir = __DIR__ . '/';
		
		// Check if the class uses the namespace prefix
		if (strpos($class, $prefix) !== 0) {
			return;
		}
		
		// Remove the prefix from the class name
		$relativeClass = substr($class, strlen($prefix));
		
		// Handle MetaBoxes namespace separately
		if (strpos($relativeClass, 'MetaBoxes\\') === 0) :
			$relativeClass = substr($relativeClass, strlen('MetaBoxes\\'));
			$filePath = $baseDir . 'meta-boxes/class-' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $relativeClass)) . '.php';
		else :
			$filePath = $baseDir . 'class-' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $relativeClass)) . '.php';
		endif;
		
		// Debug: Log the class and file path
		//debug("Autoloader: Trying to load $class from $filePath");
		
		// If the file exists, require it
		if (file_exists($filePath)) :
			require $filePath;
		//debug("Autoloader: Successfully loaded $class");
		else :
			//debug("Autoloader: File not found for $class at $filePath");
		endif;
	});
	
	/*
	spl_autoload_register(function ($class) {
		$prefix = 'ClothQrcode\\';
		$base_dir = __DIR__ . '/';
		
		// Check if the class uses the namespace prefix
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) !== 0) :
			return;
		endif;
		
		// Get the relative class name
		$relative_class = substr($class, $len);
		
		// Convert PascalCase to snake_case for the file name
		$file_name = preg_replace('/([a-z])([A-Z])/', '$1-$2', $relative_class);
		$file_name = strtolower($file_name);
		
		// Construct the file path
		$file = $base_dir . 'class-' . $file_name . '.php';
		
		// Debug: Log the class and file path
		debug("Trying to load class: $class, file: $file");
		
		// If the file exists, require it
		if (file_exists($file)) :
			require $file;
			debug("Successfully loaded class: $class");
		else :
			debug("Failed to load class: $class, file not found: $file");
		endif;
	});
	*/

