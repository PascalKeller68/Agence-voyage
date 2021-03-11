(() => {
    let accountsTable = null
    document.addEventListener('click', e => {
        if (e.target.className.indexOf('wpte-add-account') > -1) {
            accountsTable = accountsTable || document.getElementById('wte-bank-transfer-accounts-table')
            let tr = document.createElement('tr')
            let tbody = accountsTable && accountsTable.querySelector('tbody')
            let trCount = tbody && tbody.querySelectorAll('tr') && tbody.querySelectorAll('tr').length
            let index = trCount || 0
            let tds = `<td></td>
            <td>
                <input type="text" name="wp_travel_engine_settings[bank_transfer][accounts][${index}][account_name]"/>
            </td>
            <td>
                <input type="text" name="wp_travel_engine_settings[bank_transfer][accounts][${index}][account_number]"/>
            </td>
            <td>
                <input type="text" name="wp_travel_engine_settings[bank_transfer][accounts][${index}][bank_name]"/>
            </td>
            <td>
                <input type="text" name="wp_travel_engine_settings[bank_transfer][accounts][${index}][sort_code]"/>
            </td>
            <td>
                <input type="text" name="wp_travel_engine_settings[bank_transfer][accounts][${index}][iban]"/>
            </td>
            <td>
                <input type="text" name="wp_travel_engine_settings[bank_transfer][accounts][${index}][swift]"/>
            </td>
            <td><button class="wpte-btn wpte-danger wpte-remove-account">X</button></td>`
            tr.innerHTML = tds;
            tbody && tbody.appendChild(tr)
        }

        if( e.target.className.indexOf('wpte-remove-account') > -1 ) {
            let etr = e.target.closest('tr')
            etr && etr.remove();
        }
    })
})()
