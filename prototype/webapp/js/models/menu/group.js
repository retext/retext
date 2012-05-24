define([
], function () {
    var MenuGroup = Backbone.Model.extend({
        defaults:{
            children:[],
            align:null
        }
    });
    return MenuGroup;
});