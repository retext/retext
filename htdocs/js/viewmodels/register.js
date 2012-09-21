/**
 * ViewModel für die Register-View
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    return Backbone.Model.extend({
        defaults:{
            error:false,
            error_name:null,
            error_message:null,
            email:null,
            code:null,
            success:false
        }
    });
});