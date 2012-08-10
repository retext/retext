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
            text:null,
            user:null,
            comment:null,
            createdAt:null
        }
    });
});
