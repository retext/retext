
define([
    'text!templates/modules/project/mode-switcher.html'
], function (Template) {
    return Backbone.View.extend({
        template:_.template(Template),
        className:'btn-group',
        initialize:function () {
        },
        render:function () {
            $(this.el).html(this.template({}));
            return this;
        }
    });
})
;
