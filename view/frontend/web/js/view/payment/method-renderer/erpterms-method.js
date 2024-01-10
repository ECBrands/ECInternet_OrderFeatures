define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'ECInternet_OrderFeatures/payment/erpterms'
            },

            context: function () {
                return this;
            },

            getCode: function () {
                return 'ecinternet_erpterms';
            },

            isActive: function () {
                return true;
            }
        });
    }
);
