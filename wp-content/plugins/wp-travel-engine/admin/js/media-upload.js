jQuery(document).ready(function ($) {
  $('#logo-upload-btn').on('click', function (e) {
    e.preventDefault();
    var image = wp.media({
      title: 'Upload Image',
      // mutiple: true if you want to upload multiple files at once
      multiple: false
    }).open()
      .on('select', function (e) {
        // This will return the selected image from the Media Uploader, the result is an object
        var uploaded_image = image.state().get('selection').first();
        // We convert uploaded_image to a JSON object to make accessing it easier
        // Output to the console uploaded_image
        var image_url = uploaded_image.toJSON().url;
        // Let's assign the url value to the input field
        $('#email-logo').val(image_url);
      });
  });
  $('#logo-remove-btn').on('click', function (e) {
    e.preventDefault();
    $('#email-logo').val('');
  });

  $('.map-img-upload #upload-btn1').on('click', function (e) {
    e.preventDefault();
    var image = wp.media({
      title: 'Upload Image',
      // mutiple: true if you want to upload multiple files at once
      multiple: false
    }).open()
      .on('select', function (e) {
        // This will return the selected image from the Media Uploader, the result is an object
        var uploaded_image = image.state().get('selection').first();
        // We convert uploaded_image to a JSON object to make accessing it easier
        // Output to the console uploaded_image
        var image_id = uploaded_image.toJSON().id;
        var image_url = uploaded_image.toJSON().url;
        // Let's assign the url value to the input field
        $('.map-img-upload .preview img').attr('src', image_url);
        $('.map-img-upload #image_url').val(image_id);
        $('.map-img-upload #upload-btn1').hide();
        $('.map-img-upload #remove-btn').show();
      });
  });
  $('.map-img-upload #remove-btn').on('click', function (e) {
    e.preventDefault();
    $('.map-img-upload .preview img').attr('src', '');
    $('.map-img-upload #image_url').val('');
    $(this).hide();
    $('.map-img-upload #upload-btn1').show();
  });
});
