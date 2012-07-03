/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */

define([
    'models/texttype'
], function (Model) {
    var Collection = Backbone.Collection.extend({
        model:Model
    });
    return Collection;
});