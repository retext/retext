define([
    'vm',
    'views/menu/group'
], function (Vm, MenuGroupView) {
    var MenuView = Backbone.View.extend({
        initialize:function () {
            this.model.bind("change", this.render, this);
        },
        events:{
            'click a':'linkClicked'
        },
        render:function () {
            $(this.el).empty();
            _.each(this.model.models, function (menuGroup) {
                $(this.el).append(new MenuGroupView({model:menuGroup}).render().el)
            }, this);
            return this;
        },
        linkClicked:function (ev) {
            if ($(ev.target).attr('href') == '#status') {
                Vm.pages['status'].model.fetch({
                    success:function (messages) {
                        Vm.pages['status'].render();
                    }
                });
            }
        }
    });
    return MenuView;
});