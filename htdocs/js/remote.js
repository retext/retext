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
        0:'Oops! An unexcpected error occurred.', // Unknown error
        1:'Login failed.',
        404:'Endpoint not found.',
        901:'Database error.'
    };
    var getErrorMessage = function (response) {
        var message;
        try {
            var httpCode = response.status;
            if (httpCode == 404) {
                message = errorCodes[httpCode];
            } else {
                var status = JSON.parse(response.responseText);
                var code = status.code;
                message = errorCodes[code];
            }
        } catch (e) {
            message = errorCodes[0];
        }
        return message;
    };
    return {
        getErrorMessage:getErrorMessage,
        apiUrlBase:apiHost()
    };
});