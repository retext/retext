define([
    'models/breadcrumb',
    'text!templates/modules/project/breadcrumb.html'
], function (ModuleModel, ModuleTemplate) {
    var Module = Backbone.View.extend({
        template:_.template(ModuleTemplate),
        initialize:function (options) {
            this.project = options.project;
            this.project.bind("change", this.render, this);
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            var el = $(this.el).html(this.template({project:this.project.toJSON(), breadcrumbs:this.model.toJSON()}));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
    return Module;
})
;