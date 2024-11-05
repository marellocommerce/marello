define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const BaseComponent = require('oroui/js/app/components/base/component');

    const SupplierComponent = BaseComponent.extend({
        options: {
            poSendBySelector: 'select[name$="[poSendBy]"]',
            emailSelector: 'input[name$="[email]"]',
        },

        $poSendBy: null,
        $email: null,

        initialize: function(options) {
            this.options = _.defaults(options || {}, this.options);
            this.$poSendBy = $(this.options._sourceElement).find(this.options.poSendBySelector);
            this.$email = $(this.options._sourceElement).find(this.options.emailSelector);

            this.$poSendBy.on('change', _.bind(this.toggleEmailRequiredParam, this));
            this.toggleEmailRequiredParam();
        },

        toggleEmailRequiredParam: function() {
            let value = this.$poSendBy.val();

            if (value === 'email') {
                this.addRequiredFlag();
            } else {
                this.removeRequiredFlag();
            }
        },

        addRequiredFlag: function() {
            const label = this.getEmailLabel();
            if (!label.hasClass('required')) {
                label
                    .addClass('required')
                    .find('em').html('*');
            }
        },

        removeRequiredFlag: function() {
            const label = this.getEmailLabel();
            if (label.hasClass('required')) {
                label
                    .removeClass('required')
                    .find('em').html('&nbsp;');
            }
        },

        getEmailLabel: function() {
            let id = this.$email.attr('id');

            return $('label[for="' + id + '"]');
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.$poSendBy.off('change', _.bind(this.toggleEmailRequiredParam, this));
            this.options._sourceElement.off();
            SupplierComponent.__super__.dispose.call(this);
        }
    });

    return SupplierComponent;
});
