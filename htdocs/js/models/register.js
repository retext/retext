/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote'
], function (Remote) {
    return Backbone.Model.extend({
        urlRoot:Remote.apiUrlBase + 'user',
        defaults:{
            email:null
        }
    });
});