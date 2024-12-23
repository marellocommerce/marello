define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const __ = require('orotranslation/js/translator');
    const BaseView = require('oroui/js/app/views/base/view');
    const StandardConfirmation = require('oroui/js/standart-confirmation');

    const ConfigIntegerView = BaseView.extend({
        /**
         * @property {Object}
         */
        options: {},

        /**
         * @property {jQuery}
         */
        $form: null,

        /**
         * @constructor
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            this.initLayout().done(_.bind(this.handleLayoutInit, this));
            ConfigIntegerView.__super__.initialize.apply(this, options);
        },

        /**
         * Doing something after loading child components
         */
        handleLayoutInit: function() {
            this.$form = this.$el.closest('form');
            this.$relatedItemElement = this.$form.find(':input[id="'+ this.options.sourceElement + '"]');
            this.$relatedItemElementOldVal = this.$relatedItemElement.val();
            this.$relatedItemElement.on('change', _.bind(this.onRelatedItemAssignedChange, this));
        },

        onRelatedItemAssignedChange: function() {
            if (this.$relatedItemElement.val() < this.$relatedItemElementOldVal) {
                const confirmation = new StandardConfirmation({
                    content: __('marello.product.system_configuration.change_related_item_assigned_confirmation'),
                    okText: _.__('OK')
                });
                confirmation.on('ok', () => {
                    this.$relatedItemElementOldVal = this.$relatedItemElement.val();
                });
                confirmation.on('cancel', () => {
                    this.$relatedItemElement.val(this.$relatedItemElementOldVal);
                });

                confirmation.open();
            } else {
                this.$relatedItemElementOldVal = this.$relatedItemElement.val();
            }
        }
    });

    return ConfigIntegerView;
});
