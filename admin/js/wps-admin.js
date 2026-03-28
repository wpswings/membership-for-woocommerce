// code for datatable and API reference accordion.

jQuery(document).ready(function($) {
    $('#mfw-datatable').DataTable({
        stateSave: true,
        dom: '<"wps-dt-buttons"fB>tr<"bottom"lip>',
        ordering: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
        ],
        language: {
            lengthMenu: 'Rows per page _MENU_',
            paginate: {
                next: '<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.99984 0L0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 0Z" fill="#8E908F"/></svg>',
                previous: '<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.00016 12L7.41016 10.59L2.83016 6L7.41016 1.41L6.00016 -1.23266e-07L0.000156927 6L6.00016 12Z" fill="#8E908F"/></svg>'
            }
        },
    });

    var $apiDetails = $('.wps_msfw_parent_api_details_wrapper');
    var $apiSections = $apiDetails.find('> .wps_msfw_rest_api_response');
    var $apiTriggers = $apiDetails.find('> h4');

    $apiSections.hide().first().show();
    $apiTriggers.removeClass('is-open').first().addClass('is-open');

    $(document).on('click', '.wps_msfw_parent_api_details_wrapper > h4', function() {
        var $trigger = $(this);
        var $panel = $trigger.next('.wps_msfw_rest_api_response');

        if (! $panel.length) {
            return;
        }

        if ($panel.is(':visible')) {
            $panel.stop(true, true).slideUp(180);
            $trigger.removeClass('is-open');
            return;
        }

        $apiSections.not($panel).stop(true, true).slideUp(180);
        $apiTriggers.not($trigger).removeClass('is-open');
        $panel.stop(true, true).slideDown(180);
        $trigger.addClass('is-open');
    });
});
