/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/project',
    'remote'
], function (Project, Remote) {
    return Backbone.Collection.extend({
        'model':Project,
        'url':Remote.apiUrlBase + 'project'
    });
});