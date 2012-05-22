define([
    'vm',
    'views/menuitem'
], function (Vm, MenuItemView) {
    var MenuView = Backbone.View.extend({
        'tagName':'ul',
        'className':'nav',
        'initialize':function () {
            this.model.bind("change", this.render, this);
        },
        'events':{
            'click a':'linkClicked'
        },
        'render':function () {
            $(this.el).empty();
            _.each(this.model.models, function (menuItem) {
                $(this.el).append(new MenuItemView({'model':menuItem, 'className':menuItem.get('active') ? 'active' : ''}).render().el)
            }, this);
            return this;
        },
        'linkClicked':function (ev) {
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