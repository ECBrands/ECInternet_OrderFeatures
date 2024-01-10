define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (placeOrderAction) {
        /** Override default place-order action and add fields to request*/
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            if (paymentData['extension_attributes'] === undefined) {
                paymentData['extension_attributes'] = {};
            }

            paymentData['extension_attributes']['po_number']     = $('input[name="po_number"]').val();
            paymentData['extension_attributes']['order_comment'] = $('textarea[name="order_comment"]').val();

            return originalAction(paymentData, messageContainer);
        })
    };
});