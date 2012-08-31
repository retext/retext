/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/textversion'
], function (Model) {
    return Backbone.Collection.extend({
        model:Model
    });
});
