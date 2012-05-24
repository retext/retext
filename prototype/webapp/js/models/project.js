define([
], function () {
    var Project = Backbone.Model.extend({
        validate:function (attrs) {
            if (!attrs.hasOwnProperty('name')) return 'missing_name';
            if (attrs.name.length < 1) return 'name_invalid';
        }
    });
    return Project;
});