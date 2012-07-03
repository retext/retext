/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/breadcrumb'
], function (Breadcrumb) {
    return Backbone.Collection.extend({
        model:Breadcrumb
    });
});