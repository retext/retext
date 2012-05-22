define([
], function () {
    var MenuItemView = Backbone.View.extend({
        'tagName':'li',
        'template':_.template($('#mainmenuitem_template').html()),
        'render':function () {
            $(this.el).append(this.template(this.model.toJSON()));
            return this;
        }
    });
    return MenuItemView;
});