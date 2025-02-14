define(function(require) {
    'use strict';

    const
        $ = require('jquery'),
        _ = require('underscore'),
        routing = require('routing'),
        mediator = require('oroui/js/mediator'),
        DialogWidget = require('oro/dialog-widget'),
        AbstractItemsView = require('marellolayout/js/app/views/abstract-items-view');

    /**
     * @export marellopurchaseorder/js/app/views/purchaseorder-items-view
     * @extends marellolayout.app.views.AbstractItemsView
     * @class marellopurchaseorder.app.views.PurchaseOrderItemsView
     */
    const PurchaseOrderItemsView = AbstractItemsView.extend({
        /**
         * @property {Object}
         */
        options: {
            supplierId: null,
            gridRoute: 'marello_purchase_order_widget_products_by_supplier'
        },

        events: {
            'click .add-btn-prod': 'addProducts',
            'click .add-btn-advise': 'addCandidatesProducts',
        },

        /**
         * @property DialogWidget
         */
        selectorDialog: null,

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            mediator.on('po:row:total:changed', this.triggerTotalsUpdateEvent, this);
            PurchaseOrderItemsView.__super__.initialize.apply(this, arguments);
        },

        triggerTotalsUpdateEvent: function(data) {
            var total = 0;
            var lineItems = this.$el.find('.purchase-order-line-item');
            _.each(lineItems, function(lineItem) {
                var $lineItem = $(lineItem);
                var amount = $lineItem.find('input[name*="orderedAmount"]').val();
                var price = $lineItem.find('input[name*="[purchasePrice][value]"]').val();
                var rowTotal = parseFloat(amount) * parseFloat(price);
                if (!isNaN(rowTotal)) {
                    total = total + rowTotal;
                }
            });
            mediator.trigger('po:items:total:changed', {'value': total.toFixed(2), 'currency': data.currency, 'type': 'additional', 'label': 'Additional items Total'});
        },

        addProducts: function(e) {
            let url = routing.generate(this.options.gridRoute, {supplierId: this.options.supplierId, type: 'supplier'});
            this.addEntities(url);
        },

        addCandidatesProducts: function(e) {
            let url = routing.generate(this.options.gridRoute, {supplierId: this.options.supplierId, type: 'advise'});
            this.addEntities(url);
        },

        addEntities: function(url) {
            if (!this.selectorDialog) {
                this.selectorDialog = new DialogWidget({
                    url: url,
                    title: this.options.selectorWindowTitle,
                    stateEnabled: false,
                    dialogOptions: {
                        modal: true,
                        width: 1024,
                        height: 500,
                        close: _.bind(function() {
                            this.selectorDialog = null;
                        }, this)
                    }
                });
                this.selectorDialog.on('completeSelection', _.bind(this.processSelectedEntities, this));
                this.selectorDialog.render();
            }
        },

        processSelectedEntities: function(added, addedModels) {
            if (added.length > 0) {
                _.each(addedModels, _.bind(function(model) {
                    this.addRow();

                    // This part was taken from abstract-items-view.js and it's related
                    // to the original 'Add product' button to get an appropriate container
                    var _self = this.$el.find('.marello-add-line-item');
                    var containerSelector = $(_self).data('container') || '.collection-fields-list';
                    var $listContainer = this.$el.find('.row-oro').find(containerSelector).first();
                    // Last index means for a next item, so we need to get lastIndex-1 for the current row
                    var currentIndex = $listContainer.data('last-index') - 1;
                    var productField = $listContainer.find('[name$="[items][' + currentIndex + '][product]"]').first();
                    var orderAmount = $listContainer.find('[name$="[items][' + currentIndex + '][orderedAmount]"]');
                    productField.inputWidget('val', model.id);
                    orderAmount.inputWidget('val', model.attributes.orderAmount);
                }, this));
            }
            mediator.trigger('po:row:product:changed');
            this.selectorDialog.remove();
        },
    });

    return PurchaseOrderItemsView;
});
