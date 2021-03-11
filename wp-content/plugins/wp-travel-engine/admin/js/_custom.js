jQuery(function ($) {
  $('.wp-travel-meta-datetime, .wp-travel-engine-datetime').datepicker({
    dateFormat: 'yy-mm-dd'
  });
  $(document).on('click', '.wte-clone-post', function (e) {
    e.preventDefault();
    var post_id = $(this).data('post_id');
    var security = $(this).data('security');
    var data = {
      post_id: post_id,
      security: security,
      action: 'wte_fxn_clone_trip_data'
    };
    $.ajax({
      url: ajaxurl,
      data: data,
      type: "post",
      dataType: "json",
      success: function success(data) {
        location.href = location.href;
      }
    });
  });
});
