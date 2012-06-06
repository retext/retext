define([
    'text!templates/modules/project/listing.html'
], function (ListingTemplate) {
    var ProjectListing = Backbone.View.extend({
        template:_.template(ListingTemplate),
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            $(this.el).html(this.template({projects:this.model.toJSON()}));
            return this;
        },
        complete: function()
        {
            this.model.fetch();
        }
    });
    return ProjectListing;
})
;
