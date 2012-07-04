/**
 * Basis-Klasse für die Auflistung der Elemente in den verschiedenen Bearbeitungsmodi
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'events',
    'collections/element',
    'collections/comment',
    'views/modules/textcomments',
    'views/modules/textinfo',
    'views/modules/containerinfo'
], function (Events, ElementCollection, CommentsCollection, CommentsCollectionView, TextInfoView, ContainerInfoView) {
    return Backbone.View.extend({
        preferredContext:'comments',
        lastSelected:null,
        mode:null,
        events:{
            'click div.gui-element':'selectElement'
        },
        initialize:function () {
            _.extend(this, Backbone.Events);
            this.elements = new ElementCollection();
            this.elements.url = this.model.get('container').getRelation('http://jsonld.retext.it/Element', true, 'http://jsonld.retext.it/ontology/child').get('href');
            this.elements.bind("reset", this.renderList, this);
            this.elements.bind("add", this.renderElement, this);
            this.elements.bind("change", this.change, this);
            this.postInitialize();
        },
        postInitialize:function () {
        },
        render:function () {
            var that = this;
            require(['text!templates/modules/element/' + this.mode + '/list.html'], function (ViewTemplate) {
                var el = $(that.el);
                el.html(ViewTemplate);
                that.list = el.find('div.view-containers');
                that.postRender();
            });
            return this;
        },
        postRender:function () {
        },
        renderList:function () {
            this.list.empty();
            _.each(this.elements.models, function (model) {
                this.renderElement(model);
            }, this);
        },
        renderElement:function (element) {
            var list = this.list;
            var postRenderElement = this.postRenderElement;
            var preRenderText = _.bind(this.preRenderText, this);
            var project = this.model.get('project');
            require(['views/modules/element/' + this.mode + '/container', 'views/modules/element/' + this.mode + '/text'], function (ContainerElementView, TextElementView) {
                var elementView;
                if (element.get('@context') == 'http://jsonld.retext.it/Container') {
                    elementView = new ContainerElementView({model:element}).render();
                } else {
                    element.set('showLanguage', project.get('defaultLanguage'));
                    element.set('showText', !_.isNull(element.get('text')) ? element.get('text')[element.get('showLanguage')] : '');
                    preRenderText(element);
                    elementView = new TextElementView({model:element}).render();
                }
                list.append(elementView.el);
                postRenderElement(elementView);
            });
        },
        preRenderText:function (element) {
        },
        postRenderElement:function (elementView) {
        },
        complete:function () {
            this.elements.fetch();
            this.postComplete();
        },
        postComplete:function () {
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
        change:function (model, info) {
            if (_.has(info.changes, 'selected') && model.get('selected')) {
                this.trigger('elementSelected', model);
                this.loadComments(model);
                this.selectChange(model);
            }
            this.postChange(model, info);
        },
        selectChange:function (selectedModel) {
        },
        postChange:function (model, info) {
        },
        loadComments:function (model) {
            if (model.get('@context') == 'http://jsonld.retext.it/Text') {
                var commentsCollection = new CommentsCollection();
                commentsCollection.url = model.getRelation('http://jsonld.retext.it/Comment', true).get('href');
                this.trigger('contextInfo:show', 'comments', CommentsCollectionView, commentsCollection);
                commentsCollection.bind('add', function () {
                    model.set('commentCount', model.get('commentCount') + 1);
                }, this);
                this.trigger('contextInfo:show', 'info', TextInfoView, model);
            } else {
                this.trigger('contextInfo:show', 'info', ContainerInfoView, model);
                this.trigger('contextInfo:clear', 'comments');
            }
        }
    });
});
