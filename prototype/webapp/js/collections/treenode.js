/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */

define([
    'models/treenode'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
