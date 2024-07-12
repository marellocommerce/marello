define(function (require) {
    'use strict';
    const
        mediator = require('oroui/js/mediator'),
        Select2AutocompleteComponent = require('oro/select2-autocomplete-component');
    const Select2AutocompleteCurrencyAwareComponent = Select2AutocompleteComponent.extend({

        /**
         * @property {Object}
         */
        options: {
            currencyDataContainer: '.marello-currency-select-container',
            attribute: 'currency'
        },

        /**
         * @property {Object}
         */
        $salesChannelSelect: null,

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options);
            this.$sourceElement = options._sourceElement;

            const $currencyContainer = $(this.options.currencyDataContainer);
            const initialCurrency = $currencyContainer.find(':selected').val();
            this.saveData({ currency: initialCurrency });

            mediator.on('marello_sales:currency:changed', this.onCurrencyChange, this);
            Select2AutocompleteCurrencyAwareComponent.__super__.initialize.call(this, options);
        },

        makeQuery: function (query) {
            var currency = this.getData().currency;

            return query + ';' + currency;
        },

        onCurrencyChange: function(e) {
            if (e.to !== undefined) {
                this.saveData({ currency: e.to });
                this.$sourceElement.inputWidget('val', '');
            }
        },

        /**
         * Return units from data attribute
         *
         * @returns {jQuery.Element}
         */
        getData: function() {
            return this.$sourceElement.data(this.options.attribute) || {};
        },

        /**
         * Save data to data attribute
         *
         * @param {Object} data
         */
        saveData: function(data) {
            this.$sourceElement.data(this.options.attribute, data);
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            mediator.off('marello_sales:currency:changed', this.onCurrencyChange, this);
            Select2AutocompleteCurrencyAwareComponent.__super__.dispose.call(this);
        }
    });

    return Select2AutocompleteCurrencyAwareComponent;
});
