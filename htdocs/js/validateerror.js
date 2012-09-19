/**
 * Hilfsklasse zum Beschreiben von Formular-Fehlern
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    var create = function () {
        var v = {
            error:false,
            missing:false,
            invalid:false,
            message:null
        };
        v.isMissing = function () {
            v.error = true;
            v.missing = true;
            v.message = "is required";
        };
        v.isInvalid = function (message) {
            v.error = true;
            v.invalid = true;
            v.message = message;
        };
        return v;
    };
    return {
        create:create
    };
});