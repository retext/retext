define([
    'models/apistatus',
    'remote'
], function (ApiStatus, Remote) {
    var ApiStatusCollection = Backbone.Collection.extend({
        'model':ApiStatus,
        'url':Remote.apiUrlBase + 'status'
    });
    return ApiStatusCollection;
});