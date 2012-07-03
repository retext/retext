/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/mode'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
