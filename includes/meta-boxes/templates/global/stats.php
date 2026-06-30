<?php
	$scan_stats = get_post_meta($post->ID, 'cloth_qrcodes_scan_stats', true);
	$qrcode_mode = get_post_meta($post->ID, 'cloth_qrcodes_mode', true);
?>

<?php if (!empty($scan_stats) && is_array($scan_stats)): ?>
    <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php _e('Scan Statistics', 'cloth-qrcode') ?>:</label>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
				<?php if ($qrcode_mode === 'campaign'): ?>
                    <th><?php _e('Link', 'cloth-qrcode'); ?></th>
                    <th><?php _e('Date', 'cloth-qrcode'); ?></th>
                    <th><?php _e('Scans', 'cloth-qrcode'); ?></th>
				<?php else: ?>
                    <th><?php _e('Link', 'cloth-qrcode'); ?></th>
                    <th><?php _e('Scans', 'cloth-qrcode'); ?></th>
				<?php endif; ?>
            </tr>
            </thead>
            <tbody>
			<?php foreach ($scan_stats as $url => $stats): ?>
				<?php if ($qrcode_mode === 'campaign'): ?>
					<?php if (is_array($stats)): ?>
						<?php foreach ($stats as $date => $count): ?>
                            <tr>
                                <td><?= esc_url($url); ?></td>
                                <td><?= esc_html($date); ?></td>
                                <td><?= esc_html($count); ?></td>
                            </tr>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php else: ?>
                    <tr>
                        <td><?= esc_url($url); ?></td>
                        <td><?= esc_html($stats); ?></td>
                    </tr>
				<?php endif; ?>
			<?php endforeach; ?>
            </tbody>
        </table>


        <div style="margin-top: 15px;">
            <a href="<?= add_query_arg([
			    'action' => 'export_scan_stats',
			    'post_id' => $post->ID,
			    '_wpnonce' => wp_create_nonce('export_scan_stats_nonce')
		    ], admin_url('admin-post.php')); ?>" class="button button-primary">
			    <?php _e('Download Statistics', 'cloth-qrcode'); ?>
            </a>
        </div>

        <!-- Add a second table for campaign mode to show total scans per URL -->
		<?php if ($qrcode_mode === 'campaign'): ?>
            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php _e('Total Scans per Link', 'cloth-qrcode') ?>:</label>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th><?php _e('Link', 'cloth-qrcode'); ?></th>
                        <th><?php _e('Total Scans', 'cloth-qrcode'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ($scan_stats as $url => $stats): ?>
						<?php if (is_array($stats)): ?>
                            <tr>
                                <td><?= esc_url($url); ?></td>
                                <td><?= esc_html(array_sum($stats)); ?></td>
                            </tr>
						<?php endif; ?>
					<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
		<?php endif; ?>
    </div>
<?php endif; ?>
