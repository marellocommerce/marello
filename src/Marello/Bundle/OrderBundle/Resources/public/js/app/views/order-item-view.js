define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const mediator = require('oroui/js/mediator');
    const AbstractItemView = require('marellolayout/js/app/views/abstract-item-view');

    /**
     * @export marelloorder/js/app/views/order-item-view
     * @extends marellolayout.app.views.AbstractItemView
     * @class marelloorder.app.views.OrderItemView
     */
    const OrderItemView = AbstractItemView.extend({
        options: {
            ftid: '',
            salable: null
        },

        /**
         * @property {Object}
         */
        data: {},

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            OrderItemView.__super__.initialize.apply(this, arguments);
        },

        /**
         * Doing something after loading child components
         */
        handleLayoutInit: function() {
            OrderItemView.__super__.handleLayoutInit.apply(this, arguments);
            this.initOrderItem();
        },

        /**
         * initialize item triggers and field events
         */
        initOrderItem: function() {
            this.addFieldEvents('product', this.updateOrderItemData);
            this.addFieldEvents('quantity', this.updateOrderItemData);
            mediator.trigger('order:get:line-items-data', _.bind(this.setOrderItemData, this));
            mediator.on('order:refresh:line-items', this.setOrderItemData, this);
        },

        /**
         * Trigger subtotals update
         */
        updateOrderItemData: function() {
            var productId = this.getRowItemIdentifier();
            if (productId.length === 0) {
                this.setOrderItemData({});
            } else {
                mediator.trigger(
                    'order:form-changes:trigger',
                    {
                        updateFields: [
                            'items',
                            'totals',
                            'inventory',
                            'possible_shipping_methods',
                            'possible_payment_methods'
                        ]
                    }
                );
            }
        },

        /**
         * @inheritDoc
         * @param data
         */
        setOrderItemData: function(data) {
            if (data === undefined || typeof(data) == 'undefined' || data.length == 0) {
                return;
            }
            var identifier = this._getItemIdentifier();
            if (identifier && data[identifier] !== undefined) {
                if(data[identifier].message !== undefined) {
                    this.data = {};
                    this.setRowTotals();
                    this.options.salable = {value: false, message: data[identifier].message};
                } else {
                    this.data = data[identifier] || {};
                    this.options.salable = {value: true, message: ''};
                }

                mediator.trigger('order:update:line-items', {'elm': this.$el, 'salable': this.options.salable},this);
            } else {
                this.data = {};
            }

            var $priceValue = parseFloat(this.getPriceValue());
            if($priceValue === "NaN" || $priceValue === null) {
                $priceValue = '';
            }

            this.fieldsByName.price.val($priceValue);
            this.fieldsByName.taxCode.val(this.getTaxCode());
            this.fieldsByName.productUnit.val(this.getProductUnit());

            this.setRowTotals();
            this.setAvailableInventory();
        },

        /**
         * @returns {String|Null}
         */
        getPriceValue: function() {
            return !_.isEmpty(this.data['price']) ? this.data['price'].value : null;
        },

        /**
         * @returns {String|Null}
         */
        getTaxCode: function() {
            return !_.isEmpty(this.data['tax_code']) ? this.data['tax_code'].code : null;
        },

        /**
         * @returns {Array|Null}
         */
        getRowTotals: function() {
            if (this.getRowItemIdentifier() === null) {
                return null;
            }
            if (_.isEmpty(this.data['row_totals'])) {
                return null;
            }
            return !_.isEmpty(this.data['row_totals'][this.getRowItemIdentifier()]) ? this.data['row_totals'][this.getRowItemIdentifier()] : null;
        },

        /**
         * @returns {Array|Null}
         */
        getProductUnit: function() {
            return !_.isEmpty(this.data['product_unit']) ? this.data['product_unit'].unit : null;
        },

        /**
         * @returns {Array|Null}
         */
        getProductInventory: function() {
            return !_.isEmpty(this.data['inventory']) ? this.data['inventory'].value : null;
        },

        /**
         * Set row totals
         */
        setRowTotals: function() {
            var row_totals = this.getRowTotals();
            if (row_totals === null) {
                this.fieldsByName.tax.val('');
                this.fieldsByName.rowTotalExclTax.val('');
                this.fieldsByName.rowTotalInclTax.val('');
            } else {
                var taxAmount = parseFloat(row_totals.taxAmount);
                var taxExcl = parseFloat(row_totals.excludingTax);
                var taxIncl = parseFloat(row_totals.includingTax);
                this.fieldsByName.tax.val(taxAmount);
                this.fieldsByName.rowTotalExclTax.val(taxExcl);
                this.fieldsByName.rowTotalInclTax.val(taxIncl);
            }
        },

        setAvailableInventory: function() {
            if (this.getProductInventory() === null) {
                return;
            }

            this.fieldsByName.availableInventory.val(this.getProductInventory());
        },

        /**
         * @returns {String|Null}
         * @private
         */
        _getItemIdentifier: function() {
            var productId = this.getProductId();
            if (productId.length === 0) {
                return null;
            }

            var rowId = this.getRowItemIdentifier();
            if (rowId.length === 0) {
                return null;
            }

            return 'product-id-' + rowId + '-' + productId;
        },

        /**
         * @inheritDoc
         */
        removeRow: function() {
            OrderItemView.__super__.removeRow.call(this);
            mediator.trigger('order:form-changes:trigger', {updateFields: ['totals', 'possible_shipping_methods', 'possible_payment_methods']});
        },

        /**
         * @inheritDoc
         */
        dispose: function() {
            if (this.disposed) {
                return;
            }

            mediator.off('order:refresh:line-items', this.setOrderItemData, this);

            OrderItemView.__super__.dispose.call(this);
        }
    });

    return OrderItemView;
});
