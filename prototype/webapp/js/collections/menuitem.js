define([
    'models/menuitem'
], function (MenuItem) {
    var MenuItemCollection = Backbone.Collection.extend({
        'model':MenuItem
    });
    return MenuItemCollection;
});