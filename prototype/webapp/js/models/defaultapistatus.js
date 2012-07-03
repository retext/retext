/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/apistatus'
], function (ApiStatus) {
    return ApiStatus.extend({
        defaults: {
            time: 'unknown',
            version: 'unknown'
        }
    });
});