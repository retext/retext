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
            'click div.gui-container':'selectContainer',
            'click button.actn-delete':'deleteContainer'
        },
        initialize:function (options) {
            _.extend(this, Backbone.Events);
            this.project = options.project;
            this.model = new ContainerCollection();
            this.model.url = options.project.url() + '/container';
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
            this.list.append(new ContainerItemView({model:container}).render().el);
        },
        complete:function () {
            this.model.fetch();
        },
        newContainer:function () {
            var container = new ContainerModel();
            container.urlRoot = this.project.url() + '/container';
            var containers = this.model;
            container.save({}, {
                success:function (model, request) {
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
                success: function() {
                    div.remove();
                }
            });
        }
    });
    return View;
});
