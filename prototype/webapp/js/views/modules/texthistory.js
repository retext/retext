define([
    'text!templates/modules/texthistory.html'
], function (ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        initialize: function()
        {
            this.model.bind('reset', this.render, this);
            this.model.bind('add', this.render, this);
        },
        render:function () {
            var el = $(this.el).html(this.template({versions:this.model.toJSON()}));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
})
;
