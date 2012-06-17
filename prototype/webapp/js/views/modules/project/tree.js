define([
    'collections/treenode',
], function (TreenodeCollection) {
    return Backbone.View.extend({
        className:'projecttree',
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.tree = new TreenodeCollection();
            this.tree.url = this.model.get('container').getRelation('http://jsonld.retext.it/Element', true, 'http://jsonld.retext.it/ontology/tree').get('href');
            this.tree.bind("reset", this.render, this);
        },
        render:function () {
            var html = '<ul>';
            _.each(this.tree.models, function (node) {
                html += this.renderNode(node.get('data'), node.get('children'));
            }, this);
            html += '<ul>';
            $(this.el).html(html);
            return this;
        },
        renderNode:function (data, children) {
            var nodeHtml = '<li>';
            var link = false;
            if (data['@context'] == 'http://jsonld.retext.it/Container') {
                nodeHtml += '<a href="#project/' + data.project + '/' + this.model.get('mode') + '/' + data.id + '">';
                link = true;
            }
            nodeHtml += '<span class="name">' + data.name + '</span>';
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
        }
    });
});
