/*global define*/

define([
    'jquery',
    "uiComponent",
    'ko',
    'Amazon_Payment/js/model/storage',
    'uiRegistry'
], function (
    $,
    Component,
    ko,
    amazonStorage,
    registry
) {
    'use strict';

    var self;

    var amazonPayment = registry.get('amazonPayment') || {};
    return Component.extend({
        defaults: {
            template: 'Amazon_Payment/checkout-sandbox-simulator'
        },
        isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
        isSandboxEnabled: ko.observable(amazonPayment.isSandboxEnabled || false),
        sandboxSimulationReference: amazonStorage.sandboxSimulationReference,
        sandboxSimulationOptions: ko.observableArray(amazonPayment.sandboxSimulationOptions || []),
        initialize: function () {
            self = this;
            this._super();
        }
    });
});
