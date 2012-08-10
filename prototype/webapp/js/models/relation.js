/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    var Relation = Backbone.Model.extend({
        defaults:{
            relatedcontext:null,
            role:null,
            href:null,
            list:false
        }
    });
    return Relation;
});