define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const BaseView = require('oroui/js/app/views/base/view');

    const CustomerPrimaryAddressView = BaseView.extend({
        options: {
            selectors: {
                usePrimaryAsShipping: '',
                shippingAddressBlock: ''
            }
        },

        $usePrimaryAsShipping: null,
        $shippingAddressBlock: null,

        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            CustomerPrimaryAddressView.__super__.initialize.apply(this, arguments);

            this.$usePrimaryAsShipping = this.$el.find(this.options.selectors.usePrimaryAsShipping);
            this.$shippingAddressBlock = this.$el.closest('form')
                .find(this.options.selectors.shippingAddressBlock)
                .closest('.responsive-cell');

            this.$el.on(
                'change',
                this.options.selectors.usePrimaryAsShipping,
                _.bind(this.onUsePrimaryAsShippingChange, this)
            );
        },

        onUsePrimaryAsShippingChange: function() {
            if (this.$usePrimaryAsShipping.prop('checked')) {
                this.$shippingAddressBlock.addClass('hide');
            } else {
                this.$shippingAddressBlock.removeClass('hide');
            }
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.$el.off(
                'change',
                _.bind(this.onUsePrimaryAsShippingChange, this)
            );

            CustomerPrimaryAddressView.__super__.dispose.call(this);
        }
    });

    return CustomerPrimaryAddressView;
});
