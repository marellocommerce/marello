define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const BaseComponent = require('oroui/js/app/components/base/component');

    const SalesChannelComponent = BaseComponent.extend({
        options: {
            channelTypeSelector: 'input[name$="[channelType]"]',
            associatedSalesChannelSelector: 'input[name$="[associatedSalesChannel]"]',
            associatedSalesChannelContainerSelector: '.control-group'
        },

        $channelType: null,
        $associatedSalesChannel: null,
        $associatedSalesChannelContainer: null,

        initialize: function(options) {
            this.options = _.defaults(options || {}, this.options);
            this.$channelType = $(this.options._sourceElement).find(this.options.channelTypeSelector);
            this.$associatedSalesChannel = $(this.options._sourceElement).find(this.options.associatedSalesChannelSelector);
            this.$associatedSalesChannelContainer = this.$associatedSalesChannel.closest(this.options.associatedSalesChannelContainerSelector);

            this.$channelType.on('change', _.bind(this.toggleAssociatedSalesChannelField, this));
            this.toggleAssociatedSalesChannelField();
        },

        toggleAssociatedSalesChannelField: function() {
            let value = this.$channelType.val();

            if (value === 'pos') {
                this.$associatedSalesChannelContainer.show();
            } else {
                this.$associatedSalesChannelContainer.hide();
                this.$associatedSalesChannel.val('').trigger('change');
            }
        },

        dispose: function() {
            if (this.disposed) {
                return;
            }

            this.$channelType.off('change', _.bind(this.toggleAssociatedSalesChannelField, this));
            this.options._sourceElement.off();
            SalesChannelComponent.__super__.dispose.call(this);
        }
    });

    return SalesChannelComponent;
});
