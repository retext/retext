define([
    'collections/apistatus',
    'models/defaultapistatus',
], function (ApiStatusCollection, DefaultApiStatusModel) {
    var StatusView = Backbone.View.extend({
        'el':$('#status'),
        'template':_.template($('#status_template').html()),
        'initialize':function () {
            this.model = new ApiStatusCollection();
            this.model.add(new DefaultApiStatusModel());
            this.model.bind("change", this.render, this);
        },
        'render':function () {
            $(this.el).empty();
            $(this.el).append(this.template(this.model.at(0).toJSON()));
            return this;
        }
    });
    return StatusView;
});