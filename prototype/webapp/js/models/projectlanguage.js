define([
    'events',
    'models/base'
], function (Events, BaseModel) {
    return BaseModel.extend({
        defaults:{
            name:null,
            description:null,
            project:null
        }
    });
});