define(function(require) {
    'use strict';

    const __ = require('orotranslation/js/translator');
    const BaseWidgetSetupView = require('orosidebar/js/app/views/base-widget/base-widget-setup-view');

    const AssignedTicketsSetupView = BaseWidgetSetupView.extend({
        template: require('tpl-loader!marelloticket/templates/sidebar-widget/assigned-tickets-setup-view.html'),

        widgetTitle: function() {
            return __('marello.ticket.assigned_tickets_widget.settings');
        },

        /**
         * @inheritdoc
         */
        constructor: function AssignedTicketsSetupView(options) {
            AssignedTicketsSetupView.__super__.constructor.call(this, options);
        },

        validation: {
            perPage: {
                NotBlank: {},
                Regex: {pattern: '/^\\d+$/'},
                Number: {min: 1, max: 20}
            }
        },

        fetchFromData: function() {
            const data = AssignedTicketsSetupView.__super__.fetchFromData.call(this);
            data.perPage = Number(data.perPage);
            return data;
        }
    });

    return AssignedTicketsSetupView;
});