define([
    'views/menu/item'
], function (MenuItemView) {
    var MenuGroupView = Backbone.View.extend({
        'tagName':'ul',
        'className':'nav',
        initialize:function () {
            this.model.get('children').bind("change", this.render, this);
        },
        'render':function () {
            var el = $(this.el);
            el.empty();
            _.each(this.model.get('children').models, function (menuItem) {
                el.append(new MenuItemView({'model':menuItem}).render().el);
            });

            var align = this.model.get('align');
            if (align) {
                if (align == 'left') el.addClass('pull-left');
                if (align == 'right') el.addClass('pull-right');
            }

            return this;
        }
    });
    return MenuGroupView;
});