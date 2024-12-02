define(function(require) {
    'use strict';

    const
        _ = require('underscore'),
        __ = require('orotranslation/js/translator'),
        $ = require('jquery'),
        DeleteConfirmation = require('oroui/js/delete-confirmation'),
        AbstractItemView = require('marellolayout/js/app/views/abstract-item-view');

    /**
     * @export marelloinventory/js/app/views/inventorylevel-item-view
     * @extends marellolayout.app.views.AbstractItemView
     * @class marelloinventory.app.views.InventoryLevelItemView
     */
    const InventoryLevelItemView = AbstractItemView.extend({
        /**
         * @property {Object}
         */
        options: {
            managedInventorySelector: 'input[name*=managedInventory]',
            quantitySelector: 'input[name*=quantity]',
            adjustmentOperatorSelector: 'select[name*=adjustmentOperator]',
            enableBatchInventorySelector: 'input[name*=enableBatchInventory]'
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            this.options.enableBatchInventory = $(document).find(this.options.enableBatchInventorySelector).is(':checked');
            InventoryLevelItemView.__super__.initialize.apply(this, arguments);
            if (this.options.enableBatchInventory === true) {
                $(this.$el).find('.inventorylevel-managed-inventory').find('.fields-row').remove();
                if ($(this.$el).find(this.options.adjustmentOperatorSelector).length > 0) {
                    $(this.$el).find('.inventorylevel-adjustment').find('.fields-row').remove();
                }
            } else {
                var managedInventoryEl = $(this.$el).find(this.options.managedInventorySelector);
                $(managedInventoryEl).on('change', _.bind(this.onManagedInventoryChange, this));
                if (!$(managedInventoryEl).is(':disabled')) {
                    $(managedInventoryEl).trigger('change');
                }
            }
        },
        
        onManagedInventoryChange: function(e) {
            var quantityEl = $(this.$el).find(this.options.quantitySelector);
            var adjustmentOperatorEl = $(this.$el).find(this.options.adjustmentOperatorSelector);
            if ($(e.target).is(':checked')) {
                $(quantityEl).prop('disabled', false);
                $(adjustmentOperatorEl).prop('disabled', false);
                $(adjustmentOperatorEl).parent('div').removeClass('disabled');
            } else {
                $(quantityEl).prop('disabled', true);
                $(adjustmentOperatorEl).prop('disabled', true);
                $(adjustmentOperatorEl).parent('div').addClass('disabled')
            }
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            InventoryLevelItemView.__super__.dispose.call(this);
        }

    });

    return InventoryLevelItemView;
});

