/*jslint nomen:true*/
/*global define*/
define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const BaseComponent = require('oroui/js/app/components/base/component');
    const mediator = require('oroui/js/mediator');

    const CurrencySelectComponent = BaseComponent.extend({
        /**
         * @property {Object}
         */
        options: {
            currencySelect: 'input[name$="[currency]"]'
        },

        /**
         * @property {Object}
         */
        $currencySelect: null,

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            console.log("Currency component init");
            this.options = _.defaults(options || {}, this.options);
            this.$el = options._sourceElement;

            this.$currencySelect = this.$el.find(this.options.currencySelect);
            this.$el.on('change', this.options.currencySelect, _.bind(this.triggerChangeEvent, this));
        },

        /**
         * Trigger add event
         *
         * @param {Object} data
         */
        triggerChangeEvent: function(data) {
            mediator.trigger('marello_sales:currency:changed', data);
            console.log("Currency changed");
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.$el.off();
            CurrencySelectComponent.__super__.dispose.call(this);
        }
    });

    return CurrencySelectComponent;
});