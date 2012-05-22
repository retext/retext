define([
    'models/apistatus'
], function (ApiStatus) {
    var DefaultApiStatus = ApiStatus.extend({
        'initialize':function () {
            this.set('time', 'unknown');
            this.set('version', 'unknown');
        }
    });
    return DefaultApiStatus;
});