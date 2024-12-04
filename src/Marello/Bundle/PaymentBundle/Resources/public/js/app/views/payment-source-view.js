define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const mediator = require('oroui/js/mediator');
    const BaseView = require('oroui/js/app/views/base/view');

    /**
     * @export marellopayment/js/app/views/payment-source-view
     * @extends oroui.app.views.base.View
     * @class marellopayment.app.views.PaymentSourceView
     */
    const PaymentSourceView = BaseView.extend({
        /**
         * @property {Object}
         */
        options: {},

        /**
         * @property {jQuery}
         */
        $field: null,

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});

            this.$field = this.$el.find(':input[data-ftid]');
            $(this.$field).on('change', function() {
                mediator.trigger('payment:form-changes:trigger', {updateFields: ['paymentMethods', 'currencies']});
            });
            $(this.$field).trigger('change');
        },

        /**
         * @inheritDoc
         */
        dispose: function() {
            if (this.disposed) {
                return;
            }

            PaymentSourceView.__super__.dispose.call(this);
        }
    });

    return PaymentSourceView;
});
