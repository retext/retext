define([
    'views/page/base',
    'views/modules/element/translate/container',
    'views/modules/element/translate/text',
    'text!templates/modules/element/translate/list.html'
], function (PageViewBase, ContainerElementView, TextElementView, ViewTemplate) {
    var View = PageViewBase.extend({
        initialize:function (options) {
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
            var elementView;
            if (element.get('@context') == 'http://jsonld.retext.it/Container') {
                elementView = new ContainerElementView({model:element}).render();
            } else {
                elementView = new TextElementView({model:element}).render();
            }
            this.list.append(elementView.el);
        },
        complete:function () {
            this.model.fetch();
        },
        selectElement:function (ev) {
            var div = $(ev.target).closest('div.gui-element');
            _.invoke(this.model.models, 'set', 'selected', false);
            var selectedModel = this.model.get(div.data('id'));
            selectedModel.set('selected', true);
            this.trigger('elementSelected', selectedModel);
        }
    });
    return View;
});
