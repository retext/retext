define([
    'events',
    'models/base'
], function (Events, BaseModel) {
    return BaseModel.extend({
        defaults:{
            email:null,
            project:null
        }
    });
});