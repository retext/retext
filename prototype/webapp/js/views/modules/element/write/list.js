define([
    'collections/element',
    'collections/texthistory',
    'views/modules/element/write/container',
    'views/modules/element/write/text',
    'views/modules/texthistory',
    'text!templates/modules/element/write/list.html'
], function (ElementCollection, TextHistoryCollection, ContainerElementView, TextElementView, TextHistoryView, ViewTemplate) {
    var View = Backbone.View.extend({
        preferredContext:'info',
        events:{
            'click div.gui-element':'selectElement',
            'focus input':'selectElement'
        },
        initialize:function () {
            _.extend(this, Backbone.Events);
            this.elements = new ElementCollection();
            this.elements.url = this.model.get('container').getRelation('http://jsonld.retext.it/Element', true).get('href');
            this.elements.bind("reset", this.renderList, this);
            this.elements.bind("add", this.renderElement, this);
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
            var elementView;
            if (element.get('@context') == 'http://jsonld.retext.it/Container') {
                elementView = new ContainerElementView({model:element}).render();
            } else {
                elementView = new TextElementView({model:element}).render();
            }
            this.list.append(elementView.el);
        },
        complete:function () {
            this.elements.fetch();
        },
        selectElement:function (ev) {
            var div = $(ev.target).closest('div.gui-element');
            _.invoke(this.elements.models, 'set', 'selected', false);
            var selectedModel = this.elements.get(div.data('id'));
            selectedModel.set('selected', true);
            this.trigger('elementSelected', selectedModel);
            if (selectedModel.get('@context') == 'http://jsonld.retext.it/Text') {
                var historyCollection = new TextHistoryCollection();
                historyCollection.url = selectedModel.getRelation('http://jsonld.retext.it/TextVersion', true).get('href');
                this.trigger('contextInfo', 'history', TextHistoryView, historyCollection);
            }
        }
    });
    return View;
});
