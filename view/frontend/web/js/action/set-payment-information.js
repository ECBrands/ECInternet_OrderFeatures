define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (placeOrderAction) {
        /** Override place-order-mixin for set-payment-information action as they differs only by method signature */
        return wrapper.wrap(placeOrderAction, function (originalAction, messageContainer, paymentData) {
            if (paymentData['extension_attributes'] === undefined) {
                paymentData['extension_attributes'] = {};
            }

            paymentData['extension_attributes']['po_number']     = $('input[name="po_number"]').val();
            paymentData['extension_attributes']['order_comment'] = $('textarea[name="order_comment"]').val();

            return originalAction(messageContainer, paymentData);
        });
    };
});