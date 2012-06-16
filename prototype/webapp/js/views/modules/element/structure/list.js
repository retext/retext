define([
    'events',
    'views/modules/element/list',
    'models/container',
    'models/text',
    'views/forms/container',
    'views/forms/text'
    /*
     'collections/element',
     'views/modules/element/structure/container',
     'views/modules/element/structure/text',
     'text!templates/modules/element/structure/list.html',
     */
], function (Events, BaseListView, ContainerModel, TextModel, ContainerForm, TextForm/* ElementCollection, ContainerElementView, TextElementView, ViewTemplate,  */) {
    return BaseListView.extend({
        preferredContext:'edit',
        mode:'structure',
        events:{
            'click div.gui-element':'selectElement',
            'click button.act-new-container':'newContainer',
            'click button.act-new-text':'newText',
            'click button.actn-delete':'deleteElement',
            'dragstart div.gui-element':'dragStartEvent',
            'dragend div.gui-element':'dragStopEvent',
            'dragenter div.gui-element':'dragEnterEvent',
            'dragleave div.gui-element':'dragLeaveEvent',
            'dragover div.gui-element':'dragOverEvent',
            'drop div.gui-element':'dragDropEvent'
        },
        postInitialize:function () {
            this.newTextModel = new TextModel({parent:this.model.get('container').id});
            this.newContainerModel = new ContainerModel({parent:this.model.get('container').id});
        },
        renderList:function () {
            this.list.empty();
            _.each(this.elements.models, function (model) {
                this.renderElement(model);
            }, this);
        },
        postRenderElement:function (elementView) {
            $(elementView.el).attr('draggable', 'true');
        },
        newContainer:function () {
            var containers = this.elements;
            var newContainerModel = this.newContainerModel.clone();
            newContainerModel.save({}, {
                success:function (model) {
                    containers.add(model);
                }
            });
        },
        newText:function () {
            var containers = this.elements;
            var newTextModel = this.newTextModel.clone();
            newTextModel.save({}, {
                success:function (model) {
                    containers.add(model);
                }
            });
        },
        selectChange:function (selectedModel) {
            var Form = (selectedModel.get('@context') == 'http://jsonld.retext.it/Container') ? ContainerForm : TextForm;
            this.trigger('contextInfo:show', 'edit', Form, selectedModel);
        },
        deleteElement:function (ev) {
            ev.stopPropagation();
            var div = $(ev.target).closest('div.gui-element');
            var selectedModel = this.elements.get(div.data('id'));
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
            this.model.get('container').save({childOrder:order}, {wait:true, silent:true});
        }
    });
});
