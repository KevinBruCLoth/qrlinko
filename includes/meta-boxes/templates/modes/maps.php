<?php if (empty($code_mode) || $code_mode === 'maps'): ?>
	<div id="cloth_qrcodes_maps_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'maps' ? 'display: block;' : 'display: none;'; ?>">
		<div style="margin-bottom: 15px;">
			<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_name">
				<?php _e('Location Name:', 'cloth-qrcode'); ?>
			</label>
			<input type="text" id="cloth_qrcodes_maps_name" name="cloth_qrcodes_maps_name"
			       value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_name', true)); ?>" style="width: 100%;"/>
			<p class="description"><?php _e('Enter the name of the location (e.g., "My Restaurant").', 'cloth-qrcode') ?></p>
		</div>
		
		<p style="font-weight: bold; margin-bottom: 10px;"><?php _e('Enter either coordinates or address:', 'cloth-qrcode'); ?></p>
		
		<div style="margin-bottom: 15px;">
			<input type="radio" id="cloth_qrcodes_maps_use_coordinates" name="cloth_qrcodes_maps_input_type" value="coordinates"
			       <?= (get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) === 'coordinates' || !get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true)) ? 'checked' : ''; ?>>
			<label for="cloth_qrcodes_maps_use_coordinates"><?php _e('Use Coordinates', 'cloth-qrcode'); ?></label>
			
			<input type="radio" id="cloth_qrcodes_maps_use_address" name="cloth_qrcodes_maps_input_type" value="address"
			       <?= get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) === 'address' ? 'checked' : ''; ?>>
			<label for="cloth_qrcodes_maps_use_address"><?php _e('Use Address', 'cloth-qrcode'); ?></label>
		</div>
		
		<div id="cloth_qrcodes_maps_coordinates_container" style="margin-bottom: 15px; <?= (get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) === 'address') ? 'display: none;' : ''; ?>">
			<div style="margin-bottom: 15px;">
				<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_lat">
					<?php _e('Latitude:', 'cloth-qrcode'); ?>
				</label>
				<input type="text" id="cloth_qrcodes_maps_lat" name="cloth_qrcodes_maps_lat"
				       value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_lat', true)); ?>" style="width: 100%;"
				       data-required-group="maps" data-required="coordinates"/>
				<p class="description"><?php _e('Enter the latitude of the location (e.g., 50.8503).', 'cloth-qrcode') ?></p>
			</div>
			
			<div style="margin-bottom: 15px;">
				<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_lng">
					<?php _e('Longitude:', 'cloth-qrcode'); ?>
				</label>
				<input type="text" id="cloth_qrcodes_maps_lng" name="cloth_qrcodes_maps_lng"
				       value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_lng', true)); ?>" style="width: 100%;"
				       data-required-group="maps" data-required="coordinates"/>
				<p class="description"><?php _e('Enter the longitude of the location (e.g., 4.3517).', 'cloth-qrcode') ?></p>
			</div>
		</div>
		
		<div id="cloth_qrcodes_maps_address_container" style="margin-bottom: 15px; <?= (get_post_meta($post->ID, 'cloth_qrcodes_maps_input_type', true) !== 'address') ? 'display: none;' : ''; ?>">
			<div style="margin-bottom: 15px;">
				<label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_maps_address">
					<?php _e('Address:', 'cloth-qrcode'); ?>
				</label>
				<input type="text" id="cloth_qrcodes_maps_address" name="cloth_qrcodes_maps_address"
				       value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_maps_address', true)); ?>" style="width: 100%;"
				       data-required-group="maps" data-required="address"/>
				<p class="description"><?php _e('Enter the full address of the location (e.g., "123 Main St, Brussels, Belgium").', 'cloth-qrcode') ?></p>
			</div>
		</div>
	</div>
<?php endif; ?>