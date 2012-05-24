define([
], function () {
    var ProjectList = Backbone.View.extend({
        tagName:'ul',
        template:_.template('<li><%= name %></li>'),
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            var el = $(this.el);
            var tpl = this.template;
            _.each(this.model.models, function (project) {
                el.append(tpl(project.toJSON()));
            });
            return this;
        }
    });
    return ProjectList;
})
;