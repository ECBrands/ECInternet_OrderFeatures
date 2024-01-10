define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'ecinternet_erpterms',
                component: 'ECInternet_OrderFeatures/js/view/payment/method-renderer/erpterms-method'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
