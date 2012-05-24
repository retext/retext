define([
    'collections/menu/group'
], function (MenuGroupCollection) {
    var pages = {};
    _.each(['about'], function (pageId) {
        pages[pageId] = new Backbone.View({'el':$('#' + pageId), 'id':pageId});
    });
    var menuGroups = new MenuGroupCollection();
    return {
        pages:pages,
        menuGroups:menuGroups
    };
});