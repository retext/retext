define([
    'views/page/base',
    'collections/project',
    'text!templates/page/project/list.html'
], function (PageViewBase, ProjectCollection, ProjectListPageTemplate) {
    var StatusView = PageViewBase.extend({
        template:_.template(ProjectListPageTemplate),
        initialize:function () {
            this.model = new ProjectCollection();
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            $(this.el).html(this.template({projects: this.model.toJSON()}));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
    return StatusView;
});