define([
], function () {
    var MenuItem = Backbone.Model.extend({
        'initialize':function () {
            if (!this.has('active')) this.set('active', false);
            if (!this.has('children')) this.set('children', []);
            if (!this.has('icon')) this.set('icon', false);
            if (!this.has('align')) this.set('icon', null);
        }
    });
    return MenuItem;
});