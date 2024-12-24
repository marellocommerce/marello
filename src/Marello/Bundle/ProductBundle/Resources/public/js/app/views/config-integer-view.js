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
         * @property {Object}
         */
        $relatedItemConfig: {},

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
            this.$relatedItemElement.change(_.bind(function() {
                this.setRelatedItemValueHistory(this._getRelatedItemValue());
                this.onRelatedItemAssignedChange();
            }, this));

            this.initRelatedItemValueHistory();
        },

        /**
         * get related item limit config value
         * @returns {string}
         * @protected
         */
        _getRelatedItemValue: function() {
            return this.$relatedItemElement.length !== 0 ? this.$relatedItemElement.val() : '';
        },

        /**
         * update the current and prev relatedItemConfigValues
         * @param relatedItemConfigValue
         */
        setRelatedItemValueHistory: function(relatedItemConfigValue) {
            let $prevElm =  'prev-' + this.options.sourceElement;
            let $currentElm =  'current-' + this.options.sourceElement;
            this.$relatedItemConfig[$prevElm] = (this.$relatedItemConfig[$currentElm] === null) ? parseInt(relatedItemConfigValue) : this.$relatedItemConfig[$currentElm];
            this.$relatedItemConfig[$currentElm] = parseInt(relatedItemConfigValue);
        },

        /**
         * initialize relatedItemConfig history (current and prev changed relatedConfigValues)
         */
        initRelatedItemValueHistory: function () {
            this.setRelatedItemValueHistory(this._getRelatedItemValue());
        },

        onRelatedItemAssignedChange: function() {
            let $prevElm =  'prev-' + this.options.sourceElement;
            let $currentElm =  'current-' + this.options.sourceElement;
            if (this.$relatedItemConfig[$currentElm] < this.$relatedItemConfig[$prevElm]) {
                const confirmation = new StandardConfirmation({
                    content: __('marello.product.system_configuration.change_related_item_assigned_confirmation'),
                    okText: _.__('OK')
                });
                confirmation.on('cancel', () => {
                    this.$relatedItemElement.val(this.$relatedItemConfig[$prevElm]);
                });

                confirmation.open();
            }
        }
    });

    return ConfigIntegerView;
});
