define([
    'views/page/base',
    'models/project',
    'text!templates/modules/project/info.html'
], function (PageViewBase, Model, ModuleTemplate) {
    var ModelView = PageViewBase.extend({
        template:_.template(ModuleTemplate),
        initialize:function () {
            this.model.bind("change", this.render, this);
        },
        render:function () {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
    return ModelView;
});