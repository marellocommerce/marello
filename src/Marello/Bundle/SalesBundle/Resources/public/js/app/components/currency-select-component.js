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
            currencySelect: 'select[name$="[currency]"]',
            selectedCurrency: 'option[selected="selected"]',
            attribute: 'currency'
        },

        /**
         * @property {Object}
         */
        $currencySelect: null,

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = _.defaults(options || {}, this.options);
            this.$el = options._sourceElement;

            this.$currencySelect = this.$el.find(this.options.selectedCurrency);

            this.options.attribute = this.$el.find(this.options.selectedCurrency.valueOf()).val();

            this.$el.on('change', this.$currencySelect.selected, _.bind(this.triggerChangeEvent, this));
        },

        /**
         * Trigger changed event
         *
         * @param {Object} data
         */
        triggerChangeEvent: function(data) {
            this.options.attribute = this.$el.find(this.options.selectedCurrency.valueOf()).val();
            console.log(this.options.attribute);
            console.log(data);

            mediator.trigger('marello_sales:currency:changed', data);
            console.log('Currency changed');
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