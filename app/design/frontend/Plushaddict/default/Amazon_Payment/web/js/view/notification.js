/*global define*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'uiComponent',
        'Amazon_Payment/js/model/storage',
    'uiRegistry'
    ],
    function (
        $,
        _,
        ko,
        Component,
        amazonStorage,
    registry
    ) {
        'use strict';

        var self;
        var amazonPayment = registry.get('amazonPayment') || {}
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/notification'
            },
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            chargeOnOrder: ko.observable(amazonPayment.chargeOnOrder || false),
            isEuPaymentRegion: ko.observable(amazonPayment.isEuPaymentRegion || false),
            initialize: function () {
                self = this;
                this._super();
            }
        });
    }
);
