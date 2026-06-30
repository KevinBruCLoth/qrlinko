# Qrlinko

Qrlinko is a WordPress plugin for creating and managing dynamic QR codes.

It lets you create QR codes from the WordPress admin, redirect scans to different destinations, track scan statistics, download generated QR codes, and manage several QR code modes such as regular links, campaigns, maps, payments, vCards, Wi-Fi, and scan limits.

> Technical note: the current plugin slug/text domain in the code is `cloth-qrcode`.

## Requirements

* WordPress 5.0+
* PHP 8.0+
* Composer
* Writable WordPress uploads directory

## Features

* Custom post type for QR codes
* QR code categories
* Dynamic redirect URLs
* QR code permalink redirect support
* SVG QR code generation
* PNG QR code generation
* QR code preview in admin
* Download generated QR codes
* Shortcode support
* Scan statistics
* Excel export for statistics
* Default QR size and color settings
* Translation-ready structure

## QR Code Modes

Qrlinko currently supports:

* Regular URL QR codes
* Campaign mode
* Payment mode
* Google Maps mode
* vCard contact mode
* Wi-Fi mode
* Scan Limit mode

## Installation

Clone the repository or copy the plugin folder into your WordPress plugins directory:

```bash
wp-content/plugins/qrlinko
```

Then install PHP dependencies:

```bash
composer install
```

Activate the plugin from:

```text
WordPress Admin → Plugins → Qrlinko
```

After activation, go to:

```text
WordPress Admin → Qrlinko
```

## Usage

### Create a QR Code

1. Go to `Qrlinko → Add New`.
2. Enter a title.
3. Choose a QR Code mode.
4. Fill in the mode-specific fields.
5. Publish the QR Code.

Once published, Qrlinko generates the QR code and displays:

* QR code preview
* shortcode
* redirect URL
* permalink URL
* download buttons
* scan statistics

## Shortcode

Use the shortcode to display a QR code anywhere on the site:

```text
[cloth_qrcode id="123"]
```

Optional size parameter:

```text
[cloth_qrcode id="123" size="medium"]
```

## Redirect URLs

Each QR code has a dynamic redirect URL:

```text
/qr-redirect/{post_id}/
```

Published QR code permalinks also use the same redirect and statistics logic:

```text
/qrcodes/{qr-code-slug}/
```

This means both the QR redirect URL and the public permalink can track scans and redirect through the same logic.

## Statistics

Qrlinko stores scan statistics per QR code.

Depending on the mode, statistics can include:

* scanned URL
* scan count
* scan date
* campaign link totals

Statistics can be viewed directly inside the QR code edit screen.

They can also be exported from the admin as an Excel file.

## Settings

The plugin includes a settings page:

```text
Qrlinko → Settings
```

Available settings:

* Default QR code size
* Default QR code color

## Project Structure

```text
qrlinko/
├── cloth-qrcode.php
├── composer.json
├── includes/
│   ├── class-admin.php
│   ├── class-autoloader.php
│   ├── class-cpt.php
│   ├── class-export-stats.php
│   ├── class-meta-boxes.php
│   ├── class-plugin.php
│   ├── class-qr-generator.php
│   ├── class-redirect.php
│   ├── class-shortcode.php
│   ├── class-vcard.php
│   ├── helpers.php
│   └── meta-boxes/
│       ├── class-ajax.php
│       ├── class-enqueue.php
│       ├── class-renderer.php
│       ├── class-saver.php
│       └── templates/
│           ├── global/
│           │   ├── mode-selector.php
│           │   ├── qrcode.php
│           │   ├── stats.php
│           │   └── url.php
│           └── modes/
│               ├── campaign.php
│               ├── limit.php
│               ├── maps.php
│               ├── payment.php
│               ├── regular.php
│               ├── vcard.php
│               └── wifi.php
├── assets/
│   ├── css/
│   ├── js/
│   └── font/
├── languages/
├── vendor/
├── uninstall.php
└── README.md
```

## Composer Dependencies

The plugin uses:

* `endroid/qr-code`
* `phpoffice/phpspreadsheet`
* `dasprid/enum`

Install them with:

```bash
composer install
```

## Development Notes

The plugin is organized around a modular structure:

* CPT registration is handled in `class-cpt.php`
* QR generation is handled in `class-qr-generator.php`
* Redirect logic is handled in `class-redirect.php`
* Meta box rendering and saving are separated inside `includes/meta-boxes/`
* Admin scripts and styles are stored in `assets/`
* Translations are stored in `languages/`

When adding new QR modes, keep the logic separated by mode and avoid changing the existing redirect/statistics flow unless necessary.

## Translations

Qrlinko is translation-ready and includes language files in:

```text
languages/
```

Current translation files include French and German.

## License

GPL v2 or later.
