define([
], function () {
    var MenuItem = Backbone.Model.extend({
        'initialize':function () {
            if (!this.has('active')) this.set('active', false);
        }
    });
    return MenuItem;
});