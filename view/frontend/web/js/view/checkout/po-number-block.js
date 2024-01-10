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
            template: 'ECInternet_OrderFeatures/checkout/po-number-block'
        },

        initialize: function () {
            this._super();
        }
    });
});
