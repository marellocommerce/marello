define(function(require) {
    'use strict';

    const BaseComponent = require('oroui/js/app/components/base/component');
    const _ = require('underscore');
    const $ = require('jquery');

    const ProductVariantComponent = BaseComponent.extend({
        /**
         * @property {Object}
         */
        options: {
            productVariantFieldsSelector: 'input[type=checkbox]',
        },

        variantFieldCheckboxes: [],

        /**
         * @inheritdoc
         */
        constructor: function ProductVariantComponent(options) {
            ProductVariantComponent.__super__.constructor.call(this, options);
        },

        /**
         * @inheritdoc
         */
        initialize: function(options) {
            this.options = _.defaults(options || {}, this.options);
            this.variantFieldCheckboxes = this.options._sourceElement.find(this.options.productVariantFieldsSelector);
            if (this.variantFieldCheckboxes) {
                this.options._sourceElement
                    .on('change', this.productVariantFieldsSelector, this.onVariantFieldChange.bind(this));
            }
        },

        onVariantFieldChange: function() {
            let variantFields = [];
            this.variantFieldCheckboxes.each((idx, el) => {
                if (el.checked) {
                    variantFields.push(this.getFieldName(el));
                }
            });
            // Set null value instead of empty array (empty array will not be sent)
            if (variantFields.length === 0) {
                variantFields = null;
            }
        },

        /**
         *
         * @param el
         * @returns {string}
         */
        getFieldName: function(el) {
            return $(el).attr('data-original-name');
        },

        /**
         * @inheritdoc
         */
        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.options._sourceElement.off('change');
            ProductVariantComponent.__super__.dispose.call(this);
        }
    });

    return ProductVariantComponent;
});