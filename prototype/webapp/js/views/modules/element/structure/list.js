define([
    'views/page/base',
    'views/modules/element/structure/container',
    'views/modules/element/structure/text',
    'text!templates/modules/element/structure/list.html'
], function (PageViewBase, ContainerElementView, TextElementView, ViewTemplate) {
    var View = PageViewBase.extend({
        events:{
            'click button.act-new-container':'newContainer',
            'click button.act-new-text':'newText',
            'click div.gui-element':'selectElement',
            'click button.actn-delete':'deleteElement',
            'dragstart div.gui-element':'dragStartEvent',
            'dragend div.gui-element':'dragStopEvent',
            'dragenter div.gui-element':'dragEnterEvent',
            'dragleave div.gui-element':'dragLeaveEvent',
            'dragover div.gui-element':'dragOverEvent',
            'drop div.gui-element':'dragDropEvent'
        },
        initialize:function (options) {
            this.newContainerModel = options.newContainerModel;
            this.newTextModel = options.newTextModel;
            _.extend(this, Backbone.Events);
            this.model.bind("reset", this.renderList, this);
            this.model.bind("add", this.renderElement, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(ViewTemplate);
            this.list = el.find('div.view-containers');
            return this;
        },
        renderList:function () {
            this.list.empty();
            _.each(this.model.models, function (model) {
                this.renderElement(model);
            }, this);
        },
        renderElement:function (element) {
            if (element.get('@context') == 'http://jsonld.retext.it/Container') {
                var elementView = new ContainerElementView({model:element}).render();
            } else {
                var elementView = new TextElementView({model:element}).render();
            }
            $(elementView.el).attr('draggable', 'true');
            this.list.append(elementView.el);
        },
        complete:function () {
            this.model.fetch();
        },
        newContainer:function () {
            var containers = this.model;
            var newContainerModel = this.newContainerModel.clone();
            newContainerModel.save({}, {
                success:function (model) {
                    containers.add(model);
                }
            });
        },
        newText:function () {
            var containers = this.model;
            var newTextModel = this.newTextModel.clone();
            newTextModel.save({}, {
                success:function (model) {
                    containers.add(model);
                }
            });
        },
        selectElement:function (ev) {
            var div = $(ev.target).closest('div.gui-element');
            _.invoke(this.model.models, 'set', 'selected', false);
            var selectedModel = this.model.get(div.data('id'));
            selectedModel.set('selected', true);
            this.trigger('elementSelected', selectedModel);
        },
        deleteElement:function (ev) {
            ev.stopPropagation();
            var div = $(ev.target).closest('div.gui-element');
            var selectedModel = this.model.get(div.data('id'));
            selectedModel.url = selectedModel.get('@subject');
            selectedModel.destroy({
                success:function () {
                    div.remove();
                }
            });
        },
        /* Drag&Drop */
        dragStartEvent:function (ev) {
            var f = $(ev.target).closest('div.gui-element');
            ev.dataTransfer.setData('text/plain', f.data('id'));
            f.addClass('dragging');
        },
        dragStopEvent:function (ev) {
            var f = $(ev.target).closest('div.gui-element');
            f.removeClass('dragging');
        },
        dragEnterEvent:function (ev) {
            var f = $(ev.target).closest('div.gui-element');
            if (f.hasClass('dragging')) return;
            ev.preventDefault(); // Must be called to enable drop
            f.after('<div class="gui-drop-preview"></div>');
            f.addClass('drag-over');
        },
        dragLeaveEvent:function (ev) {
            ev.preventDefault(); // Must be called to enable drop
            var f = $(ev.target).closest('div.gui-element');
            f.removeClass('drag-over');
            f.next('div.gui-drop-preview').remove();
        },
        dragOverEvent:function (ev) {
            ev.preventDefault(); // Must be called to enable drop
        },
        dragDropEvent:function (ev) {
            var f = $(ev.target).closest('div.gui-element');
            if (f.hasClass('dragging')) return;
            f.next('div.gui-drop-preview').remove();

            var draggedId = ev.dataTransfer.getData('text/plain');
            var dragged = _.filter($(this.el).find('div.gui-element'), function (element) {
                return $(element).data('id') == draggedId;
            });
            $(dragged).detach();
            f.after(dragged);
            var order = _.map($(this.el).find('div.gui-element'), function (element) {
                return $(element).data('id');
            });
            this.trigger('elementsReordered', order);
        }
    });
    return View;
});
