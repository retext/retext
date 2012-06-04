define([
    'models/base'
], function (BaseModel) {
    var Breadcrumb = BaseModel.extend({
        defaults:{
            '@relations':[],
            id:null,
            name:'Kein Name'
        }
    });
    return Breadcrumb;
});