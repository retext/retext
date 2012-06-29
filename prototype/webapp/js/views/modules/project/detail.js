define([
    'vm',
    'models/project',
    'models/projectprogress',
    'views/modules/project/progress',
    'text!templates/modules/project/detail.html'
], function (Vm, Model, ProjectProgressModel, ProjectProgressView, ModuleTemplate) {
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
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
});
