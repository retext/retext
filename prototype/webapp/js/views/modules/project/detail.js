define([
    'vm',
    'models/project',
    'models/projectprogress',
    'views/modules/project/progress',
    'views/modules/project/contributors',
    'views/modules/project/languages',
    'text!templates/modules/project/detail.html'
], function (Vm, Model, ProjectProgressModel, ProjectProgressView, ProjectContributorView, ProjectLanguagesView, ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        progressModel:null,
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.progressModel = new ProjectProgressModel();
            this.progressModel.url = this.model.url() + '/progress';
        },
        render:function () {
            $(this.el).html(this.template({model:this.model.toJSON()}));
            $(this.el).find('.view-project-progress').html(Vm.create(this, 'project-detail-progress', ProjectProgressView, {model:this.progressModel}).el);
            $(this.el).find('.view-project-contributors').html(Vm.create(this, 'project-detail-contributors', ProjectContributorView, {model:this.model}).el);
            $(this.el).find('.view-project-languages').html(Vm.create(this, 'project-detail-languages', ProjectLanguagesView, {model:this.model}).el);
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
});
