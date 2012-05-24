define([
    'models/menu/group'
], function (MenuGroup) {
    var MenuGroupCollection = Backbone.Collection.extend({
        'model':MenuGroup
    });
    return MenuGroupCollection;
});