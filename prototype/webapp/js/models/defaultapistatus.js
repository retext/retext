define([
    'models/apistatus'
], function (ApiStatus) {
    var DefaultApiStatus = ApiStatus.extend({
        defaults: {
            time: 'unknown',
            version: 'unknown'
        }
    });
    return DefaultApiStatus;
});