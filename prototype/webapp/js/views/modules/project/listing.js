/**
 * Zeigt die Projekte des Nutzers an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'vm',
    'models/projectprogress',
    'views/modules/project/progress',
    'text!templates/modules/project/listing.html'
], function (Vm, ProjectProgressModel, ProjectProgressView, ListingTemplate) {
    return Backbone.View.extend({
        template:_.template(ListingTemplate),
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({projects:this.model.toJSON()}));
            _.each(el.children('div'), function (div) {
                var projectDiv = $(div);
                var progressModel = new ProjectProgressModel();
                var projectId = projectDiv.data('project');
                progressModel.url = this.model.get(projectId).url() + '/progress';
                var v = Vm.create(this, 'project-progress-' + projectId, ProjectProgressView, {model:progressModel});
                $(v.el).addClass('compact');
                projectDiv.find('h2').after(v.el);
            }, this)
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
});
