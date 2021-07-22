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
  $("#mwb_mfw_license_key").on("click", function(e) {
    $("#mwb_mfw_license_activation_status").html("");
  });

  $("form#mwb_mfw_license_form").on("submit", function(e) {
    e.preventDefault();

    //   $("#mwb_license_ajax_loader").show();
    var license_key = $("#mwb_mfw_license_key").val();
    mwb_mfw_send_license_request(license_key);
  });

  function mwb_mfw_send_license_request(license_key) {
    $.ajax({
      type: "POST",
      dataType: "JSON",
      url: mfw_admin_param.ajaxurl,
      data: {
        action: "mwb_membership_mfw_validate_license_key",
        purchase_code: license_key,
      },

      success: function(data) {
        //   $("#mwb_upsell_license_ajax_loader").hide();

        if (data.status == true) {
          $("#mwb_mfw_license_activation_status").css("color", "#42b72a");

          jQuery("#mwb_mfw_license_activation_status").html(data.msg);

          location = mfw_admin_param.mfw_admin_param_location;
        } else {
          $("#mwb_mfw_license_activation_status").css("color", "#ff3333");

          jQuery("#mwb_mfw_license_activation_status").html(data.msg);

          jQuery("#mwb_mfw_license_key").val("");
        }
      },
    });
  }
});


jQuery(document).ready(function($) {


  // Avoid negative values for amount/discount and convert it to zero.
  $('input[name="mwb_membership_plan_price"]').keyup(function() {

      if ($(this).val() < 0) {
          $(this).val(0);
      }
  });

  $('input[name="mwb_memebership_plan_discount_price"]').keyup(function() {

      if ($(this).val() < 0) {
          $(this).val(0);
      }
  });


  // Display already selected option field.
  function selected() {

      var selection_access = $("#mwb_membership_plan_access_type  option:selected").val();

      var selection_radio = $("input[name='mwb_membership_plan_access_type']:checked").val();

      var shipping_value = $("input[name='mwb_memebership_plan_free_shipping']:checked").val();

      var show_notice = $("input[name='mwb_membership_show_notice']:checked").val();

      var attch_inv = $( "input[name='mwb_membership_attach_invoice']:checked" ).val();

      switch (selection_access) {

          case 'limited':
              $("#mwb_membership_duration").show('500');
              $("#mwb_membership_recurring_plan").show('500');
              break;

          default:
              $("#mwb_membership_duration").hide('500');
              $("#mwb_membership_recurring_plan").hide('500');

      }

      switch (selection_radio) {

          case 'immediate_type':
              $("#mwb_membership_plan_time_duratin_display").hide('500');
              break;

          case 'delay_type':
              $("#mwb_membership_plan_time_duratin_display").show('500');
      }

      if ('yes' == shipping_value) {
          $(".mwb_membership_free_shipping_link").show('500');
      } else {
          $(".mwb_membership_free_shipping_link").hide('500');
      }

      if ('yes' == show_notice) {
          $(".mwb_membership_notice_message").show('500');
      } else {
          $(".mwb_membership_notice_message").hide('500');
      }

      if ( 'yes' == attch_inv ) {
          $('.mfw_membership_invoice_pdf').show();
      } else {
          $('.mfw_membership_invoice_pdf').hide();
      }
  }

  selected(); // calling the function when the page is ready.

  // Display access type form fields as per user seletcion.
  $("#mwb_membership_plan_access_type").on('change', function() {
      var selection = $(this).val();

      switch (selection) {

          case 'limited':
              $("#mwb_membership_duration").show('500');

              $("#mwb_membership_plan_duration_type").on("change", function() {
                  var duration_type = $("#mwb_membership_plan_duration_type").val();
                  if ('days' == duration_type) {
                      $("#mwb_membership_plan_duration_type").attr({ min: 1, max: 31 })
                  } else {
                      $("#mwb_membership_plan_duration_type").removeAttr("min");
                      $("#mwb_membership_plan_duration_type").removeAttr("max");
                  }
              });

              $("#mwb_membership_recurring_plan").show('500');
              break;

          default:
              $("#mwb_membership_duration").hide('500');
              $("#mwb_membership_plan_duration").val("");
              $("#mwb_membership_plan_duration_type").prop("selectedIndex", 0);
              $("#mwb_membership_recurring_plan").hide('500');

      }
  });
  
  $("#mwb_membership_plan_immediate_type").on("click", function() {
      $("#mwb_membership_plan_time_duratin_display").hide('500');
      $("#mwb_membership_plan_time_duration").val("");
      $("#mwb_membership_plan_time_duration_type").prop("selectedIndex", 0);
  }); 
  $("#mwb_membership_plan_time_type").on("click", function() {
      $("#mwb_membership_plan_time_duratin_display").show('500');

  }); 


  // Display free shipping link as per user selection.
  $("input[name='mwb_memebership_plan_free_shipping']").on("change", function() {

      if ($(this).is(":checked")) {

          $(".mwb_membership_free_shipping_link").show('500');

      } else {

          $(".mwb_membership_free_shipping_link").hide('500');
      }
  });


  // Import CSV modal.
  $("#import_all_membership").on("click", function(e) {
      e.preventDefault();

      $(".import_csv_field_wrapper").dialog("open");

      // Ajax call for import CSV.
      $("#upload_csv_file").on("click", function(e) {
          e.preventDefault();

          var empty_check = $("#mwb_membership_csv_file_upload").val();

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
              var file = $(document).find("#mwb_membership_csv_file_upload");

              var single_file = file[0].files[0];

              form.append("file", single_file);
              form.append("action", "mwb_membership_csv_file_upload");
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
  var mwb_status = $('.membership_status').each(function() {

      if ($(this).text() == 'Live') {
          $(this).css({ 'background-color': '#c6e1c6', 'color': '#5b841b' });
      } else if ($(this).text() == 'Sandbox') {
          $(this).css({ 'background-color': '#f8dda7', 'color': '#94660c' });
      }

  });

  // Image uploader in global settings email log.
  $('#upload_img').on( 'click', function(e) {
      e.preventDefault();

     var media_modals = wp.media.frames.media_modal = wp.media({
          title: 'Upload a logo.',
          button: { text: 'Select' },
          library: { type: 'image' },
      });

      media_modals.on('select', function() {

          var attachment = media_modals.state().get('selection').first().toJSON();
          var img = attachment.sizes.thumbnail || attachment.sizes.medium || attachment.sizes.full;
          $('#mwb_membership_invoice_logo').val(attachment.url);
          $('#img_thumbnail').find('img').attr('src', img.url);
          $('#img_thumbnail').show(500);
          $('#upload_img').addClass('button_hide');
          $('#remove_img').removeClass('button_hide');
      });

      media_modals.open();
  });

  // Remove image button.
  $(document).on('click', '#remove_img', function(e) {
      e.preventDefault();
      $('#mwb_membership_invoice_logo').val('');
      $('#img_thumbnail').hide(500);
      $('#img_thumbnail').find('img').attr('src', '');
      $('#upload_img').removeClass('button_hide');
      $('#remove_img').addClass('button_hide');
  });

 

  // Add default plan title.
  var post_title = $('input[name="post_title"]').val();
  var post_id = $('input[name="post_ID"]').val();

  if (!post_title) {
      $('input[name="post_title"]').val('Plan ' + '#' + post_id);
  }

  // Display warning if plan title field is empty.
  $('input[name="post_title"]').on('keyup', function() {
      var post_title = $('input[name="post_title"]').val();

      if (!post_title) {
          var title_msg = '<span class="title_warning">*Title field cant\'t be empty</span>';
          $('div#titlewrap').append(title_msg);
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

    $(".mwb-password-hidden").click(function() {
      if ($(".mwb-form__password").attr("type") == "text") {
        $(".mwb-form__password").attr("type", "password");
      } else {
        $(".mwb-form__password").attr("type", "text");
      }
    });
  });

  $(window).load(function() {
    // add select2 for multiselect.
    if ($(document).find(".mwb-defaut-multiselect").length > 0) {
      $(document)
        .find(".mwb-defaut-multiselect")
        .select2();
    }
  });
})(jQuery);
// License.