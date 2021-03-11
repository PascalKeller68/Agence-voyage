(function () {
    const allInfos = document.querySelectorAll('.wpte-checkout-payment-info')
    const paymentMethodsRadio = document.querySelectorAll('[name=wpte_checkout_paymnet_method]')
    paymentMethodsRadio && paymentMethodsRadio.forEach(el => {
        el.checked && el.parentElement.classList.add('wpte-active-payment-method') && el.parentElement.querySelector('.wpte-checkout-payment-info').removeAttribute('style')
        el.addEventListener('change', e => {
            if (!!allInfos) {
                allInfos.forEach(el => {
                    el.style.display = 'none'
                    el.parentElement.classList.remove('wpte-active-payment-method')
                })
            }
            let parentEl = e.target.parentElement
            parentEl.classList.add('wpte-active-payment-method')
            let infoEl = e.target.parentElement.querySelector('.wpte-checkout-payment-info')
            infoEl && infoEl.removeAttribute('style')
        })
    })
})()
