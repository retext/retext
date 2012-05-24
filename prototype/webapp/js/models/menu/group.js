define([
], function () {
    var MenuGroup = Backbone.Model.extend({
        'initialize':function () {
            if (!this.has('children')) this.set('children', []);
            if (!this.has('align')) this.set('icon', null);
        }
    });
    return MenuGroup;
});