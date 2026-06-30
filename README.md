# Qrcode-Plugin

> [!WARNING]
> !!!! SERVER NEED TO BE SET UP ON PHP 8 (or above) !!!! 

## PLUGIN INSTALL 

- Clone repo 
- Open termninal & get on your project folder
- Install dependencies:
```
composer install
```
- Upload the plugin into you wordpress project (Plugins folder of you worpdress project) 

## PLUGIN STRUCTURE
```
cloth-qrcode/
├── cloth-qrcode.php                # Main plugin file (minimal code)
│ 
├── includes/
│   ├── meta-boxes/
│   │   ├── class-renderer.php      # Renders meta boxes
│   │   ├── class-saver.php         # Saves meta box data
│   │   ├── class-ajax.php          # Handles AJAX requests
│   │   ├── class-enqueue.php       # Enqueues scripts/styles
│   │   │
│   │   └── templates/              # Template files for each mode
│   │       ├── /global
│   │       │    ├── mode-selector.php
│   │       │    ├── qrcode.php
│   │       │    └── stats.php
│   │       │
│   │       └── /modes
│   │            ├── regular.php
│   │            ├── campaign.php
│   │            ├── payment.php
│   │            ├── maps.php
│   │            └── wifi.php
│   │
│   ├── class-autoloader.php        # Autoload classes
│   ├── class-cpt.php               # CPT and taxonomy registration
│   ├── class-qr-generator.php      # QR code generation logic
│   ├── class-redirect.php          # Redirect logic
│   ├── class-plugin.php            # Plugin Instance
│   ├── class-meta-boxes.php        # Meta box logic
│   ├── class-export-stats.php      # Export Stats for qrcode logic
│   └── class-admin.php             # Admin-related logic
│
├── assets/
│   ├── js/                         # JavaScript files
│   ├── font/                       # Font file
│   └── css/                        # CSS files
│
├── languages/                      # Translation files
│
└── vendor/                         # Composer dependencies
 
```