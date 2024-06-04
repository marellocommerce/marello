
define(function(require) {
    'use strict';

    const $ = require('jquery');
    const mediator = require('oroui/js/mediator');
    const routing = require('routing');
    const LoadingMask = require('oroui/js/app/views/loading-mask-view');
    const BaseView = require('oroui/js/app/views/base/view');
    const template =
        require('tpl-loader!marelloticket/templates/sidebar-widget/assigned-tickets-content-view.html');

    const AssignedTicketsContentView = BaseView.extend({
        defaultPerPage: 5,
        defaultStatuses: [],

        template: template,

        events: {
            'click .assigned-ticket-widget-row': 'onClickAssignedTicket'
        },

        listen: {
            refresh: 'reloadAssignedTickets'
        },

        /**
         * @inheritdoc
         */
        constructor: function AssignedTicketsContentView(options) {
            AssignedTicketsContentView.__super__.constructor.call(this, options);
        },

        render: function() {
            this.reloadAssignedTickets();
            return this;
        },

        onClickAssignedTicket: function(event) {
            const url = $(event.currentTarget).data('url');
            mediator.execute('redirectTo', {url: url});
        },

        reloadAssignedTickets: function() {
            const view = this;
            const settings = this.model.get('settings');
            settings.perPage = settings.perPage || this.defaultPerPage;
            settings.statuses = settings.statuses || this.defaultStatuses;

            const routeParams = {
                perPage: settings.perPage,
                statuses: settings.statuses
            };
            const url = routing.generate('marello_ticket_widget_sidebar_assigned_tickets', routeParams);

            const loadingMask = new LoadingMask({
                container: view.$el
            });
            loadingMask.show();

            $.get(url, function(content) {
                loadingMask.dispose();
                view.$el.html(view.template({content: content}));
            });
        }
    });

    return AssignedTicketsContentView;
});