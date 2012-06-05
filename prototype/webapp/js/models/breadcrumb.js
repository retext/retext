define([
    'models/base'
], function (BaseModel) {
    var Breadcrumb = BaseModel.extend({
        defaults:{
            id:null,
            name:'Kein Name'
        }
    });
    return Breadcrumb;
});