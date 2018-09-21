define(function(require) {
    'use strict';

    var PossibleShippingMethodsView;
    var $ = require('jquery');
    var _ = require('underscore');
    var mediator = require('oroui/js/mediator');
    var BaseView = require('oroui/js/app/views/base/view');
    var ElementsHelper = require('marellocore/js/app/elements-helper');
    var LoadingMaskView = require('oroui/js/app/views/loading-mask-view');
    var StandardConfirmation = require('oroui/js/standart-confirmation');
    var possibleShippingMethodsTemplate = require('tpl!./../templates/possible-shipping-methods-template.html');
    var selectedShippingMethodTemplate = require('tpl!./../templates/selected-shipping-method-template.html');
    var noShippingMethodsAvailableTemplate = require('tpl!./../templates/no-shipping-methods-available.html');
    var NumberFormatter = require('orolocale/js/formatter/number');

    PossibleShippingMethodsView = BaseView.extend(_.extend({}, ElementsHelper, {
        autoRender: true,

        options: {
            savedShippingMethod: null,
            savedShippingMethodLabel: null,
            possibleShippingMethodsTemplate: possibleShippingMethodsTemplate,
            selectedShippingMethodTemplate: selectedShippingMethodTemplate,
            noShippingMethodsAvailableTemplate: noShippingMethodsAvailableTemplate
        },

        elements: {
            toggleBtn: '[data-role="possible_shipping_methods_btn"]',
            possibleShippingMethodForm: '[data-content="possible_shipping_methods_form"]',
            calculateShipping: '[data-name="field__calculate-shipping"]',
            shippingMethod: '[data-name="field__shipping-method"]',
            shippingMethodType: '[data-name="field__shipping-method-type"]',
            estimatedShippingCostAmount: '[data-name="field__estimated-shipping-cost-amount"]',
            overriddenShippingCostAmount: ['$document', '[name*="[overriddenShippingCostAmount]"]']
        },

        elementsEvents: {
            toggleBtn: ['click', 'onToggleBtnClick'],
            overriddenShippingCostAmount: ['change', 'onOverriddenShippingCostChange'],
            possibleShippingMethodForm: ['change', 'onShippingMethodTypeChange'],
            '$form': ['submit', 'onSaveForm']
        },

        initialize: function(options) {
            PossibleShippingMethodsView.__super__.initialize.apply(this, arguments);

            this.options = $.extend(true, {}, this.options, options || {});
            this.orderHasChanged = false;

            this.$form = this.$el.closest('form');
            this.$document = $(document);

            this.initializeElements(options);

            mediator.on('order:form-changes:trigger', this.showLoadingMask, this);
            mediator.on('order:form-changes:load', this.onOrderChange, this);
            mediator.on('order:form-changes:load:after', this.hideLoadingMask, this);
        },

        render: function() {
            this.getElement('possibleShippingMethodForm').hide();
        },

        onToggleBtnClick: function(e) {
            this.getElement('calculateShipping').val(true);
            mediator.trigger('order:form-changes:trigger', {updateFields: ['possible_shipping_methods']});
        },

        onSaveForm: function(e) {
            this.getElement('calculateShipping').val(true);

            var $form = this.getElement('$form');
            $form.validate();
            if ($form.valid() && this.orderHasChanged && !this.getElement('overriddenShippingCostAmount').val()) {
                this.showConfirmation($form);
                return false;
            }

            return true;
        },

        showConfirmation: function(form) {
            this.removeSubview('confirmation');
            this.subview('confirmation', new StandardConfirmation({
                title: _.__('marello.order.possible_shipping_methods.confirmation.title'),
                content: _.__('marello.order.possible_shipping_methods.confirmation.content'),
                okText: _.__('Save'),
                cancelText: _.__('marello.order.continue_editing')
            }));

            this.subview('confirmation')
                .off('ok').on('ok', _.bind(function() {
                    this.orderHasChanged = false;
                    this.getElement('$form').trigger('submit');
                }, this))
                .open();
        },

        showLoadingMask: function() {
            this.orderHasChanged = true;
            if (this.getElement('calculateShipping').val()) {
                this.removeSubview('loadingMask');
                this.subview('loadingMask', new LoadingMaskView({
                    container: this.$el
                }));
                this.subview('loadingMask').show();
            }
        },

        hideLoadingMask: function() {
            this.removeSubview('loadingMask');
        },

        onOrderChange: function(e) {
            if (e.totals && _.size(e) === 1) {
                this.orderHasChanged = false;
                return;
            }

            if (e.possibleShippingMethods !== undefined) {
                this.getElement('calculateShipping').val(null);
                this.getElement('toggleBtn').parent('div').hide();
                this.updatePossibleShippingMethods(e.possibleShippingMethods);
                this.getElement('possibleShippingMethodForm').show();
                this.orderHasChanged = false;
            } else if (this.recalculationIsNotRequired === true) {
                this.orderHasChanged = false;
                this.recalculationIsNotRequired = false;
            } else {
                this.getElement('possibleShippingMethodForm').hide();
                this.getElement('toggleBtn').parent('div').show();
                this.orderHasChanged = true;
            }
        },

        onOverriddenShippingCostChange: function() {
            this.recalculationIsNotRequired = true;
            mediator.trigger('order:form-changes:trigger', {updateFields: ['totals']});
        },

        updatePossibleShippingMethods: function(methods) {
            var selectedMethod = this.getSelectedMethod();
            if (!selectedMethod && this.options.savedShippingMethod) {
                selectedMethod = this.options.savedShippingMethod;
            }
            var selectedFound = false;
            var str = this.options.noShippingMethodsAvailableTemplate();
            if (_.size(methods) > 0) {
                str = this.options.possibleShippingMethodsTemplate({
                    methods: methods,
                    selectedMethod: selectedMethod,
                    createMethodObject: this.createMethodObject,
                    areMethodsEqual: this.areMethodsEqual,
                    NumberFormatter: NumberFormatter
                });

                selectedFound = this.isMethodAvailable(methods, selectedMethod);
            }

            this.removeSelectedShippingMethod();
            if (!selectedFound) {
                this.setElementsValue(null);
                if (this.options.savedShippingMethod) {
                    this.renderPreviousSelectedShippingMethod();
                }
            }

            this.getElement('possibleShippingMethodForm').html(str);
        },

        getSelectedMethod: function() {
            var selectedMethod = this.getElement('shippingMethod').val();
            var selectedType = this.getElement('shippingMethodType').val();
            var selectedCost = this.getElement('estimatedShippingCostAmount').val();
            if (selectedMethod && selectedType && selectedCost) {
                return this.createMethodObject(selectedMethod, selectedType, selectedCost);
            }
            return null;
        },

        isMethodAvailable: function(methods, expectedMethod) {
            var selectedFound = false;
            if (!expectedMethod) {
                return selectedFound;
            }
            _.each(methods, function(method) {
                if (method.identifier !== expectedMethod.method) {
                    return;
                }
                _.each(method.types, function(type) {
                    if (type.price.value === null || type.identifier !== expectedMethod.type) {
                        return;
                    }
                    selectedFound = parseFloat(expectedMethod.cost) === parseFloat(type.price.value);
                }, this);
            }, this);

            return selectedFound;
        },

        /**
         * @param {object|null} method
         */
        setElementsValue: function(method) {
            if (!method) {
                method = this.createMethodObject(null, null, null);
            }
            this.getElement('shippingMethod').val(method.method);
            this.getElement('shippingMethodType').val(method.type);
            this.getElement('estimatedShippingCostAmount').val(method.cost);
            this.updateTotals();
        },

        removeSelectedShippingMethod: function() {
            this.$document.find('.selected-shipping-method').closest('.control-group').remove();
        },

        renderPreviousSelectedShippingMethod: function(label) {
            this.removeSelectedShippingMethod();
            var $prevDiv = $('<div>').html(this.options.selectedShippingMethodTemplate({
                shippingMethodLabel: _.__('marello.order.previous_shipping_method.label'),
                shippingMethodClass: 'selected-shipping-method',
                selectedShippingMethod: this.options.savedShippingMethodLabel
            }));
            this.$el.closest('.responsive-cell').prepend($prevDiv);
        },

        /**
         * @param {Event} event
         */
        onShippingMethodTypeChange: function(event) {
            var target = $(event.target);
            var method = this.createMethodObject(
                target.data('shipping-method'),
                target.val(),
                target.data('shipping-price')
            );

            this.setElementsValue(method);

            this.removeSelectedShippingMethod();
            if (this.options.savedShippingMethod && !this.areMethodsEqual(method, this.options.savedShippingMethod)) {
                this.renderPreviousSelectedShippingMethod();
            }

            this.updateTotals();
        },

        areMethodsEqual: function(methodA, methodB) {
            var equals = false;
            if (methodA && methodB) {
                equals = methodA.method === methodB.method;
                equals = equals && methodA.type === methodB.type;
                equals = equals && parseFloat(methodA.cost) === parseFloat(methodB.cost);
            }
            return equals;
        },

        createMethodObject: function(method, type, cost) {
            return {
                method: method,
                type: type,
                cost: cost
            };
        },

        updateTotals: function() {
            var overriddenCost = this.getElement('overriddenShippingCostAmount').val();
            var cost = parseFloat(overriddenCost);
            if (isNaN(cost)) {
                this.recalculationIsNotRequired = true;
            }
            mediator.trigger('order:form-changes:trigger', {updateFields: ['totals']});
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.disposeElements();

            delete this.$form;
            delete this.$document;
            delete this.options;

            mediator.off(null, null, this);

            PossibleShippingMethodsView.__super__.dispose.apply(this, arguments);
        }
    }));

    return PossibleShippingMethodsView;
});
