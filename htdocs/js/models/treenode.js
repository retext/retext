/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    return Backbone.Model.extend({
        defaults:{
            '@relations':null,
            '@subject':null,
            data:null,
            children:[]
        }
    });
});
