define([
    'remote'
], function (Remote) {
    var Project = Backbone.Model.extend({
        urlRoot: Remote.apiUrlBase + 'project',
        defaults:{
            id:null,
            name:null
        },
        validate:function (attrs) {
            if (!attrs.hasOwnProperty('name')) return 'missing_name';
            if (attrs.name == null || attrs.name.length < 1) return 'name_invalid';
        }
    });
    return Project;
});