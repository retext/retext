/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/projectlanguage',
    'remote'
], function (Model, Remote) {
    return Backbone.Collection.extend({
        model:Model
    });
});