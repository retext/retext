/**
 * Zeigt Informationen zu einem Text-Block an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'text!templates/modules/textinfo.html'
], function (TextInfoTemplate) {
    return Backbone.View.extend({
        template:_.template(TextInfoTemplate),
        initialize:function () {
            this.model.bind('change', this.render, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({model:this.model.toJSON()}));
            return this;
        }
    });
})
;
