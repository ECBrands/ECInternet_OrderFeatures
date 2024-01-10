var config = {
    map: {
        '*': {
            'Magento_Checkout/template/billing-address.html':
                'ECInternet_OrderFeatures/template/checkout/billing-address.html'
        }
    },

    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'ECInternet_OrderFeatures/js/action/place-order': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'ECInternet_OrderFeatures/js/action/set-payment-information': true
            }
        }
    }
};
