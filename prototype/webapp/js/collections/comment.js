/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/comment'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
