/**
 * Einstellungen für den Zugriff auf die API
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    var apiHost = function () {
        var hostParts = window.location.hostname.split(".");
        hostParts[0] = "api";
        return window.location.protocol + '//' + hostParts.join(".") + '/';
    };
    var errorCodes = {
        loginFailed:1
    };
    return {
        apiUrlBase:apiHost(),
        errorCode:errorCodes
    };
});