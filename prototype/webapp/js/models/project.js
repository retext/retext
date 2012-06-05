define([
    'remote',
    'models/base'
], function (Remote, BaseModel) {
    var Project = BaseModel.extend({
        urlRoot:Remote.apiUrlBase + 'project',
        defaults:{
            '@relations':[],
            id:null,
            name:null,
            rootContainer:null
        },
        validate:function (attrs) {
            if (!attrs.hasOwnProperty('name')) return 'missing_name';
            if (attrs.name == null || attrs.name.length < 1) return 'name_invalid';
        }
    });
    return Project;
});