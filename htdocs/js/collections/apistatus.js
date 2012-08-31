/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/apistatus',
    'remote'
], function (ApiStatus, Remote) {
    return Backbone.Collection.extend({
        'model':ApiStatus,
        'url':Remote.apiUrlBase + 'status'
    });
});