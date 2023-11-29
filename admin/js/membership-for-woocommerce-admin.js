(function($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

jQuery(document).ready(function($) {

   
    jQuery('.wps-membership__plan--pro-disabled').on('click', function(){
	    $( '.wps_ubo_lite_go_pro_popup_wrap' ).addClass( 'wps_ubo_lite_go_pro_popup_show' );
    });

    $('.wps_ubo_lite_go_pro_popup_close').on( 'click', function (e) {

        // Hide Go pro popup.
        e.preventDefault();
        $( '.wps_ubo_lite_go_pro_popup_wrap' ).removeClass('wps_ubo_lite_go_pro_popup_show' );
        $( 'body' ).removeClass( 'wps_ubo_lite_go_pro_popup_body' );
    });
    $('body').click
    (
      function(e)
      { 
        if( e.target.className == 'wps_ubo_lite_go_pro_popup_wrap wps_ubo_lite_go_pro_popup_show' )
        {   
            $( '.wps_ubo_lite_go_pro_popup_wrap' ).removeClass( 'wps_ubo_lite_go_pro_popup_show' );
            $( 'body' ).removeClass( 'wps_ubo_lite_go_pro_popup_body' );
        }
      }
    );

    jQuery('#preview-action').hide();
    jQuery('#edit-slug-box').hide();
    jQuery('#message').hide();

     // Remove image button.
  $(document).on('change', '#wps_membership_plan_duration_type', function(e) {
    e.preventDefault();

    var duration_type = jQuery('#wps_membership_plan_duration_type option:selected').val();
    var duration_text = jQuery('#wps_membership_plan_duration_type option:selected').text();
    duration_type =  duration_type.substring(0,duration_type.length - 1)
    jQuery('#wps_membership_subscription_expiry_type option:selected').val(duration_type);
    jQuery('#wps_membership_subscription_expiry_type option:selected').text(duration_text);

    });
    $(document).on('blur', '#wps_membership_subscription_expiry', function(e) {
     e.preventDefault();

     var duration = jQuery('#wps_membership_plan_duration').val();
     var subscription = jQuery('#wps_membership_subscription_expiry').val();
     if ( parseInt(duration)  > parseInt(subscription) ) {
        alert('Please enter subscription expiry value greater or equal to duration ');
        var subscription = jQuery('#wps_membership_subscription_expiry').val('');
     }
    });

    $(document).on('blur', '#wps_membership_plan_duration', function(e) {
        e.preventDefault();
   
        var duration = jQuery('#wps_membership_plan_duration').val();
        var subscription = jQuery('#wps_membership_subscription_expiry').val();
        if ( parseInt(duration)  > parseInt(subscription) ) {
           var subscription = jQuery('#wps_membership_subscription_expiry').val('');
        }
    });

    $(document).on('click','.media-button',function() {
        jQuery('.media-modal-close').trigger('click');
      });
    
  // Avoid negative values for amount/discount and convert it to zero.
  $('input[name="wps_membership_plan_price"]').keyup(function() {

      if ($(this).val() < 0) {
          $(this).val(0);
      }
  });

  $('input[name="wps_memebership_plan_discount_price"]').keyup(function() {

      if ($(this).val() < 0) {
          $(this).val(0);
      }
  });

  // Display already selected option field.
  function selected() {

      var selection_access = $("#wps_membership_plan_access_type  option:selected").val();

      var selection_radio = $("input[name='wps_membership_plan_access_type']:checked").val();

      var shipping_value = $("input[name='wps_memebership_plan_free_shipping']:checked").val();

      var show_notice = $("input[name='wps_membership_show_notice']:checked").val();

      var attch_inv = $( "input[name='wps_membership_attach_invoice']:checked" ).val();

      switch (selection_access) {
        
          case 'limited':
              $("#wps_membership_duration").show('500');
              $("#wps_membership_subscription_tr").show('500');
              $("#wps_membership_subscription_expiry_tr").show('500');
              
              $("#wps_membership_recurring_plan").show('500');
              break;

          default:
              $("#wps_membership_duration").hide('500');
              $("#wps_membership_subscription_tr").hide('500');
              $("#wps_membership_recurring_plan").hide('500');
              $("#wps_membership_subscription_expiry_tr").hide('500');

      }

      switch (selection_radio) {

          case 'immediate_type':
              $("#wps_membership_plan_time_duratin_display").hide('500');
              break;

          case 'delay_type':
              $("#wps_membership_plan_time_duratin_display").show('500');
      }

      if ('yes' == shipping_value) {
          $(".wps_membership_free_shipping_link").show('500');
      } else {
          $(".wps_membership_free_shipping_link").hide('500');
      }

      if ('yes' == show_notice) {
          $(".wps_membership_notice_message").show('500');
      } else {
          $(".wps_membership_notice_message").hide('500');
      }

      if ( 'yes' == attch_inv ) {
          $('.mfw_membership_invoice_pdf').show();
      } else {
          $('.mfw_membership_invoice_pdf').hide();
      }
  }

  selected(); // calling the function when the page is ready.

  // Display access type form fields as per user seletcion.
  $("#wps_membership_plan_access_type").on('change', function() {
      var selection = $(this).val();

      switch (selection) {

          case 'limited':
              $("#wps_membership_duration").show('500');
              $("#wps_membership_subscription_tr").show('500');
              $("#wps_membership_subscription_expiry_tr").show('500');

              $("#wps_membership_plan_duration_type").on("change", function() {
                  var duration_type = $("#wps_membership_plan_duration_type").val();
                  if ('days' == duration_type) {
                      $("#wps_membership_plan_duration_type").attr({ min: 1, max: 31 })
                  } else {
                      $("#wps_membership_plan_duration_type").removeAttr("min");
                      $("#wps_membership_plan_duration_type").removeAttr("max");
                  }
              });

              $("#wps_membership_recurring_plan").show('500');
              break;

          default:
              $("#wps_membership_duration").hide('500');
              $("#wps_membership_subscription_tr").hide('500');
              $("#wps_membership_subscription_expiry_tr").hide('500');
              $("#wps_membership_plan_duration").val("");
              $("#wps_membership_plan_duration_type").prop("selectedIndex", 0);
              $("#wps_membership_recurring_plan").hide('500');

      }
  });
  
  $("#wps_membership_plan_immediate_type").on("click", function() {
      $("#wps_membership_plan_time_duratin_display").hide('500');
      $("#wps_membership_plan_time_duration").val("");
      $("#wps_membership_plan_time_duration_type").prop("selectedIndex", 0);
  }); 
  $("#wps_membership_plan_time_type").on("click", function() {
      $("#wps_membership_plan_time_duratin_display").show('500');

  }); 


  // Display free shipping link as per user selection.
  $("input[name='wps_memebership_plan_free_shipping']").on("change", function() {

      if ($(this).is(":checked")) {

          $(".wps_membership_free_shipping_link").show('500');

      } else {

          $(".wps_membership_free_shipping_link").hide('500');
      }
  });


  // Import CSV modal.
  $("#import_all_membership").on("click", function(e) {
      e.preventDefault();

      $(".import_csv_field_wrapper").dialog("open");

      // Ajax call for import CSV.
      $("#upload_csv_file").on("click", function(e) {
          e.preventDefault();

          var empty_check = $("#wps_membership_csv_file_upload").val();

          // If no file selected close the dialog box and show 'failure' sweet alert.
          if (empty_check.length == 0) {

              // CLose the import modal.
              $(".import_csv_field_wrapper").dialog("close");

              // Show "failure" response via sweet-alert.
              Swal.fire({
                  icon: 'error',
                  title: 'Oops..!!',
                  text: 'No file selected',
              });

          } else {

              var form = new FormData();
              var file = $(document).find("#wps_membership_csv_file_upload");

              var single_file = file[0].files[0];

              form.append("file", single_file);
              form.append("action", "wps_membership_csv_file_upload");
              form.append("nonce", admin_ajax_obj.nonce)

              $.ajax({
                  url: admin_ajax_obj.ajaxurl,
                  type: "POST",
                  data: form,
                  dataType: 'json',
                  contentType: false,
                  processData: false,

                  success: function(response) {
                    

                      // Close the import modal.
                      $(".import_csv_field_wrapper").dialog("close");

                      if ('success' == response['status']) {

                          // Show "success" response via sweet-alert.
                          Swal.fire({
                              icon: 'success',
                              title: response['message'],
                          });

                          // Reload page after click on ok in
                          $(".swal2-confirm").on("click", function() {
                              window.location.href = response['redirect'];
                          });
                      } else if ('failed' == response['status']) {

                          // Show "failure" response via sweet-alert.
                          Swal.fire({
                              icon: 'error',
                              title: 'Oops..!!',
                              text: response['message']
                          });
                      }
                  },

              });
          }

      });
  });


  
 

  // Applying script to admin part in all product list page
  var wps_status = $('.membership_status').each(function() {

      if ($(this).text() == 'Live') {
          $(this).css({ 'background-color': '#c6e1c6', 'color': '#5b841b' });
      } else if ($(this).text() == 'Sandbox') {
          $(this).css({ 'background-color': '#f8dda7', 'color': '#94660c' });
      }

  });


  // Image uploader in global settings email log.
  $('#upload_img').on( 'click', function(e) {
      e.preventDefault();

      if ( jQuery('.media-modal-content').length == 0) {
            
     var media_modals = wp.media.frames.media_modal = wp.media({
          title: 'Upload a logo.',
          button: { text: 'Select' },
          library: { type: 'image' },
      });

      media_modals.on('select', function() {

          var attachment = media_modals.state().get('selection').first().toJSON();
          var img = attachment.sizes.thumbnail || attachment.sizes.medium || attachment.sizes.full;
          $('#wps_membership_invoice_logo').val(attachment.url);
          $('#img_thumbnail').find('img').attr('src', img.url);
          $('#img_thumbnail').show(500);
          $('#upload_img').addClass('button_hide');
          $('#remove_img').removeClass('button_hide');
      });

      media_modals.open();
    }
  });

  // Remove image button.
  $(document).on('click', '#remove_img', function(e) {
      e.preventDefault();
      $('#wps_membership_invoice_logo').val('');
      $('#img_thumbnail').hide(500);
      $('#img_thumbnail').find('img').attr('src', '');
      $('#upload_img').removeClass('button_hide');
      $('#remove_img').addClass('button_hide');
  });
 
  // Add default plan title.
  var post_title = $('input[name="post_title"]').val();
  var post_id = $('input[name="post_ID"]').val();

  if (!post_title) {
      $('input[name="post_title"]').val( admin_ajax_obj.Plan + '#' + post_id);
  }

  // Display warning if plan title field is empty.
  $('input[name="post_title"]').on('keyup', function() {
      var post_title = $('input[name="post_title"]').val();
     var title_warning = jQuery('.title_warning').html()

      if (!post_title) {
          if ( title_warning == undefined ) {
            var title_msg = '<span class="title_warning">*'+ admin_ajax_obj.Plan_warning +'</span>';
            $('div#titlewrap').append(title_msg);
          }
         
      }
  });

 // overview buttons animation
 $('.mfw_overview-contact').children('a').on('mouseenter', function(){
  $(this).children('span').css({'width':'150px','opacity':'1'});
}).on('mouseleave',function(){
  $(this).children('span').css({'width':'0','opacity':'0'});
});




jQuery(".import_csv_field_wrapper").dialog({
  modal: true,
  autoOpen: false,
  show: { effect: "blind", duration: 800 },
  width: 600
});



});



$(document).ready(function() {
    const MDCText = mdc.textField.MDCTextField;
    const textField = [].map.call(
      document.querySelectorAll(".mdc-text-field"),
      function(el) {
        return new MDCText(el);
      }
    );
    const MDCRipple = mdc.ripple.MDCRipple;
    const buttonRipple = [].map.call(
      document.querySelectorAll(".mdc-button"),
      function(el) {
        return new MDCRipple(el);
      }
    );
    const MDCSwitch = mdc.switchControl.MDCSwitch;
    const switchControl = [].map.call(
      document.querySelectorAll(".mdc-switch"),
      function(el) {
        return new MDCSwitch(el);
      }
    );

  
   
    $(document).on('click','.wps-password-hidden',function() {
      if ($(".wps-form__password").attr("type") == "text") {
        $(".wps-form__password").attr("type", "password");
      } else {
        $(".wps-form__password").attr("type", "text");
      }
    });
  });

  $(window).load(function() {
    // add select2 for multiselect.
    if ($(document).find(".wps-defaut-multiselect").length > 0) {
      $(document)
        .find(".wps-defaut-multiselect")
        .select2();
    }
    

    
  });

  

})(jQuery);
// License.

jQuery( document ).ready(
    function($){
        $( document ).on(
            'click',
            '#dismiss-banner',
            function(e){
                e.preventDefault();
                var data = {
                    action:'wps_mfw_dismiss_notice_banner',
                    wps_nonce:admin_ajax_obj.nonce
                };
                $.ajax(
                    {
                        url: admin_ajax_obj.ajaxurl,
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
    }
 );
 