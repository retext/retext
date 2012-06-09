define([
    'collections/element',
    'models/container',
    'models/text',
    'views/modules/element/structure/container',
    'views/modules/element/structure/text',
    'text!templates/modules/element/structure/list.html',
    'views/forms/container',
    'views/forms/text'
], function (ElementCollection, ContainerModel, TextModel, ContainerElementView, TextElementView, ViewTemplate, ContainerForm, TextForm) {
    var View = Backbone.View.extend({
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
        initialize:function () {
            _.extend(this, Backbone.Events);
            this.elements = new ElementCollection();
            this.elements.url = this.model.get('container').getRelation('http://jsonld.retext.it/Element', true).get('href');
            this.elements.bind("reset", this.renderList, this);
            this.elements.bind("add", this.renderElement, this);
            this.newTextModel = new TextModel({parent:this.model.get('container').id});
            this.newContainerModel = new ContainerModel({parent:this.model.get('container').id});
        },
        render:function () {
            var el = $(this.el);
            el.html(ViewTemplate);
            this.list = el.find('div.view-containers');
            return this;
        },
        renderList:function () {
            this.list.empty();
            _.each(this.elements.models, function (model) {
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
            this.elements.fetch();
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
        selectElement:function (ev) {
            var div = $(ev.target).closest('div.gui-element');
            _.invoke(this.elements.models, 'set', 'selected', false);
            var selectedModel = this.elements.get(div.data('id'));
            selectedModel.set('selected', true);
            var Form = (selectedModel.get('@context') == 'http://jsonld.retext.it/Container') ? ContainerForm : TextForm;
            this.trigger('showForm', Form, selectedModel);
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
    return View;
});
