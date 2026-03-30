jQuery(document).ready(function($) {

    if (window.mdc && mdc.textField && mdc.textField.MDCTextField) {
        const MDCText = mdc.textField.MDCTextField;
        [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
            return new MDCText(el);
        });
    }

    if (window.mdc && mdc.ripple && mdc.ripple.MDCRipple) {
        const MDCRipple = mdc.ripple.MDCRipple;
        [].map.call(document.querySelectorAll('.mdc-button'), function(el) {
            return new MDCRipple(el);
        });
    }

    if (window.mdc && mdc.switchControl && mdc.switchControl.MDCSwitch) {
        const MDCSwitch = mdc.switchControl.MDCSwitch;
        [].map.call(document.querySelectorAll('.mdc-switch'), function(el) {
            return new MDCSwitch(el);
        });
    }

    var wps_deactivation_skip_button_id = wps_mfw_onboarding.mfw_current_supported_slug[0] + "-no_thanks_deactive";
    var wps_onboarding_popup_id = wps_mfw_onboarding.mfw_current_supported_slug[0] + "-onboarding_popup";
    var onboarding_overlay = $('.wps-mfw-onboarding-overlay');


    var dialog = null;
    if (window.mdc && mdc.dialog && mdc.dialog.MDCDialog) {
        if ('plugins.php' == wps_mfw_onboarding.mfw_current_screen) {
            var deactivation_dialog = document.querySelector('#' + wps_onboarding_popup_id);

            if (deactivation_dialog) {
                dialog = mdc.dialog.MDCDialog.attachTo(deactivation_dialog);
            }
        } else {
            var onboarding_dialog = document.querySelector('.' + wps_mfw_onboarding.mfw_current_supported_slug[0]);

            if (onboarding_dialog && onboarding_dialog.classList.contains('mdc-dialog')) {
                dialog = mdc.dialog.MDCDialog.attachTo(onboarding_dialog);
            }
        }
    }

    /*if device is mobile*/
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        jQuery('body').addClass('mobile-device');
    }

    var deactivate_url = '';

    // Add Select2.
    jQuery('.on-boarding-select2').select2({
        placeholder: 'Select All Suitable Options...',
    });

    // On click of deactivate.
    if ('plugins.php' == wps_mfw_onboarding.mfw_current_screen) {

        // Add Deactivation id to all deactivation links.
        wps_mfw_embed_id_to_deactivation_urls();
        wps_mfw_add_deactivate_slugs_callback(wps_mfw_onboarding.mfw_current_supported_slug);

        jQuery(document).on('change', '.mfw-on-boarding-radio-field', function(e) {

            e.preventDefault();
            if ('other' == jQuery(this).attr('id')) {
                jQuery('#deactivation-reason-text').removeClass('wps-mfw-keep-hidden');
            } else {
                jQuery('#deactivation-reason-text').addClass('wps-mfw-keep-hidden');
            }
        });
    } else {
        // Show Popup after 1 second of entering into the WPS pagescreen.
        if (jQuery('#wps-mfw-show-counter').length > 0 && jQuery('#wps-mfw-show-counter').val() == 'not-sent') {
            setTimeout(wps_mfw_show_onboard_popup, 1000);
        }
    }

    /* Close Button Click */
    jQuery(document).on('click', '.wps-mfw-on-boarding-close-btn a, .wps-mfw-onboarding-close', function(e) {
        e.preventDefault();
        wps_mfw_hide_onboard_popup();
    });

    /* Skip and deactivate. */
    jQuery(document).on('click', '#' + wps_deactivation_skip_button_id, function(e) {

        window.location.replace(deactivate_url);
        wps_mfw_hide_onboard_popup();
    });

    /* Skip For a day. */
    jQuery(document).on('click', '.wps-mfw-on-boarding-no_thanks, .wps-mfw-onboarding-skip', function(e) {
        e.preventDefault();
        jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url: wps_mfw_onboarding.ajaxurl,
            data: {
                nonce: wps_mfw_onboarding.mfw_auth_nonce,
                action: 'mfw_skip_onboarding_popup',
            },
            success: function(msg) {
                wps_mfw_hide_onboard_popup();
            }
        });

    });

    /* Submitting Form */
    jQuery(document).on('submit', 'form.wps-mfw-on-boarding-form, form.wps-mfw-onboarding-form', function(e) {

        e.preventDefault();
        var form_data = JSON.stringify(jQuery(this).serializeArray());

        jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url: wps_mfw_onboarding.ajaxurl,
            data: {
                nonce: wps_mfw_onboarding.mfw_auth_nonce,
                action: 'wps_mfw_send_onboarding_data',
                form_data: form_data,
            },
            success: function(msg) {

                if ('plugins.php' == wps_mfw_onboarding.mfw_current_screen) {
                    window.location.replace(deactivate_url);
                }
                wps_mfw_hide_onboard_popup();
            }
        });
    });

    /* Open Popup */
    function wps_mfw_show_onboard_popup() {
        if (dialog && 'function' === typeof dialog.open) {
            dialog.open();
        }

        if (onboarding_overlay.length > 0) {
            onboarding_overlay.removeClass('wps-mfw-hidden').attr('aria-hidden', 'false');
        }

        if (!jQuery('body').hasClass('mobile-device')) {
            jQuery('body').addClass('wps-mfw-on-boarding-wrapper-control');
        }
    }

    /* Close Popup */
    function wps_mfw_hide_onboard_popup() {
        if (dialog && 'function' === typeof dialog.close) {
            dialog.close();
        }

        if (onboarding_overlay.length > 0) {
            onboarding_overlay.addClass('wps-mfw-hidden').attr('aria-hidden', 'true');
        }

        if (!jQuery('body').hasClass('mobile-device')) {
            jQuery('body').removeClass('wps-mfw-on-boarding-wrapper-control');
        }
    }



    /* Apply deactivate in all the WPS plugins. */
    function wps_mfw_add_deactivate_slugs_callback(all_slugs) {

        for (var i = all_slugs.length - 1; i >= 0; i--) {

            jQuery(document).on('click', '#deactivate-' + all_slugs[i], function(e) {

                e.preventDefault();
                deactivate_url = jQuery(this).attr('href');
                plugin_name = jQuery(this).attr('aria-label');
                plugin_name = plugin_name.replace('Deactivate ', '');
                jQuery('#plugin-name').val(plugin_name);
                jQuery('.wps-mfw-on-boarding-heading').text(plugin_name + ' Feedback');
                var placeholder = jQuery('#wps-mfw-deactivation-reason-text').attr('placeholder');
                jQuery('#wps-mfw-deactivation-reason-text').attr('placeholder', placeholder.replace('{plugin-name}', plugin_name));
                wps_mfw_show_onboard_popup();
            });
        }
    }

    /* Add deactivate id in all the plugins links. */
    function wps_mfw_embed_id_to_deactivation_urls() {
        jQuery('a').each(function() {
            if ('Deactivate' == jQuery(this).text() && 0 < jQuery(this).attr('href').search('action=deactivate')) {
                if ('undefined' == typeof jQuery(this).attr('id')) {
                    var slug = jQuery(this).closest('tr').attr('data-slug');
                    jQuery(this).attr('id', 'deactivate-' + slug);
                }
            }
        });
    }

    // End of scripts.
    // Dismiss banner notification.
    $( document ).on(
        'click',
        '#dismiss-banner',
        function(e){
            e.preventDefault();
            var data = {
                action:'wps_mfw_dismiss_notice_banner',
                wps_nonce:wps_mfw_onboarding.wps_nonce
            };
            $.ajax(
                {
                    url: wps_mfw_onboarding.ajaxurl,
                    type: "POST",
                    data: data,
                    success: function(response)
                    {
                        window.location.reload();
                    }
                }
            );
        }
    );
});
