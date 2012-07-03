/**
 * Zeigt die Breadcrump-Navigation zur aktuellen Eben an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'collections/breadcrumb',
    'text!templates/modules/project/breadcrumb.html'
], function (BreadcrumbCollection, ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        initialize:function () {
            this.breadcrumbCollection = new BreadcrumbCollection();
            this.breadcrumbCollection.url = this.model.get('container').getRelation('http://jsonld.retext.it/Breadcrumb', true).get('href');
            this.breadcrumbCollection.bind("change", this.render, this);
            this.breadcrumbCollection.bind("reset", this.render, this);
        },
        render:function () {
            var el = $(this.el).html(this.template({project:this.model.get('project').toJSON(), mode:this.model.get('mode'), breadcrumbs:this.breadcrumbCollection.toJSON()}));
            return this;
        },
        complete:function () {
            this.breadcrumbCollection.fetch();
        }
    });
});
