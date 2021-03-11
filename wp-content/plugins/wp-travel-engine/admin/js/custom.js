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

(function () {
  var accountsTable = null;
  document.addEventListener('click', function (e) {
    if (e.target.className.indexOf('wpte-add-account') > -1) {
      accountsTable = accountsTable || document.getElementById('wte-bank-transfer-accounts-table');
      var tr = document.createElement('tr');
      var tbody = accountsTable && accountsTable.querySelector('tbody');
      var trCount = tbody && tbody.querySelectorAll('tr') && tbody.querySelectorAll('tr').length;
      var index = trCount || 0;
      var tds = "<td></td>\n            <td>\n                <input type=\"text\" name=\"wp_travel_engine_settings[bank_transfer][accounts][".concat(index, "][account_name]\"/>\n            </td>\n            <td>\n                <input type=\"text\" name=\"wp_travel_engine_settings[bank_transfer][accounts][").concat(index, "][account_number]\"/>\n            </td>\n            <td>\n                <input type=\"text\" name=\"wp_travel_engine_settings[bank_transfer][accounts][").concat(index, "][bank_name]\"/>\n            </td>\n            <td>\n                <input type=\"text\" name=\"wp_travel_engine_settings[bank_transfer][accounts][").concat(index, "][sort_code]\"/>\n            </td>\n            <td>\n                <input type=\"text\" name=\"wp_travel_engine_settings[bank_transfer][accounts][").concat(index, "][iban]\"/>\n            </td>\n            <td>\n                <input type=\"text\" name=\"wp_travel_engine_settings[bank_transfer][accounts][").concat(index, "][swift]\"/>\n            </td>\n            <td><button class=\"wpte-btn wpte-danger wpte-remove-account\">X</button></td>");
      tr.innerHTML = tds;
      tbody && tbody.appendChild(tr);
    }

    if (e.target.className.indexOf('wpte-remove-account') > -1) {
      var etr = e.target.closest('tr');
      etr && etr.remove();
    }
  });
})();
