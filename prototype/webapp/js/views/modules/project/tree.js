/**
 * Zeigt die Baum-Ansicht des Projekts an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'events',
    'collections/treenode',
    'text!templates/modules/project/tree.html'
], function (Events, TreenodeCollection, ModuleTemplate) {
    return Backbone.View.extend({
        className:'projecttree',
        template:_.template(ModuleTemplate),
        showTexts:false,
        events:{
            'click #tree-show-texts':'toggleTexts'
        },
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.tree = new TreenodeCollection();
            this.tree.url = this.model.get('container').getRelation('http://jsonld.retext.it/Element', true, 'http://jsonld.retext.it/ontology/tree').get('href');
            this.tree.bind("reset", this.render, this);
            Events.on('projectProgressChanged', this.complete, this);
            Events.on('projectTreeChanged', this.complete, this);
        },
        render:function () {
            var tree = '<ul class="gui-tree">';
            if (!this.showTexts) {
                tree = '<ul class="gui-tree hide-texts">';
            } else {
                tree = '<ul class="gui-tree">';
            }
            _.each(this.tree.models, function (node) {
                tree += this.renderNode(node.get('data'), node.get('children'));
            }, this);
            tree += '<ul>';
            $(this.el).html(this.template({showTexts:this.showTexts}));
            $(this.el).find('.gui-tree').html(tree);
            return this;
        },
        renderNode:function (data, children) {
            var nodeHtml = '';
            var link = false;
            if (data['@context'] == 'http://jsonld.retext.it/Container') {
                nodeHtml += '<li class="gui-container-node"><i class="icon-list-alt"></i> ';
                nodeHtml += '<a href="#project/' + data.project + '/' + this.model.get('mode') + '/' + data.id + '">';
                link = true;
            } else {
                nodeHtml += '<li class="gui-text-node"><i class="icon-pencil"></i> ';
            }
            nodeHtml += '<span class="name">' + (_.isUndefined(data.name) ? '<em>Kein Name</em>' : data.name) + '</span>';
            if (data['@context'] == 'http://jsonld.retext.it/Text') {
                if (data.approvedProgress < 0.5) {
                    nodeHtml += ' <i class="icon-exclamation-sign" title="Weniger als die Hälfte erledigt"></i>';
                } else if (data.approvedProgress < 1) {
                    nodeHtml += ' <i class="icon-adjust" title="Mehr als die Hälfte erledigt"></i>';
                }
            }

            if (link) nodeHtml += '</a>';
            if (children.length > 0) {
                nodeHtml += '<ul>';
                _.each(children, function (child) {
                    nodeHtml += this.renderNode(child.data, child.children);
                }, this)
                nodeHtml += '</ul>';
            }
            nodeHtml += '</li>';
            return nodeHtml;
        },
        complete:function () {
            this.tree.fetch();
        },
        toggleTexts:function (ev) {
            var input = $(ev.target).closest('input');
            if (_.isUndefined(input.attr('checked'))) {
                this.showTexts = false;
                $(this.el).find('ul.gui-tree').addClass('hide-texts');
            } else {
                this.showTexts = true;
                $(this.el).find('ul.gui-tree').removeClass('hide-texts');
            }
        }
    });
});
