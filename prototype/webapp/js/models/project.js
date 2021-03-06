/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote',
    'models/base'
], function (Remote, BaseModel) {
    return BaseModel.extend({
        urlRoot:Remote.apiUrlBase + 'project',
        defaults:{
            id:null,
            name:null,
            defaultLanguage:null,
            rootContainer:null
        },
        validate:function (attrs) {
            if (!attrs.hasOwnProperty('name')) return 'missing_name';
            if (attrs.name == null || attrs.name.length < 1) return 'name_invalid';
        }
    });
});