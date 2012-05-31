define([
    'text!templates/modules/project/breadcrumb.html'
], function (ModuleTemplate) {
    var Module = Backbone.View.extend({
        template:_.template(ModuleTemplate),
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            var el = $(this.el).html(this.template(this.model.toJSON()));
            return this;
        }
    });
    return Module;
})
;