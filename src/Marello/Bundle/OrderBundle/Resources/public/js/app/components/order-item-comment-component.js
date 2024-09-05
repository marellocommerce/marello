define(function(require) {
    'use strict';

    const $ = require('jquery');
    const _ = require('underscore');
    const BaseComponent = require('oroui/js/app/components/base/component');

    /**
     * @export oroorder/js/app/components/notes-component
     * @extends oroui.app.components.base.Component
     * @class oroorder.app.components.NotesComponent
     */
    const NotesComponent = BaseComponent.extend({
        /**
         * @property {Object}
         */
        options: {
            selectors: {
                edit: '.comment-widget-edit',
                preview: '.comment-widget-preview',
                editBtn: '.comment-widget-edit-btn',
                addBtn: '.comment-widget-add-btn',
                removeBtn: '.comment-widget-remove-btn'
            },
            template: '#order-comment-widget'
        },

        /**
         * @property {Function}
         */
        template: null,

        /**
         * @property {jQuery}
         */
        $el: null,

        /**
         * @property {jQuery}
         */
        $notes: null,

        /**
         * @property {jQuery}
         */
        $edit: null,

        /**
         * @property {jQuery}
         */
        $preview: null,

        /**
         * @property {jQuery}
         */
        $addBtn: null,

        /**
         * @property {jQuery}
         */
        $editBtn: null,

        /**
         * @property {jQuery}
         */
        $removeBtn: null,

        /**
         * @inheritDoc
         */
        constructor: function NotesComponent(options) {
            NotesComponent.__super__.constructor.call(this, options);
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options || {});
            this.$el = options._sourceElement;
            this.template = _.template($(this.options.template).text());

            this.initUI();
        },

        initUI: function() {
            this.$notes = this.$el.find('textarea');
            this.$el.html(this.template());

            this.$edit = this.$el.find(this.options.selectors.edit);
            this.$preview = this.$el.find(this.options.selectors.preview);
            this.$addBtn = this.$el.find(this.options.selectors.addBtn);
            this.$editBtn = this.$el.find(this.options.selectors.editBtn);
            this.$removeBtn = this.$el.find(this.options.selectors.removeBtn);

            this.$edit.prepend(this.$notes);

            this.$notes.change(_.bind(this.change, this));
            this.$notes.blur(_.bind(this.change, this));
            this.$preview.click(_.bind(this.addNotes, this));
            this.$addBtn.click(_.bind(this.addNotes, this))
                .mousedown(_.bind(this.addNotes, this));
            this.$editBtn.click(_.bind(this.addNotes, this))
                .mousedown(_.bind(this.addNotes, this));
            this.$removeBtn.click(_.bind(this.removeNotes, this))
                .mousedown(_.bind(this.removeNotes, this));

            this.changed();
            this.$el.show();
        },

        hasVal: function() {
            return this.$notes.val().replace(/\s/g, '').length > 0;
        },

        change: function(e) {
            if (e.relatedTarget === this.$addBtn.get(0) || e.relatedTarget === this.$editBtn.get(0)) {
                this.addNotes(e);
            } else if (e.relatedTarget === this.$removeBtn.get(0)) {
                this.removeNotes(e);
            } else {
                this.changed();
            }
        },

        changed: function() {
            if (!this.hasVal()) {
                this.removeNotes();
            } else {
                this.showPreview();
            }
        },

        addNotes: function(e) {
            this.$notes.show().focus();
            this.$preview.hide();
            this.$removeBtn.show();
            this.$addBtn.hide();
            this.$editBtn.hide();
            if (e) {
                e.preventDefault();
            }
        },

        removeNotes: function(e) {
            this.$notes.val('');
            this.showPreview();
            if (e) {
                e.preventDefault();
            }
        },

        showPreview: function() {
            if (this.hasVal()) {
                this.$preview.text(this.$notes.val()).show();
                this.$addBtn.hide();
                this.$editBtn.show();
            } else {
                this.$notes.val('');
                this.$preview.text('').hide();
                this.$addBtn.show();
                this.$editBtn.hide();
            }
            this.$notes.hide();
            this.$removeBtn.hide();
        }
    });

    return NotesComponent;
});
