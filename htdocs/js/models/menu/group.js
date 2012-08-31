/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    return Backbone.Model.extend({
        defaults:{
            children:[],
            align:null
        }
    });
});