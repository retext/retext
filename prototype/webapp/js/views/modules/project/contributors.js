/**
 * Zeigt die Projekt-Mitarbeiter an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/project',
    'collections/projectcontributor',
    'text!templates/modules/project/contributors.html'
], function (Model, ContributorsCollection, ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        contributors:null,
        events:{
            'submit form':'addContributor',
            'click button.btn-danger':'removeContributor'
        },
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.contributors = new ContributorsCollection();
            this.contributors.url = this.model.getRelation('http://jsonld.retext.it/ProjectContributor', true).get('href');
            this.contributors.bind('reset', this.render, this);
            this.contributors.bind('add', this.render, this);
            this.contributors.bind('destroy', this.render, this);
        },
        render:function () {
            $(this.el).html(this.template({model:this.model.toJSON(), contributors:this.contributors.toJSON()}));
            return this;
        },
        addContributor:function (ev) {
            ev.preventDefault();
            var newContributor = this.contributors.create({email:$(this.el).find('input[type=email]').attr('value')});
            newContributor.save();
        },
        removeContributor:function (ev) {
            var id = $(ev.target).closest('button.btn-danger').data('id');
            var theContributor = this.contributors.get(id);
            theContributor.destroy();
        },
        complete:function () {
            this.contributors.fetch();
        }
    });
});
