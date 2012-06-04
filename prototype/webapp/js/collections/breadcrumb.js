define([
    'models/breadcrumb'
], function (Breadcrumb) {
    var BreadcrumbCollection = Backbone.Collection.extend({
        model:Breadcrumb
    });
    return BreadcrumbCollection;
});