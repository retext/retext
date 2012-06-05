define([
    'views/page/base',
    'views/modules/container/item',
    'models/container',
    'collections/container',
    'text!templates/modules/container/list.html'
], function (PageViewBase, ContainerItemView, ContainerModel, ContainerCollection, ViewTemplate) {
    var View = PageViewBase.extend({
        events:{
            'click button.act-new-container':'newContainer',
            'click button.act-new-text':'newText',
            'click div.gui-container':'selectContainer',
            'click button.actn-delete':'deleteContainer',
            'dragstart div.gui-container':'dragStartEvent',
            'dragend div.gui-container':'dragStopEvent',
            'dragenter div.gui-container':'dragEnterEvent',
            'dragleave div.gui-container':'dragLeaveEvent',
            'dragover div.gui-container':'dragOverEvent',
            'drop div.gui-container':'dragDropEvent'
        },
        initialize:function (options) {
            this.newContainerModel = options.newContainerModel;
            this.newTextModel = options.newTextModel;
            _.extend(this, Backbone.Events);
            this.model.bind("reset", this.renderList, this);
            this.model.bind("add", this.renderItem, this);
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
                this.renderItem(model);
            }, this);
        },
        renderItem:function (container) {
            var containerView = new ContainerItemView({model:container}).render();
            $(containerView.el).attr('draggable', 'true');
            this.list.append(containerView.el);
            this.list.append('<div class="gui-droptarget" data-order="' + container.get('order') + '"></div>');
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
        selectContainer:function (ev) {
            var div = $(ev.target).closest('div.gui-container');
            _.invoke(this.model.models, 'set', 'selected', false);
            var selectedModel = this.model.get(div.data('id'));
            selectedModel.set('selected', true);
            this.trigger('containerSelected', selectedModel);
        },
        deleteContainer:function (ev) {
            ev.stopPropagation();
            var div = $(ev.target).closest('div.gui-container');
            var selectedModel = this.model.get(div.data('id'));
            selectedModel.destroy({
                success:function () {
                    div.remove();
                }
            });
        },
        dragStartEvent:function (ev) {
            var f = $(ev.target);
            ev.dataTransfer.setData('text/plain', f.data('id'));
            f.addClass('dragging');
        },
        dragStopEvent:function (ev) {
            var f = $(ev.target);
            f.removeClass('dragging');
        },
        dragEnterEvent:function (ev) {
            ev.preventDefault(); // Must be called to enable drop
            var f = $(ev.target);
            f.addClass('drag-over');
            f.next('div.gui-droptarget').addClass('drag-over');
        },
        dragLeaveEvent:function (ev) {
            ev.preventDefault(); // Must be called to enable drop
            var f = $(ev.target);
            f.removeClass('drag-over');
            f.next('div.gui-droptarget').removeClass('drag-over');
        },
        dragOverEvent:function (ev) {
            ev.preventDefault(); // Must be called to enable drop
        },
        dragDropEvent:function (ev) {
            var f = $(ev.target);
            f.next('div.gui-droptarget').removeClass('drag-over');
            var collection = this.model;
            var newOrder = collection.get(f.data('id')).get('order') + 1;
            var draggedContainer = collection.get(ev.dataTransfer.getData('text/plain'));
            draggedContainer.save({order:newOrder}, {
                success:function () {
                    collection.fetch();
                }
            });
        }
    });
    return View;
});
