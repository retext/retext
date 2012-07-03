/**
 * Zeigt Informationen zu einem Container an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'text!templates/modules/containerinfo.html'
], function (ContainerInfoTemplate) {
    return Backbone.View.extend({
        template:_.template(ContainerInfoTemplate),
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
