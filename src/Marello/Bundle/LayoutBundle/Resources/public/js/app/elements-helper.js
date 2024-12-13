define(function(require) {
    'use strict';

    /**
     * This helper use in the context of component View
     */
    const $ = require('jquery');
    const _ = require('underscore');
    require('jquery.validate');

    return {
        elementsInitialized: false,

        $elements: null,

        elements: {},

        elementsEvents: null,

        modelElements: {},

        modelEvents: null,

        elementEventNamespace: '.elementEvent',

        deferredInitializeCheck: function(options, checkOptions) {
            const $deferredInitialize = this.$el.parent().closest('[data-layout="deferred-initialize"]');
            if (checkOptions === undefined || !$deferredInitialize.length) {
                return this.deferredInitialize(options);
            }

            let wait = false;
            _.each(checkOptions, function(option) {
                if (options[option] === undefined) {
                    wait = true;
                }
            });

            if (!wait) {
                return this.deferredInitialize(options);
            }

            $deferredInitialize.one('deferredInitialize', _.bind(function(e, deferredOptions) {
                e.preventDefault();
                e.stopPropagation();

                this.deferredInitialize(_.extend({}, options, deferredOptions));
                if (deferredOptions.callback) {
                    deferredOptions.callback(this);
                }
            }, this));
        },

        deferredInitialize: function(options) {
        },

        initializeSubviews: function(options) {
            this._deferredRender();
            const layout = this.$el.data('layout');
            if (layout === 'deferred-initialize') {
                this.$el.trigger('deferredInitialize', options);
                this.handleLayoutInit();
            } else if (layout === 'separate') {
                this.initLayout(options)
                    .done(_.bind(this.handleLayoutInit, this));
            } else {
                this.handleLayoutInit();
            }
        },

        handleLayoutInit: function() {
            this._resolveDeferredRender();
        },

        initializeElements: function(options) {
            this.$html = $('html');
            this.elementsInitialized = true;
            const optionNames = ['elements', 'modelElements'];
            Object.assign(this, $.extend(true, {},
                _.pick(this, optionNames),
                _.pick(options, optionNames)
            ));
            this.$elements = this.$elements || {};
            this.elementsEvents = $.extend({}, this.elementsEvents || {});
            this.modelEvents = $.extend({}, this.modelEvents || {});

            this.initializeModelElements();
            this.delegateElementsEvents();
        },

        disposeElements: function() {
            if (!this.elementsInitialized) {
                return;
            }
            this.undelegateElementsEvents();

            const props = ['$elements', 'elements', 'elementsEvents', 'modelElements', 'modelEvents'];
            for (let i = 0, length = props.length; i < length; i++) {
                delete this[props[i]];
            }
        },

        initializeModelElements: function() {
            if (!this.model) {
                return;
            }
            _.each(this.modelElements, function(elementKey, modelKey) {
                if (this.elementsEvents[elementKey + ' setModelValue'] === undefined) {
                    this.elementsEvents[elementKey + ' setModelValue'] = ['change', _.bind(function(e) {
                        return this.setModelValueFromElement(e, modelKey, elementKey);
                    }, this)];
                }

                if (this.modelEvents[modelKey + ' setElementValue'] === undefined) {
                    this.modelEvents[modelKey + ' setElementValue'] = ['change', _.bind(function(e) {
                        return this.setElementValueFromModel(e, modelKey, elementKey);
                    }, this)];
                }

                if (this.modelEvents[modelKey + ' focus'] === undefined) {
                    this.modelEvents[modelKey + ' focus'] = ['focus', _.bind(function() {
                        this.getElement(elementKey).focus();
                    }, this)];
                }
            }, this);

            this.setModelValueFromElements();
        },

        setModelValueFromElements: function() {
            _.each(this.modelElements, function(elementKey, modelKey) {
                this.setModelValueFromElement(null, modelKey, elementKey);
            }, this);
        },

        delegateElementsEvents: function() {
            if (!this.elementsInitialized) {
                return;
            }
            _.each(this.elementsEvents, function(eventCallback, eventKey) {
                if (!eventCallback) {
                    return;
                }
                const key = eventKey.split(' ')[0];
                const event = eventCallback[0];
                const callback = eventCallback[1];
                this.delegateElementEvent(key, event, callback);
            }, this);

            _.each(this.modelEvents, function(eventCallback, eventKey) {
                if (!eventCallback) {
                    return;
                }
                const key = eventKey.split(' ')[0];
                const event = eventCallback[0];
                const callback = eventCallback[1];
                this.delegateModelEvent(key, event, callback);
            }, this);
        },

        delegateElementEvent: function(key, event, callback) {
            const self = this;
            if (!_.isFunction(callback)) {
                callback = _.bind(this[callback], this);
            }
            this.getElement(key).on(event + this.elementEventNamespace + this.cid, function(e, options) {
                options = options || {};
                options.manually = self.isChangedManually(this, e);
                return callback(e, options);
            });
        },

        delegateModelEvent: function(key, event, callback) {
            if (!_.isFunction(callback)) {
                callback = _.bind(this[callback], this);
            }
            this.model.on(event + ':' + key, function(model, attribute, options) {
                callback(options || {});
            }, this);
        },

        undelegateElementsEvents: function() {
            if (!this.elementsInitialized) {
                return;
            }
            if (this.$elements) {
                const elementEventNamespace = this.elementEventNamespace + this.cid;
                _.each(this.$elements, function($element) {
                    $element.off(elementEventNamespace);
                });
            }

            if (this.model) {
                this.model.off(null, null, this);// off all events with this context.
            }
        },

        createElementByTemplate: function(element) {
            let $element = this.getElement(element);
            if ($element.length > 0) {
                if ($element.is('script')) {
                    $element = $($element.html());
                }
            } else if (typeof this.templates[element] === 'function') {
                $element = $(this.templates[element]());
            } else {
                $element = _.template($(this.templates[element]).html());
            }

            this.$elements[element] = $element;
            return $element;
        },

        getElement: function(key, $default) {
            if (this.$elements[key] === undefined) {
                this.$elements[key] = this._findElement(key) || $default || $([]);
            }
            return this.$elements[key];
        },

        clearElementsCache: function() {
            this.$elements = {};
        },

        _findElement: function(key) {
            if (this.elements[key] === undefined && this[key] !== undefined) {
                return this[key];
            }

            let selector = this.elements[key] || null;
            if (!selector) {
                return null;
            }

            if (selector instanceof $) {
                return selector;
            }

            let $context;
            if (!_.isArray(selector)) {
                // selector = '[data-name="element"]'
                $context = this.getElement('$el');
            } else {
                // selector = ['$el', '[data-name="element"]']
                $context = this.getElement(selector[0]);
                selector = selector[1] || null;
            }

            if (!$context || !selector) {
                return null;
            }

            return $context.find(selector);
        },

        setModelValueFromElement: function(e, modelKey, elementKey) {
            const $element = this.getElement(elementKey);
            const element = $element.get(0);
            if (!$element.length) {
                return false;
            }

            const elementViewValue = $element.val();
            const modelValue = this.viewToModelElementValueTransform(elementViewValue, elementKey);
            if (modelValue === this.model.get(modelKey)) {
                return;
            }

            const options = {
                event: e,
                manually: this.isChangedManually(element, e)
            };

            if (options.manually) {
                this.model.set(modelKey + '_changed_manually', true);
            }

            this.model.set(modelKey, modelValue, options);
        },

        setElementValueFromModel: function(e, modelKey, elementKey) {
            const $element = this.getElement(elementKey);
            if (!$element.length) {
                return false;
            }

            const modelValue = this.model.get(modelKey);
            const viewValue = this.modelToViewElementValueTransform(modelValue, elementKey);
            if (viewValue === $element.val()) {
                return;
            }

            $element.val(viewValue).change();
        },
        /**
         * This function is added to add possibility to transform model value representation into the
         * view value representation. To use this function you could extend it with your own in the descendant
         *
         * @param {*}      modelValue
         * @param {String} elementKey
         * @returns {*}
         */
        modelToViewElementValueTransform: function(modelValue, elementKey) {
            return modelValue;
        },
        /**
         * This function is added to add possibility to transform view value representation into the
         * model value representation. To use this function you could extend it with your own in the descendant
         *
         * @param {*}      elementViewValue
         * @param {String} elementKey
         * @returns {*}
         */
        viewToModelElementValueTransform: function(elementViewValue, elementKey) {
            return elementViewValue;
        },

        isChangedManually: function(element, e) {
            let manually = false;
            if (e) {
                if (e.manually !== undefined) {
                    manually = e.manually;
                } else {
                    manually = Boolean(e.originalEvent && e.currentTarget === element);
                }
                e.manually = manually;
            }
            return manually;
        }
    };
});
