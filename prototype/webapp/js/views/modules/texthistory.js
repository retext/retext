define([
    'text!templates/modules/texthistory.html'
], function (ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        tagName:'ul',
        className:'texthistory',
        initialize:function () {
            this.model.bind('reset', this.render, this);
            this.model.bind('add', this.render, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({versions:this.model.toJSON()}));
            var timestamps = el.find('time');
            if (timestamps.length > 0)  _.each(timestamps, function(t) {
                $(t).html($.timeago(t));
            });
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
})
;
