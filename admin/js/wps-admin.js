//code for datatable

jQuery(document).ready(function() {

    jQuery('#mfw-datatable').DataTable({
        stateSave: true,
        dom: '<"wps-dt-buttons"fB>tr<"bottom"lip>',
        "ordering": true, // enable ordering
   
        
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
        ],
        language: {
            "lengthMenu": 'Rows per page _MENU_',

            paginate: { next: '<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.99984 0L0.589844 1.41L5.16984 6L0.589844 10.59L1.99984 12L7.99984 6L1.99984 0Z" fill="#8E908F"/></svg>', previous: '<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.00016 12L7.41016 10.59L2.83016 6L7.41016 1.41L6.00016 -1.23266e-07L0.000156927 6L6.00016 12Z" fill="#8E908F"/></svg>' }
        },
    });

    // API details js.
    jQuery(document).find('.wps_msfw_rest_api_response').hide();
    jQuery('.wps_msfw_rest_api_response').first().show();
    jQuery(document).on('click', '.wps_msfw_parent_api_details_wrapper h4', function(){
        
        jQuery(this).next().slideToggle();
    });
});