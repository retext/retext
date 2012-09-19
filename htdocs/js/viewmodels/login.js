/**
 * ViewModel für die Login-View
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'validateerror'
], function (ValidateError) {
    return Backbone.Model.extend({
        defaults:{
            authorized:false,
            error:false,
            validate_error:false,
            validate:{
                email:ValidateError.create(),
                password:ValidateError.create()
            },
            unexpected_error:false,
            errormessage:null,
            loading:false,
            email:null,
            password:null
        }
    });
});