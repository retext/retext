define([
    'collections/element',
    'collections/comment',
    'views/modules/element/check/container',
    'views/modules/element/check/text',
    'views/modules/textcomments',
    'text!templates/modules/element/check/list.html'
], function (ElementCollection, CommentsCollection, ContainerElementView, TextElementView, CommentsCollectionView, ViewTemplate) {
    var View = Backbone.View.extend({
        preferredContext:'comments',
        lastSelected:null,
        events:{
            'click div.gui-element':'selectElement'
        },
        initialize:function () {
            _.extend(this, Backbone.Events);
            this.elements = new ElementCollection();
            this.elements.url = this.model.get('container').getRelation('http://jsonld.retext.it/Element', true).get('href');
            this.elements.bind("reset", this.renderList, this);
            this.elements.bind("add", this.renderElement, this);
            this.elements.bind("change", this.triggerSelectChange, this)
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
            var selectedModel = this.elements.get(div.data('id'));
            // Allen anderen Models deselectieren
            _.invoke(_.filter(this.elements.models, function (model) {
                return !_.isEqual(model.id, selectedModel.id);
            }), 'set', 'selected', false);
            // Aktuelles Model selektieren
            selectedModel.set('selected', true);
        },
        // Dieses Callback wird verwendet, um bei mehrfachem auswählen des selben Elements nicht mehrfach zur Triggern
        triggerSelectChange:function (model, info) {
            if (!_.has(info.changes, 'selected') && !model.get('selected')) return;
            this.trigger('elementSelected', model);
            if (model.get('@context') == 'http://jsonld.retext.it/Text') {
                var commentsCollection = new CommentsCollection();
                commentsCollection.url = model.getRelation('http://jsonld.retext.it/Comment', true).get('href');
                this.trigger('contextInfo', 'comments', CommentsCollectionView, commentsCollection);
                commentsCollection.bind('add', function () {
                    model.set('commentCount', model.get('commentCount') + 1);
                }, this);
            }
        }
    });
    return View;
});
