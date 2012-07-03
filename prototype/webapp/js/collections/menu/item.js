/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/menu/item'
], function (MenuItem) {
    return Backbone.Collection.extend({
        'model':MenuItem
    });
});