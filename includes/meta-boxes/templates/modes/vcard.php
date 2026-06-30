<?php if (empty($code_mode) || $code_mode === 'vcard') : ?>
    <div id="cloth_qrcodes_vcard_container" data-element="container" style="margin-bottom: 15px; <?= $code_mode === 'vcard' ? 'display: block;' : 'display: none;'; ?>">
        <p class="description" style="margin-bottom: 15px;">
			<?php _e('Create a dynamic vCard QR code. The printed QR keeps working after you edit these contact details.', 'cloth-qrcode'); ?>
        </p>

        <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; margin-bottom:15px;">
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_first_name"><?php _e('First Name *', 'cloth-qrcode'); ?></label>
                <input data-required="required" type="text" id="cloth_qrcodes_vcard_first_name" name="cloth_qrcodes_vcard_first_name"
                       value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_first_name', true)); ?>" style="width:100%;"/>
            </div>
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_last_name"><?php _e('Last Name', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_last_name" name="cloth_qrcodes_vcard_last_name" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_last_name', true)); ?>" style="width:100%;"/>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; margin-bottom:15px;">
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_mobile"><?php _e('Mobile Phone', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_mobile" name="cloth_qrcodes_vcard_mobile" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_mobile', true)); ?>" style="width:100%;"/>
            </div>
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_phone"><?php _e('Work Phone', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_phone" name="cloth_qrcodes_vcard_phone" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_phone', true)); ?>" style="width:100%;"/>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; margin-bottom:15px;">
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_email"><?php _e('Email', 'cloth-qrcode'); ?></label>
                <input type="email" id="cloth_qrcodes_vcard_email" name="cloth_qrcodes_vcard_email" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_email', true)); ?>" style="width:100%;"/>
            </div>
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_website"><?php _e('Website', 'cloth-qrcode'); ?></label>
                <input type="url" id="cloth_qrcodes_vcard_website" name="cloth_qrcodes_vcard_website" value="<?= esc_url(get_post_meta($post->ID, 'cloth_qrcodes_vcard_website', true)); ?>" style="width:100%;"/>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; margin-bottom:15px;">
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_organization"><?php _e('Company / Organization', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_organization" name="cloth_qrcodes_vcard_organization" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_organization', true)); ?>"
                       style="width:100%;"/>
            </div>
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_job_title"><?php _e('Job Title', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_job_title" name="cloth_qrcodes_vcard_job_title" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_job_title', true)); ?>" style="width:100%;"/>
            </div>
        </div>

        <div style="margin-bottom:15px;">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_street"><?php _e('Street Address', 'cloth-qrcode'); ?></label>
            <input type="text" id="cloth_qrcodes_vcard_street" name="cloth_qrcodes_vcard_street" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_street', true)); ?>" style="width:100%;"/>
        </div>

        <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 15px; margin-bottom:15px;">
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_postal_code"><?php _e('Postal Code', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_postal_code" name="cloth_qrcodes_vcard_postal_code" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_postal_code', true)); ?>"
                       style="width:100%;"/>
            </div>
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_city"><?php _e('City', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_city" name="cloth_qrcodes_vcard_city" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_city', true)); ?>" style="width:100%;"/>
            </div>
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_region"><?php _e('Region / State', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_region" name="cloth_qrcodes_vcard_region" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_region', true)); ?>" style="width:100%;"/>
            </div>
            <div>
                <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_country"><?php _e('Country', 'cloth-qrcode'); ?></label>
                <input type="text" id="cloth_qrcodes_vcard_country" name="cloth_qrcodes_vcard_country" value="<?= esc_attr(get_post_meta($post->ID, 'cloth_qrcodes_vcard_country', true)); ?>" style="width:100%;"/>
            </div>
        </div>

        <div style="margin-bottom:15px;">
            <label style="font-weight: bold; margin-bottom: 10px; display:block;" for="cloth_qrcodes_vcard_note"><?php _e('Note', 'cloth-qrcode'); ?></label>
            <textarea id="cloth_qrcodes_vcard_note" name="cloth_qrcodes_vcard_note" style="width:100%; min-height:80px;"><?= esc_textarea(get_post_meta($post->ID, 'cloth_qrcodes_vcard_note', true)); ?></textarea>
        </div>
    </div>
<?php endif; ?>
