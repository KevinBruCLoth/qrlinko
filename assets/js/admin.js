addEventListener('DOMContentLoaded', () => {
    const ajaxConfig = window.plugin_ajax_object || {};
    const ajaxUrl = ajaxConfig.ajax_url || '';
    const ajaxNonce = ajaxConfig.nonce || '';
    const modeSelect = document.getElementById('cloth_qrcodes_mode');
    let mode = '';

    if (!modeSelect) {
        return;
    }

    const containers = {
        regular: document.getElementById('cloth_qrcodes_regular_container'),
        campaign: document.getElementById('cloth_qrcodes_campaign_container'),
        payment: document.getElementById('cloth_qrcodes_payment_container'),
        maps: document.getElementById('cloth_qrcodes_maps_container'),
        wifi: document.getElementById('cloth_qrcodes_wifi_container'),
        vcard: document.getElementById('cloth_qrcodes_vcard_container'),
        limit: document.getElementById('cloth_qrcodes_limit_container'),
    };

    function toggleContainer(container, display) {
        if (container) {
            container.style.display = display;
        }
    }

    function setRequired(container, isRequired) {
        if (!container) {
            return;
        }

        container.querySelectorAll('[data-required="required"]').forEach((field) => {
            field.required = isRequired;
        });
    }

    function resetRequiredFields() {
        Object.values(containers).forEach((container) => setRequired(container, false));
    }

    function showMode(mode) {
        Object.values(containers).forEach((container) => toggleContainer(container, 'none'));
        resetRequiredFields();

        if (containers[mode]) {
            toggleContainer(containers[mode], 'block');
            setRequired(containers[mode], true);
        }
    }

    modeSelect.addEventListener('change', function () {
        showMode(this.value);

        if(mode === 'campaign' && this.value !== 'campaign'){
            const rows = document.querySelectorAll('.cloth-qrcode-remove-campaign-row');
            if(rows.length) removeRow(rows);
        }

        if (this.value === 'campaign') {
            const tableBodyCampaign = document.querySelector('#cloth-qrcode-campaign-table tbody');
            const addCampaignRow = document.querySelector('#cloth-qrcode-add-campaign-row');

            if (tableBodyCampaign && addCampaignRow && !tableBodyCampaign.children.length) {
                addCampaignRow.click();
            }
        }
    });

    function removeRow(rows){
        rows.forEach(function (row) {
            console.log('row',row);
            row.click();
        })
    }


    showMode(modeSelect.value);

    /**********************************************************************
     * REGULAR QRCODE LOGIC
     **********************************************************************/
    const regularContainer = containers.regular;
    const tableBody = document.querySelector('#cloth-qrcode-params-table tbody');
    const addParamsRegularBtn = document.querySelector('#cloth-qrcode-add-row-params');

    if (regularContainer) {
        const queryParamRadios = document.querySelectorAll('input[name="cloth_qrcodes_url_params_type"]');
        const paramsBlock = document.getElementById('cloth-qrcode-params-block');

        queryParamRadios.forEach((queryParamRadio) => {
            queryParamRadio.addEventListener('change', function () {
                if (paramsBlock) {
                    paramsBlock.style.display = this.value !== 'none' ? 'block' : 'none';
                }
            });
        });

        if (addParamsRegularBtn && tableBody) {
            addParamsRegularBtn.addEventListener('click', async () => {
                const row = document.createElement('tr');
                const response = await ajaxCall({
                    action: 'get_template_row_regular_params',
                    index: Date.now(),
                });

                if (response?.success && response.data?.row) {
                    row.innerHTML = response.data.row;
                    tableBody.appendChild(row);
                }
            });

            tableBody.addEventListener('click', (event) => {
                if (event.target.classList.contains('cloth-qrcode-remove-row')) {
                    event.target.closest('tr')?.remove();
                }
            });
        }
    }

    function toggleLinkContainers(event) {
        const container = event.target.closest('[data-element="container"]');

        if (!container) {
            return;
        }

        const linkTypeSelect = container.querySelector('#cloth_qrcodes_link_type');
        const externalLinkContainer = container.querySelector('#cloth_qrcodes_external_link_container');
        const internalLinkContainer = container.querySelector('#cloth_qrcodes_internal_link_container');

        if (linkTypeSelect && externalLinkContainer && internalLinkContainer) {
            externalLinkContainer.style.display = linkTypeSelect.value === 'external' ? 'block' : 'none';
            internalLinkContainer.style.display = linkTypeSelect.value === 'internal' ? 'block' : 'none';
        }

        const fallbackLinkTypeSelect = container.querySelector('#cloth_qrcodes_fallback_link_type');
        const externalFallbackLinkContainer = container.querySelector('#cloth_qrcodes_external_fallback_link_container');
        const internalFallbackLinkContainer = container.querySelector('#cloth_qrcodes_internal_fallback_link_container');

        if (fallbackLinkTypeSelect && externalFallbackLinkContainer && internalFallbackLinkContainer) {
            externalFallbackLinkContainer.style.display = fallbackLinkTypeSelect.value === 'external' ? 'block' : 'none';
            internalFallbackLinkContainer.style.display = fallbackLinkTypeSelect.value === 'internal' ? 'block' : 'none';
        }
    }

    document.querySelectorAll('#cloth_qrcodes_link_type, #cloth_qrcodes_fallback_link_type').forEach((select) => {
        select.addEventListener('change', toggleLinkContainers);
        toggleLinkContainers({ target: select });
    });

    /**********************************************************************
     * LIMIT SCAN QRCODE LOGIC
     **********************************************************************/
    const tableBodyLimit = document.querySelector('#cloth-qrcode-limit-table tbody');
    const limitRows = document.querySelectorAll('#cloth-qrcode-limit-table tbody tr');
    const addLimitRow = document.querySelector('#cloth-qrcode-add-limit-row');

    if (addLimitRow && tableBodyLimit) {
        addLimitRow.addEventListener('click', async () => {
            const postId = document.getElementById('post_ID')?.value || 0;
            const response = await ajaxCall({
                action: 'get_template_row_limit',
                post_id: postId,
                index: Date.now(),
            });

            if (response?.success && response.data?.row) {
                const newRow = document.createElement('tr');
                newRow.id = String(Date.now());
                newRow.draggable = true;
                newRow.innerHTML = response.data.row;
                displayLimitLinkType(newRow);
                tableBodyLimit.appendChild(newRow);
            }
        });

        limitRows.forEach((limitRow) => displayLimitLinkType(limitRow));

        document.querySelector('#cloth-qrcode-limit-table')?.addEventListener('click', (event) => {
            if (event.target.classList.contains('cloth-qrcode-remove-limit-row')) {
                event.target.closest('tr')?.remove();
            }
        });

        initLimitDragAndDrop(tableBodyLimit);
    }

    function displayLimitLinkType(limitRow) {
        const trigger = limitRow.querySelector('.cloth-qrcode-limit-link-type');

        if (!trigger) {
            return;
        }

        trigger.addEventListener('change', () => {
            const externalLink = limitRow.querySelector('.cloth-qrcode-limit-external-link');
            const internalLink = limitRow.querySelector('.cloth-qrcode-limit-internal-link');

            if (!externalLink || !internalLink) {
                return;
            }

            const isInternal = trigger.value === 'internal';
            externalLink.style.display = isInternal ? 'none' : 'block';
            externalLink.required = !isInternal;
            internalLink.style.display = isInternal ? 'block' : 'none';
            internalLink.required = isInternal;
        });
    }

    function initLimitDragAndDrop(tbody) {
        let draggedRow = null;

        tbody.addEventListener('dragstart', (event) => {
            if (event.target.tagName === 'TR') {
                draggedRow = event.target;
                event.target.classList.add('dragging');
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/html', event.target.innerHTML);
            }
        });

        tbody.addEventListener('dragend', (event) => {
            if (event.target.tagName === 'TR') {
                event.target.classList.remove('dragging');
            }
        });

        tbody.addEventListener('dragover', (event) => {
            event.preventDefault();
            const targetRow = getClosestRow(event.target, tbody);

            if (!targetRow || targetRow === draggedRow) {
                return;
            }

            const rect = targetRow.getBoundingClientRect();
            const beforeTarget = (event.clientY - rect.top) <= (rect.height / 2);
            tbody.insertBefore(draggedRow, beforeTarget ? targetRow : targetRow.nextElementSibling);
        });

        tbody.addEventListener('drop', (event) => {
            event.preventDefault();
            updateRowIndices(tbody);
        });
    }

    function getClosestRow(element, tbody) {
        while (element && element !== tbody) {
            if (element.tagName === 'TR') {
                return element;
            }
            element = element.parentElement;
        }
        return null;
    }

    function updateRowIndices(tbody) {
        tbody.querySelectorAll('tr').forEach((row, index) => {
            row.setAttribute('data-index', index);
        });
    }

    /**********************************************************************
     * CAMPAIGN QRCODE LOGIC
     **********************************************************************/
    const tableBodyCampaign = document.querySelector('#cloth-qrcode-campaign-table tbody');
    const campaignRows = document.querySelectorAll('#cloth-qrcode-campaign-table tbody tr');
    const addCampaignRow = document.querySelector('#cloth-qrcode-add-campaign-row');

    if (addCampaignRow && tableBodyCampaign) {
        addCampaignRow.addEventListener('click', async () => {
            const postId = document.getElementById('post_ID')?.value || 0;
            const response = await ajaxCall({
                action: 'get_template_row_campaign',
                post_id: postId,
                index: Date.now(),
            });

            if (response?.success && response.data?.row) {
                const newRow = document.createElement('tr');
                newRow.id = String(Date.now());
                newRow.innerHTML = response.data.row;
                displayCampaignLinkType(newRow);
                tableBodyCampaign.appendChild(newRow);
                initDatePickers();
            }
        });

        campaignRows.forEach((campaignRow) => displayCampaignLinkType(campaignRow));

        document.querySelector('#cloth-qrcode-campaign-table')?.addEventListener('click', (event) => {
            if (event.target.classList.contains('cloth-qrcode-remove-campaign-row')) {
                event.target.closest('tr')?.remove();
            }
        });
    }

    function displayCampaignLinkType(campaignRow) {
        const trigger = campaignRow.querySelector('.cloth-qrcode-campaign-link-type');

        if (!trigger) {
            return;
        }

        trigger.addEventListener('change', () => {
            const externalLink = campaignRow.querySelector('.cloth-qrcode-campaign-external-link');
            const internalLink = campaignRow.querySelector('.cloth-qrcode-campaign-internal-link');

            if (!externalLink || !internalLink) {
                return;
            }

            const isInternal = trigger.value === 'internal';
            externalLink.style.display = isInternal ? 'none' : 'block';
            externalLink.required = !isInternal;
            internalLink.style.display = isInternal ? 'block' : 'none';
            internalLink.required = isInternal;
        });
    }

    /**********************************************************************
     * MAPS QRCODE LOGIC
     **********************************************************************/
    const inputTypeRadios = document.querySelectorAll('input[name="cloth_qrcodes_maps_input_type"]');
    const coordinatesContainer = document.getElementById('cloth_qrcodes_maps_coordinates_container');
    const addressContainer = document.getElementById('cloth_qrcodes_maps_address_container');

    inputTypeRadios.forEach((radio) => {
        radio.addEventListener('change', function () {
            const useCoordinates = this.value === 'coordinates';

            if (coordinatesContainer) {
                coordinatesContainer.style.display = useCoordinates ? 'block' : 'none';
            }

            if (addressContainer) {
                addressContainer.style.display = useCoordinates ? 'none' : 'block';
            }
        });
    });

    /**********************************************************************
     * GLOBAL INIT DATE PICKER
     **********************************************************************/
    function initDatePickers() {
        jQuery(document).ready(($) => {
            $('.cloth-qrcode-datetimepicker').datetimepicker({
                dateFormat: 'dd-mm-yy',
                timeFormat: 'HH:mm:ss',
                stepMinute: 1,
                stepSecond: 1,
            });
        });
    }

    initDatePickers();


    /**********************************************************************
     * COPY QR URL
     **********************************************************************/
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('.cloth-qrcode-copy-url');

        if (!button) {
            return;
        }

        const targetId = button.getAttribute('data-copy-target');
        const field = targetId ? document.getElementById(targetId) : null;

        if (!field) {
            return;
        }

        const value = field.value || '';
        const originalText = button.textContent;

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(value);
            } else {
                field.focus();
                field.select();
                document.execCommand('copy');
            }

            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 1500);
        } catch (error) {
            field.focus();
            field.select();
        }
    });

    /**********************************************************************
     * GLOBAL AJAX METHOD
     **********************************************************************/
    async function ajaxCall(data) {
        const form = new FormData();

        Object.entries(data).forEach(([key, value]) => {
            form.append(key, value);
        });

        form.append('nonce', ajaxNonce);

        const params = new URLSearchParams(form);

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: new Headers({
                    'Content-Type': 'application/x-www-form-urlencoded',
                    Accept: 'application/json',
                }),
                body: params,
            });

            if (response.ok) {
                return response.json();
            }
        } catch (error) {
            return null;
        }

        return null;
    }
}, false);
