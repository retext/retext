define([
], function(){
    return {
        apiUrlBase: window.location.protocol + '//' + window.location.host.replace(/^app\./, '') + '/api/'
    };
});