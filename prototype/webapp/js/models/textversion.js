/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    return Backbone.Model.extend({
        defaults:{
            project:null,
            parent:null,
            text:null,
            createdAt:null
        }
    });
});
