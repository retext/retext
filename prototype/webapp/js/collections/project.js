define([
    'models/project',
    'remote'
], function (Project, Remote) {
    var ProjectCollection = Backbone.Collection.extend({
        'model':Project,
        'url':Remote.apiUrlBase + 'project'
    });
    return ProjectCollection;
});