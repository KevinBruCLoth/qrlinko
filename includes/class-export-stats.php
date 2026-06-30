<?php
	
	namespace ClothQrcode;
	
	class ExportStats {
		
		public function __construct() {
			add_action('admin_post_export_scan_stats', [$this, 'handle_export_stats']);
		}
		
		public function handle_export_stats() {
			if (isset($_GET['action']) && $_GET['action'] === 'export_scan_stats') {
				if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'export_scan_stats_nonce')) {
					wp_die('Security check failed');
				}
				
				$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
				if (!$post_id) {
					wp_die('Invalid post ID');
				}
				
				$qrcode_mode = get_post_meta($post_id, 'cloth_qrcodes_mode', true);
				$scan_stats = get_post_meta($post_id, 'cloth_qrcodes_scan_stats', true);
				
				if (!empty($scan_stats) && is_array($scan_stats)) {
					$this->export_scan_stats_to_excel($scan_stats, $qrcode_mode);
				} else {
					wp_die('No scan statistics found');
				}
			}
		}
		
		private function export_scan_stats_to_excel($scan_stats, $qrcode_mode) {
			require_once CLOTH_QRCODE_PATH . 'vendor/autoload.php';
			
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			
			// Set headers based on mode
			if ($qrcode_mode === 'campaign') {
				$sheet->setCellValue('A1', 'Link');
				$sheet->setCellValue('B1', 'Date');
				$sheet->setCellValue('C1', 'Scans');
			} else {
				$sheet->setCellValue('A1', 'Link');
				$sheet->setCellValue('B1', 'Scans');
			}
			
			// Fill data
			$row = 2;
			foreach ($scan_stats as $url => $stats) {
				if ($qrcode_mode === 'campaign') {
					if (is_array($stats)) {
						foreach ($stats as $date => $count) {
							$sheet->setCellValue('A' . $row, $url);
							$sheet->setCellValue('B' . $row, $date);
							$sheet->setCellValue('C' . $row, $count);
							$row++;
						}
					}
				} else {
					$sheet->setCellValue('A' . $row, $url);
					$sheet->setCellValue('B' . $row, $stats);
					$row++;
				}
			}
			
			// Set file headers for download
			$filename = 'scan-stats-' . date('d-m-Y') . '.xlsx';
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			
			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save('php://output');
			exit;
		}
	}
