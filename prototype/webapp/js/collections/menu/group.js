/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/menu/group'
], function (MenuGroup) {
    return Backbone.Collection.extend({
        'model':MenuGroup
    });
});