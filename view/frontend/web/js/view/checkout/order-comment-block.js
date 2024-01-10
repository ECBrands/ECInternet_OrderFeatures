define([
    'jquery',
    'uiComponent'
], function (
    $,
    Component
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ECInternet_OrderFeatures/checkout/order-comment-block'
        },

        initialize: function () {
            this._super();
        }
    });
});
